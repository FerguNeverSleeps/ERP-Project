<?php
session_start();
ob_start();

require('../../nomina/tcpdf/tcpdf.php');
include("../../nomina/lib/common.php");
date_default_timezone_set('America/Panama');
require_once('../../nomina/lib/database.php');
$db = new Database($_SESSION['bd']);
//--------------------------------------------------------------------------------------------------------------------------------------
//consulta de datos
$sql = "SELECT nomposicion_id,IdDepartamento,apellidos,nombres,cedula,fecnac,fecing,suesal,estado,num_decreto,tipnom,codcargo,nomfuncion_id FROM nompersonal";
$result1 = $db->query($sql, $conexion);
//------------------------------------------------------------
$sql3 = "SELECT * FROM departamento";
$result3 = $db->query($sql3, $conexion);
$i=0;
while($departamentos=mysqli_fetch_array($result3))
{
    $dep[$i]=$departamentos['IdDepartamento'];
    $descripcion_d[$i]=$departamentos['Descripcion'];
    $i++;
}
$total_dep=$i;
//------------------------------------------------------------
$sql4 = "SELECT * FROM nomcargos";
$result4 = $db->query($sql4, $conexion);
$i=0;
while($cargos=mysqli_fetch_array($result4))
{
    $codcargo[$i]=$cargos['cod_car'];
    $desc_car[$i]=$cargos['des_car'];
    $i++;
}
$total_car=$i;
$j=0;
while($j<$total_car)
{
     $codcargo[$j];
     $desc_car[$j];
     $j++;
}
//------------------------------------------------------------
$sql5 = "SELECT * FROM nomfuncion";
$result5 = $db->query($sql5, $conexion);
$i=0;
while($funcion=mysqli_fetch_array($result5))
{
    $nom_id[$i]=$funcion['nomfuncion_id'];
    $desc_fun[$i]=$funcion['descripcion_funcion'];
    $i++;
}
$total_fun=$i;
$j=0;
while($j<$total_fun)
{
     $nom_id[$j];
     $desc_fun[$j];
     $j++;
}
//------------------------------------------------------------------------------------------------------------------------------------
$sql2="SELECT * FROM nomempresa";
$res2 = $db->query($sql2);
$empresa = mysqli_fetch_array($res2);
//--------------------------------------------------------------------------------------------------------------------------------------
function departamentos($id,$total_dep,$dep,$descripcion_d){
      $exist=0;
      $per_dep=$id;
      // esto es para saber si el departamento existe
      $j=0;
      while($j< $total_dep)
      {
          if($per_dep==$dep[$j])
          {
            $exist=1;
            $desc=$descripcion_d[$j];
          } 
            $j++;
      }
      if($exist==0)
      {
            return 'SIN ASIGNAR';
      }
      if($exist==1)
      {
            return $desc;
      }
}
function codcargo($id,$codcargo,$total_car,$desc_car)
{
      $exist_car=0;
      $per_car=$id;
      $j=0;
      while($j<$total_car)
      {
            if($per_car==$codcargo[$j])
            {
            $exist_car=1;
            $cargo_persona=$desc_car[$j];
          }
            $j++;
      }
      if($exist_car==0)
      {
            return 'SIN ASIGNAR';
      }
      if($exist_car==1)
      {
            return $cargo_persona;
      }
}
function nomfuncion($id,$total_fun,$nom_id,$desc_fun)
{
      $exist_fun=0;
      $per_fun=$id;
      $j=0;
      while($j<$total_fun)
      {
            if($per_fun==$nom_id[$j])
            {
            $exist_fun=1;
            $funcion_persona=$desc_fun[$j];
          }
            $j++;
      }
      if($exist_fun==0)
      {
            return 'SIN ASIGNAR';
      }
      if($exist_fun==1)
      {
            return $funcion_persona;
      }
}
//-----------------------------------------------------
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
$MiPDF->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
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
    $MiPDF->SetFont('helvetica', 'B', 10);
    $MiPDF->Image($ruta_img.'ginteven_logo.jpg', $x='15', $y='10', $w=25, $h=20, $type='jpg', $link='', $align='', $resize=false, $dpi=300, $palign='', $ismask=false, $imgmask=false, $border='LTB', $fitbox=false, $hidden=false, $fitonpage=false);

    $MiPDF->Multicell(205,5,'','T',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='40',$y='10',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

    $MiPDF->Multicell(205,5,$empresa['nom_emp'],0,$alineacion='C',$fondo=false,$salto_de_linea=0,$x='40',$y='15',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

    $MiPDF->SetFont('times','', 9);
    $MiPDF->Multicell(205,5,'PLANILLA SICLAR',0,$alineacion='C',$fondo=false,$salto_de_linea=0,$x='40',$y='20',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

    $MiPDF->Multicell(205,5,'','B',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='40',$y='25',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

    $MiPDF->Image($ruta_img.'ginteven_logo.jpg', $x='245', $y='10', $w=25, $h=20, $type='jpg', $link='', $align='', $resize=false, $dpi=300, $palign='', $ismask=false, $imgmask=false, $border='TRB', $fitbox=false, $hidden=false, $fitonpage=false);
    $MiPDF->SetFont('helvetica', 'B', 10);

    $MiPDF->Multicell(65,10,"Usuario:".$_SESSION['nombre'].' '.date('h:m:s'),$borde=1,$alineacion='C',$fondo=false,$salto_de_linea=0,$x='275',$y='10',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='B', $fitcell=true);
    $MiPDF->Multicell(65,10,'Fecha de creacion:'. date('d-m-Y'),$borde=1,$alineacion='C',$fondo=false,$salto_de_linea=1,$x='275',$y='20',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='B', $fitcell=true);

    $MiPDF->Cell(0, 6,"", $border='',1, $align, $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $MiPDF->SetFont('helvetica', 'B',8);
    $MiPDF->Cell(65,8,"Departamento", $border='LTRB', $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $MiPDF->Cell(15,8,"Planilla", $border='TRB', $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $MiPDF->Cell(15,8,"Posicion", $border='TRB', $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $MiPDF->Cell(30,8,"Apellidos", $border='TRB', $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $MiPDF->Cell(30,8,"Nombres", $border='TRB', $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $MiPDF->Cell(20,8,"Cedula",$border='TRB',$ln,$align='C',$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $MiPDF->Cell(20,8,"F. Nacimiento", $border='TRB', $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $MiPDF->Cell(35,8,"Cargo", $border='TRB', $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $MiPDF->Cell(20,8,"Funcion", $border='TRB', $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $MiPDF->Cell(20,8,"F. Inicio", $border='TRB', $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $MiPDF->Cell(15,8,"Salario", $border='TRB', $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $MiPDF->Cell(20,8,"Estado",$border='TRB',$ln,$align='C',$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $MiPDF->Cell(20,8,"Resolucion",$border='TRB',1,$align='C',$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $i=0;
    
    while ($data = fetch_array($result1))
    {
        $date = date_create($data["fecnac"]);
        $nuevofn = date_format($date, "d-m-Y");
        $fecha = date_create($data["fecing"]);
        $nuevofi = date_format($fecha,"d-m-Y");
        $MiPDF->Cell(65,8,departamentos($data['IdDepartamento'],$total_dep,$dep,$descripcion_d), $border='LR', $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(15,8,$data['tipnom'], $border='R', $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(15,8,$data['nomposicion_id'], $border='R', $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(30,8,$data['apellidos'], $border='R', $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(30,8,$data['nombres'], $border='R', $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(20,8,$data['cedula'], $border='R', $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(20,8,$nuevofn, $border='R', $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(35,8,codcargo($data['codcargo'],$codcargo,$total_car,$desc_car), $border='R', $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(20,8,nomfuncion($data['nomfuncion_id'],$total_fun,$nom_id,$desc_fun), $border='R', $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(20,8,$nuevofi, $border='R', $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(15,8,$data['suesal'], $border='R', $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(20,8,$data['estado'], $border='R', $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(20,8,$data['num_decreto'],$border='R',1,$align='C',$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $i=$i+1;
    }
    $MiPDF->SetFont('helvetica', 'B',9);
    $MiPDF->Cell(40,8,"TOTAL POSICIONES DISPONIBLES: ", $border='TB', $ln, $align='R', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $MiPDF->Cell(20,8,$i, $border='TB', $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $MiPDF->Cell(130,8,"", $border='TB', $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);

    $MiPDF->Output('posiciones_vacante_minsa_pdf.pdf','I');
?>