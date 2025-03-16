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

//datos recibidos
$ficha          						    = ( isset($_GET['ficha']) )   ? $_GET['ficha']          : '' ;
$tipnom                          	        = ( isset($_GET['tipnom']) )  ? $_GET['tipnom']         : '' ;


function fechas($fecha,$formato)
{
    $meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
    $separa = explode("-",$fecha);
    $anio = $separa[0];
    $mes = $separa[1];
    $dia = $separa[2];
    switch ($formato)
    {
        case 1:
            // ejemplo = DEL 01 DE ENERO DE 2016
            $f = $dia.'/'.$mes.'/'.$anio;
            break;
        case 2:
            // ejemplo = DEL 01 DE ENERO DE 2016
                $f = $dia." dias del mes de ".$meses[$mes-1]. " de ".$anio ;            
            break;
        default:
            $f = date('Y');
            break;
    }
    return $f;
}

$sqlemp = "SELECT * FROM nomempresa";
$resemp = $db->query($sqlemp);
$emp = fetch_array($resemp);
global $empresa;
$empresa = $emp['nom_emp'];              
class MYPDF extends TCPDF
{   
    public function Header() 
    {
        global $empresa;
        $this->SetFont('times', 'B', 11);
        $image_file = "/var/www/html/migracion_rrhh/nomina/imagenes/migracion.jpg";
        $altura=8;

        $this->Image($image_file, $x=20, $y=8, $w=35,   $h=35, $type='', $link='', $align='C', $resize=false, $dpi=300, $palign='', $ismask=false, $imgmask=false, $border=0, $fitbox=false, $hidden=false, $fitonpage=false);
        $this->Multicell(210,$altura,'',$border=0,$align='C',$fill=0,$ln=1,$x='0',$y='',$reseth=true,$stretch=0,$ishtml=false,$autopadding=true,$maxh=5,$valign='M',$fitcell=false);
        $this->Multicell(210,7,'REPÚBLICA DE PANAMÁ',$border=0,$align='C',$fill=0,$ln=1,$x='0',$y='',$reseth=true,$stretch=0,$ishtml=false,$autopadding=true,$maxh=10,$valign='M',$fitcell=false);
        $this->Multicell(210,7,strtoupper($empresa),$border=0,$align='C',$fill=0,$ln=1,$x='0',$y='',$reseth=true,$stretch=0,$ishtml=false,$autopadding=true,$maxh=10,$valign='M',$fitcell=false); 
         $this->Multicell(210,7,'REGISTRO Y CONTROL',$border=0,$align='C',$fill=0,$ln=1,$x='0',$y='',$reseth=true,$stretch=0,$ishtml=false,$autopadding=true,$maxh=10,$valign='M',$fitcell=false); 
         $this->Multicell(210,7,'EXPEDIENTE DE FUNCIONARIO',$border=0,$align='C',$fill=0,$ln=1,$x='0',$y='',$reseth=true,$stretch=0,$ishtml=false,$autopadding=true,$maxh=10,$valign='M',$fitcell=false);
         //$this->Multicell(210,20,'DATOS PERSONALES',$border=0,$align='C',$fill=0,$ln=1,$x='0',$y='',$reseth=true,$stretch=0,$ishtml=false,$autopadding=true,$maxh=20,$valign='M',$fitcell=false); 
        //($w, $h, $txt, $border=0, $align='J',$fill=false, $ln=1, $x='', $y='', $reseth=true, $stretch=0, $ishtml=false, $autopadding=true, $maxh=0, $valign='T', $fitcell=false)

        // Title
        //$this->Cell(195,$altura,"REPÚBLICA DE PANAMÁ",1,1,$align='C',0,0,1,0,'',''); //order(ancho,alto,mensaje,borde,salto de linea,alineacion,fill,link,strech,ignore min  altura, caling, valing)
        //$this->Cell(195,$altura,"SERVICIO NACIONAL DE MIGRACIÓN",1,1,$align='C',0,0,1,0,'',''); 
    }
    public function Footer()
    {

    }
}
# creamos  un nuevo objeto (MiPDF) utilizando la clase TCPDF
$MiPDF=new MYPDF('P','mm','A4');
#desactivamos encabezado y pie de página
# el método SetAuthor nos permite incluir el nombre del autor
$MiPDF->SetAuthor('Aquiles Penott');
# el método SetTitle nos permite incluir un título
$MiPDF->SetTitle('Resumen de Expediente');
# el método SetDisplayMode nos permite incluir palabras claves
# separadas por espacios y dentro de una misma cadena
$MiPDF->SetDisplayMode('fullpage','two');
# creamos una página en blanco. Incluimos, para esta primera página
# un cambio de orientación respecto a la inicial
$MiPDF->SetMargins(0,40,0, true);

$MiPDF->AddPage('P');

$ruta_img='../../nomina/imagenes/';

$MiPDF->SetFillColor(215, 235, 255); // Color de Fondo

//Consulta DATOS PRINCIPALES
$primer_n  =  NULL;                                        // INICIALIZAR EL PRIMER NOMBRE 
$second_n  =  NULL;                                        // INICIALIZAR SEGUNDO NOMBRE
$primer_a  =  NULL;                                        // INICIALIZAR EL PRIMER APELLIDO 
$second_a  =  NULL;                                        // INICIALIZAR EL SEGUNDO APELLIDO 
$sexo      =  NULL;                                        // INICIALIZAR EL SEXO
$cedula    =  NULL;                                        // INICIALIZAR CEDULA
$email     =  NULL;                                        // INICIALIZAR EMAIL
$fecha_nac =  NULL;                                        // INICIALIZAR FECHA
$telefonR  =  NULL;                                        // INICIALIZAR TELEFONO CASA
$telefonoc =  NULL;                                        // INICIALIZAR TELEFONO CEL

$sql       = "SELECT a.* FROM nompersonal AS a WHERE a.ficha='$ficha'";
$res       = $db->query($sql);
if($res->num_rows>=1)
{
        // DATOS DEL PRIMER RENGLON
        $persona   = $res->fetch_object();

        //SACAR EL PRIMER NOMBRE Y EL SEGUNDO
        $nom       = $persona ->nombres;                         // PRIMER Y SEGUNDO NOMBRES EN LA BD       
        $nom2      = $persona ->nombres2;                         // PRIMER Y SEGUNDO NOMBRES EN LA BD       
        $nombre    = strtoupper($nom); 
        $noma      = explode(" ",$nombre);

        if(isset($noma[0])){ $primer_n  = $noma[0]; }             // SI EXISTE EL PRIMER NOMBRE EN LA BD

        ///if(isset($noma[1])){ $second_n  = $noma[1]; }             // SI EXISTE EL SEGUNDO NOMBRE EN LA BD
        $second_n  = $persona -> apellidos;

        $ape       = $persona ->apellidos;
        $apellidos = strtoupper($ape);
        $apem      = explode(" ",$apellidos);

        //if(isset($apem[0])){ $primer_a  = $apem[0];}              // SI EXISTE EL PRIMER APELLIDO EN LA BD

        $s_a               =  $persona ->apellido_materno; 
        
        $second_a          =  strtoupper($s_a);                           // SEGUNDO APELLIDO
        
        $sex               =  $persona ->sexo; 
        $foto              =  $persona ->foto;                            // FOTO  
        $num_resolucion    =  $persona ->num_resolucion;                  // FOTO  
        $fecha_resolucion  =  $persona ->fecha_resolucion;                // FOTO  
        $fecha_permanencia =  $persona ->fecha_permanencia;               // FOTO  
        
        $sexo              =  strtoupper($sex);                           // SEXO
        $cedula            =  $persona ->cedula;                          // CEDULA
        $email             =  $persona ->email;                           // EMAIL
        $fnac              =  $persona ->fecnac;                          // FECHA EN LA BD
        $fecha_nac         =  fechas($fnac,1);                            // VOLTEAMOS LA FECHA CON ESTA FUNCION
        $telefonR          =  $persona ->TelefonoResidencial;             // TELEFONO RESIDENCIAL
        $telefonoc         =  $persona ->TelefonoCelular;  
        $useruid           =  $persona ->useruid;  
        $foto1             =  '../../nomina/paginas/'.$foto;
}
        //$image_file = "/var/www/html/migracion_rrhh/nomina/imagenes/migracion.png";

