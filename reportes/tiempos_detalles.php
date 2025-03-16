<?php
session_start();
ob_start();
/**
 * Creates an example PDF TEST document using TCPDF
 * @package com.tecnick.tcpdf
 * @abstract TCPDF - Example: Custom Header and Footer
 * @author Nicola Asuni
 * @since 2008-03-04
 */
/*
require_once('../tcpdf.php');
require_once '../../lib/config.php';
require_once '../../lib/common.php';
*/
require('../../nomina/tcpdf/tcpdf.php');
include("../../nomina/lib/common.php");
date_default_timezone_set('America/Panama');
require_once('../../nomina/lib/database.php');

// Extend the TCPDF class to create custom Header and Footer
class MYPDF extends TCPDF {
	public $cedula, $tipo, $fecharegistro, $quincena, $mes, $año, $id;
	//Page header
	public function Header() {
		// Logo
		$conexion = new bd($_SESSION['bd']);
		$sql_2         = "SELECT apenom, cedula, nombres, apellidos from nompersonal where cedula = '{$this->cedula}'";
		$result_2      = $conexion->query($sql_2, $conexion);
		$nombre_titulo = $result_2->fetch_array();
		$sql_3         = "SELECT * from tipo_justificacion where idtipo = '{$this->tipo}'";
		$result_3      = $conexion->query($sql_3, $conexion);
		$nombre_titulo_2 = $result_3->fetch_array();
		$this->SetFont('helvetica', 'B', 14);
		$this->SetTextColor(0);
		$this->SetFillColor(255, 255, 255);
		$this->SetTextColor(0,0,0);
		$this->SetDrawColor(100, 100, 100);
		$this->Cell(220,8,$nombre_titulo_2['descripcion']." (Horas) ".$nombre_titulo['apenom'], 0, 0, 'L', 1); 	 
		$this->SetFont('helvetica', 'B', 9);
		$this->Cell(50,8,"Pag. ".$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, 0, 'R', 1); 	 
		$this->Ln();
		$this->Ln();
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
	public function CuerpoTablaHtml()
	{
		$conexion = new bd($_SESSION['bd']);
	    if($this->getAliasNumPage() <= 1)
		{
			$Y=14;
			$this->SetTopMargin ($Y);
			$this->SetTopMargin ($Y);

		}
		else
		{
			$Y=14;
			$this->SetTopMargin($Y);
			$this->SetTopMargin ($Y);

		}

		$fill              = 1;
		$border            =0;
		$ln                =0;
		$fill              = 0;
		$align             ='C';
		$link              =0;
		$stretch           =1;
		$ignore_min_height =0;
		$calign            ='T';
		$valign            ='m';
		$height            =5.75;//alto de cada columna
		$YMax              =260;
		$altura            =9;

	 

		$this->SetFont('helvetica', 'B', 14);
		$this->SetTextColor(0);
		$this->SetFillColor(255, 255, 255);
		$this->SetTextColor(0,0,0);
		$this->SetDrawColor(100, 100, 100);
		$w = array(25, 15,13,13,10,22,30,60,60,30);

		$sql_2         = "SELECT apenom, cedula, nombres, apellidos from nompersonal where cedula = '{$this->cedula}'";
		$result_2      = $conexion->query($sql_2, $conexion);
		$nombre_titulo = $result_2->fetch_array();
		$sql_3         = "SELECT * from tipo_justificacion where idtipo = '{$this->tipo}'";
		$result_3      = $conexion->query($sql_3, $conexion);
		$nombre_titulo_2 = $result_3->fetch_array();

		/*$this->Cell(200,8,$nombre_titulo_2['descripcion']." (Horas) ".$nombre_titulo['apenom'], 0, 0, 'L', 1); 	 
		$this->Ln();
		$this->Ln();*/
        $this->SetFont('helvetica', 'B', 7);

			$this->SetY(25);

		$cabecera = array('Fecha', 'Tiempo' , 'Observación','Dias','Horas','Minutos');
    	$cuerpo=array($fecha,$turno);
       	$w = array(25, 25,190,10,10,10);
  	  	$num_headers = count($cabecera);

		for($i = 0; $i < $num_headers; ++$i) {
			$this->Cell($w[$i], 8, $cabecera[$i], 1, 0, 'C', 1);

		}
		$this->Ln();
		$sql_columnas = "SELECT A.*, date_format(A.fecha,'%d/%b/%Y') fecha2";

		$sql_FROM = " FROM dias_incapacidad as A";

		$sql_WHERE = " WHERE A.cedula='{$this->cedula}' AND A.tipo_justificacion='{$this->tipo}' ";
		$fragmento_sql = "";


		$fragmento_sql .= "ORDER BY A.fecha DESC";


		$sql_WHERE .= $fragmento_sql;
		$sql       .= $sql_columnas;
		$sql       .= $sql_FROM;
		$sql       .= $sql_WHERE;
		$sql       .= $sql_LIMIT;

		$res1=$conexion->query($sql);			
		$this->SetFont('helvetica', '', 9);

	    while($fila = mysqli_fetch_array($res1))
	    {
	    	//print_r($fila);exit;
			//$header = array('Fecha', 'Turno', 'Entr.','SA','EA','Sal.','Sal. DS','Desc.','Ord.','Dom','Nac','Tard.','EM','DNT','Extra','ExtraExt','ExtraNoc','NocExt');

			$this->SetFillColor(255, 255, 255);
			$this->SetTextColor(0,0,0);
			$this->SetDrawColor(100, 100, 100);
			$this->SetLineWidth(0.3);
			// Header
			$num_headers = count($header);			

			if(	$this->GetY() >172)
			{
				$this->AddPage();
			}
			if($fila['tipo_justificacion']==3)
		    {
		        if($fila['dias_restante']!=NULL)
		            $dias = $fila['dias_restante'];
		        else
		            $dias =0;
		        if($fila['horas_restante']!=NULL)
		            $horas = $fila['horas_restante'];
		        else
		            $horas = 0;
		        if($fila['minutos_restante']!=NULL)
		            $minutos = $fila['minutos_restante'];
		        else
		            $minutos = 0;
		    }
		    else
		    {
		        if($fila['dias']!=NULL)
		            $dias = $fila['dias'];
		        else
		            $dias = 0;
		        if($fila['horas']!=NULL)
		            $horas = $fila['horas'];
		        else
		            $horas = 0;
		        if($fila['minutos']!=NULL)
		            $minutos = $fila['minutos'];
		        else
		           $minutos = 0;
		    }

			$border=1;
			$ixx=0;
			$this->Multicell(25,$altura,$fila['fecha2']." ".$this->GetY(),"TBLR",$alineacion='C',$fondo=false,$salto_de_linea=0,$x='',$y='',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=0,$maxh=50,$alineacion_vertical='T', $fitcell=false);

			$this->Multicell(25,$altura,$fila['tiempo'],"TBLR",$alineacion='C',$fondo=false,$salto_de_linea=0,$x='',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=0,$maxh=50,$alineacion_vertical='T', $fitcell=false);

			$this->Multicell(190,$altura,substr(utf8_encode($fila['observacion']), 0,250) ,"TBLR",$alineacion='L',$fondo=false,$salto_de_linea=0,$x='',$y='',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=0,$maxh=50,$alineacion_vertical='T', $fitcell=false);

			$this->Multicell(10,$altura,$dias,"TBLR",$alineacion='C',$fondo=false,$salto_de_linea=0,$x='',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=0,$maxh=50,$alineacion_vertical='T', $fitcell=false);

			$this->Multicell(10,$altura,$horas,"TBLR",$alineacion='C',$fondo=false,$salto_de_linea=0,$x='',$y='',$reseth=true,$ajuste_horizontal=true,$ishtml=false,$autopadding=0,$maxh=50,$alineacion_vertical='T', $fitcell=false);

			$this->Multicell(10,$altura,$minutos,"TBLR",$alineacion='C',$fondo=false,$salto_de_linea=0,$x='',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=0,$maxh=50,$alineacion_vertical='T', $fitcell=false);
		    $this->Ln();
		    if ($this->GetY()>172) {
		    	$this->AddPage();
			$this->SetY(25);

		    }

         }//fin del while
	}
	public function Footer() {
		// Logo
		//$this->SetY(-40);

		// Set font
		// Page number
		
	}
}//fin clase
		
		
		

// create new PDF document
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', true);
$pdf->cedula=$_REQUEST['cedula'];
$pdf->tipo=$_REQUEST['tipo'];

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Amaxonia');
$pdf->SetTitle('Tiempos Detalles');
$pdf->SetSubject('Reporte Tiempos Detalles');
$pdf->SetKeywords('TCPDF, PDF, reporte, Tiempos, Detalles, funcionario');

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
$pdf->AddPage('L', 'A4');
$pdf->cedula=$_GET['cedula'];

//$pdf->ColoredTable($reg);
$pdf->CuerpoTablaHtml();
ob_clean();

// ---------------------------------------------------------

//Close and output PDF document
$pdf->Output('pdf_vacaciones_por_funcionario.pdf', 'I');

//============================================================+
// END OF FILE                                                
//============================================================+
//$informes->desconectar_bd();