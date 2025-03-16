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
$ficha                                      = ( isset($_GET['ficha']) )   ? $_GET['ficha']          : '' ;
$tipnom                                     = ( isset($_GET['tipnom']) )  ? $_GET['tipnom']         : '' ;


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

        $this->Image($image_file, $x=20, $y=8, $w=35,   $h=35, $type='', $link='', $align='C', $resize=false, $dpi=300, $palign='', $ismask=false, $imgmask=false, $border=0, $fitbox=false, $hidden=false, $fitonpage=false);
        $this->Multicell(210,10,'',$border=0,$align='C',$fill=0,$ln=1,$x='0',$y='',$reseth=true,$stretch=0,$ishtml=false,$autopadding=true,$maxh=5,$valign='M',$fitcell=false);
        $this->Multicell(210,7,'REPÚBLICA DE PANAMÁ',$border=0,$align='C',$fill=0,$ln=1,$x='0',$y='',$reseth=true,$stretch=0,$ishtml=false,$autopadding=true,$maxh=10,$valign='M',$fitcell=false);
        $this->Multicell(210,7,strtoupper($empresa),$border=0,$align='C',$fill=0,$ln=1,$x='0',$y='',$reseth=true,$stretch=0,$ishtml=false,$autopadding=true,$maxh=10,$valign='M',$fitcell=false); 
         $this->Multicell(210,7,'REGISTRO Y CONTROL',$border=0,$align='C',$fill=0,$ln=1,$x='0',$y='',$reseth=true,$stretch=0,$ishtml=false,$autopadding=true,$maxh=10,$valign='M',$fitcell=false); 
         $this->Multicell(210,7,'EXPEDIENTE DE FUNCIONARIO',$border=0,$align='C',$fill=0,$ln=1,$x='0',$y='',$reseth=true,$stretch=0,$ishtml=false,$autopadding=true,$maxh=10,$valign='M',$fitcell=false);
         //$this->Multicell(210,20,'DATOS PERSONALES',$border=0,$align='C',$fill=0,$ln=1,$x='0',$y='',$reseth=true,$stretch=0,$ishtml=false,$autopadding=true,$maxh=20,$valign='M',$fitcell=false); 
        //($w, $h, $txt, $border=0, $align='J',$fill=false, $ln=1, $x='', $y='', $reseth=true, $stretch=0, $ishtml=false, $autopadding=true, $maxh=0, $valign='T', $fitcell=false)

        // Title
        //$this->Cell(195,10,"REPÚBLICA DE PANAMÁ",1,1,$align='C',0,0,1,0,'',''); //order(ancho,alto,mensaje,borde,salto de linea,alineacion,fill,link,strech,ignore min  altura, caling, valing)
        //$this->Cell(195,10,"SERVICIO NACIONAL DE MIGRACIÓN",1,1,$align='C',0,0,1,0,'',''); 
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
$MiPDF->SetTitle('Decreto');
# el método SetDisplayMode nos permite incluir palabras claves
# separadas por espacios y dentro de una misma cadena
$MiPDF->SetDisplayMode('fullpage','two');
# creamos una página en blanco. Incluimos, para esta primera página
# un cambio de orientación respecto a la inicial
$MiPDF->SetMargins(0,40,0, true);

$MiPDF->AddPage('P');

$ruta_img='../../nomina/imagenes/';

$MiPDF->SetFillColor(199, 199, 199); // Color de Fondo

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

$MiPDF->Multicell(185,10,'DATOS PERSONALES',0,$alineacion='L',$fondo=true,$salto_de_linea=1,$x='15',$y='45',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);

//PRIMER RENGLON (DATOS)

$MiPDF->SetFont('times', '', 9);

