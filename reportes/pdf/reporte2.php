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
$id_estructura     						    = ( isset($_GET['id_estructura']) )   ? $_GET['id_estructura']          : '' ;

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
class MYPDF extends TCPDF
{   
    public function Header() 
    {
        $this->SetFont('times', 'B', 10);
        $this->Multicell(275,5,'',0,$align='L',$fill=0,$ln=1,$x='15',$y='',$reseth=true,$stretch=0,$ishtml=false,$autopadding=true,$maxh=5,$valign='M',$fitcell=false);
        $this->Multicell(90,5,'FECHA: '.date('d-m-Y'),0,$align='L',$fill=0,$ln=0,$x='10',$y='',$reseth=true,$stretch=0,$ishtml=false,$autopadding=true,$maxh=5,$valign='M',$fitcell=false);
        $this->Multicell(100,5,'MINISTERIO DE SALUD',0,$align='C',$fill=0,$ln=0,$x='100',$y='',$reseth=true,$stretch=0,$ishtml=false,$autopadding=true,$maxh=5,$valign='M',$fitcell=false);
        $this->Multicell(90,5,'PAGINA No. '.$this->getAliasNumPage(),0,$align='R',$fill=0,$ln=1,$x='200',$y='',$reseth=true,$stretch=0,$ishtml=false,$autopadding=true,$maxh=5,$valign='M',$fitcell=false);
        $this->Multicell(280,5,'DIRECCION DE RECURSOS HUMANOS',0,$align='C',$fill=0,$ln=1,$x='10',$y='',$reseth=true,$stretch=0,$ishtml=false,$autopadding=true,$maxh=5,$valign='M',$fitcell=false); 
         $this->Multicell(280,5,'DEPARTAMENTOS DE ADMINISTRACION DE RECURSOS HUMANOS',0,$align='C',$fill=0,$ln=1,$x='10',$y='',$reseth=true,$stretch=0,$ishtml=false,$autopadding=true,$maxh=5,$valign='M',$fitcell=false); 
         $this->Multicell(280,5,'MODIFICACIONES A LA ESTRUCTURA DE SUELDO FIJOS',0,$align='C',$fill=0,$ln=1,$x='10',$y='',$reseth=true,$stretch=0,$ishtml=false,$autopadding=true,$maxh=5,$valign='M',$fitcell=false);
         //CABEZA
         $this->SetFont('times', 'B', 8);
         $this->Multicell(15,15,'POS','TB',$align='C',$fill=0,$ln=0,$x='10',$y='',$reseth=true,$stretch=0,$ishtml=false,$autopadding=true,$maxh=15,$valign='M',$fitcell=false);
         $this->Multicell(15,15,'UNIDAD','TB',$align='C',$fill=0,$ln=0,$x='25',$y='',$reseth=true,$stretch=0,$ishtml=false,$autopadding=true,$maxh=15,$valign='M',$fitcell=false);
         $this->Multicell(40,15,'NOMBRE','TB',$align='C',$fill=0,$ln=0,$x='40',$y='',$reseth=true,$stretch=0,$ishtml=false,$autopadding=true,$maxh=15,$valign='M',$fitcell=false);
         $this->Multicell(20,15,'CARGO','TB',$align='C',$fill=0,$ln=0,$x='80',$y='',$reseth=true,$stretch=0,$ishtml=false,$autopadding=true,$maxh=15,$valign='M',$fitcell=false);
         $this->Multicell(60,15,'TITULO','TB',$align='C',$fill=0,$ln=0,$x='100',$y='',$reseth=true,$stretch=0,$ishtml=false,$autopadding=true,$maxh=15,$valign='M',$fitcell=false);
         $this->Multicell(60,15,'                   SITUACION ACTUAL                    SLD1 M1 SLD2 M2 ANUAL','TB',$align='C',$fill=0,$ln=0,$x='160',$y='',$reseth=true,$stretch=0,$ishtml=false,$autopadding=true,$maxh=15,$valign='M',$fitcell=false);
         $this->Multicell(70,15,'                    SITUACION PROPUESTA                        SLD1 M1 SLD2 M2 SLD3 M3 ANUAL DIF.','TB',$align='C',$fill=0,$ln=0,$x='220',$y='',$reseth=true,$stretch=0,$ishtml=false,$autopadding=true,$maxh=15,$valign='M',$fitcell=false);
    }
    public function Footer()
    {

    }
}
# creamos  un nuevo objeto (MiPDF) utilizando la clase TCPDF
$MiPDF=new MYPDF('L','mm','A4');
#desactivamos encabezado y pie de página
# el método SetAuthor nos permite incluir el nombre del autor
$MiPDF->SetAuthor('Aquiles Penott');
# el método SetTitle nos permite incluir un título
$MiPDF->SetTitle('Decreto');
# el método SetDisplayMode nos permite incluir palabras claves
# separadas por espacios y dentro de una misma cadena
$MiPDF->SetDisplayMode('fullpage','two');
# creamos una página en blanco. Incluimos, para esta primera página
# un cambio de orientación respecto a la inicial
$MiPDF->SetMargins(10,55,0, true);

