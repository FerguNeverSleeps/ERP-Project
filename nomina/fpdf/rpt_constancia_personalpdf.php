<?php 
//header('Content-Type: text/html; charset=iso-8859-1');
if (!isset($_SESSION)) {
  session_start();
}
require('fpdf.php');
require_once '../lib/config.php';
require_once '../lib/common.php';
include('numerosALetras.class.php');
require_once '../paginas/funciones_nomina.php';


class PDF extends FPDF
{

//Cabecera de página
function Header()
{
        $Conn=conexion();
	$var_sql="select * from nomempresa";
	$rs = query($var_sql,$Conn);
	$row_rs = fetch_array($rs);
// 	$var_encabezado1=$row_rs['nom_emp'];
// 	$var_izquierda='../imagenes/'.$row_rs[imagen_izq];
// 	$var_derecha='../imagenes/'.$row_rs[imagen_der];
	
	$var_encabezado1=$row_rs['nom_emp'];
	$var_encabezado2=$row_rs['encabezado2'];
	$var_encabezado3=$row_rs['encabezado3'];
	$var_encabezado4=$row_rs['encabezado4'];
	$var_imagen_izq='../imagenes/'.$row_rs['imagen_izq'];
	$var_imagen_der='../imagenes/'.$row_rs['imagen_der'];
	$rif=$row_rs[rif];

	$this->SetFont("Arial","B",12);
	$this->Image($var_imagen_der,10,8,33);
 	$this->Ln(11);
	$this->Cell(55);
	
	$this->MultiCell(80,5,$var_encabezado1,0,'C');
	$this->SetFont("Arial","",9);
	$this->Cell(100,8,$rif,0,0,"L");
	//$this->Cell(100,8,$var_encabezado1,0,0,"C");
// 	$this->Image($var_imagen_der,170,15,33);

// 	$this->Image($var_izquierda,10,6,30,15);
// 	$this->Image($var_derecha,10,6,30,15);

	$this->Ln(10);
}
function Footer(){
	$this->SetY(-40);
	$this->Cell(188,10,'','B',0);
	$conexion=conexion();
	$conOCS = 'select * from nomempresa';
	$resOCS = query($conOCS,$conexion);
	$filaOCS = fetch_array($resOCS);
	
	$direccion=$filaOCS['dir_emp'].' '.$filaOCS['ciu_emp'].' '.$filaOCS['edo_emp'];
	$this->Ln();
	$this->SetFont('Arial','I',8);
	$this->Cell(188,5,$direccion,'',0,'C');

	$telefono='Telefonos: '.$filaOCS['tel_emp'];//.' Fax: '.$filaOCS['fax_emp'];
	$this->Ln();
	$this->Cell(188,5,$telefono,'',0,'C');
	
	$this->SetY(-15);
	$this->SetFont('Arial','I',8);
	//$this->Cell(188,10,'Elaborado Por: '.$_SESSION['nombre'],0,0,'L');
	
}
	
function detalle(){

	$conexion=conexion();
	$conOCS = 'select * from nomempresa';
	$resOCS = query($conOCS,$conexion);
	$filaOCS = fetch_array($resOCS);
	$RRHH = $filaOCS['ger_rrhh'];

	$registro_id=$_GET[registro_id];
	$tipo=$_GET[tipN];
	$esta=$_GET[est];
	$query="select * from nompersonal where ficha = '$registro_id' and tipnom='$tipo'";
	$resultado = query($query,$conexion);
	$personal = fetch_array($resultado);
	
	$persona=$personal['apenom'];
	$cedula=$personal['cedula'];
	$fecha=$personal['fecing'];
	$fecha1=$personal['fecing'];

	list($anio,$mes,$dia)=explode("-",$fecha);
   	$fecha= $dia."/".$mes."/".$anio; 
	
	$id_cargo=$personal['codcargo'];
	$query="select * from nomcargos where cod_car = '$id_cargo'";
	$resultado1 = query($query,$conexion);
	$cargo = fetch_array($resultado1);
	$cargo=$cargo['des_car'];

	$id_gerencia=$personal['codnivel2'];
	$query="select * from nomnivel2 where codorg = '$id_gerencia'";
	$resultado2 = query($query,$conexion);
	$ger = fetch_array($resultado2);
	$gerencia=$ger['descrip'];
	
	$monto=$personal['suesal'];
	$ficha=$personal['ficha'];
	
	$monto=$monto;
	
	
	$query="select * from nomcampos_adic_personal where ficha ='$ficha' and id=5";
	$resultado4 = query($query,$conexion);	
	$extra= fetch_array($resultado4);
	$profesional=$extra['valor'];

	$query="select * from nomcampos_adic_personal where ficha ='$ficha' and id=11";
	$resultado4 = query($query,$conexion);	
	$extra= fetch_array($resultado4);
	$ant2=$extra['valor'];
	
	$fecha2=date("Y-m-d");
	
	//$anios=edad($fecha1,$fecha2)-3;
	if($anios>0)
		$mto=($anios*30)+40;
	else
		$mto=0;
// 	echo $mto;
	if($ant2!=0)
		$mto=$ant2;
	
	$mto=$mto+$profesional;
	
	$n = new numerosALetras();
	$salarioletras=$n->convertir($monto+ $compensacion);
	
	$salario=number_format($monto+ $compensacion,2,',','.');
	$sex=$personal['sexo'];
	if($sex=='Femenino'){
		$ciu='la ciudadana ';
		$ad='adscrita ';
	}
	else{
		$ciu='el ciudadano ';
		$ad='adscrito ';
	}

	$conOCS = 'select * from nomempresa';
	$resOCS = query($conOCS,$conexion);
	$filaOCS = fetch_array($resOCS);
	
	$enc=$filaOCS['ger_rrhh'];

	$this->Ln(20);
	$this->SetFont('Arial','B',16);
	$this->Cell(188,7,'CONSTANCIA DE TRABAJO',0,0,'C');
	$this->Ln(20);
	$this->SetFont('Arial','I',11);
	$contenido='Quien suscribe, Coordinadora(or) de Recursos Humanos del Instituto Municipal De Mercados Del Municipio Chacao, por la presente hace constar que el Ciudadano(a) abajo señalado presta servicios en esta Institución y certifica que los datos que a continuación se transcriben provienen fiel y exactamente de nuestros registros de personal:';//, titular de la Cédula de Identidad Nº '..', presta sus servicios en el Instituto Autonomo De Mercados Del Municipio Chacao,  desde el '..', desempeñando actualmente el cargo de '.$cargo.', devengando un salario mensual de '.$salarioletras.' (Bs. '.$salario.').';

// 	if ($esta=='SI'){
// 	$contenido=$contenido.' Adicionalmente percibe bono de alimentación, de acuerdo a Gaceta Oficial Nº 38094 de fecha 27/12/04.';
// 	}


	$n = new numerosALetras();
	$dialetra=$n->convertirdia(date('d'));
	$añoletra=$n->convertirdia(date('Y'));

	$subcontenido='     Constancia que se expide a solicitud de la parte interesada, en Caracas a los '.$dialetra.' ('.date('d').') días del mes de '.mesaletras(date('m')).' del año '.$añoletra.' ('.date('Y').').';
	$subcontenido2=$enc;
	$subcontenido3='Coordinadora(or) de Recursos Humanos';
	//$subcontenido1='Atentamente,';
	$this->Cell(10,5,'',0,0);
	$this->MultiCell(168,5,utf8_decode($contenido),0,'J');
	$this->Ln(10);
	$this->Cell(20,5,'',0,0);
	$this->Cell(70,5,'NOMBRE:',0,0);
	$this->Cell(50,5,utf8_decode($persona),0,1);
	//$this->Ln(2);
	$this->Cell(20,5,'',0,0);
	$this->Cell(70,5,utf8_decode('CÉDULA DE IDENTIDAD:'),0,0);
	$this->Cell(50,5,number_format($cedula,0,',','.'),0,1);
	//$this->Ln(2);
	$this->Cell(20,5,'',0,0);
	$this->Cell(70,5,'FECHA DE INGRESO:',0,0);
	$this->Cell(50,5,$fecha,0,1);
// 	$this->Ln(2);
	$this->Cell(20,5,'',0,0);
	$this->Cell(70,5,'CARGO ACTUAL:',0,0);
	$this->Cell(50,5,utf8_decode($cargo),0,1);
// 	$this->Ln(2);
	$this->Cell(20,5,'',0,0);
	$this->Cell(70,5,utf8_decode('ADSCRIPCIÓN:'),0,0);
	$this->Cell(50,5,utf8_decode($gerencia),0,1);
	if ($esta=='SI')
	{
		$this->Cell(20,5,'',0,0);
		$this->Cell(70,5,utf8_decode('SUELDO BÁSICO MENSUAL Bs.F:'),0,0);
		$this->Cell(50,5,number_format($monto,2,',','.'),0,1);
		$this->Cell(20,5,'',0,0);
		$this->Cell(70,5,utf8_decode('OTRAS ASIGNACIONES Bs.F:'),0,0);
		$this->Cell(50,5,number_format($mto,2,',','.'),0,1);
		$this->Cell(20,5,'',0,0);
		$this->Cell(70,5,utf8_decode('TOTAL Bs.F:'),0,0);
		$this->Cell(50,5,number_format($monto+$mto,2,',','.'),0,1);
	}
	
	$this->Ln(10);
	$this->Cell(10,5,'',0,0);
	$this->MultiCell(168,5,utf8_decode($subcontenido),0,'J');
	$this->Ln(10);
	$this->Cell(10,5,'',0,0);
	$this->MultiCell(168,7,utf8_decode($subcontenido1),0,'C');  
	$this->Ln(40);
	$this->Cell(10,7,'',0,0);
	$this->MultiCell(168,7,utf8_decode($subcontenido2),0,'C'); 
	$this->Ln(0.2);
	$this->Cell(10,6,'',0,0);
	$this->MultiCell(168,7,utf8_decode($subcontenido3),0,'C');
	$this->Ln();
	
}
}
//Creación del objeto de la clase heredada

$pdf=new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Times','',12);
$pdf->detalle();
$pdf->Output();
?>
