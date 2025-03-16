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
////////////Creacion de la carpeta que contiene los html.

$retorno=[];

$query="select nomvis_per_movimiento.* from nomvis_per_movimiento inner join nompersonal on nompersonal.cedula=nomvis_per_movimiento.cedula where codnom='".$_GET['codigo_nomina']."' and nomvis_per_movimiento.tipnom='".$_SESSION['codigo_nomina']."' and nompersonal.email<>'' ";		
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

    function header(){

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

    function personas($nomina_id,$codtp,$registro_id){

            $conexion=conexion();
            $var_sql="select * from nomempresa";
            $rs = query($var_sql,$conexion);
            $row_rs = fetch_array($rs);
            $var_encabezado=$row_rs['nom_emp'];
            //$this->Image($var_izquierda,10,6,30,15);

            $this->SetFont('Arial','',9);
            $date1=date('d/m/Y');
            $date2=date('h:i a');

            $query="select nf.descrip as frec,tn.descrip as tiponomina,np.mes as mes, np.anio as anio from nom_nominas_pago as np inner join nomfrecuencias as nf on np.frecuencia=nf.codfre inner join nomtipos_nomina as tn on np.tipnom=tn.codtip where np.codnom = '".$nomina_id."' AND np.codtip= '".$_SESSION['codigo_nomina']."' ";
            $result2=query($query,$conexion);
            $fila2 = fetch_array($result2);

            $query="select * from nomvis_per_movimiento where codnom='".$nomina_id."' and tipnom='".$_SESSION['codigo_nomina']."' and ficha=".$registro_id;
            $resultado_lote=query($query,$conexion);
            $personas=0;
            $totalbd=num_rows($resultado_lote);
            $pers=0;

            while ($fila=fetch_array($resultado_lote))
            {
                $descripcion=$fila2['tiponomina']." ".$fila2['frec']." Mes: ".$fila2['mes']." Año: ".$fila2['anio'];
                $logo='../imagenes/'.$row_rs['imagen_izq'];
                $this->Cell(30,4,'',0,0,'L');
                if(file_exists($logo))
                    $this->Image($logo,5,7,25,13);
               // $this->Cell(120,4,utf8_decode('Republica Bolivariana de Venezuela'),0,0,'L');
                $this->Cell(38,4,utf8_decode('Periodo de Pago de Planilla: '),0,1,'R');
                $this->Cell(30,4,'',0,0,'L');
                $this->Cell(120,4,utf8_decode($var_encabezado),0,0,'L');
                $this->Cell(38,4,utf8_decode($descripcion),0,1,'R');
                $this->Ln(4);
                $this->Cell(188,4,utf8_decode('Recibo de Pago'),0,0,'C');
                $this->Ln(4);

                //Datos personal
                $registro_id=$fila['ficha'];
                $query="select * from nompersonal where ficha = '$registro_id' and tipnom='".$_SESSION['codigo_nomina']."'";
                $result=query($query,$conexion);
                $fila = fetch_array ($result);
                $cargo_id=$fila['codcargo'];
                $ingreso=$fila['fecing'];

                $query="select des_car from nomcargos where cod_car = '$cargo_id'";
                $result=query($query,$conexion);
                $row = fetch_array ($result);
                $nompre_cargo=$row[des_car];

                $consulta_n4="SELECT descrip FROM nomnivel4 WHERE codorg='".$fila['codnivel4']."'";
                $resultado_n4=query($consulta_n4,$conexion);
                $fetchn4=fetch_array($resultado_n4);
                $sub_total_dedu=0;

                $query="select cod_ban,des_ban from nombancos where cod_ban='".$fila[codbancob]."'";
                $resultado=query($query,$conexion);
                $row = mysqli_fetch_array($resultado);

                $query="select cod_car,des_car from nomcargos where cod_car='".$fila[codcargo]."'";
                $result=query($query,$conexion);
                $row = mysqli_fetch_array($result);

                $this->SetFont('Arial','',9);
                $this->SetWidths(array(118,70));
                $this->SetAligns(array('L','L'));
                $this->Setceldas(array('LT','TR'));
                $this->Setancho(array(5,5));
                $this->Row(array('Trabajador: '.$fila[apenom].' C.I.: '.$fila[cedula],'Sueldo Mensual: '.number_format($fila[suesal],2,'.','.')));
                $this->Setceldas(array('L','R'));
                $this->Row(array('Ubicacion: '.$fila[codnivel4].' '.  utf8_decode($fetchn4[descrip]),'Fecha Ingreso: '.fecha($fila[fecing])));
                $this->Setceldas(array('LB','BR'));
                $this->Row(array('Cargo: '.$row[des_car],'Cuenta: '. $fila[cuentacob]));
                $this->Ln(1);

                $query="select * from nom_movimientos_nomina as mn
                            inner join
                            nompersonal as pe on mn.ficha = pe.ficha
                            inner join
                            nomconceptos as c on c.codcon = mn.codcon
                            where pe.ficha = '$registro_id' and pe.tipnom =".$_SESSION['codigo_nomina']." and mn.codnom= '".$nomina_id."' and mn.tipnom='".$codtp."' and mn.tipcon<>'P'
                            group by pe.apenom,pe.ficha,c.formula,c.codcon order by pe.apenom, mn.tipcon";

                $resultado=query($query,$conexion);
                $this->SetFont('Arial','',10);
                $this->Cell(102,4,'Concepto',1,0,'C');
                $this->Cell(20,4,'Ref',1,0,'C');
                $this->Cell(33,4,'Asignaciones',1,0,'C');
                $this->Cell(33,4,'Deducciones',1,1,'C');
                $this->ln();

                $sub_total_asig=0;
                $sub_total_dedu=0;

                while ($row = mysqli_fetch_array($resultado))
                {
                    if ($row['tipcon']=='A')
                    {
                        $valor1= number_format($row['monto'],2,',','.');
                        $valor2="";
                        $sub_total_asig=$row['monto']+$sub_total_asig;
                        $total_asig=$row['monto']+$total_asig;
                        $total_asig_gerencia=$row['monto']+$total_asig_gerencia;
                    }
                    if ($row['tipcon']=='D')
                    {
                        $valor2= number_format($row['monto'],2,',','.');
                        $valor1="";
                        $sub_total_dedu=$row['monto']+$sub_total_dedu;
                        $total_dedu=$row['monto']+$total_dedu;
                        $total_deduc_gerencia=$row['monto']+$total_deduc_gerencia;
                    }

                    // llamado para hacer multilinea sin que haga salto de linea
                    $this->SetFont('Arial','I',9);
                    $this->SetWidths(array(102,20,33,33));
                    $this->SetAligns(array('L','C','R','R'));
                    $this->Setceldas(array(0,0,0,0));
                    $this->Setancho(array(5,5,5,5));
                    $this->Row(array($row[codcon].'  '.$row[descrip],$row[valor],$valor1,$valor2));
                }

                $this->Cell(122,5,'Totales: ',0,0,'C');
                $this->Cell(33,6,number_format($sub_total_asig,2,',','.'),'T',0,'R');
                $this->Cell(33,6,number_format($sub_total_dedu,2,',','.'),'T',1,'R');
                $this->Cell(115,5,'',0,0,'C');
                $this->Cell(40,5,'Neto a Cobrar : ','T',0,'C');
                $this->Cell(33,6,number_format($sub_total_asig-$sub_total_dedu,2,',','.'),'T',1,'R');
                $this->Ln(8);
                $this->Cell(64,5,'');
                $this->Cell(60,6,'RECIBO CONFORME','T',0,'C');
                $this->Cell(64,5,'');
                $this->Ln(15);
                $pers+=1;
                if($pers==2)
                {
                    $this->Ln(420);
                    $pers=0;
                }

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
	$nompre_empresa=$row['nom_emp'];
	$ciudad=$row['ciu_emp'];
	$gerente=$row['ger_rrhh'];
    $logo=$row['imagen_izq'];

    $email_correo     = $row['correo_sistemas2'];
    $password_correo  = $row['correo_sistemas2_password'];
    $remitente_correo = $row['correo_sistemas2_remitente'];
    $host_correo      = $row['correo_sistemas2_host'];
    $puerto_correo    = $row['correo_sistemas2_puerto'];
    $modo_correo      = $row['correo_sistemas2_modo'];


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
        if(!$email_correo){
            $mail->Host = "smtp.gmail.com";
            //$mail->Host = "localhost";
            
            //indico el puerto que usa Gmail
            $mail->Port = 465;
            $mail->SMTPSecure = 'ssl';
            // El correo y contraseña de donde saldran los mensajes.
            $mail->Username = "planillaexpresspanama@gmail.com";
            $mail->Password = "wxjknutzwjuqbvop";
    
              //Indicamos cual es nuestra dirección de correo y el nombre que 
            //queremos que vea el usuario que lee nuestro correo
            $mail->From = "planillaexpresspanama@gmail.com";
            $mail->FromName = "Planillaexpress";
            
        }
        else{
            $mail->Host = $host_correo;
            //$mail->Host = "localhost";
            
            //indico el puerto que usa Gmail
            $mail->Port = $puerto_correo;
            $mail->SMTPSecure = $modo_correo;
            // El correo y contraseña de donde saldran los mensajes.
            $mail->Username = $email_correo;
            $mail->Password = $password_correo;
    
              //Indicamos cual es nuestra dirección de correo y el nombre que 
            //queremos que vea el usuario que lee nuestro correo
            $mail->From = $remitente_correo;
            $mail->FromName = "Planillaecloud";

        }




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