//imágenes primera línea
//$MiPDF->Image($image_file, $x=150, $y=8, $w=35,   $h=35, $type='', $link='', $align='C', $resize=false, $dpi=300, $palign='', $ismask=false, $imgmask=false, $border=0, $fitbox=false, $hidden=false, $fitonpage=false);
//CABECERA PRIMER RENGLON
$MiPDF->SetFont('times', 'B', 11);
$altura=9;


$MiPDF->Multicell(185,$altura,'DATOS PERSONALES',0,$alineacion='L',$fondo=true,$salto_de_linea=1,$x='15',$y='45',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);

//PRIMER RENGLON (DATOS)

$MiPDF->SetFont('times', '', 9);

$MiPDF->Multicell(40,$altura,'Primer Nombre:',0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(65,$altura,$primer_n,0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='50',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(45,$altura,'Segundo Nombre:',0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='100',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(65,$altura,$nom2,0,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='135',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(40,$altura,'Primer Apellido:',0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(50,$altura,$apellidos,0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='50',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(45,$altura,'Segundo Apellido:',0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='100',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(65,$altura,$second_a,0,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='135',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(40,$altura,'Genero:',0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(160,$altura,$sexo,0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='50',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(45,$altura,'Fecha de Nacimiento:',0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='100',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(65,$altura,$fecha_nac,0,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='135',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(40,$altura,'Cedula:',0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(50,$altura,$cedula,0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='50',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(40,$altura,'Email:',0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='100',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(160,$altura,$email,0,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='135',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(40,$altura,'Teléfono Residencial:',0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(50,$altura,$telefonR,0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='50',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(45,$altura,'Teléfono Celular:',0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='100',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(65,$altura,$telefonoc,0,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='135',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

// INICIALIZAMOS TODOS

$estado        = NULL;
$cod_cat       = NULL;
$fing_n        = NULL;
$finp_n        = NULL;
$aprueba       = NULL;
$fecha_decreto = NULL;
$pext          = NULL;
$categoria     = NULL;
// DATOS DEL SEGUNDO RENGLON
if($res->num_rows>=1)
{
        $estp     =  $persona ->estado;
        $estado   =  strtoupper($estp);
        $cod_cat  =  $persona ->codcat;
        $fing     =  $persona ->fecing;
        $fing_n   =  fechas($fing,1);
        $finp     =  $persona ->fecha_permanencia;
        $finp_n   =  fechas($finp,1);
        $uid_a    =  $persona ->uid_user_aprueba;
        $externo  =  $persona ->personal_externo;

        if($uid_a == NULL)
        {
            $aprueba = "NO";
        }
        else
        {
            $aprueba = "SI";
        }
        $fecha_decreto =  fechas($persona ->fecha_resolucion,1);

        if($fecha_decreto==NULL)
        {
            $fecha_decreto="SIN FECHA";
        }

        $pext = "NO";
        if(($externo==NULL)or($externo==0))    
        {  
            $pext = "NO"; 
        }
        else                 
        {  
            if($externo==1)
            {
                $pext="SI";
            } 
            if($externo==2)
            {
                $pext="NO";
            }   
        }
        $categoria = "SIN ESTADO";
        $con_cat   = "SELECT descrip FROM nomcategorias WHERE codorg='$cod_cat'";
        $catsql    = $db->query($con_cat);
        if($catsql ->num_rows>0) 
        {
            $category     = $catsql->fetch_object();
            $categoria    = $category ->descrip;
        }
}
//CABECERA SEGUNDO RENGLON

$MiPDF->SetFont('times', 'B', 11);

$MiPDF->Image($foto1, $x=160, $y=8, $w=35,   $h=35, $type='', $link='', $align='C', $resize=false, $dpi=300, $palign='', $ismask=false, $imgmask=false, $border=1, $fitbox=false, $hidden=false, $fitonpage=false);
$MiPDF->Multicell(185,$altura,'IDENTIFICACIÓN DEL PUESTO',0,$alineacion='L',$fondo=true,$salto_de_linea=1,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);

//SEGUNDO RENGLON (DATOS)

$MiPDF->SetFont('times', '', 9);

$MiPDF->Multicell(40,$altura,'Fecha de Ingreso:',0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(45,$altura,$fing_n,0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='50',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(45,$altura,'Fecha de Permanencia:',0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='100',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(65,$altura,$finp_n,0,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='135',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(40,$altura,'Estado:',0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=15,$alineacion_vertical='T', $fitcell=false);

$MiPDF->SetFont('times', '', 10);

$MiPDF->Multicell(45,$altura,$categoria,0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='50',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=15,$alineacion_vertical='T', $fitcell=true);

$MiPDF->SetFont('times', '', 10);

$MiPDF->Multicell(45,$altura,'No de Resolución:',0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='100',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=15,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(55,$altura,$num_resolucion,0,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='135',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=15,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(45,$altura,'Fecha de Resolución:',0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='100',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=15,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(55,$altura,$fecha_decreto,0,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='135',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=15,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(40,$altura,'¿Está activo?',0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(45,$altura,'*',0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='55',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(45,$altura,'Condición:',0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='100',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(65,$altura,$estado,0,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='135',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(40,$altura,'¿Aprueba solicitudes?',0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(45,$altura,$aprueba,0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='55',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='T', $fitcell=false);


$MiPDF->Multicell(45,$altura,'¿Es personal externo?',0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='100',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(65,$altura,$pext,0,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='135',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);
// INICIALIZAR DATOS

$estado_civil      =  NULL;
$clave_ir          =  NULL;
$nacionalidad      =  NULL;
$sangre            =  NULL;
$seguro            =  NULL;
$n_edu             =  NULL;
$n_prof            =  NULL;
$hijos             =  NULL;
$cemerg            =  NULL;
$Tcemerg           =  NULL;
$Tdisc             =  NULL;
$FTdisc            =  NULL;
$direccion         =  NULL;
$enfer             =  NULL;
$comentario        =  NULL;

// DATOS DEL TERCER RENGLON
if($res->num_rows>=1)
{
    $est_c      =  $persona ->estado_civil;
    $estado_civil   =  strtoupper($est_c);
    $clave_ir   =  $persona ->clave_ir;
    $idnac      =  $persona ->nacionalidad;
    $t_s        =  $persona ->IdTipoSangre;
    $seguro     =  $persona ->seguro_social;
    $id_edu     =  $persona ->IdNivelEducativo;
    $codpro     =  $persona ->codpro;
    $hijos      =  $persona ->hijos;
    $cemerg     =  $persona ->ContactoEmergencia;
    $Tcemerg    =  $persona ->TelefonoContactoEmergencia;
    $disc       =  $persona ->tiene_discapacidad;
    $fdisc      =  $persona ->tiene_familiar_disca;
    $direccion  =  $persona ->direccion;
    $enfer      =  $persona ->enfermedadesYAlergias;
    $comentario =  $persona ->comentario;
    if(($idnac>=1)AND($idnac<=3))
    {
        if($idnac == 1)
        {
            $nacionalidad = "PANAMEÑO";
        }
        if($idnac == 2)
        {
            $nacionalidad = "EXTRANJERO";
        }
        if($idnac == 3)
        {
            $nacionalidad = "NACIONALIZADO";
        }
    }
    else
    {
           $nacionalidad = "NO DISP";
    }
    if($t_s == NULL)
    {
        $sangre = "NO DISPONIBLE";
    }
    else
    {
        $sangre = $t_s;
    }
    if($id_edu==0)
    {
        $n_edu="SIN NIVEL";
    }
    else
    {
        $sql2    = "SELECT Descripcion FROM niveleducativo WHERE IdNivelEducativo='$id_edu'";
        $res2    = $db->query($sql2);
        $nivel   = $res2->fetch_object();
        $n_edu   = $nivel ->Descripcion;
    }
    if($codpro==NULL OR $codpro =='0')
    {
        $n_prof="SIN PROFESION";
    }
    else
    {
        $db     = new Database($_SESSION['bd']);
        $sql3   = "SELECT descrip FROM nomprofesiones WHERE codorg='$codpro'";
        $res3   = $db->query($sql3);
        $prof   = $res3->fetch_object();
        $n_prof = $prof ->descrip;
    }
    if($hijos==NULL)     {  $hijos  =  0;                                                }
    if($hijos==NULL)     {  $hijos  =  0;                                                }
    if($cemerg==NULL)    {  $cemerg =  "NO DISP";                                        }
    if($Tcemerg==NULL)   {  $Tcemerg = "NO DISP";                                        }
    $Tdisc = "NO";
    if(($disc==NULL)or($disc==0))      {  $Tdisc = "NO";                                 }
    else                 {  if($disc==1){$Tdisc="SI";} if($disc==2){$Tdisc="NO";}        }
    $FTdisc = "NO";
    if(($fdisc==NULL)or($fdisc==0))    {  $FTdisc = "NO";                                }
    else                 {  if($fdisc==1){$FTdisc="SI";} if($fdisc==2){$FTdisc="NO";}    }
    if(($enfer==NULL)or($enfer=='')) {  $enfer = "SIN INFORMACION DISPONIBLE";           }
    if(($comentario==NULL)or($comentario=='')) {  $comentario = "SIN COMENTARIOS";       }
//CABECERA TERCER RENGLON
}
$MiPDF->SetFont('times', 'B', 11);

$MiPDF->Multicell(185,$altura,'OTROS DATOS',0,$alineacion='L',$fondo=true,$salto_de_linea=1,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);

//TERCER RENGLON (DATOS)

$MiPDF->SetFont('times', '', 9);

$MiPDF->Multicell(40,$altura,'Estado Civil:',0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(45,$altura,$estado_civil,0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='50',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(45,$altura,'Clave ISR:',0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='100',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(65,$altura,$clave_ir,0,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='135',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(40,$altura,'Nacionalidad:',0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(45,$altura,$nacionalidad,0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='50',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(45,$altura,'Tipo de Sangre:',0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='100',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(65,$altura,$sangre,0,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='135',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(40,$altura,'Seguro Social:',0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(45,$altura,$seguro,0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='50',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(45,$altura,'Nivel Educativo:',0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='100',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(65,$altura,$n_edu,0,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='135',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(40,$altura,'Profesión: ',0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(45,$altura,$n_prof,0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='50',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(45,$altura,'Hijos:',0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='100',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(65,$altura,$hijos,0,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='135',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(40,$altura,'Contacto Emergencia: ',0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(45,$altura,$cemerg,0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='50',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(45,$altura,'Teléfono del Contacto:',0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='100',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(65,$altura,$Tcemerg,0,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='135',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(40,$altura,'¿Tiene discapacidad?',0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(45,$altura,$Tdisc,0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='50',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(45,$altura,'¿Tiene familiar con discapacidad?',0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='100',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(65,$altura,$FTdisc,0,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='135',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(40,$altura,'Dirección:',0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(160,$altura,$direccion,0,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='50',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(40,15,'Enfermedades y Alergías:',0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=15,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(150,15,$enfer,0,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='50',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=15,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(40,$altura,'Comentario:',0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(160,$altura,$comentario,0,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='50',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);
$MiPDF->AddPage();

//INICIALIZAR DATOS

$nomposicion_id           = NULL;
$cuenta_contable          = NULL;
$tipnom                   = NULL;
$tipemp                   = NULL;
$departamento             = NULL;
$cargo                    = NULL;
$funcion                  = NULL;
$sueldo                   = NULL;
$gastos_rep               = NULL;
$otros                    = NULL;
$cm_gasto_responsabilidad = NULL;
$cm_incentivo_titulo      = NULL;
$cm_ascenso               = NULL;
$fecha_ing                = NULL;
//CABECERA CUARTO RENGLON
if($res->num_rows>=1)
{
    $MiPDF->SetFont('times', 'B', 10);

    $MiPDF->Multicell(185,$altura,'CARGO ESTRUCTURA',0,$alineacion='L',$fondo=true,$salto_de_linea=1,$x='15',$y='45',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);

    //DATOS CUARTO RENGLON

    $nomposicion_id    =     $persona ->nomposicion_id;
    $ctac              =     $persona ->ctacontab;
    if(($ctac==NULL)or($ctac=='')) {  $cuenta_contable = "NO DISPONIBLE"; }else{     $cuenta_contable=$ctac;      }
    $tipnom            = $persona ->tipnom;
    //$tipemp            = $persona ->tipo_funcionario;
    $sql_tip           = "SELECT Descripcion as tip_funcionario from tipoempleado where IdTipoEmpleado = '{$persona ->tipo_funcionario}'";
    $res_tip           = $db->query($sql_tip);
    $tipemp            = $res_tip ->fetch_array();

    //DEPARTAMENTO DEL EMPLEADO
    $departamento      =     "SIN DEPARTAMENTO";                                // INICIALIZAR DEPARTAMENTO
    $Iddepartamento    =     $persona ->IdDepartamento;                         // TRAERMOS EL ID DEL DEPARTAMENTO DE LA TABLA nompersonal BD
    $sql4          = "SELECT Descripcion FROM departamento WHERE IdDepartamento='$Iddepartamento'";// CONSULTA EN LA TABLA departamento BD
    
    $res4          = $db->query($sql4);                                         // CONSULTAMOS EN departamento
    if($res4->num_rows>0)                                                       // SI HAY REGISTROS CAMBIAMOS EL DEPARTAMENTO
    {
        $dept          = $res4->fetch_object();
        $departamento  = $dept ->Descripcion;
    }

    //CARGO DEL EMPLEADO
    $cargo_estructura             =     "SIN CARGO";                                        // INICIALIZAR CARGO
    $cargo_id          =     $persona ->codcargo;                                // TRAERMOS EL ID DEL CARGO DE LA TABLA nompersonal BD
    $sql5          = "SELECT des_car FROM nomcargos WHERE cod_car='$cargo_id'";  // CONSULTA DEL CARGO EN LA TABLA nomcargos
    $res5          = $db->query($sql5);                                          // CONSULTAMOS EN nomcargos
    if($res5->num_rows>0)                                                        // SI HAY REGISTROS CAMBIAMOS EL CARGO
    {
       $carg          = $res5->fetch_object();
       $cargo_estructura        = $carg ->des_car; 
    }

    //FUNCION DEL EMPLEADO
    $funcion           = "SIN FUNCION";                                          // INICIALIZAR FUNCION
    $idfuncion         = $persona ->nomfuncion_id;                               // TRAERMOS EL ID DE LA FUNCION DE LA TABLA nompersonal BD
    $sql6          = "SELECT descripcion_funcion FROM nomfuncion WHERE nomfuncion_id='$idfuncion'"; //CONSULTA DEL CARGO EN LA TABLA nomfuncion
    $res6          = $db->query($sql6);                                          // CONSULTAMOS EN nomfuncion
    if($res6->num_rows>0)                                                        // SI HAY REGISTROS CAMBIAMOS LA FUNCION
    {
        $ftio          = $res6->fetch_object();
        $funcion       = $ftio->descripcion_funcion;
    }

    $sueldo                   = $persona ->suesal;
    $gastos_rep               = $persona ->gastos_representacion;
    $cm_gasto_responsabilidad = $persona ->cm_gasto_responsabilidad;
    $cm_incentivo_titulo      = $persona ->cm_incentivo_titulo;
    $cm_ascenso               = $persona ->cm_ascenso;
    $otros                    = $persona ->otros;
    $fecing                   = $persona ->fecing;
    $fecha_ing                = fechas($fecing,1);
}
//CUARTO RENGLON (DATOS)

$MiPDF->SetFont('times', '', 9);

$MiPDF->Multicell(40,$altura,'Posición:',0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(160,$altura,$nomposicion_id,0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='55',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(40,$altura,'Fecha de Inicio:',0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='100',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(160,$altura,$fecha_ing,0,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='145',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(40,$altura,'Cuenta Contable:',0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(45,$altura,$cuenta_contable,0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='55',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(45,$altura,'Planilla:',0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='100',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(65,$altura,$tipnom,0,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='145',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(40,$altura,'Departamento:',0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(160,$altura,$departamento,0,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='55',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(40,$altura,'Tipo de Empleado:',0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(45,$altura,$tipemp['tip_funcionario'],0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='55',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(45,$altura,'Cargo:',0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='100',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(65,$altura,$cargo_id,0,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='145',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(40,$altura,'Función:',0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(160,$altura,$funcion,0,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='55',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(40,$altura,'Salario:',0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(45,$altura,$sueldo,0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='55',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(45,$altura,'Gastos de Representación:',0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='100',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(65,$altura,$gastos_rep,0,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='145',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(40,$altura,'Sobresueldo:',0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(45,$altura,$otros,0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='55',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(45,$altura,'Gasto Responsabilidad:',0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='100',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(65,$altura,$gastos_rep,0,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='145',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(40,$altura,'Incentivo Titulo:',0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(45,$altura,$cm_incentivo_titulo,0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='55',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(45,$altura,'Ascenso:',0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='100',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(65,$altura,$cm_ascenso,0,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='145',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='T', $fitcell=false);

$b = 0; //BORDE

//CABECERA QUINTO RENGLON

$sqlcargos          = "SELECT A.*, B.des_car, C.descripcion_funcion, D.Descripcion as departamento, E.Descripcion as tipo_funcionario  "
                     . "FROM posicionempleadohist as A"
                    . " LEFT JOIN nomcargos AS B ON A.IdTituloInstitucional = B.cod_car "
                    . " LEFT JOIN nomfuncion AS C ON A.IdFuncion = C.nomfuncion_id "
                    . " LEFT JOIN departamento AS D ON A.IdDepartamento = D.IdDepartamento "
                    . " LEFT JOIN tipoempleado AS E ON A.IdTipoEmpleado = E.IdTipoEmpleado "
                    . " WHERE A.IdEmpleado='{$ficha}'"
                    . " ORDER BY A.FechaInicio";
//echo $sqlcargos;
//exit;
$reshistorial          = $db->query($sqlcargos);

$MiPDF->SetFont('times', 'B', 11);

$MiPDF->Multicell(185,$altura,'HISTORIAL DEL CARGO SEGÚN ESTRUCTURA',0,$alineacion='L',$fondo=true,$salto_de_linea=1,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);

//QUINTO RENGLON (DATOS)

$MiPDF->SetFont('times', 'B', 9);

$MiPDF->Multicell(20,$altura,'Cuenta Contable',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
$MiPDF->Multicell(15,$altura,'Posicion',$b,$alineacion='C',$fondo=false,$salto_de_linea=0,$x='48',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
$MiPDF->Multicell(15,$altura,'Planilla',$b,$alineacion='C',$fondo=false,$salto_de_linea=0,$x='60',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
$MiPDF->Multicell(35,$altura,'Departamento',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='75',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
$MiPDF->Multicell(35,$altura,'Tipo Funcionario',$b,$alineacion='C',$fondo=false,$salto_de_linea=0,$x='100',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
$MiPDF->Multicell(30,$altura,'Cargo',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='138',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
$MiPDF->Multicell(15,$altura,'Salario',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='168',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
$MiPDF->Multicell(20,$altura,'Fecha Inicio',$b,$alineacion='C',$fondo=false,$salto_de_linea=1,$x='180',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
$MiPDF->SetFont('times', '', 9);

if($reshistorial->num_rows>0) 
{
     while ($historial = $reshistorial->fetch_object())
    {
        $MiPDF->Multicell(40,$altura,$historial->CuentaContable,$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(15,$altura,$historial->Posicion,$b,$alineacion='C',$fondo=false,$salto_de_linea=0,$x='48',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(15,$altura,$historial->Planilla,$b,$alineacion='C',$fondo=false,$salto_de_linea=0,$x='60',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(35,$altura,$historial->departamento,$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='75',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(35,$altura,$historial->tipo_funcionario,$b,$alineacion='C',$fondo=false,$salto_de_linea=0,$x='100',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(30,$altura,$historial->des_car,$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='138',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(15,$altura,$historial->Salario,$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='168',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(20,$altura,fechas($historial->FechaInicio,1),$b,$alineacion='C',$fondo=false,$salto_de_linea=1,$x='180',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
    if( $MiPDF->GetY()> 230)
    {
        $MiPDF->AddPage();
    }
    }
}
else
{
    $MiPDF->Multicell(20,$altura,'Sin Datos',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
    $MiPDF->Multicell(15,$altura,'Sin Datos',$b,$alineacion='C',$fondo=false,$salto_de_linea=0,$x='55',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
    $MiPDF->Multicell(15,$altura,'Sin Datos',$b,$alineacion='C',$fondo=false,$salto_de_linea=0,$x='70',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
    $MiPDF->Multicell(35,$altura,'Sin Datos',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='85',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
    $MiPDF->Multicell(20,$altura,'Sin Datos',$b,$alineacion='C',$fondo=false,$salto_de_linea=0,$x='125',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
    $MiPDF->Multicell(30,$altura,'Sin Datos',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='145',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
    $MiPDF->Multicell(15,$altura,'Sin Datos',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='165',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
    $MiPDF->Multicell(20,$altura,'Sin Datos',$b,$alineacion='C',$fondo=false,$salto_de_linea=1,$x='185',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
    if( $MiPDF->GetY()> 230)
    {
        $MiPDF->AddPage();
    }
}

//CABECERA SEXTO RENGLON
$sql7          = "SELECT A.*, D.Descripcion as departamento  "
                     . "FROM empleado_cargo as A"
                    . " LEFT JOIN departamento AS D ON A.IdDepartamento = D.IdDepartamento "
                    . " WHERE A.IdEmpleado='{$ficha}'";
$res7          = $db->query($sql7);
//echo $sql7;
//exit;
$MiPDF->SetFont('times', 'B', 11);

$MiPDF->Multicell(185,$altura,'MOVIMIENTOS DEL FUNCIONARIO',0,$alineacion='L',$fondo=true,$salto_de_linea=1,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);

//SEXTO RENGLON (DATOS)

$MiPDF->SetFont('times', 'B', 9);

$MiPDF->Multicell(80,$altura,'Departamento',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
$MiPDF->Multicell(30,$altura,'Fecha Inicio',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='95',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
$MiPDF->Multicell(30,$altura,'Fecha Fin',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='125',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
$MiPDF->Multicell(45,$altura,'Tipo de Movimiento',$b,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='155',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
$MiPDF->SetFont('times', '', 9);
if($res7->num_rows>0) 
{
    while ($movfun = $res7->fetch_object())
    {
        $departamento   =  $movfun->Descripcion;
        $mfechai        =  $movfun->FechaInicio;
        $mfechaf        =  $movfun->FechaFinal;
        $mfechaini      =  fechas($mfechai,1);
        $mfechafin      =  fechas($mfechaf,1);
        $TipoMovimiento =  $movfun->TipoMovimiento;
        if($TipoMovimiento=='N'){ $n_tipo_mov="NOMBRAMIENTO"; }
        if($TipoMovimiento=='A'){ $n_tipo_mov="APOYO TEMPORAL"; }
        if($TipoMovimiento=='R'){ $n_tipo_mov="ROTACION"; }
        if($TipoMovimiento=='T'){ $n_tipo_mov="TRASLADO"; }
        $MiPDF->Multicell(80,$altura,$departamento,$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(30,$altura,$mfechaini,$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='95',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(30,$altura,$mfechafin,$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='125',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(45,$altura,$n_tipo_mov,$b,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='155',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
        if( $MiPDF->GetY()> 230)
        {
            $MiPDF->AddPage();
        }
    }
}
else
{
    $MiPDF->Multicell(80,$altura,'Sin Datos',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
    $MiPDF->Multicell(30,$altura,'Sin Datos',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='95',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
    $MiPDF->Multicell(30,$altura,'Sin Datos',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='125',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
    $MiPDF->Multicell(45,$altura,'Sin Datos',$b,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='155',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
    if( $MiPDF->GetY()> 230)
    {
        $MiPDF->AddPage();
    }
}


//CABECERA SEPTIMO RENGLON RENGLON


$MiPDF->SetFont('times', 'B', 11);
if( $MiPDF->GetY()> 215)
{
    $MiPDF->AddPage();
    $salto1 = '45';
}
else
{
    $salto1 = '';

}
$MiPDF->Multicell(185,$altura,'TIEMPOS',0,$alineacion='L',$fondo=true,$salto_de_linea=1,$x='15',$y=$salto1,$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);

//SEPTIMO RENGLON (DATOS)
$sql9 = "SELECT b.descripcion as justificacion, b.idtipo as idtipo, SUM(a.tiempo) as tiempo, SUM(a.horas) as horas, "
        . "SUM(a.minutos) as minutos , SUM(a.dias) as dias FROM dias_incapacidad as a, tipo_justificacion as b "
        . "WHERE a.tipo_justificacion=b.idtipo AND a.cedula='{$cedula}' GROUP BY a.tipo_justificacion";
$res9          = $db->query($sql9);

$MiPDF->SetFont('times', 'B', 10);

$MiPDF->Multicell(80,$altura,'Tipo',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
$MiPDF->Multicell(30,$altura,'Tiempo',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='95',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
$MiPDF->Multicell(25,$altura,'Días',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='125',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
$MiPDF->Multicell(25,$altura,'Horas',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='150',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
$MiPDF->Multicell(25,$altura,'Minutos',$b,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='175',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);

$MiPDF->SetFont('times', '', 9);

if($res9->num_rows>0) 
{
    while ($tiempos = $res9->fetch_object())
    {
//            $tipo_tiempo        =  $tiempos->tipo;
//
//            $dias               =  $tiempos->dias;
//            if($dias==NULL)
//            {
//                $dias           =  0;
//            }
//
//            $minutos            =  $tiempos->minutos;
//            if($minutos==NULL)
//            {
//                $minutos        =  0;
//            }
//
//            $horas              =  $tiempos->horas;
//            if($horas==NULL)
//            {
//                $horas        =  0;
//            }
//
//            $minsporc           =  ($minutos*100)/60;
//            if($minsporc<10)       {$minsporc="0".$minsporc;             }
//            $base               =  ($dias*8)+$horas;
//            $Total              =  $base.".".$minsporc;
            
            $dia   = intval(abs($tiempos->tiempo)/8);
            $hora  = intval(abs($tiempos->tiempo) - ($dia*8));
            $MIN   = intval(round((abs($tiempos->tiempo) - ($dia*8) - $hora),2) * 60);
             $tipo=$tiempos->idtipo;
            if ($tiempos->tiempo >= 0)
            {
                    $dias=$dia;
                    $horas=$hora;
                    $minutos=$MIN;	
            }
            else
            {
                    $dias=-1*$dia;
                    $horas=-1*$hora;
                    $minutos=-1*$MIN;	
            }    

            if($tiempos->justificacion=="VACACIONES")
            {
                    $dias=$tiempos->tiempo;
                    $horas=$minutos=0;
            }
//            if($tipo_tiempo==12){ $n_tipo_tiempo="TIEMPO COMPENSATORIO"; }
//            if($tipo_tiempo==11){ $n_tipo_tiempo="VACACIONES"; }
                        
            if($tiempos->tiempo==NULL){
                    $tiempo=0;
            }else{
                    $tiempo=number_format($tiempos->tiempo, 2, ",", ".");
            }
            if($tiempos->justificacion=="VACACIONES"){
                   $tiempo.= " (Dias)";
            }else{
                    $tiempo.= " (Horas)";
            } 
            $MiPDF->Multicell(80,$altura,$tiempos->justificacion,$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
            $MiPDF->Multicell(30,$altura,$tiempo,$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='95',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
            $MiPDF->Multicell(25,$altura,$dias,$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='125',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
            $MiPDF->Multicell(25,$altura,$horas,$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='150',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
            $MiPDF->Multicell(25,$altura,$minutos,$b,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='175',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
        if( $MiPDF->GetY()> 260)
        {
            $MiPDF->AddPage();
            $salto3 = '45';
        }
        else
        {
            $salto3 = '';
        }
    }
}
else
{
    $MiPDF->Multicell(80,$altura,'Sin Datos',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
    $MiPDF->Multicell(30,$altura,'Sin Datos',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='95',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
    $MiPDF->Multicell(25,$altura,'Sin Datos',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='125',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
    $MiPDF->Multicell(25,$altura,'Sin Datos',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='150',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
    $MiPDF->Multicell(25,$altura,'Sin Datos',$b,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='175',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
    if( $MiPDF->GetY()> 250)
    {
        $MiPDF->AddPage();
        $salto3 = '45';
    }
    else
    {
        $salto3 = '';

    }    
}

//CABECERA OCTAVO RENGLON RENGLON (CAPACITACIONES)

$sql10         = "SELECT *  "
                     . "FROM empleado_capacitacion"
                    . " WHERE IdEmpleado='{$ficha}'";
$res10          = $db->query($sql10);

$MiPDF->SetFont('times', 'B', 11);

$MiPDF->Multicell(185,$altura,'CAPACITACIONES',0,$alineacion='L',$fondo=true,$salto_de_linea=1,$x='15',$y=$salto3,$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);

//OCTAVO RENGLON (DATOS) (CAPACITACIONES)

$MiPDF->SetFont('times', 'B', 11);

$MiPDF->Multicell(25,$altura,'Fecha Inicio',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
$MiPDF->Multicell(25,$altura,'Fecha Fin',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='40',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
$MiPDF->Multicell(135,$altura,'Comentario',$b,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='65',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);

$MiPDF->SetFont('times', '', 9);
if($res10->num_rows>0) 
{
    while ($capa = $res10->fetch_object())
    {
        $inica        =  $capa->fecha_ini;
        $finca        =  $capa->fecha_fin;
        $inicio_capa  = fechas($inica,1);
        $fin_capa     = fechas($finca,1);
        $comentario   = $capa->comentario;
        $MiPDF->Multicell(25,$altura,$inicio_capa,$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(25,$altura,$fin_capa,$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='40',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(135,$altura,$comentario,$b,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='65',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
        if( $MiPDF->GetY()> 265)
        {
            $MiPDF->AddPage();
            $salto4 = '45';
        }
        else
        {
            $salto4 = '';

        }
    }
}
else
{
    $MiPDF->Multicell(25,$altura,'Sin Datos',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
    $MiPDF->Multicell(25,$altura,'Sin Datos',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='40',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
    $MiPDF->Multicell(135,$altura,'Sin Datos',$b,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='65',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);  
    if( $MiPDF->GetY()> 265)
    {
        $MiPDF->AddPage();
        $salto4 = '45';
    }
    else
    {
        $salto4 = '';

    }
}

//CABECERA DECIMO RENGLON RENGLON
$sql12         = "SELECT A.*, B.Descripcion "
                     . "FROM empleado_estudios as A"
                    . " LEFT JOIN niveleducativo AS  B ON A.IdNivelEducativo = B.IdNivelEducativo "
                    . " WHERE A.IdEmpleado='{$ficha}'"
                    . " ORDER BY A.fecha_ini";
$res12          = $db->query($sql12);

$MiPDF->SetFont('times', 'B', 11);

$MiPDF->Multicell(185,$altura,'ESTUDIOS',0,$alineacion='L',$fondo=true,$salto_de_linea=1,$x='15',$y=$salto4,$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);

//DECIMO RENGLON (DATOS)

$MiPDF->SetFont('times', 'B', 9);

$MiPDF->Multicell(30,$altura,'Nivel Educativo',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
$MiPDF->Multicell(45,$altura,'Institución',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='45',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
$MiPDF->Multicell(25,$altura,'Fecha Inicio',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='100',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
$MiPDF->Multicell(25,$altura,'Fecha Fin',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='118',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
$MiPDF->Multicell(65,$altura,'Comentario',$b,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='135',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);

$MiPDF->SetFont('times', '', 9);
if($res12->num_rows>0) 
{
    while ($edu = $res12->fetch_object())
    {
        $iniedu        = $edu->fecha_ini;
        $finedu        = $edu->fecha_fin;
        $inicio_edu    = fechas($iniedu,1);
        $fin_edu       = fechas($finedu,1);
        $niveledu      = $edu->Descripcion;
        $intitucion    = $edu->Institucion;
        $comentario    = $edu->comentario;
        $MiPDF->Multicell(30,$altura,$niveledu,$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(45,$altura,$intitucion,$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='45',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(25,$altura,$inicio_edu,$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='100',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(25,$altura,$fin_edu,$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='118',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(65,$altura,$comentario,$b,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='135',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false); 
        if( $MiPDF->GetY()> 265)
        {
            $MiPDF->AddPage();
            $salto4 = '45';
        }
        else
        {
            $salto4 = '';

        }

    }
}
else
{
    $MiPDF->Multicell(25,$altura,'Sin Datos',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
    $MiPDF->Multicell(45,$altura,'Sin Datos',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='40',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
    $MiPDF->Multicell(25,$altura,'Sin Datos',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='85',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
    $MiPDF->Multicell(25,$altura,'Sin Datos',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='110',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
    $MiPDF->Multicell(65,$altura,'Sin Datos',$b,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='135',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false); 
    if( $MiPDF->GetY()> 265)
    {
        $MiPDF->AddPage();
        $salto4 = '45';
    }
    else
    {
        $salto4 = '';

    }
}

//CABECERA NOVENO RENGLON RENGLON  (EVALUACIONES)
$sql11         = "SELECT A.*, B.apenom "
                     . "FROM empleado_evaluacion as A"
                    . " LEFT JOIN nompersonal AS  B ON A.persona_evalua = B.personal_id "
                    . " WHERE A.IdEmpleado='{$ficha}'"
                    . " ORDER BY A.fini_periodo";
$res11          = $db->query($sql11);

$MiPDF->SetFont('times', 'B', 11);

$MiPDF->Multicell(185,$altura,'EVALUACIONES',0,$alineacion='L',$fondo=true,$salto_de_linea=1,$x='15',$y=$salto4,$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);

//NOVENO RENGLON (DATOS) (EVALUACIONES)

$MiPDF->SetFont('times', 'B', 9);

$MiPDF->Multicell(25,$altura,'Fecha Ini. Periodo',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
$MiPDF->Multicell(25,$altura,'Fecha Fin. Periodo',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='40',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
$MiPDF->Multicell(25,$altura,'Fecha Evaluación',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='65',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
$MiPDF->Multicell(45,$altura,'Supervisor Evaluador',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='90',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
$MiPDF->Multicell(20,$altura,'Puntaje',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='135',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
$MiPDF->Multicell(45,$altura,'Comentario',$b,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='155',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);

$MiPDF->SetFont('times', '', 9);
if($res11->num_rows>0) 
{
    while ($eva = $res11->fetch_object())
    {
        $inieva        = $eva->fini_periodo;
        $fineva        = $eva->ffin_periodo;
        $inicio_eva    = fechas($inieva,1);
        $fin_eva       = fechas($fineva,1);
        //$evaluador     = $eva->nombre_especialista;*/
        $feval         = $eva->f_eval;
        $fecha_eval    = fechas($feval,1);
        $puntaje       = $eva->puntaje;
        $comentario    = $eva->comentarios;
        $MiPDF->Multicell(25,$altura,$inicio_eva,$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(25,$altura,$fin_eva,$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='40',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(25,$altura,$fecha_eval,$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='65',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(45,$altura,$eva->apenom,$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='90',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(20,$altura,$eva->puntaje,$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='135',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(45,$altura,$comentario,$b,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='155',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false); 
        if( $MiPDF->GetY()> 260)
        {
            $MiPDF->AddPage();
            $salto5 = '45';
        }
        else
        {
            $salto5 = '';

        }
    }
}
else
{
    $MiPDF->Multicell(25,$altura,'Sin Datos',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
    $MiPDF->Multicell(25,$altura,'Sin Datos',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='40',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
    $MiPDF->Multicell(25,$altura,'Sin Datos',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='65',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
    $MiPDF->Multicell(45,$altura,'Sin Datos',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='90',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
    $MiPDF->Multicell(20,$altura,'Sin Datos',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='135',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
    $MiPDF->Multicell(45,$altura,'Sin Datos',$b,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='155',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false); 
    if( $MiPDF->GetY()> 260)
    {
        $MiPDF->AddPage();
        $salto5 = '45';
    }
    else
    {
        $salto5 = '';

    }
}

//CABECERA ONCEAVO RENGLON RENGLON

$MiPDF->SetFont('times', 'B', 11);

$MiPDF->Multicell(185,$altura,'EXPERIENCIA LABORAL',0,$alineacion='L',$fondo=true,$salto_de_linea=1,$x='15',$y=$salto5,$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);

$sql13        = "SELECT A.* "
                     . "FROM empleado_exp_lab as A"
                    . " WHERE A.IdEmpleado='{$ficha}'"
                    . " ORDER BY A.fecha_ini";
$res13          = $db->query($sql13);

//ONCEAVO RENGLON (DATOS)

$MiPDF->SetFont('times', 'B', 9);

$MiPDF->Multicell(45,$altura,'Lugar',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
$MiPDF->Multicell(45,$altura,'Cargo',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='60',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
$MiPDF->Multicell(25,$altura,'Fecha Inicio',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='105',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
$MiPDF->Multicell(25,$altura,'Fecha Fin',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='130',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
$MiPDF->Multicell(45,$altura,'Comentario',$b,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='155',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);

$MiPDF->SetFont('times', '', 9);
if($res13->num_rows>0) 
{
    while ($labores = $res13->fetch_object())
    {
        $inilar        = $labores->fecha_ini;
        $finlar        = $labores->fecha_fin;
        $inicio_labor  = fechas($inilar,1);
        $fin_labor     = fechas($finlar,1);
        $lugarant      = $labores->institucion;
        $cargo         = $labores->cargo;
        $comentario    = $labores->comentario;
        $MiPDF->Multicell(45,$altura,$lugarant,$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(45,$altura,$cargo,$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='60',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(25,$altura,$inicio_labor,$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='105',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(25,$altura,$fin_labor,$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='130',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(45,$altura,$comentario,$b,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='155',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false); 
        if( $MiPDF->GetY()> 260)
        {
            $MiPDF->AddPage();
            $salto6 = '45';
        }
        else
        {
            $salto6 = '';

        }
    }
}
else
{
    $MiPDF->Multicell(45,$altura,'Sin Datos',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
    $MiPDF->Multicell(45,$altura,'Sin Datos',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='60',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
    $MiPDF->Multicell(25,$altura,'Sin Datos',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='105',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
    $MiPDF->Multicell(25,$altura,'Sin Datos',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='130',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
    $MiPDF->Multicell(45,$altura,'Sin Datos',$b,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='155',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);  

    if( $MiPDF->GetY()> 260)
    {
        $MiPDF->AddPage();
        $salto6 = '45';
    }
    else
    {
        $salto6 = '';

    } 
}

//CABECERA DOCEAVO RENGLON RENGLON

$MiPDF->SetFont('times', 'B', 11);
$MiPDF->Multicell(185,$altura,'ACCIONES',0,$alineacion='L',$fondo=true,$salto_de_linea=1,$x='15',$y=$salto6,$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);

$MiPDF->SetFont('times', 'B', 10);

$MiPDF->Multicell(185,$altura,'AMONESTACIONES',0,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);

$sql14        = "SELECT A.* "
                     . "FROM amonestaciones as A"
                    . " WHERE A.usr_uid='{$useruid}'"
                    . " ORDER BY A.fecha";
$res14         = $db->query($sql14);

//DOCEAVO RENGLON (DATOS)

$MiPDF->SetFont('times', 'B', 9);

$MiPDF->Multicell(25,$altura,'Fecha',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
$MiPDF->Multicell(25,$altura,'Tipo',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='40',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
$MiPDF->Multicell(20,$altura,'Artículo',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='140',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);

$MiPDF->Multicell(25,$altura,'Numeral No.',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='65',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
$MiPDF->Multicell(50,$altura,'Descripción',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='90',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
$MiPDF->Multicell(40,$altura,'Motivo',$b,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='160',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);

$MiPDF->SetFont('times', '', 9);
if($res14->num_rows>0) 
{
    while ($amon = $res14->fetch_object())
    {
        $numeral             = $amon->numeral_numero;
        $numeral_descripcion = $amon->numeral_descripcion;
        $articulo            = $amon->articulo;
        $fec_a               = $amon->fecha;
        $fec_amonestacion    = fechas($fec_a,1);
        $comentario          = $amon->motivo;
        $tipo_amon           = $amon->tipo;
        $MiPDF->Multicell(25,$altura,$fec_amonestacion,$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(25,$altura,$tipo_amon,$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='40',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(20,$altura,$articulo,$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='140',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
       
        $MiPDF->Multicell(25,$altura,$numeral,$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='65',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(50,$altura,$numeral_descripcion,$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='90',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
         $MiPDF->Multicell(40,$altura,$comentario,$b,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='160',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false); 
        if( $MiPDF->GetY()> 260)
        {
            $MiPDF->AddPage();
            $salto7 = '45';
        }
        else
        {
            $salto7 = '';

        }    
    }
}
else
{
    $MiPDF->Multicell(25,$altura,'Sin Datos',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
    $MiPDF->Multicell(25,$altura,'Sin Datos',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='40',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
    $MiPDF->Multicell(25,$altura,'Sin Datos',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='65',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
    $MiPDF->Multicell(50,$altura,'Sin Datos',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='90',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
    $MiPDF->Multicell(20,$altura,'Sin Datos',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='140',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
    $MiPDF->Multicell(40,$altura,'Sin Datos',$b,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='160',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);  
    if( $MiPDF->GetY()> 260)
    {
        $MiPDF->AddPage();
        $salto7 = '45';
    }
    else
    {
        $salto7 = '';

    } 
}

//CABECERA TRECEAVO RENGLON RENGLON

$MiPDF->SetFont('times', 'B', 10);

$MiPDF->Multicell(185,$altura,'SUSPENSIONES',0,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);

$sql15       = "SELECT A.* "
                     . "FROM suspenciones as A"
                    . " WHERE A.usr_uid='{$useruid}'"
                    . " ORDER BY A.fecha";
$res15         = $db->query($sql15);

//TRECEAVO RENGLON (DATOS)

$MiPDF->SetFont('times','B', 9);

$MiPDF->Multicell(25,$altura,'No. Resolución',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y=$salto7,$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
$MiPDF->Multicell(25,$altura,'F. Resolución',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='38',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
$MiPDF->Multicell(25,$altura,'Desde',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='60',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
$MiPDF->Multicell(25,$altura,'Hasta',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='80',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
$MiPDF->Multicell(15,$altura,'Articulo',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='100',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
$MiPDF->Multicell(15,$altura,'Numeral',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='115',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
$MiPDF->Multicell(20,$altura,'Descripcion',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='130',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);

$MiPDF->Multicell(25,$altura,'Días',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='150',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
$MiPDF->Multicell(60,$altura,'Motivo',$b,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='175',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);

$MiPDF->SetFont('times', '', 9);
if($res15->num_rows>0) 
{
    while ($sus = $res15->fetch_object())
    {
        $numero_resolucion = $sus->numero_resolucion;
        $fec_s             = $sus->fecha_resolucion;
        $fec_suspencion    = fechas($fec_s,1);
        $inisus            = $sus->fecha_desde;
        $finsus            = $sus->fecha_hasta;
        $inicio_sus        = fechas($inisus,1);
        $fin_sus           = fechas($finsus,1);
        $dias              = $sus->dias;
        $numeral_numero    = $sus->numeral_numero;
        $comentario        = $sus->numeral_descripcion;
        $motivo            = $sus->motivo;
        $nro_resolucion    = $sus->nro_resolucion;
        $articulo          = $sus->articulo;

        $MiPDF->Multicell(25,$altura,$numero_resolucion,$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(25,$altura,$fec_suspencion,$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='38',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(25,$altura,$inicio_sus,$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='60',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(25,$altura,$fin_sus,$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='80',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(25,$altura,$dias,$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='100',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(60,$altura,$comentario,$b,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='115',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false); 
        $MiPDF->Multicell(60,$altura,$comentario,$b,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='130',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false); 
        $MiPDF->Multicell(60,$altura,$dias,$b,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='150',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false); 
        $MiPDF->Multicell(60,$altura,$motivo,$b,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='175',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);  
        if( $MiPDF->GetY()> 260)
        {
            $MiPDF->AddPage();
            $salto8 = '45';
        }
        else
        {
            $salto8 = '';

        } 
    }
}
else
{
    $MiPDF->Multicell(25,$altura,'Sin Datos',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
    $MiPDF->Multicell(25,$altura,'Sin Datos',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='38',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
    $MiPDF->Multicell(25,$altura,'Sin Datos',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='60',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
    $MiPDF->Multicell(25,$altura,'Sin Datos',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='80',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
    $MiPDF->Multicell(25,$altura,'Sin Datos',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='100',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
    $MiPDF->Multicell(60,$altura,'Sin Datos',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='115',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false); 
    $MiPDF->Multicell(60,$altura,'Sin Datos',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='130',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false); 
    $MiPDF->Multicell(60,$altura,'Sin Datos',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='150',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false); 
    $MiPDF->Multicell(60,$altura,'Sin Datos',$b,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='175',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);   
    if( $MiPDF->GetY()> 260)
    {
        $MiPDF->AddPage();
        $salto8 = '45';
    }
    else
    {
        $salto8 = '';

    } 
}


//CABECERA CARTOCEAVO RENGLON RENGLON

$MiPDF->SetFont('times', 'B', 10);

$MiPDF->Multicell(185,$altura,'LICENCIAS',0,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);

$sql16         = "SELECT a.*,c.nombre_subtipo FROM expediente as a,nompersonal as b,expediente_subtipo as c WHERE a.cedula=b.cedula AND c.id_expediente_subtipo=a.subtipo AND (a.tipo = '15' OR a.tipo = '16'  OR a.tipo = '17')  AND b.ficha='$ficha'";
$res16         = $db->query($sql16);

//CARTOCEAVO RENGLON (DATOS)

$MiPDF->SetFont('times', 'B', 9);

$MiPDF->Multicell(50,$altura,'Tipo de Licencia',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y=$salto8,$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
$MiPDF->Multicell(25,$altura,'No. Resolución',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='65',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
$MiPDF->Multicell(25,$altura,'F. Resolución',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='90',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
$MiPDF->Multicell(25,$altura,'Desde',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='115',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
$MiPDF->Multicell(25,$altura,'Hasta',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='140',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
$MiPDF->Multicell(35,$altura,'Motivo',$b,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='165',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);

$MiPDF->SetFont('times', '', 9);
if($res16->num_rows>0) 
{
    while ($lic = $res16->fetch_object())
    {
        $tlic                   = $lic->nombre_subtipo;
        $numero_resolucion_lic  = $lic->numero_resolucion;
        $fec_l                  = $lic->fecha;
        $fec_licencia           = fechas($fec_l,1);
        $inilic                 = $lic->fecha_inicio;
        $finlic                 = $lic->fecha_fin;
        $inicio_lic             = fechas($inilic,1);
        $fin_lic                = fechas($finlic,1);

        $MiPDF->Multicell(50,$altura,$tlic,$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(25,$altura,$numero_resolucion_lic,$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='65',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(25,$altura,$fec_licencia,$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='90',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(25,$altura,$inicio_lic,$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='115',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(25,$altura,$fin_lic,$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='140',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(35,$altura,'No Aplica',$b,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='165',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false); 

    }
}
else
{
    $MiPDF->Multicell(50,$altura,'Sin Datos',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
    $MiPDF->Multicell(25,$altura,'Sin Datos',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='65',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
    $MiPDF->Multicell(25,$altura,'Sin Datos',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='90',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
    $MiPDF->Multicell(25,$altura,'Sin Datos',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='115',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
    $MiPDF->Multicell(25,$altura,'Sin Datos',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='140',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
    $MiPDF->Multicell(35,$altura,'Sin Datos',$b,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='165',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false); 

}
ob_end_clean();
$MiPDF->Output('resumen_expediente.pdf','I');
?>