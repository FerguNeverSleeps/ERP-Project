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
	$this->SetFont('Arial','B',14);
	$this->Cell(188,7,'CERTIFICACION',0,1,'C');
	$this->Cell(188,7,'RETENCIONES EFECTUADAS POR EL EMPLEADOR',0,1,'C');
	$this->Ln(10);
	$this->SetFont('Arial','I',12);
	$contenido='El suscrito '.utf8_decode($filaOCS['ger_rrhh']).', con cédula de identidad personal '.$filaOCS['ced_rrhh'].', debidamente facultado y en representacion de la empresa '.$filaOCS['nom_emp'].' con Registro Unico de Contribuyente (R.U.C) '.$filaOCS['rif'].', para la presente y con pleno conocimiento de las responsabilidades que señalan las leyes de la República.';

	
	$n = new numerosALetras();
	$dialetra=$n->convertirdia(date('d'));
	$añoletra=$n->convertirdia(date('Y'));

	$this->Cell(10,5,'',0,0);
	$this->MultiCell(168,5,utf8_decode($contenido),0,'J');
	$this->Ln(10);
	$this->SetFont('Arial','B',14);
	$this->Cell(188,7,'CERTIFICA',0,1,'C');
	$this->Ln(10);
	
	$this->SetFont('Arial','I',12);
	$subcontenido='Que '.utf8_decode($persona).' con cédula de identidad personal número '.$cedula.', es empleado de esta empresa y devengó las siguientes remuneraciones y se le retuvo durante el año fiscal '.(date(Y)-1).' las siguienes sumas:';
	
	$this->Cell(10,5,'',0,0);
	$this->MultiCell(168,5,utf8_decode($subcontenido),0,'J');
	$this->Ln();
	$this->SetFont('Arial','B',12);
	$this->Cell(10,5,'',0,0);
	$this->Cell(60,5,'CONCEPTO',1,0);
	$this->Cell(20,5,utf8_decode('AÑO'),1,0);
	$this->Cell(45,5,'RECIBIDOS',1,0);
	$this->Cell(45,5,'DEDUCCIONES',1,1);
	$this->SetFont('Arial','I',12);
	/////TOTALES
	$fecha_ingreso=explode("/",$fecha);
	//echo $fecha_ingreso[2];
	if($fecha_ingreso[2]<date(Y)){
		
			
		
			$query='select sum(monto) as monto,descrip,anio from nom_movimientos_nomina where tipcon="A" and anio="'.(date(Y)-1).'" and ficha="'.$ficha.'" group by codcon order by codcon ASC';
			$resultado = query($query,$conexion);	
			$ingreso=0;
			
			
			while ($rs=fetch_array($resultado)){
				$this->SetFont('Arial','I',10);
				$this->Cell(10,5,'',0,0);
				$this->Cell(60,5,utf8_decode($rs[descrip]),0,0);
				$this->Cell(20,5,$rs[anio],0,0);
				$this->Cell(45,5,number_format($rs[monto],2,',','.'),0,0,'R');
				$this->Cell(45,5,'',0,1);
				$ingreso+=$rs[monto];
			}
			$this->SetFont('Arial','B',10);
			$this->Cell(10,5,'',0,0);
			$this->Cell(80,5,'SUB TOTAL',0,0);
			$this->Cell(45,5,number_format($ingreso,2,',','.'),'T',1,'R');
				
			$query='select sum(monto) as monto,descrip,anio from nom_movimientos_nomina where tipcon="D" and anio="'.(date(Y)-1).'" and ficha="'.$ficha.'" group by codcon order by codcon ASC';
			$resultado = query($query,$conexion);	
			$dedu=0;
			while ($rs=fetch_array($resultado)){
				$this->SetFont('Arial','I',10);
				$this->Cell(10,5,'',0,0);
				$this->Cell(60,5,utf8_decode($rs[descrip]),0,0);
				$this->Cell(20,5,$rs[anio],0,0);
				$this->Cell(45,5,'',0,0);
				$this->Cell(45,5,number_format($rs[monto],2,',','.'),0,1,'R');
				$dedu+=$rs[monto];
			}
			$this->SetFont('Arial','B',10);
			$this->Cell(10,5,'',0,0);
			$this->Cell(80,5,'TOTALES',0,0);
			$this->Cell(45,5,number_format($ingreso,2,',','.'),'T',0,'R');
			$this->Cell(45,5,number_format($dedu,2,',','.'),'T',1,'R');
	}
	
	
	/////////////
	$this->Ln();
	$this->SetFont('Arial','I',12);
	$final='La información contenida en esta certificación es totalmente cierta y reposa en nuestros archivos, los cuales ponemos a disposición de la Dirección General de Ingresos.';
	
	$final1='Expedida y firmada hoy '.$dialetra.' ('.date('d').') días del mes de '.mesaletras(date('m')).' del año '.$añoletra.' ('.date('Y').').';
	
	$this->Cell(10,5,'',0,0);
	$this->MultiCell(168,5,utf8_decode($final),0,'J');
	$this->Ln(10);
	$this->Cell(10,5,'',0,0);
	$this->MultiCell(168,5,utf8_decode($final1),0,'J');
	$this->Ln(20);
	$this->Cell(10,5,'',0,0);
	$this->Cell(50,5,'','T',1);
	
	
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
