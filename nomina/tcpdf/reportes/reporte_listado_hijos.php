<?php
/**
 * Creates an example PDF TEST document using TCPDF
 * @package com.tecnick.tcpdf
 * @abstract TCPDF - Example: Custom Header and Footer
 * @author Nicola Asuni
 * @since 2008-03-04
 */

require_once('../tcpdf.php');
require_once '../../lib/config.php';
include ("../../paginas/funciones_nomina.php");
require_once('../../lib/database.php');


// Extend the TCPDF class to create custom Header and Footer
class MYPDF extends TCPDF {
	public $fechainicio, $fechafinal, $fecharegistro, $quincena, $mes, $año, $id;
	//Page header
public function Header() {
		// Logo
		$db = new Database($_SESSION['bd']);
		$var_sql="select * from nomempresa";
		$res = $db->query($var_sql);
		$row_rs = $res->fetch_object();
		$var_encabezado1=$row_rs->nom_emp;
		$var_izquierda='../../imagenes/'.$row_rs->imagen_der;
		$var_derecha='../../imagenes/'.$row_rs->imagen_izq;

		$image_file = K_PATH_IMAGES.'../../vista/Encabezado.png';
		//$this->Image($image_file, 10, 10, 190, '', 'JPG', '', '', false, 300, '', false, false, 0, false, false, false);
//		$this->Cell(0, 20, 'HOJA N/TOTAL DE HOJAS '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
		$this->SetFont('helvetica', 16);
		$fecha = date("d")."-".date("m")."-".date("Y");
		$fill = 1;
		$border="LTRB";
		$ln=0;
		$fill = 0;
		$align='C';
		$link=0;
		$stretch=1;
		$ignore_min_height=0;
		$calign='T';
		$valign='T';
		$height=5.75;//alto de cada columna
		$YMax=260;
		$altura=21;
		$membrete="<b>".$var_encabezado1."<br>LISTADO DE HIJOS</b>";
        $this->Image($var_izquierda,11,11,19,19); 

		$this->MultiCell(40, $altura,"", $border="LTB", $align='C', $fill=false, $ln=1, $x=10,$y=10, $reseth=true, $stretch=0, $ishtml=true, $autopadding=true, $maxh=0, $valign='T', $fitcell=false);
		$this->MultiCell(110, $altura,$membrete, $border="TB", $align='C', $fill=false, $ln=1,$x=50,$y=10,  $reseth=true, $stretch=0, $ishtml=true, $autopadding=true, $maxh=0, $valign='T', $fitcell=false);
		$this->MultiCell(40, $altura,"", $border="TRB", $align='C', $fill=false, $ln=1, $x=160,$y=10, $reseth=true, $stretch=0, $ishtml=true, $autopadding=true, $maxh=0, $valign='T', $fitcell=false);

		$this->Ln();
		
		
	}

