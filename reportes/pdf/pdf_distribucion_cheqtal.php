<?php
session_start();
ob_start();

require('../../nomina/tcpdf/tcpdf.php');
include("../../nomina/lib/common.php");
date_default_timezone_set('America/Panama');
require_once('../../nomina/lib/database.php');
$db = new Database($_SESSION['bd']);
//------------------------------------------------------------------------------------------------------------
//consulta de datos
$codnom = $_POST['planilla'];
//------------------------------------------------------------
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
date_default_timezone_set('America/Panama');

function fechas($fecha,$formato)
{
    $meses = array("ENERO","FEBRERO","MARZO","ABRIL","MAYO","JUNIO","JULIO","AGOSTO","SEPTIEMBRE","OCTUBRE","NOVIEMBRE","DICIEMBRE");
    $separa = explode("-",$fecha);
    $anio = $separa[0];
    $mes = $separa[1];
    $dia = $separa[2];
    switch ($formato)
    {
        case 1:
            // ejemplo = DEL 01 DE ENERO DE 2016
            $f = 'DEL '.$dia." DE ".$meses[$mes-1]. " DE ".$anio ;
            break;
        case 2:
            // ejemplo = DEL 01 DE ENERO DE 2016
                $f = $dia." DIAS DEL MES DE ".$meses[$mes-1]. " DE ".$anio ;            
            break;
        default:
            $f = date('Y');
            break;
    }
    return $f;
}
class MYPDF extends TCPDF
{   
    public function Header() 
    {
        $this->SetFont('helvetica', 'B',12);
        $this->Cell(0,8,"", "", 1, $align='C', 0,0,1,0,'','');
        $this->Cell(0,8,"INSTITUTO PANAMEÑO AUTONOMO COOPERATIVO", "", 1, $align='C', 0,0,1,0,'','');
        $this->Cell(0,8,"DEPARTAMENTO DE RECURSOS HUMANOS", "", 1, $align='C', 0,0,1,0,'','');
        $this->Cell(80,8,"FECHA : ".date("d-m-Y"), "B", 0, $align='C', 0,0,1,0,'','');
        $this->Cell(160,8,"CONTROL DE ENTREGA DE CHEQUES", "B", 0, $align='C', 0,0,1,0,'','');
        $this->Cell(80,8,'Pagina '.$this->getAliasNumPage(), "B", 1, $align='C', 0,0,1,0,'','');
    }
    public function Footer()
    {

    }
}
# creamos  un nuevo objeto (MiPDF) utilizando la clase TCPDF
$MiPDF=new MYPDF('L','mm','LEGAL');
#desactivamos encabezado y pie de página
# el método SetAuthor nos permite incluir el nombre del autor
$MiPDF->SetAuthor('Pedro Martinez');
// set margins
$MiPDF->SetMargins(PDF_MARGIN_LEFT, 35, PDF_MARGIN_RIGHT);
# el método SetTitle nos permite incluir un título
$MiPDF->SetTitle('Reporte planilla SICLAR');
# el método SetDisplayMode nos permite incluir palabras claves
# separadas por espacios y dentro de una misma cadena
$MiPDF->SetDisplayMode('fullpage','two');
# creamos una página en blanco. Incluimos, para esta primera página
# un cambio de orientación respecto a la inicial
$MiPDF->AddPage('L');

    $fill = FALSE;
    $border=0;
    $ln=0;
    $fill = 0;
    $align='L';
    $link=0;
    $stretch=1;
    $ignore_min_height=0;
    $calign='';
    $valign='';
    $height=6;
    $ruta_img='../../nomina/imagenes/';
    $reg="";
    //Imprimiendo empleados
    $sql_emp = "SELECT a.*,b.apenom, b.cedula,b.codnivel1,b.codnivel2
    		FROM nom_nomina_netos a
    		LEFT JOIN nompersonal b ON b.cedula=a.cedula
    		WHERE a.codnom =1
    		ORDER BY b.codnivel1,b.codnivel2,b.ficha ASC";
    $res = $db->query($sql_emp);
	while ($emp = mysqli_fetch_array($res)) {
		if ($emp['codnivel2'] !=$reg) {
			$reg =$emp['codnivel2'];
			$sql = "SELECT a.descrip nivel2, b.descrip nivel1
					FROM nomnivel2 a
					LEFT JOIN nomnivel1 b ON b.codorg=a.gerencia
					WHERE a.codorg = '".$emp['codnivel2']."'";
			$res_niv = $db->query($sql);
			$nivel = mysqli_fetch_array($res_niv);
			//Regiones
		    $MiPDF->SetFont('helvetica', 'B',14);
		    $MiPDF->Cell(0,1,"", "", 1, $align='L', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		    $MiPDF->Cell(40,8,"Region :", "T", $ln, $align='R', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		    $MiPDF->Cell(280,8,$nivel['nivel1'], "T", 1, $align='L', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		    $MiPDF->Cell(40,8,"Departamento :", "B", $ln, $align='R', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		    $MiPDF->Cell(280,8,$nivel['nivel2'], "B", 1, $align='L', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		    $MiPDF->SetFont('helvetica', 'B',12);
		    $MiPDF->Cell(30,8,"No. Empleado", $border='LTRB', $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		    $MiPDF->Cell(120,8,"Nombre del Empleado", $border='TRB', $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		    $MiPDF->Cell(30,8,"No. Cedula", $border='TRB', $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		    $MiPDF->Cell(50,8,"No. Cheque/Talonario", $border='TRB', $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		    $MiPDF->Cell(30,8,"Valor Neto", $border='TRB', $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		    $MiPDF->Cell(60,8,"Recibido por", $border='TRB', 1, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		    $MiPDF->Cell(0,1,"", "", 1, $align='L', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		}

		$MiPDF->SetFont('helvetica', 'B',12);
		$MiPDF->Cell(30,8,$emp['ficha'], "", $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$MiPDF->Cell(120,8,$emp['apenom'], "", $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$MiPDF->Cell(30,8,$emp['cedula'], "", $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$MiPDF->Cell(50,8,$emp['cta_ban'], "", $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$MiPDF->Cell(30,8,$emp['neto'], "", $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$MiPDF->Cell(5,8,"", "", 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$MiPDF->Cell(50,8,"", "B", 1, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
	}

    $MiPDF->Output('distribucion_cheques_talonarios.pdf','I');
?>