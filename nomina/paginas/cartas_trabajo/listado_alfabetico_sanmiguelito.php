<?php
require_once 'vendor/phpword/PhpWord/Autoloader.php';
date_default_timezone_set('America/Panama');

require_once('../../lib/database.php');
require ('numeros_letras_class.php');
	
	$nomina_id = $_GET['nomina_id'];
    $codt = $_GET['codt'];
	$nomposicion_id = $_GET['nomposicion_id'];
	$tipnom = $_GET['tipnom'];
	$db = new Database($_SESSION['bd']);

$sql = "SELECT * FROM nomnivel1";
$res1 = $db->query($sql);

$res2 = $db->query($sql);
# incluimos la clase TCPDF que está en este mismo directorio
require('alfabetico_sanmiguelito.php');
# creamos  un nuevo objeto (MiPDF) utilizando la clase TCPDF
# incluyendo en este caso los valores a utilizar por el constructor
$MiPDF=new MYPDF('P','mm','LEGAL');
#desactivamos encabezado y pie de página
# el método SetAuthor nos permite incluir el nombre del autor
$MiPDF->SetAuthor('Pedro Martinez');
# el método SetCreator nos permite incluir el nombre de la
# aplicacion que genera el pdf
// set margins
$MiPDF->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$MiPDF->SetCreator('clase TCPDF');
# el método SetTitle nos permite incluir un título
$MiPDF->SetTitle('Reporte de Salarios');
# el método SetDisplayMode nos permite incluir palabras claves
# separadas por espacios y dentro de una misma cadena
$MiPDF->SetDisplayMode('fullpage','two');
# creamos una página en blanco. Incluimos, para esta primera página
# un cambio de orientación respecto a la inicial
$MiPDF->Addpage('L');

	$fill = FALSE;
    $border=0;
    $ln=0;
    $fill = 0;
    $align='L';
    $link=0;
    $stretch=0;
    $ignore_min_height=0;
    $calign='';
    $valign='';
    $height=6;//alto de cada columna
    $MiPDF->SetFont('helvetica', 'B', 10);
    $MiPDF->Cell(70, 6,"Nombre del Empleado", $border, $ln, 'L', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $MiPDF->Cell(20, 6, "Cedula", $border,$ln, $align, $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $MiPDF->Cell(20, 6, "Fecha ing", $border,$ln, $align, $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
	$MiPDF->Cell(45, 6, "T. Servicio", $border,$ln, $align, $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $MiPDF->Cell(20, 6,"Posicion", $border, $ln, $align, $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $MiPDF->Cell(70, 6,"Nombre Dpto", $border, $ln, $align, $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $MiPDF->Cell(40, 6,"Partida", $border, $ln, $align, $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $MiPDF->Cell(25, 6,"Sueldo Quin",$border,$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $MiPDF->Cell(25, 6,"Sueldo Men",$border,1,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $MiPDF->Cell(335, 6,"",$border,1,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
	$total=0;
	$sql = "SELECT a.nomposicion_id,a.cedula,a.apenom,a.suesal,a.fecing,b.descrip,b.markar FROM nompersonal AS a,nomnivel1 AS b WHERE a.codnivel1 = b.codorg ORDER BY apenom ASC";

	$res2 = $db->query($sql);
	$subtotal1=0;
	$subtotal2=0;
	$j=0;
	while($data2 = mysqli_fetch_array($res2))
	{
	    if ((empty($data2["fecing"])) || (!isset($data2["fecing"])))
	    {
	    	$tiempo='0000-00-00';
	    }
	    else
	    {
	    	$datetime1 = new DateTime($data2["fecing"]);
			$datetime2 = new DateTime(date('Y-m-d'));
			$interval = $datetime1->diff($datetime2);
			$tiempo = $interval->format('%Y años, %M meses, %D días');
	    }
	    $MiPDF->SetFont('helvetica', 'N', 8);
	    $MiPDF->Cell(70, 6, $data2["apenom"], $border, $ln, 'L', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
	    $MiPDF->Cell(20, 6, $data2["cedula"], $border,$ln, $align, $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
	    $MiPDF->Cell(20, 6, $data2["fecing"], $border,$ln, $align, $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
	    $MiPDF->Cell(45, 6, $tiempo, $border,$ln, $align, $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
	    $MiPDF->Cell(20, 6, $data2["nomposicion_id"], $border,$ln, $align, $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
	    $MiPDF->Cell(70, 6, $data2["descrip"], $border, $ln, $align, $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
	    $MiPDF->Cell(40, 6, $data2["markar"], $border, $ln, $align, $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
	    $MiPDF->Cell(25, 6,"$".$data2["suesal"]/2,$border,$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
	    $MiPDF->Cell(25, 6,"$".$data2["suesal"],$border,1,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
	    $MiPDF->Ln();
		$subtotal1=$subtotal1+$data2["suesal"];
		$subtotal2=$subtotal2+($data2["suesal"]*12);
	}
$MiPDF->Output('listado_alfabetico.pdf','I');
?>