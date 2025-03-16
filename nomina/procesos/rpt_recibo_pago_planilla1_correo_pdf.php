<?php 
session_start();
ob_start();
$termino=$_SESSION['termino'];
	
include ("../header.php");
include ("../paginas/func_bd.php");
include ("../lib/common.php");
require('../fpdf/fpdf.php');

require("PHPMailer_5.2.4/class.phpmailer.php");
include("PHPMailer_5.2.4/class.smtp.php");
$mail = new PHPMailer();
$mail->IsSMTP();

$nomina_id=$_GET['codigo_nomina'];
$codtp=$_SESSION['codigo_nomina'];

//function fecha($value) { // fecha de YYYY/MM/DD a DD/MM/YYYY
// if ( ! empty($value) ) return substr($value,8,2) ."/". substr($value,5,2) ."/". substr($value,0,4);
//}

////////////Creacion de la carpeta que contiene los html.

$retorno=[];

//$query="select nomvis_per_movimiento.* from nomvis_per_movimiento inner join nompersonal on nompersonal.cedula=nomvis_per_movimiento.cedula where codnom='".$_GET['codigo_nomina']."' and nomvis_per_movimiento.tipnom='".$_SESSION['codigo_nomina']."' and nompersonal.email<>'' ";		
$query="SELECT mn.*
        FROM nom_movimientos_nomina mn 
        LEFT JOIN nompersonal pe on mn.ficha = pe.ficha
        WHERE mn.codnom='".$nomina_id."' and mn.tipnom='".$codtp."' AND (pe.estado NOT LIKE '%Egresado%' AND pe.estado  NOT LIKE '%De Baja%' AND pe.estado  NOT LIKE '%Retirado%')
        AND mn.codcon in (100,102) AND pe.email<>''
        GROUP by pe.ficha 
        ORDER by pe.ficha ASC"; 
$result_lote=sql_ejecutar($query);	

$directorio="recibos/nomina_".$_GET['codigo_nomina']."_tipo_".$_SESSION['codigo_nomina']."_".date("Y_m_d_H_m");
if (!is_dir($directorio))
{
    mkdir($directorio,0777,true);
}
echo "<div align='center' style=\"background-color : #84225b; color : #fdfdfd; font-family : 'Arial Black'; font-size : 15px;\">Envío de Correo</div>";
//echo "Directrorio Creado";
echo "</br>";
        
class PDF extends FPDF
{

    var $nomempresa,$rif;
    function header(){

        $conexion=conexion();
        $var_sql="select * from nomempresa";
        $rs = $conexion->query($var_sql);;
        $row_rs = $rs->fetch_array();
    	$this->nomempresa =$row_rs['nom_emp'];
        $this->rif=$row_rs['rif'];
        $var_encabezado=$row_rs["nom_emp"];
        $var_rif=$row_rs['rif'];
        $var_izquierda='../imagenes/'.$row_rs[imagen_izq];
        $var_derecha='../imagenes/'.$row_rs[imagen_der];
        //$this->Image($var_derecha,10,6,30,15);

        $this->SetFont('Arial','',9);
        $date1=date('d/m/Y');
        $date2=date('h:i a');	
        //$this->Ln(10);

        $this->Cell(70,4,utf8_decode($var_encabezado),0,0,'L');
        //	$this->Cell(70,5,utf8_decode($var_encabezado),0,0,'L');
        $this->Cell(50,4,'RECIBO DE PAGO'.$ANIO,0,0,'C');
        $this->Cell(70,4,'Fecha:  '.$date1,0,1,'R');
        //	$this->Cell(50,5,'Gobierno de Carabobo',0,0,'L');  
        $this->Cell(70,4,utf8_decode($var_rif),0,0,'L');
        $this->Cell(150,4,'',0,0,'L');
        $this->Ln(3);
        //	
    }

    //Hacer que sea multilinea sin que haga un salto de linea
    var $widths;
    var $aligns;
    var $celdas;
    var $ancho;



