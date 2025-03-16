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
	$this->Cell(100,8,$rif,0,1,"L");

	$enc='Panamá, '.date('d').' de '.mesaletras(date('m')).' de año '.date('Y').'.';
	$this->SetFont("Arial","",12);
	$this->MultiCell(180,5,utf8_decode($enc),0,'R');

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
	
	
	$query="select * from nomcampos_adic_personal where ficha ='$ficha' and id=3";
	$resultado4 = query($query,$conexion);	
	$extra= fetch_array($resultado4);
	$ivss=$extra['valor'];

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
		$ciu='a Sra(ita.)';
		$ad='adscrita ';
	}
	else{
		$ciu='o Sr';
		$ad='adscrito ';
	}

	$conOCS = 'select * from nomempresa';
	$resOCS = query($conOCS,$conexion);
	$filaOCS = fetch_array($resOCS);
	
	$enc=$filaOCS['ger_rrhh'];

	//$this->Ln(20);
	$this->SetFont("Arial","",12);
	$this->Cell(10,5,'Estimad'.$ciu.' :',0,1);
	$this->Ln();
	$fecha_ingreso=explode("/",$fecha);
	$this->SetFont('Arial','I',12);
	$contenido='Por este medio certificamos que '.utf8_decode($persona).', con cédula de identidad personal número '.$cedula.' y seguro social número '.$ivss.', labora en nuestra empresa desde el '.$fecha_ingreso[0].' de '.mesaletras($fecha_ingreso[1]).' de '.$fecha_ingreso[2].' desempeñando el cargo de '.$cargo.' y debenga un salario base mensual de B/.'.number_format($monto,2,',','.');

	
	$n = new numerosALetras();
	$dialetra=$n->convertirdia(date('d'));
	$añoletra=$n->convertirdia(date('Y'));

	//$this->Cell(10,5,'',0,0);
	$this->MultiCell(178,7,utf8_decode($contenido),0,'J');
	$this->Ln(10);
	
	
	$this->SetFont('Arial','B',12);
	$this->Cell(160,5,'Sus deducciones legales son: ',0,1);
	
	$this->SetFont('Arial','',12);
	/////TOTALES
	
	//echo $fecha_ingreso[2];
	$meses=antiguedad($fecha1,date('Y-m-d'),'M');
	if($meses>1){
		if(date('m')==1){
			$me=12;
			$anio=date(Y)-1;
		}else{
			$me=date('m')-1;
			$anio=date(Y);
		}
			$query='select sum(monto) as monto,descrip,anio from nom_movimientos_nomina where tipcon="D" and anio="'.$anio.'" and mes='.$me.' and ficha="'.$ficha.'" group by codcon order by codcon ASC';
			$resultado = query($query,$conexion);	
			$dedu=0;
			while ($rs=fetch_array($resultado)){
				$this->SetFont('Arial','I',10);
				$this->Cell(60,5,utf8_decode($rs[descrip]),0,0);
				$this->Cell(45,5,number_format($rs[monto],2,',','.'),0,1,'R');
				$dedu+=$rs[monto];
			}
			$this->SetFont('Arial','B',10);
			$this->Cell(60,5,'TOTALES',0,0);
			$this->Cell(45,5,number_format($dedu,2,',','.'),'T',1,'R');
	}
	
	$this->SetFont('Arial','B',12);
	$this->Cell(160,5,'Descuentos Comerciales:',0,1);
	$this->Ln();
	
	//buscar en prestamos
	
	$prestamo="select np.descrip,nd.salinicial as monto,np.codigopr as tipo from nomprestamos_detalles nd inner join nomprestamos_cabecera nc on nd.numpre=nc.numpre and nc.estadopre='Pendiente' inner join nomprestamos np on np.codigopr = nc.codigopr where nd.ficha='".$ficha."' and nd.estadopre='Pendiente' order by nd.numcuo ASC ";
	
	$resultadop = query($prestamo,$conexion);	
	$pres=0;$tipo=0;
	while ($rs=fetch_array($resultadop)){
		if($tipo!=$rs[tipo]){
			$this->SetFont('Arial','I',10);
			$this->Cell(60,5,utf8_decode($rs[descrip]),0,0);
			$this->Cell(45,5,number_format($rs[monto],2,',','.'),0,1,'R');
			$pres+=$rs[monto];
			$tipo=$rs[tipo];
		}
	}
	//campos adicionales prestamo
	//$campo_adi='select * from nomcampos_adic_personal where id=2 and ficha="'.$ficha.'"';
	
	$this->SetFont('Arial','B',10);
	$this->Cell(60,5,'TOTALES',0,0);
	$this->Cell(45,5,number_format($pres,2,',','.'),'T',1,'R');
	$this->Ln();
	
	/////////////
	$this->SetFont('Arial','B',12);
	$this->Cell(160,5,utf8_decode('Detalle de los salarios devengados en los últimos seis meses:'),0,1);
	
	$me=date('m');
	$anio=date(Y);
		$this->Cell(50,5,'MES',0,0);
		$this->Cell(20,5,utf8_decode('AÑO'),0,0);
		$this->Cell(45,5,'MONTO B/.',0,0);
		$this->Cell(45,5,'OTROS ING.',0,1);
	$this->SetFont('Arial','',12);
	$tsueldo=$tingreso=0;
	for($i=1;$i<=6;$i++){
		if($me==1){
			$me=12;
			$anio-=1;
		}else{
			$me-=1;
		}
		$query='select sum(monto) as monto,descrip,anio from nom_movimientos_nomina where tipcon="A" and anio="'.$anio.'" and mes='.$me.' and ficha="'.$ficha.'" and codcon=100 group by codcon order by codcon ASC';
		$resultado = query($query,$conexion);
		$rsa=fetch_array($resultado);
		$query='select sum(monto) as monto,descrip,anio from nom_movimientos_nomina where tipcon="A" and anio="'.$anio.'" and mes='.$me.' and ficha="'.$ficha.'" and codcon<>100 group by codcon order by codcon ASC';
		$resultado = query($query,$conexion);
		$rsi=fetch_array($resultado);
		$this->SetFont('Arial','I',10);
		$this->Cell(50,5,mesaletras($me),0,0);
		$this->Cell(20,5,$anio,0,0);
		$this->Cell(40,5,number_format($rsa[monto],2,',','.'),0,0,'R');
		$this->Cell(40,5,number_format($rsi[monto],2,',','.'),0,1,'R');
		$tsueldo+=$rsa[monto];
		$tingreso+=$rsi[monto];
		
	}
	$this->SetFont('Arial','I',10);
	$this->Cell(70,5,'TOTAL',0,0);
	$this->Cell(40,5,number_format($tsueldo,2,',','.'),'T',0,'R');
	$this->Cell(40,5,number_format($tingreso,2,',','.'),'T',1,'R');
	$this->Ln(5);
	$this->SetFont('Arial','',12);
	$this->Cell(30,5,'Atentamente,',0,1);

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
