<?php
session_start();
ob_start();

require('../../nomina/tcpdf/tcpdf.php');
include("../../nomina/lib/common.php");
date_default_timezone_set('America/Panama');
require_once('../../nomina/lib/database.php');
$db = new Database($_SESSION['bd']);

error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
date_default_timezone_set('America/Panama');
//----------------------------------------------------------------------------------------------------------------------------
//consulta de datos
$sql="SELECT a.id,b.apenom,b.cedula,((SUM(a.tiempo)*-1)-144) AS tiempo_exceso,(((SUM(a.tiempo)*-1)-144)/8) AS dias_exceso,c.descrip AS dpto 
    FROM dias_incapacidad AS a 
    LEFT JOIN nompersonal AS b ON a.cod_user = b.nomposicion_id 
    LEFT JOIN nomnivel2 AS c ON b.codnivel2 = c.codorg 
    WHERE b.estado != 'Egresado' AND b.estado != 'De Baja' 
    GROUP BY a.cod_user 
    ORDER BY a.id";
$res_emp = $db->query($sql);
//----------------------------------------------------------------------------------------------------------------------------
$sql4="SELECT * FROM nomempresa";
$res4 = $db->query($sql4);
$emp = mysqli_fetch_array($res4);
//----------------------------------------------------------------------------------------------------------------------------
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
            $f = $dia." DE ".$meses[$mes-1]. " DE ".$anio ;
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
        
    }
    public function Footer()
    {

    }
}
$ruta_img='../../includes/imagenes/logo_empresa.png';
# creamos  un nuevo objeto (MiPDF) utilizando la clase TCPDF
# incluyendo en este caso los valores a utilizar por el constructor
$MiPDF=new MYPDF('P','mm','LETTER');
#desactivamos encabezado y pie de página
# el método SetAuthor nos permite incluir el nombre del autor
$MiPDF->SetAuthor('Pedro Martinez');
# el método SetCreator nos permite incluir el nombre de la
# aplicacion que genera el pdf
$MiPDF->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$MiPDF->SetCreator('clase TCPDF');
# el método SetTitle nos permite incluir un título
$MiPDF->SetTitle('Reporte de Salarios');
# el método SetDisplayMode nos permite incluir palabras claves
# separadas por espacios y dentro de una misma cadena
$MiPDF->SetDisplayMode('fullpage','two');
# creamos una página en blanco. Incluimos, para esta primera página
# un cambio de orientación respecto a la inicial
    while ($fila = mysqli_fetch_array($res_emp))
    {
        $MiPDF->Addpage('H');
        $MiPDF->Image($ruta_img, $x='100', $y='8', $w=25, $h=20, $type='png', $link='', $align='C', $resize=false, $dpi=300, $palign='', $ismask=false, $imgmask=false, $border='LTB', $fitbox=false, $hidden=false, $fitonpage=false);
        $MiPDF->SetFont('helvetica', 'B', 14);
        $MiPDF->Cell(0, 6,strtoupper($emp['nom_emp']), 0, 1,'C');
        $MiPDF->Cell(0, 6,"DEPARTAMENTO DE REGISTRO Y CONTROL", 0, 1,'C');
        $MiPDF->Cell(0, 12,"", 0, 1,'C');
        $MiPDF->SetFont('helvetica', 'B', 12);
        $MiPDF->Cell(0, 6,"TEL.".$emp['tel_emp'], 0, 1,'R');
        $MiPDF->Cell(0, 6,"EXT.".$emp['fax_emp'], 0, 1,'R');
        $MiPDF->Cell(0, 12,"", 0, 1,'C');
        $MiPDF->SetFont('helvetica', 'B', 14);
        $MiPDF->Cell(0, 6,"AN/DRH/DRC/N°426", 0, 1,'L');
        $MiPDF->Cell(0, 6,fechas(date('Y-m-d'),1), 0, 1,'L');
        $MiPDF->Cell(0, 12,"", 0, 1,'C');
        $MiPDF->Cell(0, 6,"SEÑOR(A)", 0, 1,'L');
        $MiPDF->SetFont('helvetica', 'N', 12);
        $MiPDF->Cell(0, 6,strtoupper($fila['apenom']), 0, 1,'L');
        $MiPDF->Cell(0, 6,strtoupper($fila['dpto']), 0, 1,'L');
        $MiPDF->Cell(0, 6,"E.  S.  M", 0, 1,'L');
        $MiPDF->Cell(0, 6,"", 0, 1,'C');
        $MiPDF->Cell(0, 6,"SEÑOR(A):", 0, 1,'L');
        $MiPDF->Cell(0, 6,"", 0, 1,'C');
        $dias="cinco (05)";
        
        $MiPDF->Multicell(0,6,"Le comunicamos que de acuerdo a los registros del Sistema Tempo desde el 01 de enero al ".fechas(date('Y-m-d'),1).", usted se excedio en ".$dias." de los 18 o 144 horas a que tiene derecho durante ese año, por lo que se le aplico el articulo 174 del Reglamento de Administracion de Recursos Humanos.",0,$alineacion='J',$fondo=false,$salto_de_linea=1,$x='15',$y='',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=true);
        $MiPDF->Cell(0, 6,"", 0, 1,'C');
        $MiPDF->Multicell(0,6,'"...cuando el servidor publico sus dieciocho dias, el tiempo adicional podra ser descontado de su tiempo compensatorio o de las vacaciones."',0,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='15',$y='',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=true);

        $MiPDF->Cell(0, 6,"Atentamente,", 0, 1,'L');
        $MiPDF->Cell(0, 6,"", 0, 1,'C');
        $MiPDF->Cell(0, 6,"LIC. ELÍAS MEDINA HOA", 0, 1,'L');
        $MiPDF->Cell(0, 6,"JEFE DE REGISTRO Y CONTROL", 0, 1,'L');
        $MiPDF->SetFont('helvetica', 'B', 10);
        $MiPDF->Cell(0, 12,"", 0, 1,'L');
        $MiPDF->Cell(0, 6,"EMH/db", 0, 1,'L');
        $MiPDF->Cell(20, 6,"C.c.:", 0, 0,'L');
        $MiPDF->Cell(0, 6,"Lic. Carlos Jaime / Secretaria Tecnica Relaciones Internacionales", 0, 1,'L');
        $MiPDF->Cell(20, 6,"", 0, 0,'L');
        $MiPDF->Cell(0, 6,"Lic. Maria M. Solis / Direccion de Asesoria Legal y Tecnica de Comisiones.", 0, 1,'L');
        $MiPDF->Cell(20, 6,"", 0, 0,'L');
        $MiPDF->Cell(0, 6,"Expediente", 0, 1,'L');
    }
    $MiPDF->Output('tiempo_incapacidad.pdf','I');
    /*
    SELECT b.id,a.apenom,a.cedula,((SUM(b.tiempo)*-1)-144) AS tiempo_exceso,(((SUM(b.tiempo)*-1)-144)/8) AS dias_exceso 
    FROM `nompersonal` AS a,dias_incapacidad AS b
    WHERE a.estado != 'Egresado' AND a.estado != 'De Baja' AND a.nomposicion_id = b.cod_user
    GROUP BY a.nomposicion_id
    ORDER BY b.id
    */
?>