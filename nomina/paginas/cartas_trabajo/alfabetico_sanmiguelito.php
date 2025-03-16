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
        $this->Cell(70, 6,$_SESSION['nombre'].' '.date('h:m:s'), 0, 1,'L');

        $this->Cell(70, 6,date('Y-m-d'), 0, 0,'C');
        $this->Cell(200, 6,"LISTADO ALFABETICO DE EMPLEADOS", 0, 0,'C');
        $this->Cell(70, 6,'Pagina '.$this->getAliasNumPage(), 0, 1,'C');
    }
    public function Footer()
    {

    }
}
?>