$MiPDF->AddPage('L');

$ruta_img='../../nomina/imagenes/';

$MiPDF->SetFillColor(215, 235, 255); // Color de Fondo
/*
//Consulta DATOS PRINCIPALES
$sql       = "SELECT a.nombres,a.apellidos,a.apellido_materno,a.cedula,a.sexo,a.email,a.fecnac,a.codcat,a.TelefonoResidencial,a.TelefonoCelular,a.estado,a.fecing,a.fecha_permanencia,a.uid_user_aprueba,a.fecha_decreto,a.estado_civil,a.clave_ir,a.nacionalidad,a.IdTipoSangre,a.seguro_social,a.IdNivelEducativo,a.codpro,a.hijos,a.ContactoEmergencia,a.TelefonoContactoEmergencia,a.tiene_discapacidad,a.tiene_familiar_disca,a.direccion,a.enfermedadesYAlergias,a.comentario,a.nomposicion_id,a.ctacontab,a.tipnom,a.Iddepartamento,a.tipemp,a.codcargo,a.nomfuncion_id,a.suesal,a.gastos_representacion,a.fecing,a.personal_externo FROM nompersonal AS a WHERE a.ficha='$ficha' AND a.tipnom='$tipnom'";
$res       = $db->query($sql);

// DATOS DEL PRIMER RENGLON
$persona   = $res->fetch_object();

//SACAR EL PRIMER NOMBRE Y EL SEGUNDO
$nom       =  $persona ->nombres;                         // PRIMER Y SEGUNDO NOMBRES EN LA BD       
$nombre    = strtoupper($nom); 
$noma      = explode(" ",$nombre);

$primer_n  = NULL;                                        // INICIALIZAR EL PRIMER NOMBRE 
if(isset($noma[0])){ $primer_n  = $noma[0]; }             // SI EXISTE EL PRIMER NOMBRE EN LA BD

$second_n  =  NULL;                                       // INICIALIZAR SEGUNDO NOMBRE
if(isset($noma[1])){ $second_n  = $noma[1]; }             // SI EXISTE EL SEGUNDO NOMBRE EN LA BD

$ape       = $persona ->apellidos;
$apellidos = strtoupper($ape);
$apem      = explode(" ",$apellidos);

$primer_a  = NULL;                                        // INICIALIZAR EL PRIMER APELLIDO 
if(isset($apem[0])){ $primer_a  = $apem[0];}              // SI EXISTE EL PRIMER APELLIDO EN LA BD

$second_a  =  NULL;

$s_a       =  $persona ->apellido_materno; 
$second_a  =  strtoupper($s_a);                           // SEGUNDO APELLIDO

$sex       =  $persona ->sexo;                            // SEXO
$sexo      =  strtoupper($sex);                           
$cedula    =  $persona ->cedula;                          // CEDULA
$email     =  $persona ->email;                           // EMAIL
$fnac      =  $persona ->fecnac;                          // FECHA EN LA BD
$fecha_nac =  fechas($fnac,1);                            // VOLTEAMOS LA FECHA CON ESTA FUNCION
$telefonR  =  $persona ->TelefonoResidencial;             // TELEFONO RESIDENCIAL
$telefonoc =  $persona ->TelefonoCelular;                 // TELEFONO CELULAR


//CABECERA PRIMER RENGLON
$MiPDF->SetFont('times', 'B', 12);

$MiPDF->Multicell(185,10,'DATOS PERSONALES',0,$alineacion='L',$fondo=true,$salto_de_linea=1,$x='15',$y='55',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);

//PRIMER RENGLON (DATOS)

$MiPDF->SetFont('times', '', 12);

$MiPDF->Multicell(40,10,'Primer Nombre:',0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(45,10,$primer_n,0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='55',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(45,10,'Segundo Nombre:',0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='100',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(65,10,$second_n,0,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='145',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(40,10,'Primer Apellido:',0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(50,10,$primer_a,0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='55',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(45,10,'Segundo Apellido:',0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='100',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(65,10,$second_a,0,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='145',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(40,10,'Genero:',0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(160,10,$sexo,0,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='55',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(40,10,'Cedula:',0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(50,10,$cedula,0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='55',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(45,10,'Correo Electronico:',0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='100',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(65,10,$email,0,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='145',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(40,10,'Fecha de Nacimiento:',0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(160,10,$fecha_nac,0,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='55',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(40,10,'Teléfono Residencial:',0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(50,10,$telefonR,0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='55',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(45,10,'Teléfono Celular:',0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='100',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(65,10,$telefonoc,0,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='145',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

// DATOS DEL SEGUNDO RENGLON

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
$fecha_decreto =  $persona ->fecha_decreto;
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
$categoria= "SIN ESTADO";
$con_cat   = "SELECT descrip FROM nomcategorias WHERE codorg='$cod_cat'";
$catsql    = $db->query($con_cat);
if($catsql ->num_rows>0) 
{
    $category     = $catsql->fetch_object();
    $categoria    = $category ->descrip;
}

//CABECERA SEGUNDO RENGLON

$MiPDF->SetFont('times', 'B', 12);

$MiPDF->Multicell(185,10,'IDENTIFICACIÓN DEL PUESTO',0,$alineacion='L',$fondo=true,$salto_de_linea=1,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);

//SEGUNDO RENGLON (DATOS)

$MiPDF->SetFont('times', '', 12);

$MiPDF->Multicell(40,10,'Fecha de Ingreso:',0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(45,10,$fing_n,0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='55',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(45,10,'Fecha de Permanencia:',0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='100',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(65,10,$finp_n,0,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='145',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(40,15,'Estado:',0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=15,$alineacion_vertical='T', $fitcell=false);

$MiPDF->SetFont('times', '', 12);

$MiPDF->Multicell(45,15,$categoria,0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='55',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=15,$alineacion_vertical='T', $fitcell=true);

$MiPDF->SetFont('times', '', 12);

$MiPDF->Multicell(45,15,'Fecha de Resolución:',0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='100',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=15,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(55,15,$fecha_decreto,0,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='145',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=15,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(40,10,'¿Está activo?',0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(45,10,'*',0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='55',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(45,10,'Condición:',0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='100',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(65,10,$estado,0,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='145',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(40,10,'¿Aprueba solicitudes?',0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(45,10,$aprueba,0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='55',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(45,10,'Usuario que Aprueba:',0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='100',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(65,10,'*',0,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='145',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(40,10,'¿Es personal externo?',0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(160,10,$pext,0,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='55',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

// DATOS DEL TERCER RENGLON

$est_c     =  $persona ->estado_civil;
$estado_civil   =  strtoupper($est_c);
$clave_ir     =  $persona ->clave_ir;
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
if($codpro==NULL)
{
    $n_prof="SIN PROFESION";
}
else
{
    $sql3    = "SELECT descrip FROM nomprofesiones WHERE codorg='$codpro'";
    $res3    = $db->query($sql3);
    $prof    = $res3->fetch_object();
    $n_prof  = $prof ->descrip;
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

$MiPDF->SetFont('times', 'B', 12);

$MiPDF->Multicell(185,10,'OTROS DATOS',0,$alineacion='L',$fondo=true,$salto_de_linea=1,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);

//TERCER RENGLON (DATOS)

$MiPDF->SetFont('times', '', 12);

$MiPDF->Multicell(40,10,'Estado Civil:',0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(45,10,$estado_civil,0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='55',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(45,10,'Clave ISR:',0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='100',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(65,10,$clave_ir,0,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='145',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(40,10,'Nacionalidad:',0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(45,10,$nacionalidad,0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='55',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(45,10,'Tipo de Sangre:',0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='100',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(65,10,$sangre,0,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='145',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(40,10,'Seguro Social:',0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(45,10,$seguro,0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='55',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(45,10,'Nivel Educativo:',0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='100',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(65,10,$n_edu,0,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='145',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(40,10,'Profesión: ',0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(45,10,$n_prof,0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='55',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(45,10,'Hijos:',0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='100',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(65,10,$hijos,0,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='145',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(40,10,'Contacto Emergencia: ',0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(45,10,$cemerg,0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='55',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(45,10,'Teléfono del Contacto:',0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='100',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(65,10,$Tcemerg,0,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='145',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(40,10,'¿Tiene discapacidad?',0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(45,10,$Tdisc,0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='55',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(45,10,'¿Tiene familiar con discapacidad?',0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='100',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(65,10,$FTdisc,0,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='145',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(40,10,'Dirección:',0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(160,10,$direccion,0,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='55',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(40,15,'Enfermedades y Alergías:',0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=15,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(150,15,$enfer,0,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='55',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=15,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(40,10,'Comentario:',0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(160,10,$comentario,0,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='55',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

//CABECERA CUARTO RENGLON

$MiPDF->SetFont('times', 'B', 12);

$MiPDF->Multicell(185,10,'CARGO ESTRUCTURA',0,$alineacion='L',$fondo=true,$salto_de_linea=1,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);

//DATOS CUARTO RENGLON

$nomposicion_id    =     $persona ->nomposicion_id;
$ctac              =     $persona ->ctacontab;
if(($ctac==NULL)or($ctac=='')) {  $cuenta_contable = "NO DISPONIBLE"; }else{     $cuenta_contable=$ctac;      }
$tipnom            =     $persona ->tipnom;
$tipemp            =     $persona ->tipemp;

//DEPARTAMENTO DEL EMPLEADO
$departamento      =     "SIN DEPARTAMENTO";                                // INICIALIZAR DEPARTAMENTO
$Iddepartamento    =     $persona ->Iddepartamento;                         // TRAERMOS EL ID DEL DEPARTAMENTO DE LA TABLA nompersonal BD
$sql4          = "SELECT Descripcion FROM departamento WHERE Iddepartamento='$Iddepartamento'";// CONSULTA EN LA TABLA departamento BD
$res4          = $db->query($sql4);                                         // CONSULTAMOS EN departamento
if($res4->num_rows>0)                                                       // SI HAY REGISTROS CAMBIAMOS EL DEPARTAMENTO
{
    $dept          = $res4->fetch_object();
    $departamento  = $dept ->Descripcion;
}

//CARGO DEL EMPLEADO
$cargo             =     "SIN CARGO";                                        // INICIALIZAR CARGO
$cargo_id          =     $persona ->codcargo;                                // TRAERMOS EL ID DEL CARGO DE LA TABLA nompersonal BD
$sql5          = "SELECT des_car FROM nomcargos WHERE cod_car='$cargo_id'";  // CONSULTA DEL CARGO EN LA TABLA nomcargos
$res5          = $db->query($sql5);                                          // CONSULTAMOS EN nomcargos
if($res5->num_rows>0)                                                        // SI HAY REGISTROS CAMBIAMOS EL CARGO
{
   $carg          = $res5->fetch_object();
   $cargo         = $carg ->des_car; 
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

$sueldo        = $persona ->suesal;
$gastos_rep    = $persona ->gastos_representacion;
$fecing        = $persona ->fecing;
$fecha_ing     = fechas($fecing,1);
//CUARTO RENGLON (DATOS)

$MiPDF->SetFont('times', '', 12);

$MiPDF->Multicell(40,10,'Posición:',0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(160,10,$nomposicion_id,0,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='55',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(40,10,'Cuenta Contable:',0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(45,10,$cuenta_contable,0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='55',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(45,10,'Planilla:',0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='100',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(65,10,$tipnom,0,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='145',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(40,10,'Departamento:',0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(160,10,$departamento,0,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='55',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(40,10,'Tipo de Empleado:',0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(45,10,$tipemp,0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='55',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(45,10,'Cargo:',0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='100',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(65,10,$cargo,0,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='145',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(40,10,'Función:',0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(160,10,$funcion,0,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='55',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(40,10,'Salario:',0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(45,10,$sueldo,0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='55',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(45,10,'Gastos de Representación:',0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='100',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(65,10,$gastos_rep,0,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='145',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(40,10,'Fecha de Inicio:',0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(160,10,$fecha_ing,0,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='55',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

$b = 0; //BORDE

//CABECERA QUINTO RENGLON

$sqlcargos          = "SELECT a.* FROM posicionempleadohist as a,nompersonal as b WHERE b.personal_id=a.IdEmpleado AND b.ficha='$ficha'";
$rescargos          = $db->query($sqlcargos);

$MiPDF->SetFont('times', 'B', 12);

$MiPDF->Multicell(185,10,'HISTORIAL DEL CARGO SEGÚN ESTRUCTURA',0,$alineacion='L',$fondo=true,$salto_de_linea=1,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);

//QUINTO RENGLON (DATOS)

$MiPDF->SetFont('times', '', 10);

$MiPDF->Multicell(40,10,'CuentaContable',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
$MiPDF->Multicell(15,10,'Posicion',$b,$alineacion='C',$fondo=false,$salto_de_linea=0,$x='55',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
$MiPDF->Multicell(15,10,'Planilla',$b,$alineacion='C',$fondo=false,$salto_de_linea=0,$x='70',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
$MiPDF->Multicell(40,10,'Departamento',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='85',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
$MiPDF->Multicell(20,10,'Tipo Empleado',$b,$alineacion='C',$fondo=false,$salto_de_linea=0,$x='125',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
$MiPDF->Multicell(20,10,'Cargo',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='145',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
$MiPDF->Multicell(20,10,'Salario',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='165',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
$MiPDF->Multicell(20,10,'Fecha Inicio',$b,$alineacion='C',$fondo=false,$salto_de_linea=1,$x='185',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
if($rescargos->num_rows>0) 
{
     while ($cargosfun = $rescargos->fetch_object())
    {
        $MiPDF->Multicell(40,10,'*',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(15,10,'*',$b,$alineacion='C',$fondo=false,$salto_de_linea=0,$x='55',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(15,10,'*',$b,$alineacion='C',$fondo=false,$salto_de_linea=0,$x='70',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(40,10,'*',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='85',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(20,10,'*',$b,$alineacion='C',$fondo=false,$salto_de_linea=0,$x='125',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(20,10,'*',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='145',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(20,10,'*',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='165',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(20,10,'*',$b,$alineacion='C',$fondo=false,$salto_de_linea=1,$x='185',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
    }
}
else
{
        $MiPDF->Multicell(40,10,'Sin Datos',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(15,10,'Sin Datos',$b,$alineacion='C',$fondo=false,$salto_de_linea=0,$x='55',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(15,10,'Sin Datos',$b,$alineacion='C',$fondo=false,$salto_de_linea=0,$x='70',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(40,10,'Sin Datos',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='85',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(20,10,'Sin Datos',$b,$alineacion='C',$fondo=false,$salto_de_linea=0,$x='125',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(20,10,'Sin Datos',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='145',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(20,10,'Sin Datos',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='165',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(20,10,'Sin Datos',$b,$alineacion='C',$fondo=false,$salto_de_linea=1,$x='185',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
}
$MiPDF->Multicell(160,10,'',0,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='55',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);




//CABECERA SEXTO RENGLON

$sql7          = "SELECT a.* FROM expediente as a,nompersonal as b WHERE a.cedula=b.cedula AND a.tipo = '25'  AND b.ficha='$ficha'";
$res7          = $db->query($sql7);

$MiPDF->SetFont('times', 'B', 12);

$MiPDF->Multicell(185,10,'MOVIMIENTOS DEL FUNCIONARIO',0,$alineacion='L',$fondo=true,$salto_de_linea=1,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);

//SEXTO RENGLON (DATOS)

$MiPDF->SetFont('times', 'B', 12);

$MiPDF->Multicell(80,10,'Departamento',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
$MiPDF->Multicell(30,10,'Fecha de Inicio',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='95',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
$MiPDF->Multicell(30,10,'Fecha Final',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='125',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
$MiPDF->Multicell(45,10,'Tipo de Movimiento',$b,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='155',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
$MiPDF->SetFont('times', '', 12);
if($res7->num_rows>0) 
{
    while ($movfun = $res7->fetch_object())
    {
        $departamento   =  $movfun->descripcion;
        $mfechai        =  $movfun->fecha_retorno;
        $mfechaf        =  $movfun->fecha_salida;
        $mfechaini      =  fechas($mfechai,1);
        $mfechafin      =  fechas($mfechaf,1);
        $TipoMovimiento =  $movfun->tipo;
        if($TipoMovimiento==25){ $n_tipo_mov="ROTACION"; }
        $MiPDF->Multicell(80,10,$departamento,$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(30,10,$mfechaini,$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='95',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(30,10,$mfechafin,$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='125',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(45,10,$n_tipo_mov,$b,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='155',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
    }
}
else
{
        $MiPDF->Multicell(80,10,'Sin Datos',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(30,10,'Sin Datos',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='95',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(30,10,'Sin Datos',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='125',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(45,10,'Sin Datos',$b,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='155',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
}
$MiPDF->Multicell(160,10,'',0,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='55',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);


//CABECERA SEPTIMO RENGLON RENGLON


$MiPDF->SetFont('times', 'B', 12);

$MiPDF->Multicell(185,10,'TIEMPOS',0,$alineacion='L',$fondo=true,$salto_de_linea=1,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);

//SEPTIMO RENGLON (DATOS)

$sql9          = "SELECT a.* FROM expediente as a,nompersonal as b WHERE a.cedula=b.cedula AND (a.tipo = '11' OR  a.tipo = '12')  AND b.ficha='$ficha'";
$res9          = $db->query($sql9);

$MiPDF->SetFont('times', 'B', 12);

$MiPDF->Multicell(80,10,'Tiempo Disponible',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
$MiPDF->Multicell(30,10,'Tiempo Total',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='95',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
$MiPDF->Multicell(25,10,'Días',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='125',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
$MiPDF->Multicell(25,10,'Horas',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='150',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
$MiPDF->Multicell(25,10,'Minutos',$b,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='175',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);

$MiPDF->SetFont('times', '', 12);

if($res9->num_rows>0) 
{
    while ($tiempos = $res9->fetch_object())
    {
            $tipo_tiempo        =  $tiempos->tipo;

            $dias               =  $tiempos->dias;
            if($dias==NULL)
            {
                $dias           =  0;
            }

            $minutos            =  $tiempos->minutos;
            if($minutos==NULL)
            {
                $minutos        =  0;
            }

            $horas              =  $tiempos->horas;
            if($horas==NULL)
            {
                $horas        =  0;
            }

            $minsporc           =  ($minutos*100)/60;
            if($minsporc<10)       {$minsporc="0".$minsporc;             }
            $base               =  ($dias*8)+$horas;
            $Total              =  $base.".".$minsporc;

            if($tipo_tiempo==12){ $n_tipo_tiempo="TIEMPO COMPENSATORIO"; }
            if($tipo_tiempo==11){ $n_tipo_tiempo="VACACIONES"; }
            $MiPDF->Multicell(80,10,$n_tipo_tiempo,$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
            $MiPDF->Multicell(30,10,$Total,$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='95',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
            $MiPDF->Multicell(25,10,$dias,$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='125',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
            $MiPDF->Multicell(25,10,$horas,$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='150',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
            $MiPDF->Multicell(25,10,$minutos,$b,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='175',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
    }
}
else
{
    $MiPDF->Multicell(80,10,'Sin Datos',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
    $MiPDF->Multicell(30,10,'Sin Datos',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='95',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
    $MiPDF->Multicell(25,10,'Sin Datos',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='125',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
    $MiPDF->Multicell(25,10,'Sin Datos',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='150',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
    $MiPDF->Multicell(25,10,'Sin Datos',$b,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='175',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
}

$MiPDF->Multicell(160,10,'',0,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='55',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

//CABECERA OCTAVO RENGLON RENGLON (CAPACITACIONES)

$sql10          = "SELECT a.* FROM expediente as a,nompersonal as b WHERE a.cedula=b.cedula AND a.tipo = '2' AND b.ficha='$ficha'";
$res10          = $db->query($sql10);


$MiPDF->SetFont('times', 'B', 12);

$MiPDF->Multicell(185,10,'CAPACITACIONES',0,$alineacion='L',$fondo=true,$salto_de_linea=1,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);

//OCTAVO RENGLON (DATOS) (CAPACITACIONES)

$MiPDF->SetFont('times', 'B', 12);

$MiPDF->Multicell(25,10,'Fecha Inicio',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
$MiPDF->Multicell(25,10,'Fecha Fin',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='40',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
$MiPDF->Multicell(135,10,'Comentario',$b,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='65',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);

$MiPDF->SetFont('times', '', 12);
if($res10->num_rows>0) 
{
    while ($capa = $res10->fetch_object())
    {
        $inica        =  $capa->fecha_retorno;
        $finca        =  $capa->fecha_salida;
        $inicio_capa  = fechas($inica,1);
        $fin_capa     = fechas($finca,1);
        $comentario   = $capa->descripcion;
        $MiPDF->Multicell(25,10,$inicio_capa,$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(25,10,$fin_capa,$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='40',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(135,10,$comentario,$b,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='65',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
    }
}
else
{
        $MiPDF->Multicell(25,10,'Sin Datos',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(25,10,'Sin Datos',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='40',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(135,10,'Sin Datos',$b,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='65',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);  
}
$MiPDF->Multicell(160,10,'',0,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='55',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

//CABECERA NOVENO RENGLON RENGLON  (EVALUACIONES)

$sql11          = "SELECT a.* FROM expediente as a,nompersonal as b WHERE a.cedula=b.cedula AND a.tipo = '10' AND b.ficha='$ficha'";
$res11          = $db->query($sql11);

$MiPDF->SetFont('times', 'B', 12);

$MiPDF->Multicell(185,10,'EVALUACIONES',0,$alineacion='L',$fondo=true,$salto_de_linea=1,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);

//NOVENO RENGLON (DATOS) (EVALUACIONES)

$MiPDF->SetFont('times', 'B', 10);

$MiPDF->Multicell(25,10,'Fecha Inicio',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
$MiPDF->Multicell(25,10,'Fecha Fin',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='40',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
$MiPDF->Multicell(25,10,'E. Evaluación',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='65',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
$MiPDF->Multicell(45,10,'Supervisor que evaluó',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='90',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
$MiPDF->Multicell(20,10,'Puntaje',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='135',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
$MiPDF->Multicell(45,10,'Comentario',$b,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='155',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);

$MiPDF->SetFont('times', '', 10);
if($res11->num_rows>0) 
{
    while ($eva = $res11->fetch_object())
    {
        $inieva        = $eva->fecha_retorno;
        $fineva        = $eva->fecha_salida;
        $inicio_eva    = fechas($inieva,1);
        $fin_eva       = fechas($fineva,1);
        //$evaluador     = $eva->nombre_especialista;
        $feval         = $eva->fecha;
        $fecha_eval    = fechas($feval,1);
        //$puntaje       = $eva->puntaje;
        $comentario    = $eva->descripcion;
        $MiPDF->Multicell(25,10,$inicio_eva,$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(25,10,$fin_eva,$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='40',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(25,10,$fecha_eval,$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='65',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(45,10,'*',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='90',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(20,10,'*',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='135',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(45,10,$comentario,$b,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='155',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
    }
}
else
{
        $MiPDF->Multicell(25,10,'Sin Datos',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(25,10,'Sin Datos',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='40',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(25,10,'Sin Datos',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='65',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(45,10,'Sin Datos',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='90',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(20,10,'Sin Datos',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='135',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(45,10,'Sin Datos',$b,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='155',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
}

$MiPDF->Multicell(160,10,'',0,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='55',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

//CABECERA DECIMO RENGLON RENGLON

$sql12          = "SELECT a.*,c.nombre_subtipo FROM expediente as a,nompersonal as b,expediente_subtipo as c WHERE a.cedula=b.cedula AND c.id_expediente_subtipo=a.subtipo AND a.tipo = '1' AND b.ficha='$ficha'";
$res12          = $db->query($sql12);

$MiPDF->SetFont('times', 'B', 12);

$MiPDF->Multicell(185,10,'ESTUDIOS',0,$alineacion='L',$fondo=true,$salto_de_linea=1,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);

//DECIMO RENGLON (DATOS)

$MiPDF->SetFont('times', 'B', 10);

$MiPDF->Multicell(25,10,'Nivel Educativo',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
$MiPDF->Multicell(45,10,'Institución',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='40',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
$MiPDF->Multicell(25,10,'Fecha Inicio',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='85',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
$MiPDF->Multicell(25,10,'Fecha Fin',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='110',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
$MiPDF->Multicell(65,10,'Comentario',$b,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='135',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);

$MiPDF->SetFont('times', '', 10);
if($res12->num_rows>0) 
{
    while ($edu = $res12->fetch_object())
    {
        $iniedu        = $edu->fecha_retorno;
        $finedu        = $edu->fecha_salida;
        $inicio_edu    = fechas($iniedu,1);
        $fin_edu       = fechas($finedu,1);
        $niveledu      = $edu->nombre_subtipo;
        $intitucion    = $edu->institucion;
        $comentario    = $edu->descripcion;
        $MiPDF->Multicell(25,10,$niveledu,$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(45,10,$intitucion,$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='40',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(25,10,$inicio_edu,$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='85',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(25,10,$fin_edu,$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='110',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(65,10,$comentario,$b,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='135',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);

    }
}
else
{
        $MiPDF->Multicell(25,10,'Sin Datos',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(45,10,'Sin Datos',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='40',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(25,10,'Sin Datos',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='85',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(25,10,'Sin Datos',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='110',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(65,10,'Sin Datos',$b,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='135',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
}
$MiPDF->Multicell(160,10,'',0,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='55',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

//CABECERA ONCEAVO RENGLON RENGLON

$MiPDF->SetFont('times', 'B', 12);

$MiPDF->Multicell(185,10,'EXPERIENCIA LABORAL',0,$alineacion='L',$fondo=true,$salto_de_linea=1,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);

$sql13         = "SELECT a.* FROM expediente as a,nompersonal as b WHERE a.cedula=b.cedula AND a.tipo = '14' AND b.ficha='$ficha'";
$res13          = $db->query($sql13);

//ONCEAVO RENGLON (DATOS)

$MiPDF->SetFont('times', 'B', 10);

$MiPDF->Multicell(45,10,'Lugar',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
$MiPDF->Multicell(45,10,'Cargo',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='60',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
$MiPDF->Multicell(25,10,'Fecha Inicio',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='105',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
$MiPDF->Multicell(25,10,'Fecha Fin',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='130',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
$MiPDF->Multicell(45,10,'Comentario',$b,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='155',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);

$MiPDF->SetFont('times', '', 10);
if($res13->num_rows>0) 
{
    while ($labores = $res13->fetch_object())
    {
        $inilar        = $labores->fecha_retorno;
        $finlar        = $labores->fecha_salida;
        $inicio_labor  = fechas($inilar,1);
        $fin_labor     = fechas($finlar,1);
        $lugarant      = $labores->institucion;
        //$laboren       = $labores->labor;
        $comentario    = $labores->descripcion;
        $MiPDF->Multicell(45,10,$lugarant,$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(45,10,'*',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='60',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(25,10,$inicio_labor,$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='105',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(25,10,$fin_labor,$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='130',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(45,10,$comentario,$b,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='155',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
    }
}
else
{
        $MiPDF->Multicell(45,10,'Sin Datos',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(45,10,'Sin Datos',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='60',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(25,10,'Sin Datos',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='105',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(25,10,'Sin Datos',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='130',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(45,10,'Sin Datos',$b,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='155',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);  
}
$MiPDF->Multicell(160,10,'',0,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='55',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

//CABECERA DOCEAVO RENGLON RENGLON

$MiPDF->SetFont('times', 'B', 12);

$MiPDF->Multicell(185,10,'ACCIONES',0,$alineacion='L',$fondo=true,$salto_de_linea=1,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);

$MiPDF->SetFont('times', 'B', 11);

$MiPDF->Multicell(185,10,'AMONESTACIONES',0,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);

$sql14         = "SELECT a.*,c.nombre_subtipo FROM expediente as a,nompersonal as b,expediente_subtipo as c WHERE a.cedula=b.cedula AND c.id_expediente_subtipo=a.subtipo AND a.tipo = '5' AND b.ficha='$ficha'";
$res14         = $db->query($sql14);

//DOCEAVO RENGLON (DATOS)

$MiPDF->SetFont('times', 'B', 10);

$MiPDF->Multicell(25,10,'Fecha',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
$MiPDF->Multicell(25,10,'Tipo',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='40',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
$MiPDF->Multicell(25,10,'Numeral No.',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='65',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
$MiPDF->Multicell(50,10,'Descripción',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='90',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
$MiPDF->Multicell(20,10,'Artículo',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='140',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
$MiPDF->Multicell(40,10,'Motivo',$b,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='160',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);

$MiPDF->SetFont('times', '', 10);
if($res14->num_rows>0) 
{
    while ($amon = $res14->fetch_object())
    {
        $numeral           = $amon->numero_resolucion;
        $articulo          = $amon->numero_decreto;
        $fec_a             = $amon->fecha;
        $fec_amonestacion  = fechas($fec_a,1);
        $comentario        = $amon->descripcion;
        $tipo_amon         = $amon->nombre_subtipo;
        $MiPDF->Multicell(25,10,$fec_amonestacion,$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(25,10,$tipo_amon,$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='40',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(25,10,$numeral,$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='65',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(50,10,$comentario ,$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='90',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(20,10,$articulo,$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='140',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(40,10,'*',$b,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='160',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);   
    }
}
else
{
        $MiPDF->Multicell(25,10,'Sin Datos',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(25,10,'Sin Datos',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='40',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(25,10,'Sin Datos',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='65',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(50,10,'Sin Datos',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='90',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(20,10,'Sin Datos',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='140',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(40,10,'Sin Datos',$b,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='160',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false); 
}
$MiPDF->Multicell(160,10,'',0,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='55',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

//CABECERA TRECEAVO RENGLON RENGLON

$MiPDF->SetFont('times', 'B', 11);

$MiPDF->Multicell(185,10,'SUSPENSIONES',0,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);

$sql15         = "SELECT a.* FROM expediente as a,nompersonal as b WHERE a.cedula=b.cedula AND a.tipo = '6' AND b.ficha='$ficha'";
$res15         = $db->query($sql15);

//TRECEAVO RENGLON (DATOS)

$MiPDF->SetFont('times','B', 10);

$MiPDF->Multicell(25,10,'No. Resolución',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
$MiPDF->Multicell(25,10,'F. Resolución',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='40',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
$MiPDF->Multicell(25,10,'Desde',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='65',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
$MiPDF->Multicell(25,10,'Hasta',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='90',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
$MiPDF->Multicell(25,10,'Días',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='115',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
$MiPDF->Multicell(60,10,'Motivo',$b,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='140',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);

$MiPDF->SetFont('times','', 10);
if($res15->num_rows>0) 
{
    while ($sus = $res15->fetch_object())
    {
                $numero_resolucion = $sus->numero_resolucion;
                $fec_s             = $sus->fecha;
                $fec_suspencion    = fechas($fec_s,1);
                $inisus            = $sus->fecha_salida;
                $finsus            = $sus->fecha_retorno;
                $inicio_sus        = fechas($inisus,1);
                $fin_sus           = fechas($finsus,1);
                $dias              = $sus->dias;
                $comentario        = $sus->descripcion;

                $MiPDF->Multicell(25,10,$numero_resolucion,$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
                $MiPDF->Multicell(25,10,$fec_suspencion,$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='40',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
                $MiPDF->Multicell(25,10,$inicio_sus,$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='65',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
                $MiPDF->Multicell(25,10,$fin_sus,$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='90',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
                $MiPDF->Multicell(25,10,$dias,$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='115',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
                $MiPDF->Multicell(60,10,$comentario,$b,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='140',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false); 
    }
}
else
{
                $MiPDF->Multicell(25,10,'Sin Datos',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
                $MiPDF->Multicell(25,10,'Sin Datos',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='40',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
                $MiPDF->Multicell(25,10,'Sin Datos',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='65',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
                $MiPDF->Multicell(25,10,'Sin Datos',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='90',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
                $MiPDF->Multicell(25,10,'Sin Datos',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='115',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
                $MiPDF->Multicell(60,10,'Sin Datos',$b,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='140',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);   
}

$MiPDF->Multicell(160,10,'',0,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='55',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

//CABECERA CARTOCEAVO RENGLON RENGLON

$MiPDF->SetFont('times', 'B', 11);

$MiPDF->Multicell(185,10,'LICENCIAS',0,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);

$sql16         = "SELECT a.*,c.nombre_subtipo FROM expediente as a,nompersonal as b,expediente_subtipo as c WHERE a.cedula=b.cedula AND c.id_expediente_subtipo=a.subtipo AND (a.tipo = '15' OR a.tipo = '16'  OR a.tipo = '17')  AND b.ficha='$ficha'";
$res16         = $db->query($sql16);

//CARTOCEAVO RENGLON (DATOS)

$MiPDF->SetFont('times', 'B', 10);

$MiPDF->Multicell(50,10,'Tipo de Licencia',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
$MiPDF->Multicell(25,10,'No. Resolución',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='65',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
$MiPDF->Multicell(25,10,'F. Resolución',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='90',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
$MiPDF->Multicell(25,10,'Desde',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='115',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
$MiPDF->Multicell(25,10,'Hasta',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='140',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
$MiPDF->Multicell(35,10,'Motivo',$b,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='165',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);

$MiPDF->SetFont('times', '', 10);
if($res16->num_rows>0) 
{
    while ($lic = $res16->fetch_object())
    {
        $tlic                   = $lic->nombre_subtipo;
        $numero_resolucion_lic  = $lic->numero_resolucion;
        $fec_l                  = $lic->fecha;
        $fec_licencia           = fechas($fec_l,1);
        $inilic                 = $lic->fecha_salida;
        $finlic                 = $lic->fecha_retorno;
        $inicio_lic             = fechas($inilic,1);
        $fin_lic                = fechas($finlic,1);

        $MiPDF->Multicell(50,10,$tlic,$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(25,10,$numero_resolucion_lic,$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='65',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(25,10,$fec_licencia,$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='90',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(25,10,$inicio_lic,$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='115',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(25,10,$fin_lic,$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='140',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(35,10,'No Aplica',$b,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='165',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
    }
}
else
{
        $MiPDF->Multicell(50,10,'Sin Datos',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(25,10,'Sin Datos',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='65',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(25,10,'Sin Datos',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='90',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(25,10,'Sin Datos',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='115',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(25,10,'Sin Datos',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='140',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(35,10,'Sin Datos',$b,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='165',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
}
$MiPDF->Multicell(160,10,'',0,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='55',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);
*/

$MiPDF->Output('resumen_expediente.pdf','I');
?>