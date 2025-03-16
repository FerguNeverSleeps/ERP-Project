<?php
error_reporting(0);
try{
	// Set Error Handler
	// A user-defined exception handler function
	function myException($exception) {
		ob_end_clean();
		echo "<b>Exception:</b> ". $exception->getMessage();
	}
	// Set user-defined exception handler function
	set_exception_handler("myException");

	if (!isset($_SESSION)) {
		session_start();
	}
	require_once '../../nomina/fpdf/fpdf.php';
	require_once '../../nomina/lib/config.php';
	require_once '../../nomina/lib/pdfcommon.php';
	require_once '../../nomina/lib/common.php';
	require_once '../../nomina/procesos/PHPMailer_5.2.4/class.phpmailer.php';
	require_once '../../nomina/procesos/PHPMailer_5.2.4/class.smtp.php';
	require_once '../../nomina/paginas/funciones_nomina.php';


	$cantidad_registros=13;
	$conexion=conexion();
        
        $var_sql="select * from nomempresa";
        $rs = query($var_sql,$conexion);
        $row_rs = fetch_array($rs);
        $var_encabezado=$row_rs['nom_emp'];
	$var_imagen_izq=$row_rs['imagen_izq'];
        $correo_sistemas2=$row_rs['correo_sistemas2'];
        $correo_sistemas2_password=$row_rs['correo_sistemas2_password'];
        $correo_sistemas2_remitente=$row_rs['correo_sistemas2_remitente'];
        $correo_sistemas2_host=$row_rs['correo_sistemas2_host'];
        $correo_sistemas2_puerto=$row_rs['correo_sistemas2_puerto'];
        $correo_sistemas2_modo=$row_rs['correo_sistemas2_modo'];

	function fecha($value) { // fecha de YYYY/MM/DD a DD/MM/YYYY
		if ( ! empty($value) ) return substr($value,8,2) ."/". substr($value,5,2) ."/". substr($value,0,4);
	}

	class PDF extends FPDF
	{
		//Cabecera de página
		function header(){
			//vacia
		}
	
		//Pie de página
		function Footer()
		{
			//Posición: a 1,5 cm del final
			$this->SetY(-15);
	
			$this->SetFont('Arial','I',8);
			$this->Cell(0,5,utf8_decode('Página ').$this->PageNo().'/{nb}',0,1,'C');
			$this->Cell(0,5,'Elaborado Por: '.$_SESSION['nombre'],0,0,'L');
	
			//Número de página
			// $this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
		}
		//Hacer que sea multilinea sin que haga un salto de linea
		var $widths;
		var $aligns;
		var $celdas;
		var $ancho;
		var $nro_ocs;
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
		//fi1
	
		function getcuotas($ficha,$codigopr,$f_ini,$f_fin){
	
			$conexion=conexion();
			$consulta = "SELECT count(pd.numcuo) cuotas
			FROM nomprestamos_detalles as pd 
			inner join nomprestamos_cabecera as pc on (pd.numpre=pc.numpre) 
			WHERE pd.ficha='".$ficha."' and pd.fechaven between '".$f_ini."' and '".$f_fin."' 
			and pd.estadopre in ('Pendiente' , 'Cancelada')
			and pc.numpre='".$codigopr."'";
			/*$consulta = "SELECT pd.numcuo
			FROM nomprestamos_detalles as pd 
			inner join nomprestamos_cabecera as pc on (pd.numpre=pc.numpre) 
			WHERE pd.ficha='".$ficha."' and pd.fechaven between '".$f_ini."' and '".$f_fin."' 
			and pd.estadopre in ('Pendiente' , 'Cancelada')
			and pc.numpre='".$codigopr."'
			group by pc.numpre";*/
			$resultsal=query($consulta,$conexion);
			$rowsal = fetch_array($resultsal);
			$cuotas = $rowsal['cuotas'];
			//$saldo = number_format($saldo,2,'.',',');
			return $cuotas;
	
		}
	
		function getsaldo($ficha,$codigopr,$f_ini,$f_fin,$numcuo){
	
			$f_fin = date('Y-m-d', strtotime($f_fin. ' + 1 day'));
	
			$conexion=conexion();
			if($numcuo > 1)
				$limit = "limit 1,".$numcuo;
			else
				$limit = "limit 1";
	
			$consulta = "SELECT SUM(salfinal) as total
			FROM nomprestamos_detalles as pd 
			inner join nomprestamos_cabecera as pc on (pd.numpre=pc.numpre) 
			WHERE pd.ficha='".$ficha."' and pd.fechaven between '".$f_ini."' and '".$f_fin."' 
			and pd.estadopre in ('Pendiente' , 'Cancelada')
			and pc.numpre='".$codigopr."'
			group by pd.numcuo
			order by pd.numcuo ".$limit;
				
			$resultsal=query($consulta,$conexion);
			/*$cuotasQuery=[];
			$i=1;
			while( $rowsal = fetch_array($resultsal) ){
				$cuotasQuery[ $i ] = $rowsal['total'];
				$i++;
			}
			return $cuotasQuery[ $numcuo ];*/
			$rowsal = fetch_array($resultsal);
			$saldo = $rowsal['total'];
			$saldo = number_format($saldo,2,'.',',');
			return $saldo;
	
	
		}
	
		function datos($codnom,$codtip,$ficha,$boleta){
			$conexion=conexion();
	
			$var_sql="select * from nomempresa";
			$rs = query($var_sql,$conexion);
			$row_rs = fetch_array($rs);
	
			$sql1 = "select * from config_reportes_planilla where id = 3";
			$res1=query($sql1,$conexion);
			$configReporte=fetch_array($res1);
	
			$sql2 = "select * from config_reportes_planilla_columnas where id_reporte = 3";
			$res2=query($sql2,$conexion);
			
			while($rptColumnas=fetch_array($res2)){
				$conceptosQuery[$rptColumnas['nombre_corto']] = $rptColumnas['conceptos'];
			}
	
			$consulta3 = "SELECT periodo_ini, periodo_fin, periodo FROM nom_nominas_pago WHERE codnom = '".$codnom."' AND codtip = '".$codtip."'";
			$result5 = query($consulta3,$conexion);
			$nomina = fetch_array($result5);
			$var_encabezado=$row_rs['nom_emp'];
			$var_imagen_izq=$row_rs['imagen_izq'];
			
			$periodo_ini=date("d/m/Y",strtotime($nomina['periodo_ini']));
			$periodo_fin=date("d/m/Y",strtotime($nomina['periodo_fin']));
	
			$date1=date("d/m/Y");
			$date2=date("h:m:s");
			
			$logo = "../../nomina/imagenes/".$var_imagen_izq;
			
			$this->SetFont('Arial','B',10);
			$this->SetTextColor(24,13,195);
			$this->Cell(40,10, $this->Image($logo,$this->GetX(),$this->GetY(),35) ,0,0,'C');	
			$this->Cell(60,5,$var_encabezado,0,0,'L');
			$this->Cell(60,5,$configReporte['titulo1'],0,0,'L');
			$this->SetFont('Arial','',7);
			$this->Cell(20,5,'Fecha:',0,0,'R');
			$this->Cell(20,5,$date1,0,1,'L');
			
			$this->Cell(40);
			$this->SetFont('Arial','',8);	
			$this->Cell(60,5,$row_rs['dir_emp'],0,0,'L');
			$this->Cell(60,5,"",0,0,'L');
			$this->SetFont('Arial','',7);
			$this->Cell(20,5,'Hora:',0,0,'R');
			$this->Cell(20,5,$date2,0,1,'L');
			
			$this->Cell(40);	
			$this->SetFont('Arial','',7);
			$this->Cell(60,5,$row_rs['ciu_emp'],0,0,'L');
			
			$this->SetFont('Arial','B',10);
			$this->Cell(60,5,"Del ".$periodo_ini." al ".$periodo_fin,0,0,'L');
			$this->SetFont('Arial','',7);
			$this->Cell(20,5,'Boleta No.:',0,0,'R');
			$this->Cell(20,5,$boleta,0,1,'L');
			
			$this->Cell(40);	
			$this->SetFont('Arial','',8);
			$this->Cell(60,5,utf8_decode("Teléfono ".$row_rs['tel_emp']),0,0,'L');
			$this->Cell(60,5,"",0,0,'L');
			$this->Cell(20,5,'',0,0,'R');
			$this->Cell(20,5,'',0,1,'L');
	
			$this->SetTextColor(0,0,0);
			$this->ln(2);
	
	
			$tipo=$_SESSION['codigo_nomina'];
			$consulta="select a.*,b.descrip from nompersonal a left join nomnivel1 b on a.codnivel1 = b.codorg where a.ficha='{$ficha}'";
			$resultado=query($consulta,$conexion);
			$rc=fetch_array($resultado);
	
			$this->SetFont('Arial','B',9);
			$this->Cell(5,5,'',0,0,'L');
			$this->Cell(30,5,'Nombre: ',0,0,'L');
			$this->Cell(70,5,$rc['apenom'],0,0,'L');
			$this->Cell(30,5,utf8_decode('Código: '),0,0,'L');
			$this->SetFont('Arial','',9);
			$this->Cell(70,5,$rc['ficha'],0,1,'L');
	
			$this->SetFont('Arial','B',9);
			$this->Cell(5,5,'',0,0,'L');
			$this->Cell(30,5,'Centro de Costos: ',0,0,'L');
			$this->Cell(70,5,$rc['descrip'],0,0,'L');
			$this->SetFont('Arial','B',9);
			$this->Cell(30,5,utf8_decode('Identificación: '),0,0,'L');
			$this->SetFont('Arial','',9);
			$this->Cell(70,5,$rc['cedula'],0,1,'L');
	
			//$this->ln(1);
			
			$this->SetFont('Arial','B',9);
			$this->SetTextColor(24,13,195);
			$this->Cell(5,5,'',"B",0,'L');
			$this->Cell(90,5,'Beneficios ',"B",0,'L');
			
			$this->Cell(10,5,'',0,0,'L');
	
			$this->Cell(5,5,'',"B",0,'L');
			$this->Cell(50,5,'Deducciones ',"B",0,'L');
			$this->Cell(20,5,'Saldo ',"B",0,'L');
			$this->Cell(20,5,'Total ',"B",1,'L');
	
			$sqlBeneficios = $conceptosQuery['beneficios'];
			$sqlBeneficiosNeg = $conceptosQuery['beneficios_neg'];
			$sqlDeduccion = $conceptosQuery['deducciones'];		
			//imprimir beneficios
			$consulta1="SELECT a.codnom,a.tipnom,a.ficha,b.apenom,b.cedula,c.codorg,c.descrip,a.valor,d.ccosto,
			COALESCE(( IF(a.codcon in ({$sqlBeneficios}),a.descrip,'') ),'') as concepto,
			COALESCE(( SUM(IF(a.codcon in ({$sqlBeneficios}),a.monto,0)) ),0) as monto
			FROM nom_movimientos_nomina a 
			LEFT JOIN nompersonal b ON a.ficha=b.ficha 
			LEFT JOIN nomnivel1 c ON b.codnivel1=c.codorg 
			LEFT join nomconceptos as d on d.codcon = a.codcon
			WHERE  a.ficha = {$ficha} and a.codnom={$codnom} and a.monto!=0.00 and a.codcon in ({$sqlBeneficios})
			GROUP BY a.codnom,a.ficha,a.codcon
			ORDER BY c.codorg,b.apenom,a.codcon";
			$data = array();
			$resultado=query($consulta1,$conexion);
			$i=0;
	
			while($b=fetch_array($resultado)){
	
				$data [$i] ['beneficios'] = [
					'concepto'=>$b['concepto'],
					'valor'=>$b['valor'],
					'monto'=>$b['monto'],
					'saldo'=>"",
					'tipo'=>""
				];
				$data [$i] ['deducciones'] = [
					'concepto'=>"",
					'valor'=>"",
					'monto'=>"",
					'saldo'=>""
				];
				$i++;
			}	
			//imprimir beneficios_neg
	
			$consulta2="SELECT a.codnom,a.tipnom,a.ficha,b.apenom,b.cedula,c.codorg,c.descrip,a.valor,d.ccosto,
			COALESCE(( IF(a.codcon in ({$sqlBeneficiosNeg}),a.descrip,'') ),'') as concepto,
			COALESCE(( SUM(IF(a.codcon in ( {$sqlBeneficiosNeg}),a.monto,0)) ),0 ) as monto 
			FROM nom_movimientos_nomina a 
			LEFT JOIN nompersonal b ON a.ficha=b.ficha 
			LEFT JOIN nomnivel1 c ON b.codnivel1=c.codorg 
			LEFT join nomconceptos as d on d.codcon = a.codcon
			WHERE  a.ficha = {$ficha} and a.codnom={$codnom} and a.monto!=0.00 and a.codcon in ({$sqlBeneficiosNeg})
			GROUP BY a.codnom,a.ficha,a.codcon
			ORDER BY c.codorg,b.apenom,a.codcon";
			
			
			$resultado2=query($consulta2,$conexion);
	
			while($b=fetch_array($resultado2)){
	
				$data [$i] ['beneficios'] = [
					'concepto'=>$b['concepto'],
					'valor'=>$b['valor'],
					'monto'=>$b['monto'],
					'saldo'=>"",
					'tipo'=>"-"
				];
				$data [$i] ['deducciones'] = [
					'concepto'=>"",
					'valor'=>"",
					'monto'=>"",
					'saldo'=>""
				];
				$i++;
			}
			//imprimir deducciones
			$consulta3="SELECT a.id,a.codnom,a.tipnom,a.ficha,b.apenom,b.cedula,c.codorg,c.descrip,a.valor,d.ccosto,a.numpre,e.nom_tipos_prestamos tipo,
			a.descrip as concepto,a.tipopr,
			a.monto as monto 
			FROM nom_movimientos_nomina a 
			LEFT JOIN nompersonal b ON a.ficha=b.ficha 
			LEFT JOIN nomnivel1 c ON b.codnivel1=c.codorg 
			LEFT join nomconceptos as d on d.codcon = a.codcon
			LEFT join tipos_prestamos as e on a.tipopr = e.id_tipos_prestamos
			WHERE  a.ficha = {$ficha} and a.codnom={$codnom} and a.monto!=0.00 and a.codcon in ({$sqlDeduccion})
			GROUP BY a.codnom,a.ficha,a.id
			ORDER BY a.codcon,c.codorg,b.apenom";
			
			$resultado3=query($consulta3,$conexion);
			$i = 0;
			$numcuota=1;
			$numpre=0;
			$cuotas=0;
			
			while($d=fetch_array($resultado3)){
				$saldo = "";
				if( !isset( $data [ $i ] ['beneficios'] ) ){
					
					$data [$i] ['beneficios'] = [
						'concepto'=>"",
						'valor'=>"",
						'monto'=>"",
						'saldo'=>"",
						'tipo'=>""
					];
	
				}
	
				
				//foreach( $cuotas as $cuota ){
				if($numpre != $d['numpre'] && $d['numpre'] != 0 && $numcuota==1){
					$cuotas = $this->getcuotas($ficha,$d['numpre'],$nomina['periodo_ini'],$nomina['periodo_fin']);
					$numpre = $d['numpre'];
				}
					
	
				if($numpre == $d['numpre'] && $d['numpre'] != 0){
					$saldo = $this->getsaldo($ficha,$d['numpre'],$nomina['periodo_ini'],$nomina['periodo_fin'],$numcuota);
					$numcuota++;
				}
	
				$data[ $i ] ['deducciones'] = [
					'concepto'=>$d['concepto']." ".$d['tipo'],
					'valor'=>$d['valor'],
					'monto'=>$d['monto'],
					'saldo'=> $saldo
				];
	
				if($numcuota > $cuotas){
					$numcuota=1;
					$numpre=0;
					$cuotas=1;
				}
				$i++;
	
	
				//}
	
			}
			//print_r($data);exit;
			$this->SetFont('Arial','B',8);
			$this->SetTextColor(0,0,0);
			$this->ln(2);
			$subtotal_b = $subtotal_bn = $total_b = $total_d = 0.00;
			for( $i=0; $i<count($data); $i++ ){
				//BENEFICIOS			
				$this->Cell(50,5,$data[$i]['beneficios']['concepto'],0,0,'L');
				$this->Cell(25,5,$data[$i]['beneficios']['valor'],0,0,'L');
				$this->Cell(25,5,$data[$i]['beneficios']['tipo'].$data[$i]['beneficios']['monto'],0,0,'L');
				
				if($data[$i]['beneficios']['tipo'] == "")
					$subtotal_b = $subtotal_b + $data[$i]['beneficios']['monto'];
				else
					$subtotal_bn = $subtotal_bn + $data[$i]['beneficios']['monto'];
				
				//DEDUCCIONES
				$concep = substr($data[$i]['deducciones']['concepto'], 0, 25);
				$this->Cell(50,5,$concep,0,0,'L');
	
				//$this->MultiCell(40, 3, $data[$i]['deducciones']['concepto'], 0, 'L');
				$this->Cell(25,5,$data[$i]['deducciones']['saldo'],0,0,'L');
				$this->Cell(25,5,$data[$i]['deducciones']['monto'],0,1,'L');
				$total_d = $total_d + $data[$i]['deducciones']['monto'];
	
			}
			$total_b = $subtotal_b - $subtotal_bn;
			$this->ln(2);
			$this->SetFont('Arial','',8);
			$this->Cell(50,5,"Total Beneficios:",0,0,'R');
			$this->Cell(25,5,"","T",0,'L');
			$this->Cell(20,5,number_format($total_b,2,'.',''),"T",0,'L');
			//DEDUCCIONES
			$this->Cell(55,5,"Total Deducciones:",0,0,'R');
			$this->Cell(25,5,"",0,0,'L');
			$this->Cell(25,5,number_format($total_d,2,'.',''),0,1,'L');
			//TOTAL
			$this->Cell(150,5,"",0,0,'L');
			$this->SetFont('Arial','B',8);
			$this->Cell(25,5,"Total a Pagar:",0,0,'L');
			$this->Cell(25,5,number_format( ($total_b-$total_d) ,2,'.',''),0,1,'L');
			$this->SetFont('Arial','',8);
			$this->Cell(200,5,"","B",1,'C');
			$this->ln(2);
		}
	
	}

	$ficha=$_GET['ficha'];
	$codnom=$_GET['codnom'];
	$codtip=$_GET['codtip'];
	//print_r($_GET);
	$conexion=conexion();

	$sql2 = "select a.ficha,b.cedula,b.apenom,b.email from nom_movimientos_nomina a 
	left join nompersonal b on a.ficha=b.ficha where a.codnom = '$codnom' and a.tipnom='$codtip' and a.ficha='$ficha' limit 1";

	
    $res2=query($sql2,$conexion);
    //print_r($sql2);exit;
	$boleta_pag = $boleta = 1;

	$consulta3 = "SELECT periodo_ini, periodo_fin, periodo FROM nom_nominas_pago WHERE codnom = '".$codnom."' AND codtip = '".$codtip."'";
	$result5 = query($consulta3,$conexion);
	$nomina = fetch_array($result5);

	$directorio="recibos/nomina_".$_GET['codigo_nomina']."_tipo_".$_SESSION['codigo_nomina']."_".date("Y_m_d_H_m");
	if (!is_dir($directorio))
	{
		mkdir($directorio,0777,true);
	}
	$retorno= [];

	$email="";

	while($empleados=fetch_array($res2)){
		//Creación del objeto de la clase heredada
		$pdf=new PDF('P','mm','Letter');
		$pdf->AliasNbPages();
		$pdf->AddPage();
		$pdf->SetFont('Times','',12);
		$pdf->datos($codnom,$codtip,$empleados['ficha'],$boleta);	
		$ruta=$directorio."/".$empleados['cedula'].".pdf";
		
		/* Limpiamos el búfer */
		ob_end_clean();
		$pdf->Output($ruta,"F");
		
		chmod($directorio,0777);
		chmod($ruta,0777);
		
		$email=$empleados['email'];
		
		if($email!='')
		{
			$mail = new PHPMailer();
			$mail->SMTPAuth = true;
			$mail->isSMTP();
			$mail->SMTPDebug = 0;
			//$mail->Host = "smtp.gmail.com";
			$mail->Host = $correo_sistemas2_host;
                        
			//indico el puerto que usa Gmail
			//$mail->Port = 465;
			//$mail->SMTPSecure = 'ssl';
			// El correo y contraseña de donde saldran los mensajes.
			//$mail->Username = "planillaamx@gmail.com";
			//$mail->Password = "4m4x0n14";
                        
                        $mail->Port = $correo_sistemas2_puerto;
			$mail->SMTPSecure = $correo_sistemas2_modo;
			// El correo y contraseña de donde saldran los mensajes.
			$mail->Username = $correo_sistemas2;
			$mail->Password = $correo_sistemas2_password;

			//Indicamos cual es nuestra dirección de correo y el nombre que 
			//queremos que vea el usuario que lee nuestro correo
			$mail->From = $correo_sistemas2_remitente;
			$mail->FromName = "Planilla Amaxonia";
			
			// El asunto del mensaje.
			$tit='COMPROBANTE DE PAGO '.$_SESSION[nomina]." - DESDE: ".fecha($nomina['periodo_ini'])." HASTA: ".fecha($nomina['periodo_fin'])." - NO RESPONDER";

			$mail->Subject = $tit;

			// El cuerpo del mensaje se envia aquí.
			$asunto="Adjunto Comprobante de Pago. Por favor, NO RESPONDER a este correo. Gracias";
			$mail->Body = $asunto;
			$mail->IsHTML(true);
			// Se asigna la dirección de correo a donde se enviará el mensaje.
			$mail->AddAddress ($email);

			// Si hay archivos adjuntos se mandan así.
			$mail->AddAttachment($ruta);
			//$mail->AddAttachment("images/phpmailer_mini.gif");
			//Enviar correo
			//$send = true;
			// Comprobamos que el correo se ha enviado.
			if(!$mail->Send()) {
				$retorno[]=["cedula"=>$empleados['cedula'], "apenom"=>$empleados['apenom'], "correo"=>$email, "estatus"=>"Error","error"=>$mail->ErrorInfo];
				//echo $ruta." ".$email." Mailer Error: " . $mail->ErrorInfo;
			} else {
				$retorno[]=["cedula"=>$empleados['cedula'], "apenom"=>$empleados['apenom'], "correo"=>$email, "estatus"=>"OK","error"=>""];
			}
			$mail->ClearAddresses();
			$mail->ClearAttachments();

		}else{
			$retorno[]=["cedula"=>$empleados['cedula'], "apenom"=>$empleados['apenom'], "correo"=>"-", "estatus"=>"No enviado","error"=>""];
		}
		unset($pdf);
		$boleta++;
	}
} catch (Exception $e) {
	myException($e);
}