    function SetWidths($w)
    {
        //Set the array of column widths
        $this->widths=$w;
    }
    function SetAligns($a)
    {
        //Set the array of column alignments
        $this->aligns=$a;
    } 
    // Marco de la celda
    function Setceldas($cc)
    {
       
        $this->celdas=$cc;
    } 
    // Ancho de la celda
    function Setancho($aa)
    {
        $this->ancho=$aa;
    }
    function CheckPageBreak($h)
    {
        //If the height h would cause an overflow, add a new page immediately
        if($this->GetY()+$h>$this->PageBreakTrigger)
            $this->AddPage($this->CurOrientation);
    }

    function NbLines($w,$txt)
    {
        //Computes the number of lines a MultiCell of width w will take
        $cw=&$this->CurrentFont['cw'];
        if($w==0)
            $w=$this->w-$this->rMargin-$this->x;
        $wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
        $s=str_replace("\r",'',$txt);
        $nb=strlen($s);
        if($nb>0 and $s[$nb-1]=="\n")
            $nb--;
        $sep=-1;
        $i=0;
        $j=0;
        $l=0;
        $nl=1;
        while($i<$nb)
        {
            $c=$s[$i];
            if($c=="\n")
            {
                $i++;
                $sep=-1;
                $j=$i;
                $l=0;
                $nl++;
                continue;
            }
            if($c==' ')
                $sep=$i;
            $l+=$cw[$c];
            if($l>$wmax)
            {
                if($sep==-1)
                {
                    if($i==$j)
                        $i++;
                }
                else
                    $i=$sep+1;
                $sep=-1;
                $j=$i;
                $l=0;
                $nl++;
            }
            else
                $i++;
        }
        return $nl;
    } 
    function Row($data)
    {
        //Calculate the height of the row
        $nb=0;
        for($i=0;$i<count($data);$i++)
            $nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));
        $h=5*$nb;
        //Issue a page break first if needed
        $this->CheckPageBreak($h);
        //Draw the cells of the row
        for($i=0;$i<count($data);$i++)
        {
            $w=$this->widths[$i];
            $a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'C';
            //Save the current position
            $x=$this->GetX();
            $y=$this->GetY();
            //Draw the border
            //$this->Rect($x,$y,$w,$h);
            //Print the text
            $this->MultiCell($w,$this->ancho[$i],$data[$i],$this->celdas[$i],$a);
            //Put the position to the right of the cell
            $this->SetXY($x+$w,$y);
        }
        //Go to the next line
        $this->Ln($h);
    }
    //fin

    function personas($nomina_id,$codtp,$registro_id)
    {

            $conexion=conexion();
            $var_sql="select * from nomempresa";
            $rs = query($var_sql,$conexion);
            $row_rs = fetch_array($rs);
            $var_encabezado=$row_rs['nom_emp'];
            //$this->Image($var_izquierda,10,6,30,15);
            
            $consulta3 = "SELECT periodo_ini, periodo_fin, periodo FROM nom_nominas_pago WHERE codnom = '".$nomina_id."' AND codtip = '".$codtp."'";
            $result5 = $conexion->query($consulta3);        
            $fetch5 = $result5->fetch_array();
            
            $query="SELECT * from nompersonal where ficha = '$registro_id' and tipnom=$codtp";
            $result=$conexion->query($query);	
            $fila = $result->fetch_array();
            $cargo_id=$fila['codcargo'];
            $ingreso=$fila['fecing'];
            
            $query="SELECT des_car from nomcargos where cod_car = '$cargo_id'";		
            $result=$conexion->query($query);		
            $row = $result->fetch_array();
            $nompre_cargo=$row[des_car];
            $sub_total_dedu=0;
            $this->Ln(3);
        
            $this->SetFont('Arial','',9);
            $this->SetWidths(array(80,90,80));
            $this->SetAligns(array('L','L','L'));
            $this->Setceldas(array(0,0,0));
            $this->Setancho(array(4,4,4));
            $query="SELECT cod_ban,des_ban from nombancos where cod_ban='".$fila[codbancob]."'";
            $resultado=$conexion->query($query);	
            $row2 = mysqli_fetch_array($resultado);
            $this->Row(array('Ficha: '.$fila[ficha],utf8_decode('Nombre:').utf8_decode($fila[apenom]),utf8_decode('Cédula:').$fila[cedula]));
            
                    
            $this->SetWidths(array(60,123));
            $this->Row(array('Sueldo/salario: '.number_format($fila[suesal],2,',','.'),$_SESSION[nomina]));
            $this->SetWidths(array(100,123));
            $this->Row(array('Periodo del: '.fecha($fetch5['periodo_ini']).' al: '.fecha($fetch5['periodo_fin'])));
            $this->SetWidths(array(10,10));
            $query="SELECT cod_car,des_car from nomcargos where cod_car='".$fila[codcargo]."'";
            $result=$conexion->query($query);	
            $row = $result->fetch_array();
            $this->SetWidths(array(100,123));
            $this->SetAligns(array('L','L'));
            $this->Setceldas(array(0,0));
            $this->Setancho(array(4,4));
            $this->Row(array('Cargo: '.utf8_decode($row[des_car]),'Banco/Cuenta: '.$row[des_ban] .$row2[des_ban].'- '. $fila[cuentacob]));
            $this->Ln(1);
                        
            $query="SELECT mn.codcon, c.descrip, mn.tipcon, mn.monto, mn.valor, pe.apenom,c.formula,c.ccosto
                FROM nom_movimientos_nomina as mn
                LEFT join nompersonal as pe on mn.ficha = pe.ficha
                LEFT join nomconceptos as c on c.codcon = mn.codcon
                where pe.ficha = '$registro_id' and pe.tipnom =".$codtp." AND mn.codnom= '".$nomina_id."' AND mn.tipnom='".$codtp."' and mn.tipcon<>'P' 
                AND (pe.estado NOT LIKE '%Egresado%' AND pe.estado  NOT LIKE '%De Baja%')
                AND ((mn.codcon >=100 AND mn.codcon <=209) OR (mn.codcon>=500 AND mn.codcon<=599) OR (mn.codcon>=3000 AND mn.codcon<=3002))
                group by pe.apenom,pe.ficha,c.formula,c.codcon 
                order by pe.ficha,pe.apenom, mn.tipcon";

            $this->Cell(1,1,utf8_decode('-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------'),0,0,'L');
            $this->Ln(1);

                $result =$conexion->query($query);	
            $this->Cell(80,4,utf8_decode('Código y Descripción de Concepto'),0,0,'L');
            $this->Cell(25,4,utf8_decode('Ref'),0,0,'C');
            $this->Cell(31,4,utf8_decode('Asignación'),0,0,'R');
            $this->Cell(30,4,utf8_decode('Deducción'),0,0,'R');
            $this->Cell(25,4,utf8_decode('Saldo P.'),0,0,'R');  
            $this->Ln(3);

            $this->Cell(1,4,utf8_decode('-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------'),0,0,'L');
            $this->Ln(2);

            $sub_total_asig=0;
            $sub_total_dedu=0;
            while ($row = $result->fetch_array())
            {
                $saldo=0;
                if ($row[tipcon]=='A')
                {
                    $asig= number_format($row[monto],2,',','.');
                    $sub_total_asig=$row[monto]+$sub_total_asig;
                }
                if ($row[tipcon]=='D')
                {
                    $dedu= number_format($row[monto],2,',','.');
                    $sub_total_dedu=$row[monto]+$sub_total_dedu;
                    if(($row[codcon]>=500)&&($row[codcon]<=599))
                    {
                        $consulta = "SELECT SUM(salfinal) as total 
                        FROM nomprestamos_detalles as pd inner join nomprestamos_cabecera as pc on (pd.numpre=pc.numpre) 
                        WHERE pd.ficha='".$registro_id."' and pd.fechaven between '".$fetch5['periodo_ini']."' and '".$fetch5['periodo_fin']."' 
                        and pd.estadopre in ('Pendiente','Cancelada') and pc.codigopr='".$row[ccosto]."'";
                        $resultsal= $conexion->query($consulta);
                        $rowsal = $resultsal->fetch_array();
                        $saldo = $rowsal[total];
                        
                    }
                }
                $saldo = number_format($saldo,2,'.',',');
                $this->SetFont('Arial','',9);
                $this->SetWidths(array(80,25,31,30,25));
                $this->SetAligns(array('L','C','R','R','R'));
                $this->Setceldas(array(0,0,0,0,0));
                $this->Setancho(array(4,4,4,4,4));
                $this->Row(array($row[codcon] . ' - ' . utf8_decode($row[descrip]),$row[valor],$asig,$dedu,$saldo));
                $asig='';
                $dedu='';
                
            }
            
            $this->Cell(105,5,'Sub-Totales: ',0,0,'R');
            $this->Cell(31,4,number_format($sub_total_asig,2,',','.'),'T',0,'R');
            $this->Cell(30,4,number_format($sub_total_dedu,2,',','.'),'T',1,'R');
            //	$this->Ln(1);
            $this->Cell(120,4,'Neto a Depositar : ',0,0,'R');
            $this->Cell(46,4,number_format($sub_total_asig-$sub_total_dedu,2,',','.'),0,1,'R');
                //$this->MultiCell(188,5,'Observaciones: '.$observacion,0);
            $this->Ln(10);
                //$this->Cell(188,1,'','TB',1);
            $this->Cell(44,4,'RECIBE CONFORME',0,0,'C');
            $this->Cell(40,4,'','B',0,'C');	
            $this->Cell(44,4,'FECHA',0,0,'C');
            $this->Cell(40,4,'','B',0,'C');
            $this->Ln();
            //segundo recibo de pago 
                
            if($this->GetY()<135)
            {            
                $this->SetY(-142);

                $this->Cell(1,1,utf8_decode('-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------'),0,0,'L');

                $this->Ln();
                $this->SetFont('Arial','',9);
                $date1=date('d/m/Y');
                $date2=date('h:i a');	
                //$this->Ln(10);

                $this->Cell(70,4,utf8_decode($this->nomempresa),0,0,'L');
                //	$this->Cell(70,5,utf8_decode($var_encabezado),0,0,'L');
                $this->Cell(50,4,'RECIBO DE PAGO'.$ANIO,0,0,'C');
                $this->Cell(70,4,'Fecha:  '.$date1,0,1,'R');
                //	$this->Cell(50,5,'Gobierno de Carabobo',0,0,'L');  
                $this->Cell(70,4,utf8_decode($this->rif),0,0,'L');
                $this->Cell(150,4,'',0,0,'L');
                $this->Ln(3);

            }
            else{
                $this->Ln(420);

            }
    }

    function Code39($xpos, $ypos, $code, $baseline=0.5, $height=5)
    {

        $wide = $baseline;
        $narrow = $baseline / 3 ; 
        $gap = $narrow;

        $barChar['0'] = 'nnnwwnwnn';
        $barChar['1'] = 'wnnwnnnnw';
        $barChar['2'] = 'nnwwnnnnw';
        $barChar['3'] = 'wnwwnnnnn';
        $barChar['4'] = 'nnnwwnnnw';
        $barChar['5'] = 'wnnwwnnnn';
        $barChar['6'] = 'nnwwwnnnn';
        $barChar['7'] = 'nnnwnnwnw';
        $barChar['8'] = 'wnnwnnwnn';
        $barChar['9'] = 'nnwwnnwnn';
        $barChar['A'] = 'wnnnnwnnw';
        $barChar['B'] = 'nnwnnwnnw';
        $barChar['C'] = 'wnwnnwnnn';
        $barChar['D'] = 'nnnnwwnnw';
        $barChar['E'] = 'wnnnwwnnn';
        $barChar['F'] = 'nnwnwwnnn';
        $barChar['G'] = 'nnnnnwwnw';
        $barChar['H'] = 'wnnnnwwnn';
        $barChar['I'] = 'nnwnnwwnn';
        $barChar['J'] = 'nnnnwwwnn';
        $barChar['K'] = 'wnnnnnnww';
        $barChar['L'] = 'nnwnnnnww';
        $barChar['M'] = 'wnwnnnnwn';
        $barChar['N'] = 'nnnnwnnww';
        $barChar['O'] = 'wnnnwnnwn'; 
        $barChar['P'] = 'nnwnwnnwn';
        $barChar['Q'] = 'nnnnnnwww';
        $barChar['R'] = 'wnnnnnwwn';
        $barChar['S'] = 'nnwnnnwwn';
        $barChar['T'] = 'nnnnwnwwn';
        $barChar['U'] = 'wwnnnnnnw';
        $barChar['V'] = 'nwwnnnnnw';
        $barChar['W'] = 'wwwnnnnnn';
        $barChar['X'] = 'nwnnwnnnw';
        $barChar['Y'] = 'wwnnwnnnn';
        $barChar['Z'] = 'nwwnwnnnn';
        $barChar['-'] = 'nwnnnnwnw';
        $barChar['.'] = 'wwnnnnwnn';
        $barChar[' '] = 'nwwnnnwnn';
        $barChar['*'] = 'nwnnwnwnn';
        $barChar['$'] = 'nwnwnwnnn';
        $barChar['/'] = 'nwnwnnnwn';
        $barChar['+'] = 'nwnnnwnwn';
        $barChar['%'] = 'nnnwnwnwn';

        $this->SetFont('Arial','',10);
        $this->Text($xpos, $ypos + $height + 4, $code);
        $this->SetFillColor(0);

        $code = '*'.strtoupper($code).'*';
        for($i=0; $i<strlen($code); $i++){
            $char = $code[$i];
            if(!isset($barChar[$char])){
                $this->Error('Invalid character in barcode: '.$char);
            }
            $seq = $barChar[$char];
            for($bar=0; $bar<9; $bar++){
                if($seq[$bar] == 'n'){
                    $lineWidth = $narrow;
                }else{
                    $lineWidth = $wide;
                }
                if($bar % 2 == 0){
                    $this->Rect($xpos, $ypos, $lineWidth, $height, 'F');
                }
                $xpos += $lineWidth;
            }
            $xpos += $gap;
        }
    }

    function Footer(){
    // 	$this->SetY(-15);
    // 	$this->Cell(0,10,utf8_decode('Página ').$this->PageNo().'/{nb}',0,0,'C');
    }

}