    function CambiarFecha($fecha)
	{
		if($fecha!='')
		{
		$otra_fecha= explode("-", $fecha);// en la posición 1 del arreglo se encuentra el mes en texto.. lo comparamos y cambiamos
	  //$buena= $otra_fecha[0]."/".$otra_fecha[1]."/".$otra_fecha[2];// volvemos a armar la fecha

		$buena= $otra_fecha[2]."-".$otra_fecha[1]."-".$otra_fecha[0];// volvemos a armar la fecha
	  	return $buena;
	  }
	}
	public function CuerpoTablaHtml($reg,$fecha_inicio,$fecha_fin,$fecha_registro)
	{
		$db = new Database($_SESSION['bd']);
	    if($this->getAliasNumPage() <= 1)
		{
			$Y=35;
			$this->SetTopMargin ($Y);
			$this->SetTopMargin ($Y);

		}
		else
		{
			$Y=30;
			$this->SetTopMargin($Y);
			$this->SetTopMargin ($Y);

		}

		$fill = 1;
		$border=0;
		$ln=0;
		$fill = 0;
		$align='C';
		$link=0;
		$stretch=1;
		$ignore_min_height=0;
		$calign='T';
		$valign='T';
		$height=5.75;//alto de cada columna
		$YMax=260;
		$altura=7;
		$consulta="select * from nompersonal as np INNER JOIN nomfamiliares as nf ON np.ficha=nf.ficha and nf.cedula=np.cedula and np.estado<>'Egresado' ORDER BY np.tipnom,np.codnivel1,np.apenom";
		$res1 = $db->query($consulta);
	
	
		$cantidad_registros=45;
		$cod_gerencia='';
		$cod_persona='';
		$cod_nomina='';
		$TOTALTRABAJADOR=0;
		$TOTALCARGAFAMILIAR=0;
		$masculino=0;
		$femenino=0;
		while($fila = $res1->fetch_assoc())
		{
			$codg=$fila['codnivel4'];
			$codper=$fila['ficha'];
			$codnom=$fila['tipnom'];
			$anos=antiguedad($fila['fecha_nac'],date('Y-m-d'),'A');
			
			$fecha = time() - strtotime($fila['fecha_nac']);
			$edad = floor((($fecha / 3600) / 24) / 360);
			if ($codper!=$cod_persona) {
				if($codnom!=$cod_nomina)
					{
						$consul="select * from nomtipos_nomina where codtip=$codnom";
						$q=$db->query($consul);
						$resuln=$q->fetch_assoc();
						$nomnomina=$resuln['descrip'];
						$this->SetFont('helvetica', 'B', 8);
						$this->Cell(188,7,utf8_decode($codnom.".- ".$nomnomina),0,1,'L');
					

						/*$consul="select * from nomnivel4 where codorg=$codg";
						$q=$db->query($consul);
						$resul=$q->fetch_assoc();
						$nomgerencia=$resul['descrip'];
						$this->SetFont("helvetica","B",10);
						$this->Cell(188,7,utf8_decode($codg.".- ".$nomgerencia),0,1,'L');*/
						$cod_nomina=$codnom;
					}
				$this->SetFont("helvetica","I",10);

				$car="select * from nomcargos where cod_car='".$fila['codcargo']."'";
				$q=$db->query($car);
				$rc=$q->fetch_assoc();
				$this->Ln();
				$ca='('.$fila['ficha'].')'.utf8_encode($fila['apenom']).' -  Cargo: '.$rc['des_car'];
				$this->MultiCell(188,5,$ca,0,'L');
				$this->SetFont("helvetica","I",10);
				$w = array(40, 20,25,35,35,30);
				$cabecera=array('Nombre y Apellido','Edad','Sexo','Talla Franela','Talla Mono','Parentesco');
				$num_headers = count($cabecera);
				for($i = 0; $i < $num_headers; ++$i) {
					$this->Cell($w[$i], 8, $cabecera[$i], 1, 0, 'C');

				}
				$this->Ln();
			$TOTALTRABAJADOR+=1;

				$con="select * from nomparentescos where codorg=$fila[codpar]";
				$qu=$db->query($con);
				$filas=$qu->fetch_assoc();

				$this->SetFont('helvetica','I',8);

				// Header
				$num_headers = count($header);			

				if(	$this->GetY() >172)
				{
					$this->AddPage();
				}
				$cod_persona=$codper;

				$border=1;
		  		$fill = 1;
				$ln=0;
				$fill = 0;
				$align='C';
				$link=0;
				$stretch=1;
				$ignore_min_height=0;
				$calign='T';
				$valign='T';
				$height=5.75;//alto de cada columna
				$YMax=260;
				$altura=7;
				$this->SetFillColor(255, 255, 255);
				$this->SetTextColor(0,0,0);
				$this->SetDrawColor(100, 100, 100);
				$this->SetLineWidth(0.3);

				$this->Cell(40, $altura, $fila['apellido'].' , '.$fila['nombre'], $border,$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
				$this->Cell(20, $altura, $edad, $border,$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
				$this->Cell(25, $altura, $fila['sexo'], $border,$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
				if($fila['sexo']=='Femenino')
				{
					$femenino++;
				}
				else
				{
					$masculino++;
				}
				$this->Cell(35, $altura, $fila['tallafranela'], $border,$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
				$this->Cell(35, $altura, $fila['tallamono'], $border,$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
				$this->Cell(30, $altura, $filas['descrip'], $border,$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
				$this->Ln();
				$TOTALCARGAFAMILIAR+=1;

			}
			else
			{
				$border=1;
		  		$fill = 1;
				$ln=0;
				$fill = 0;
				$align='C';
				$link=0;
				$stretch=1;
				$ignore_min_height=0;
				$calign='T';
				$valign='T';
				$height=5.75;//alto de cada columna
				$YMax=260;
				$altura=7;
				$this->SetFillColor(255, 255, 255);
				$this->SetTextColor(0,0,0);
				$this->SetDrawColor(100, 100, 100);
				$this->SetLineWidth(0.3);
				$this->Cell(40, $altura, $fila['apellido'].' , '.$fila['nombre'], $border,$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
				$this->Cell(20, $altura, $edad, $border,$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
				$this->Cell(25, $altura, $fila['sexo'], $border,$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
				if($fila['sexo']=='Femenino')
				{
					$femenino++;
				}
				else
				{
					$masculino++;
				}
				$this->Cell(35, $altura, $fila['tallafranela'], $border,$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
				$this->Cell(35, $altura, $fila['tallamono'], $border,$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
				$this->Cell(30, $altura, $filas['descrip'], $border,$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
				$this->Ln();
				$TOTALCARGAFAMILIAR+=1;

			}
			

	

			

		}
				$this->Ln();
		
		$this->Cell(188,5,'Total de Trabajadores => '.$TOTALTRABAJADOR,0,1,'R');
	$this->Cell(188,5,'Total de Carga Familiar => '.$TOTALCARGAFAMILIAR,0,1,'R');	
		$this->Cell(188,5,'Total por Sexo Masculino => '.$masculino,0,1,'R');
		$this->Cell(188,5,'Total por Sexo Femenino => '.$femenino,0,1,'R');
	}

}//fin clase
		
		
		

// create new PDF document
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', true);
$pdf->fechainicio=$_REQUEST['fecha_ini'];
$pdf->fechafinal=$_REQUEST['fecha_fin'];
$pdf->fecharegistro=$_REQUEST['fecha_reg'];

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Selectra');
$pdf->SetTitle('Listado de Hijos');
$pdf->SetSubject('Reporte Listado de Hijos');
$pdf->SetKeywords('TCPDF, PDF, reporte, cambio, cedula');

// set default header data
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);
// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

//set margins
$pdf->SetMargins(10, 10, 10,true);
$pdf->SetLeftMargin(10);
$pdf->SetHeaderMargin(10);
$pdf->SetRightMargin(10);
//set auto page breaks
$pdf->SetAutoPageBreak(TRUE, 15	);

//set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

//set some language-dependent strings
$pdf->setLanguageArray($l);

// ---------------------------------------------------------

// set font
$pdf->SetFont('helvetica', 'BI', 14);

// add a page
$pdf->AddPage('P', 'A4');

$reg=$_REQUEST['reg'];
$pdf->quincena=$_REQUEST['quincena'];
$pdf->mes=$_REQUEST['mes'];
$pdf->ano=$_REQUEST['ano'];

//$pdf->ColoredTable($reg);
$pdf->CuerpoTablaHtml($reg,$fecha_inicio,$fecha_fin,$fecha_registro);

// ---------------------------------------------------------

//Close and output PDF document
$pdf->Output('Informe_Listado_HIjos.pdf', 'I');

//============================================================+
// END OF FILE                                                
//============================================================+
//$informes->desconectar_bd();