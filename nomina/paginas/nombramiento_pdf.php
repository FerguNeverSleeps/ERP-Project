<?php
if(!isset($_SESSION)) session_start();

error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
date_default_timezone_set('America/Panama');

require('vendor/tcpdf/tcpdf.php');
require_once('../../lib/database.php');

class MYPDF extends TCPDF
{	
    public function Header() 
    {
        $this->SetFont('helvetica', 'B', 12);
        $this->Cell(340, 6,"", 0, 1,'C');
        $this->Cell(70, 6,"", 0, 0,'C');
        $this->Cell(200, 6,"Municipio de San Miguelito", 0, 0,'C');
        $this->Cell(70, 6,"", 0, 1,'C');

        $this->Cell(70, 6,date('Y-m-d'), 0, 0,'C');
        $this->Cell(200, 6,"Departamento de Planificacion y Presupuesto", 0, 0,'C');
        $this->Cell(70, 6,$_SESSION['nombre'].' '.date('h:m:s'), 0, 1,'L');

        $this->Cell(70, 6,"", 0, 0,'C');
        $this->Cell(200, 6,"Estructura de Personal ".date('Y'), 0, 0,'C');
        $this->Cell(70, 6,'Pagina '.$this->getAliasNumPage(), 0, 1,'C');
    }
    public function Footer()
    {

    }
}
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
    $align='C';
    $link=0;
    $stretch=0;
    $ignore_min_height=0;
    $calign='';
    $valign='';
    $height=6;//alto de cada columna
    $MiPDF->SetFont('helvetica', 'B', 12);
    $MiPDF->Cell(20, 6,"Numero", $border, $ln, $align, $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $MiPDF->Cell(80, 6,"Cargo", $border, $ln, $align, $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $MiPDF->Cell(60, 6,"Nombre", $border, $ln, $align, $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $MiPDF->Cell(60, 6,"Apellido", $border, $ln, $align, $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $MiPDF->Cell(50, 6,"Partida Presupuestal", $border, $ln, $align, $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $MiPDF->Cell(30, 6,"Salario",$border,$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $MiPDF->Cell(30, 6,"Salario Anual", $border,1,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
	$total=0;
	$sql = "SELECT a.nomposicion_id,a.nombres, a.apellidos,a.suesal,a.codnivel1,b.des_car,c.codorg,c.descrip,c.markar FROM nompersonal AS a,nomcargos AS b,nomnivel1 AS c WHERE a.codnivel1 = c.codorg AND a.codcargo = b.cod_cargo ORDER BY codorg,ficha ASC";

	$res2 = $db->query($sql);
	$subtotal1=0;
	$subtotal2=0;
	$j=0;
	$x=1;
	$i=0;
	while($data2 = mysqli_fetch_array($res2))
	{
	 	if ($data2["codorg"] > $x)
	    {
	    	$MiPDF->SetFont('helvetica', 'B', 12);
		    $MiPDF->Cell(40, 10,"", $border, $ln, $align, $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		    $MiPDF->Cell(60, 10, "Total por Departamento ".$i, $border, $ln, $align, $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		    $MiPDF->Cell(120, 10,"", $border, $ln, $align, $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		    $MiPDF->Cell(50, 10,"Subtotal $/.", $border,$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		    $MiPDF->Cell(30, 10,"$".$subtotal1,$border,$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		    $MiPDF->Cell(30, 10,"$".$subtotal2, $border,1,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		    $MiPDF->SetFont('helvetica', 'B', 14);
			$MiPDF->Cell(40, 10,"Dpto: ".$data2["codorg"], $border, 0, $align, $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
			$MiPDF->Cell(60, 10, $data2["descrip"], $border, 1, $align, $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
			$x=$data2["codorg"];
			$j=$data2["codorg"];
			$i=0;
	    }
	    if ($data2["codorg"] > $j)
	    {
		    $MiPDF->SetFont('helvetica', 'B', 14);
			$MiPDF->Cell(40, 10,"Dpto: ".$data2["codorg"], $border, 0, $align, $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
			$MiPDF->Cell(60, 10, $data2["descrip"], $border, 1, $align, $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
			$j=$data2["codorg"];
	    }
		    $MiPDF->SetFont('helvetica', 'N', 12);
		    if (empty($data2["nomposicion_id"])){$posicion='IND';}else{$posicion=$data2["nomposicion_id"];}
		    if (empty($data2["nomposicion_id"])){$posicion='IND';}else{$posicion=$data2["nomposicion_id"];}
		    $MiPDF->Cell(20, 6, $posicion, $border, $ln, $align, $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		    $MiPDF->Cell(80, 6, $data2["des_car"], $border, $ln, $align, $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		    $MiPDF->Cell(60, 6, $data2["nombres"], $border, $ln, $align, $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		    $MiPDF->Cell(60, 6, $data2["apellidos"], $border, $ln, $align, $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		    $MiPDF->Cell(50, 6, $data2["markar"], $border, $ln, $align, $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		    $MiPDF->Cell(30, 6,$data2["suesal"],$border,$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		    $MiPDF->Cell(30, 6,$data2["suesal"]*12, $border,$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		    $MiPDF->Ln();
			$subtotal1=$subtotal1+$data2["suesal"];
			$subtotal2=$subtotal2+($data2["suesal"]*12);
			$i=$i+1;
	}
$MiPDF->Output('estructura_personal.pdf','I');
?>