// recorrido de creacion y envio 
while ($fetchxx=fetch_array($result_lote))
{   
    $pdf=new PDF();
    $pdf->AliasNbPages();
    $pdf->AddPage('P','A4');
    $pdf->SetFont('Arial','',9);

    $registro_id=$fetchxx['ficha'];
	$codnomn=$fetchxx['codnom'];
	$nomina_id=$fetchxx['codnom'];
	$codti = $_SESSION[codigo_nomina];
	
	$query="select * from nom_nominas_pago where codnom = '".$nomina_id."' AND codtip = '".$codti."' ";	
	$result2=sql_ejecutar($query);	
	$fila2 = mysqli_fetch_array($result2);
	$frecuencia=$fila2['frecuencia'];


	$query="select * from nomempresa";		
	$result=sql_ejecutar($query);	
	$row = mysqli_fetch_array ($result);	
	$nompre_empresa=$row[nom_emp];
	$ciudad=$row[ciu_emp];
	$gerente=$row[ger_rrhh];
    $logo=$row[imagen_izq];


	$query="select * from nompersonal where ficha = '".$registro_id."'";

	$result=sql_ejecutar($query);	
	$fila = mysqli_fetch_array ($result);	
	$cargo_id=$fila['codcargo'];
	$ingreso=$fila['fecing'];
    $email=$fila['email'];
	$apenom=$fila['apenom'];

	$query="select des_car from nomcargos where cod_car = '".$cargo_id."'";		
	$result=sql_ejecutar($query);	
	$row = mysqli_fetch_array ($result);	
	$nompre_cargo=$row[des_car];

        $pdf->personas($nomina_id,$codtp,$registro_id);
        $code=$fila[cedula];
	$pdf->Code39(80, 160, $code, 1, 10);
	$ruta=$directorio."/".$fila[cedula].".pdf";
	//$archivo= fopen($ruta,"w");
	
        $pdf->Output($ruta,"F");
	chmod($directorio,0777);
	chmod($ruta,0777);

        //fwrite($archivo,$asunto);
	//fclose($archivo);
	

    #$email='jmpulgar@asys.com.ve';
    //echo $email."<br>";
    
    if($email!='')
    {
        $mail = new PHPMailer();
        $mail->SMTPAuth = true;
        $mail->isSMTP();
        $mail->SMTPDebug = 0;
        $mail->Host = "smtp.gmail.com";
        //$mail->Host = "localhost";
        
        //indico el puerto que usa Gmail
        $mail->Port = 465;
        $mail->SMTPSecure = 'ssl';
        //$mail->Host = "mail.yourdomain.com";
        // El puerto del servidor de correos.
        //$mail->Port = 26;

// cambios en el correo https://support.google.com/accounts/answer/6010255?hl=es
// cambios en php.ini 
        /*Check your php.ini file, on it, if you have this:

         ;extension=php_openssl.dll
        change it to

        extension=php_openssl.dll     */   


        // El correo y contraseña de donde saldran los mensajes.
        $mail->Username = "planillaexpresspanama@gmail.com";
        $mail->Password = "S3l3ctr4";

          //Indicamos cual es nuestra dirección de correo y el nombre que 
        //queremos que vea el usuario que lee nuestro correo
        $mail->From = "planillaexpresspanama@gmail.com";
        $mail->FromName = "Planillaexpress";

        // Incluimos el From que llegara al los correos enviados.
        //$mail->SetFrom('topinho1806@gmail.com', 'German Torres');

        // Por si se tiene que renviar a otro correo.
        //$mail->AddReplyTo("name@yourdomain.com","First Last");

        // El asunto del mensaje.
        $tit='RECIBO DE COBRO '.$_SESSION[nomina]." - DESDE: ".fecha($fila2[periodo_ini])." HASTA: ".fecha($fila2[periodo_fin])." - NO RESPONDER";

        $mail->Subject = $tit;

        // El cuerpo del mensaje se envia aquí.
        //$mail->MsgHTML($asunto);
        //$mail->MsgHTML("Prueba 2");
        $asunto="Adjunto Recibo de Pago. Por favor, NO RESPONDER a este correo. Gracias";
        $mail->Body = $asunto;
        $mail->IsHTML(true);
        //$ruta=$directorio."/".$fila[cedula].".html";
        //$mail->AddAttachment($directorio,$fila[cedula].".html");
        //$mail->AddAttachment("../imagenes/","logo_fundacion.jpg");
        // Se asigna la dirección de correo a donde se enviará el mensaje.
        $mail->AddAddress ($email);

        // Si hay archivos adjuntos se mandan así.
        $mail->AddAttachment($ruta);
        //$mail->AddAttachment("images/phpmailer_mini.gif");

        // Comprobamos que el correo se ha enviado.
        if(!$mail->Send()) {
            $retorno[]=["cedula"=>$fila[cedula], "apenom"=>$apenom, "correo"=>$email, "estatus"=>"Error","error"=>$mail->ErrorInfo];
            //echo $ruta." ".$email." Mailer Error: " . $mail->ErrorInfo;
        } else {
            $retorno[]=["cedula"=>$fila[cedula], "apenom"=>$apenom, "correo"=>$email, "estatus"=>"OK"];
            //echo $ruta." "."enviado a $email";
            //echo "Message enviado!";
        }
        $mail->ClearAddresses();
        $mail->ClearAttachments();

    }
    else
        $retorno[]=["cedula"=>$fila[cedula], "apenom"=>$apenom, "correo"=>"-", "estatus"=>"No enviado"];
//exit(0);
}
//echo "<br/>";
//echo "Archivos creados exitosamente";

//Mostrar la salida
print "<table class='table table-striped table-bordered table-hover'>";
print "<thead>";
print "<tr style=''>";
print "<th>Cédula</th>";
print "<th>Apellido/Nombre</th>";
print "<th>Correo</th>";
print "<th>Estatus Envío</th>";
print "</tr>";
print "</thead>";
print "<tbody>";
for($i=0; $i<count($retorno); $i++) { 
    print "<tr>";
    print "<td>".$retorno[$i]["cedula"]."</td>";
    print "<td>".utf8_encode($retorno[$i]["apenom"])."</td>";
    print "<td>".$retorno[$i]["correo"]."</td>";
    print "<td>".$retorno[$i]["estatus"]."</td>";
    print "</tr>";
}
print "</tbody>";
print "</table>";




?>
<script type="text/javascript" src="../../includes/datatable/js/jquery.dataTables.min.js"></script>
<script type="text/javascript">
$(document).ready(function(){ 

    $('#table_datatable').DataTable({});
});
</script>