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
	public $fechainicio, $fechafinal, $fecharegistro, $quincena, $ficha, $cedula, $id;
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
		$membrete="<br><b>".$var_encabezado1."<br>Vacaciones <br>".$fecha."</b>";
        $this->Image($var_izquierda,11,11,19,19); 

		$this->MultiCell(40, $altura,"", $border="LTB", $align='C', $fill=false, $ln=1, $x=10,$y=10, $reseth=true, $stretch=0, $ishtml=true, $autopadding=true, $maxh=0, $valign='T', $fitcell=false);
		$this->MultiCell(110, $altura,$membrete, $border="TB", $align='C', $fill=false, $ln=1,$x=50,$y=10,  $reseth=true, $stretch=0, $ishtml=true, $autopadding=true, $maxh=0, $valign='T', $fitcell=false);
		$this->MultiCell(40, $altura,"", $border="TRB", $align='C', $fill=false, $ln=1, $x=160,$y=10, $reseth=true, $stretch=0, $ishtml=true, $autopadding=true, $maxh=0, $valign='T', $fitcell=false);

		$this->Ln();
		
		
	}
	function Footer()
	{
	    //Posición: a 1,5 cm del final
	    $this->SetY(-15);
	   
	     $this->SetFont('helvetica','I',8);
	    $this->Cell(0,5,utf8_decode('Pagina ').$this->getAliasNumPage().'/'.$this-> getAliasNbPages(),0,1,'C');

	    //Número de página
	   // $this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
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
		$consulta="SELECT * from nompersonal WHERE  tipnom=$_SESSION[codigo_nomina]";
		$res1 = $db->query($consulta);
		;
		while ($rc = $res1->fetch_assoc()) 
		{
			# code...
		
			$car="select * from nomcargos where cod_car='".$rc['codcargo']."'";
			$q=$db->query($car);
			$car=$q->fetch_assoc();
			$this->Ln(5);
			$this->SetFont('helvetica','B',8);
			$this->Cell(15,5,utf8_decode('FICHA'),1,0);
			$this->Cell(58,5,'APELLIDO Y NOMBRES ',1,0);
			$this->Cell(27,5,utf8_decode('C.I. '),1,0);
			$this->Cell(30,5,utf8_decode('CARGO '),1,0);
			$this->Cell(30,5,utf8_decode('Fecha Inicio '),1,0);
			$this->Cell(30,5,utf8_decode('Fecha Reingreso '),1,0);
			$this->Ln();
			$this->SetFont('helvetica','I',7);
			$this->Cell(15,5,utf8_decode($rc['ficha']),0,0);
			$this->Cell(58,5,$rc['apenom'],0,0);
			$this->Cell(27,5,utf8_decode($rc['cedula']),0,0);
			$this->Cell(30,5,utf8_decode($car['des_car']),0,0);


			$consulta2="
				SELECT
				 COALESCE((SELECT n.valor FROM nomcampos_adic_personal n 
  				  WHERE  n.tiponom=".$_SESSION[codigo_nomina]." AND n.ficha=".$rc['ficha']." AND n.id=97),0) as fec_ult_vac,
				
				 COALESCE((SELECT n.valor FROM nomcampos_adic_personal n 
  				  WHERE  n.tiponom=".$_SESSION[codigo_nomina]." AND n.ficha=".$rc['ficha']." AND n.id=98),0) as fec_entrada_ult_vac,
				
				 COALESCE((SELECT n.valor FROM nomcampos_adic_personal n 
  				  WHERE  n.tiponom=".$_SESSION[codigo_nomina]." AND n.ficha=".$rc['ficha']." AND n.id=99),0) as pago_neto";
	
			$res2=$db->query($consulta2);
			$filav = $res2->fetch_assoc();
			$this->Cell(30,5,utf8_decode($filav['fec_ult_vac']),0,0);
			$this->Cell(30,5,utf8_decode($filav['fec_entrada_ult_vac']),0,0);
			// llamado para hacer multilinea sin que haga salto de linea
			$this->Ln();
			
				if (($filav['fec_ult_vac'])!='') {
					$fecha = time() - strtotime(($filav['fec_ult_vac']));
					$fecha = time() - strtotime(($filav['fec_ult_vac']));
					$diferencia_dias = floor((($fecha / 3600) / 24) / 360);
					$last_year=explode('/', $filav['fec_ult_vac']);
					$last_year=$last_year[2]."-".$last_year[1]."-".$last_year[0];
					$last_year=explode('-', $last_year);

				}
				else
				{
					$fecha = time() - strtotime(($rc['fecing']));
					$anhos = floor((($fecha / 3600) / 24) / 360);
					$year=time('Y');
					$last_year=explode('-', $rc['fecing']);
				}

				/*
				$this->SetFont('helvetica','B',8);
				$this->Cell(40,5,'Dias de Vacaciones',1,0);
				$this->Cell(30,5,utf8_decode('Ultima Vacacion'),1,0);
				$this->Cell(40,5,utf8_decode('Fecha Inicio '),1,0);
				$this->Cell(40,5,utf8_decode('Fecha Reingreso '),1,0);
				$this->Cell(40,5,utf8_decode('Pago Neto'),1,0);
				$this->Ln();
				$this->SetFont('helvetica','I',7);
				$this->Cell(40,5,$diferencia_dias,0,0);
				$this->Cell(30,5,utf8_decode($diferencia_dias),0,0);
				$this->Cell(40,5,utf8_decode($filav['fec_ult_vac']),0,0);
				$this->Cell(40,5,utf8_decode($filav['fec_entrada_ult_vac']),0,0);
				$this->Cell(40,5,utf8_decode($filav['pago_neto']),0,0);
				$this->Ln();*/
				$this->SetFont('helvetica','B',8);
				$this->Cell(60,5,'Dias de Vacaciones',1,0);
				$this->Cell(60,5,utf8_decode('Periodo'),1,0);
				$this->Cell(70,5,utf8_decode('Pago Neto'),1,0);
				$this->Ln();
				if($anhos==0)
				{
					$fecha = time() - strtotime(($filav['fec_ult_vac']));
					$diferencia_dias = floor((($fecha / 3600) / 24) / 360);
					$this->SetFont('helvetica','I',7);
					$this->Cell(60,5,'------------',0,0);
					$this->Cell(60,5,'------------',0,0);
					$this->Cell(70,5,'------------',0,0);
					$this->Ln();
				}
				else
				{
					for ($i=0; $i < $anhos; $i++) { 
						$dias=+30;
					
					
						$this->SetFont('helvetica','I',7);
						$this->Cell(60,5,$dias,0,0);
						$this->Cell(60,5,utf8_decode($last_year[0]+$i),0,0);
						if ($i==0) {
							$this->Cell(70,5,$filav['pago_neto'],0,0);
						}
						else
						{
							$this->Cell(70,5,'-------------------',0,0);
						}
						$this->Ln();
					}
				}
				$this->Cell(190, 0, '', 'T');
				
			//}
			
		}

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
$pdf->SetTitle('Listado de Personal Vacaciones');
$pdf->SetSubject('Reporte Listado de Personal Vacaciones');
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
$pdf->ficha=$_REQUEST['ficha'];
$pdf->cedula=$_REQUEST['cedula'];

//$pdf->ColoredTable($reg);
$pdf->CuerpoTablaHtml($reg,$fecha_inicio,$fecha_fin,$fecha_registro);

// ---------------------------------------------------------

//Close and output PDF document
$pdf->Output('Informe_Listado_Vacaciones.pdf', 'I');

//============================================================+
// END OF FILE                                                
//============================================================+
//$informes->desconectar_bd();