include("../../includes/dependencias.php");?>

<style>
	tbody{
		font-size: 0.8em;
	}
</style>
<div class="row">
	<div class="col-sm-12 col-md-12 col-lg-12">
		<!-- COMIENZO DEL CONTENIDO-->
		<div class="row">
			<div class="col-md-12">
				<!-- COMIENZO DE LA TABLA-->
				<div class="portlet box blue">
					<div class="portlet-title">
						<div class="caption">
							<img src="../../nomina/imagenes/21.png" width="22" height="22" class="icon"> Recibos enviados
						</div>
						<div class="pull-right">
							<a class="btn btn-primary" href="../../nominas/paginas/config_rpt_nomina_horizontal_modelo9.php?action=1">
								<i class="fa fa-arrow-left"></i>
								Atras
							</a>&nbsp;						
						</div>
					</div>
					<div class="portlet-body">
						<table class="table table-striped table-bordered table-hover" id="datatable">
							<thead>
								<tr>
									<th>#</th>
									<th>Empleado</th>
									<th>Cedula</th>
									<th>Correo</th>
									<th>Estado</th>
									<th>Mensaje</th>
								</tr>
							</thead>
							<tbody>
								<?php
								for ($i=0; $i < count($retorno); $i++)
								{
									if(!empty($retorno))
									{
								?>
										<tr>
											<td><?php echo ($i+1);?></td>
											<td><?php echo utf8_decode($retorno[$i]["apenom"]);?></td>
											<td><?php echo $retorno[$i]["cedula"];?></td>
											<td><?php echo $retorno[$i]["correo"];?></td>
											<td><?php echo $retorno[$i]["estatus"];?></td>
											<td><?php echo $retorno[$i]["error"];?></td>
										</tr>
								<?php
									}
								}
								?>
							</tbody>
						</table>
					</div>
				</div>
				<!-- FIN DE LA TABLA-->
			</div>
		</div>
		<!-- FIN DE LA PAGINA DE CONTENIDO-->
	</div>
</div>