$MiPDF->Multicell(40,8,'Primer Nombre:',0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(65,8,$primer_n,0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='50',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(45,8,'Segundo Nombre:',0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='100',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(65,8,$nom2,0,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='135',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(40,8,'Primer Apellido:',0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(50,8,$apellidos,0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='50',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(45,8,'Segundo Apellido:',0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='100',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(65,8,$second_a,0,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='135',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(40,8,'Cedula:',0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(50,8,$cedula,0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='50',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(40,8,'Genero:',0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='100',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(160,8,$sexo,0,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='135',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

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
$MiPDF->Multicell(185,10,'IDENTIFICACIÓN DEL PUESTO',0,$alineacion='L',$fondo=true,$salto_de_linea=1,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);

//SEGUNDO RENGLON (DATOS)

$MiPDF->SetFont('times', '', 9);

$MiPDF->Multicell(40,8,'Fecha de Ingreso:',0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(45,8,$fing_n,0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='50',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(45,8,'Fecha de Permanencia:',0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='100',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(65,8,$finp_n,0,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='135',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(40,8,'Fecha de Resolución:',0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=15,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(45,8,$fecha_decreto,0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='50',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=15,$alineacion_vertical='T', $fitcell=true);

$MiPDF->SetFont('times', '', 10);

$MiPDF->Multicell(45,8,'No de Resolución:',0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='100',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=15,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(55,8,$num_resolucion,0,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='135',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=15,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(45,8,'Estado:',0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=15,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(100,8,$categoria,0,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='50',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=15,$alineacion_vertical='T', $fitcell=false);
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

    $MiPDF->Multicell(185,10,'CARGO ESTRUCTURA',0,$alineacion='L',$fondo=true,$salto_de_linea=1,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);

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

$MiPDF->Multicell(40,8,'Posición:',0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(160,8,$nomposicion_id,0,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='45',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(40,8,'Departamento:',0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(160,8,$departamento,0,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='45',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(45,8,'Cargo:',0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(65,8,$cargo,0,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='45',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(40,8,'Función:',0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(160,8,$funcion,0,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='45',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

$b = 0; //BORDE


//CABECERA NOVENO RENGLON RENGLON  (EVALUACIONES)
$sql11         = "SELECT A.*, B.apenom "
                     . "FROM empleado_evaluacion as A"
                    . " LEFT JOIN nompersonal AS  B ON A.persona_evalua = B.personal_id "
                    . " WHERE A.IdEmpleado='{$ficha}'"
                    . " ORDER BY A.fini_periodo";
$res11          = $db->query($sql11);

$MiPDF->SetFont('times', 'B', 11);

$MiPDF->Multicell(185,10,'EVALUACIONES',0,$alineacion='L',$fondo=true,$salto_de_linea=1,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);

//NOVENO RENGLON (DATOS) (EVALUACIONES)

$MiPDF->SetFont('times', 'B', 9);

$MiPDF->Multicell(25,10,'Fecha Ini. Periodo',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
$MiPDF->Multicell(25,10,'Fecha Fin. Periodo',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='40',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
$MiPDF->Multicell(25,10,'Fecha Evaluación',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='65',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
$MiPDF->Multicell(45,10,'Supervisor Evaluador',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='90',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
$MiPDF->Multicell(20,10,'Puntaje',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='135',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
$MiPDF->Multicell(45,10,'Comentario',$b,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='155',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);

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
        //$yy = $MiPDF->GetY();
        $MiPDF->Multicell(25,10,$inicio_eva,$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(25,10,$fin_eva,$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='40',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(25,10,$fecha_eval,$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='65',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(45,10,$eva->apenom,$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='90',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(20,10,$eva->puntaje,$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='135',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(45,10,$comentario,$b,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='155',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
        if( $MiPDF->GetY()> 230)
        {
            //Se valida que se haya llegado a un margen
            $MiPDF->AddPage();
            $yy = '45';
        }
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
    //Se valida que se haya llegado a un margen
    if( $MiPDF->GetY()> 230)
    {
        $MiPDF->AddPage();
    }
}

$MiPDF->Multicell(160,10,'',0,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='55',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);
//CABECERA ONCEAVO RENGLON RENGLON


$MiPDF->SetFont('times', 'B', 11);

$MiPDF->Multicell(185,10,'EXPERIENCIA LABORAL',0,$alineacion='L',$fondo=true,$salto_de_linea=1,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);

$sql13        = "SELECT A.* "
                     . "FROM empleado_exp_lab as A"
                    . " WHERE A.IdEmpleado='{$ficha}'"
                    . " ORDER BY A.fecha_ini";
$res13          = $db->query($sql13);

//ONCEAVO RENGLON (DATOS)

$MiPDF->SetFont('times', 'B', 9);

$MiPDF->Multicell(45,10,'Lugar',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
$MiPDF->Multicell(45,10,'Cargo',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='60',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
$MiPDF->Multicell(25,10,'Fecha Inicio',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='105',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
$MiPDF->Multicell(25,10,'Fecha Fin',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='130',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
$MiPDF->Multicell(45,10,'Comentario',$b,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='155',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);

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
        $MiPDF->Multicell(45,10,$lugarant,$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(45,10,$cargo,$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='60',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(25,10,$inicio_labor,$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='105',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(25,10,$fin_labor,$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='130',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(45,10,$comentario,$b,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='155',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
    }
    //Se valida que se haya llegado a un margen

    if( $MiPDF->GetY()> 240)
    {
        $MiPDF->AddPage();
        $yy = '45';
    }
    else
    {
        $yy = '';
    }
}
else
{
    $MiPDF->Multicell(45,10,'Sin Datos',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
    $MiPDF->Multicell(45,10,'Sin Datos',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='60',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
    $MiPDF->Multicell(25,10,'Sin Datos',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='105',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
    $MiPDF->Multicell(25,10,'Sin Datos',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='130',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
    $MiPDF->Multicell(45,10,'Sin Datos',$b,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='155',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);  
    //Se valida que se haya llegado a un margen
    if( $MiPDF->GetY()> 240)
    {
        $MiPDF->AddPage();
        $yy = '45';
    }
    else
    {
        $yy = '';
    }
}

//CABECERA DOCEAVO RENGLON RENGLON

$MiPDF->SetFont('times', 'B', 11);

//Si el literal llega al final de una línea hace un salto de página

$MiPDF->Multicell(185,10,'ACCIONES',0,$alineacion='L',$fondo=true,$salto_de_linea=1,$x='15',$y=$yy,$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);

$MiPDF->SetFont('times', 'B', 10);

$MiPDF->Multicell(185,10,'AMONESTACIONES',0,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);

$sql14        = "SELECT A.* "
                     . "FROM amonestaciones as A"
                    . " WHERE A.usr_uid='{$useruid}'"
                    . " ORDER BY A.fecha";
$res14         = $db->query($sql14);

//DOCEAVO RENGLON (DATOS)

$MiPDF->SetFont('times', 'B', 9);

$MiPDF->Multicell(25,10,'Fecha',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
$MiPDF->Multicell(25,10,'Tipo',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='32',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
$MiPDF->Multicell(25,10,'Numeral No.',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='60',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
$MiPDF->Multicell(50,10,'Descripción',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='80',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
$MiPDF->Multicell(20,10,'Artículo',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='130',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
$MiPDF->Multicell(55,10,'Motivo',$b,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='145',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);

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
        $MiPDF->Multicell(25,10,$fec_amonestacion,$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(25,10,$tipo_amon,$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='32',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(25,10,$numeral,$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='60',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(50,10,$numeral_descripcion,$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='80',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(20,10,$articulo,$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='130',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
         $MiPDF->Multicell(55,10,$comentario,$b,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='145',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);   
    if( $MiPDF->GetY()> 230)
    {
        $MiPDF->AddPage();
    }
    }
}
else
{
    $MiPDF->Multicell(25,10,'Sin Datos',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
    $MiPDF->Multicell(25,10,'Sin Datos',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='32',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
    $MiPDF->Multicell(25,10,'Sin Datos',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='60',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
    $MiPDF->Multicell(50,10,'Sin Datos',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='80',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
    $MiPDF->Multicell(20,10,'Sin Datos',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='130',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
    $MiPDF->Multicell(55,10,'Sin Datos',$b,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='145',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false); 
    if( $MiPDF->GetY()> 230)
    {
        $MiPDF->AddPage();
    }
}
$MiPDF->Multicell(160,10,'',0,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='55',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

//CABECERA TRECEAVO RENGLON RENGLON

$MiPDF->SetFont('times', 'B', 10);

$MiPDF->Multicell(185,10,'SUSPENSIONES',0,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);

$sql15       = "SELECT A.* "
                     . "FROM suspenciones as A"
                    . " WHERE A.usr_uid='{$useruid}'"
                    . " ORDER BY A.fecha";
$res15         = $db->query($sql15);

//TRECEAVO RENGLON (DATOS)

$MiPDF->SetFont('times','B', 9);

$MiPDF->Multicell(25,10,'No. Resolución',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
$MiPDF->Multicell(25,10,'F. Resolución',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='38',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
$MiPDF->Multicell(25,10,'Desde',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='60',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
$MiPDF->Multicell(25,10,'Hasta',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='80',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
$MiPDF->Multicell(15,10,'Articulo',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='100',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
$MiPDF->Multicell(15,10,'Numeral',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='115',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
$MiPDF->Multicell(20,10,'Descripcion',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='130',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);

$MiPDF->Multicell(25,10,'Días',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='150',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
$MiPDF->Multicell(60,10,'Motivo',$b,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='175',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);

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

        $MiPDF->Multicell(25,10,$numero_resolucion,$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(25,10,$fec_suspencion,$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='38',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(25,10,$inicio_sus,$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='60',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(25,10,$fin_sus,$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='80',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(25,10,$dias,$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='100',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(60,10,$comentario,$b,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='115',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false); 
        $MiPDF->Multicell(60,10,$comentario,$b,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='130',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false); 
        $MiPDF->Multicell(60,10,$dias,$b,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='150',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false); 
        $MiPDF->Multicell(60,10,$motivo,$b,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='175',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false); 
    }
}
else
{
    $MiPDF->Multicell(25,10,'Sin Datos',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
    $MiPDF->Multicell(25,10,'Sin Datos',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='38',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
    $MiPDF->Multicell(25,10,'Sin Datos',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='60',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
    $MiPDF->Multicell(25,10,'Sin Datos',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='80',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
    $MiPDF->Multicell(25,10,'Sin Datos',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='100',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
    $MiPDF->Multicell(60,10,'Sin Datos',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='115',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false); 
    $MiPDF->Multicell(60,10,'Sin Datos',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='130',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false); 
    $MiPDF->Multicell(60,10,'Sin Datos',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='150',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false); 
    $MiPDF->Multicell(60,10,'Sin Datos',$b,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='175',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);  
}

$MiPDF->Multicell(160,10,'',0,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='55',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

//CABECERA CARTOCEAVO RENGLON RENGLON

$MiPDF->SetFont('times', 'B', 10);

$MiPDF->Multicell(185,10,'LICENCIAS',0,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);

$sql16         = "SELECT a.*,c.nombre_subtipo FROM expediente as a,nompersonal as b,expediente_subtipo as c WHERE a.cedula=b.cedula AND c.id_expediente_subtipo=a.subtipo AND (a.tipo = '15' OR a.tipo = '16'  OR a.tipo = '17')  AND b.ficha='$ficha'";
$res16         = $db->query($sql16);

//CARTOCEAVO RENGLON (DATOS)

$MiPDF->SetFont('times', 'B', 9);

$MiPDF->Multicell(50,10,'Tipo de Licencia',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
$MiPDF->Multicell(25,10,'No. Resolución',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='65',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
$MiPDF->Multicell(25,10,'F. Resolución',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='90',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
$MiPDF->Multicell(25,10,'Desde',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='115',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
$MiPDF->Multicell(25,10,'Hasta',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='140',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
$MiPDF->Multicell(35,10,'Motivo',$b,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='165',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);

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
ob_end_clean();
$MiPDF->Output('resumen_expediente.pdf','I');
?>