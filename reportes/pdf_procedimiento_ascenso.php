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
$estado=$_POST["estado"];



    function formato_fecha($fecha,$formato)
    {
	    $meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
        $separa = explode("-",$fecha);
        $dia = $separa[2];
	    $mes = $separa[1];
	    $anio = $separa[0];
	    switch ($formato)
	    {
	        case 1:
	            $f = $dia." de ".$meses[$mes-1]." de ".$anio;
	            break;
	        case 2:
	            $f = $dia." del mes ".$meses[$mes-1]." de ".$anio;
	            break;
	        default:
	            break;
	    }
	    return $f;
    }
    

            
class MYPDF extends TCPDF
{   
    public function Header() 
    {
        global $empresa;
        $this->SetFont('times', 'B', 12);
        $image_file = "/var/www/html/migracion_rrhh/nomina/imagenes/migracion.jpg";
        $altura=8;

        $this->Image($image_file, $x=20, $y=8, $w=35,   $h=35, $type='', $link='', $align='C', $resize=false, $dpi=300, $palign='', $ismask=false, $imgmask=false, $border=0, $fitbox=false, $hidden=false, $fitonpage=false);
        $this->Multicell(210,$altura,'',$border=0,$align='C',$fill=0,$ln=1,$x='0',$y='',$reseth=true,$stretch=0,$ishtml=false,$autopadding=true,$maxh=5,$valign='M',$fitcell=false);
        $this->Multicell(210,9,'Procedimiento de Ascenso 2018',$border=0,$align='C',$fill=0,$ln=1,$x='0',$y=15,$reseth=true,$stretch=0,$ishtml=false,$autopadding=true,$maxh=10,$valign='M',$fitcell=false);
        $this->Multicell(210,9,'Evaluación de Antecedentes',$border=0,$align='C',$fill=0,$ln=1,$x='0',$y='',$reseth=true,$stretch=0,$ishtml=false,$autopadding=true,$maxh=10,$valign='M',$fitcell=false); 
        
    }
    public function Footer()
    {

    }
}
# creamos  un nuevo objeto (MiPDF) utilizando la clase TCPDF
$MiPDF=new MYPDF('P','mm','Letter');
#desactivamos encabezado y pie de página
# el método SetAuthor nos permite incluir el nombre del autor
$MiPDF->SetAuthor('Silvio Orta');
# el método SetTitle nos permite incluir un título
$MiPDF->SetTitle('Procedimiento de Ascenso');
# el método SetDisplayMode nos permite incluir palabras claves
# separadas por espacios y dentro de una misma cadena
$MiPDF->SetDisplayMode('fullpage','two');
# creamos una página en blanco. Incluimos, para esta primera página
# un cambio de orientación respecto a la inicial
$MiPDF->SetMargins(0,45,0, true);

if($estado==1)
{
//        $sql_expediente   = "SELECT cod_expediente_det, cedula,  tipo, fecha, ascenso_puntaje_total "
//                        . " FROM expediente "                        
//                        . " WHERE "
//                        . " fecha=(SELECT MAX(fecha) FROM expediente WHERE tipo=64 AND ascenso_puntaje_total>=71)"
//                        . " AND tipo=64 AND ascenso_puntaje_total>=71";
        
        $sql_expediente   = "SELECT cod_expediente_det, cedula,  tipo, fecha, ascenso_puntaje_total "
                        . " FROM expediente "                        
                        . " WHERE "                        
                        . " tipo=64 AND ascenso_puntaje_total>=71"
                        . " ORDER BY ascenso_puntaje_total DESC"
                        . "  ";
        //$archivo = 'procedimiento_ascenso_aptos.xlsx';
}
else
{
//        $sql_expediente   = "SELECT cod_expediente_det, cedula,  tipo, fecha, ascenso_puntaje_total "
//                        . " FROM expediente "                        
//                        . " WHERE "
//                        . " fecha=(SELECT MAX(fecha) FROM expediente WHERE tipo=64 AND ascenso_puntaje_total>=71)"
//                        . " AND tipo=64 AND ascenso_puntaje_total<71";
        
        $sql_expediente   = "SELECT cod_expediente_det, cedula,  tipo, fecha, ascenso_puntaje_total "
                        . " FROM expediente "                        
                        . " WHERE "                      
                        . " tipo=64 AND ascenso_puntaje_total<71"
                        . " ORDER BY ascenso_puntaje_total DESC";
        //$archivo = 'procedimiento_ascenso_no_aptos.xlsx';
}
//echo $sql_expediente;
//exit;
$res_expediente = $db->query($sql_expediente);
$num =  mysqli_num_rows($res_expediente);
if ($num == 0 ) 
{
    //echo "<script>alert('No hay registros para generar el reporte')</script>";
    //echo "<script>location.href = '../../nomina/paginas/cfg_resumen_funcion.php';</script>";
} 
else 
{
    
    $contador=0;
    while ( $fila_expediente = $res_expediente->fetch_assoc()) 
    {
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

        $ced = $fila_expediente["cedula"];
        $codigo = $fila_expediente["cod_expediente_det"];
        
        $sql1   = "SELECT A.apenom , A.cedula, A.nomposicion_id, A.tipnom, A.fecha_permanencia, A.fecing, A.cm_fecha_notificacion_ingreso, A.suesal, A.foto, "
                . " B.des_car, B.cod_cargo, C.descripcion as departamento "
                        . "FROM nompersonal AS A "
                        . "LEFT JOIN nomcargos AS B ON A.codcargo = B.cod_car "
                        . "LEFT JOIN departamento AS C ON A.IdDepartamento = C.IdDepartamento "
                        . "WHERE A.cedula =  '".$ced."'";
                $res1 = $db->query($sql1);
                $persona = $res1->fetch_object();

                $nombre_apellido  = strtoupper($persona ->apenom);
                $cedula        = $persona ->cedula;
                $cargo         = strtoupper($persona ->des_car);
                $codcargo      = $persona ->cod_cargo;
                $posicion      = $persona ->nomposicion_id;
                $planilla      = $persona ->tipnom;
                $suesal        = $persona ->suesal;
                $departamento        = $persona ->departamento;

                $sql2   = "SELECT a.*,b.des_car,c.nombre FROM expediente a "
                        . "LEFT JOIN nomcargos AS b ON a.ascenso_cargo_nuevo = b.cod_car "
                        . "LEFT JOIN tipo_ascenso AS c ON a.ascenso_nivel_nuevo = c.id "
                        . "WHERE a.cod_expediente_det='".$codigo."'";
                $res2 = $db->query($sql2);
                $expediente = $res2->fetch_object();
                $fecha_resolucion = formato_fecha($expediente ->fecha_resolucion,1);
                $numero_resolucion = $expediente ->numero_resolucion;
                $fecha_inicio = formato_fecha($expediente ->fecha_inicio,1);
                $fecha_inicio_periodo = formato_fecha($expediente ->fecha_inicio_periodo,1);
                $fecha_fin_periodo = formato_fecha($expediente ->fecha_fin_periodo,1);

        $date_1 = new DateTime($persona ->fecing);
        // Todays date
        $date_2 = new DateTime(date('Y-m-d'));

        $difference = $date_2->diff( $date_1 );

        // Echo the as string to display in browser for testing
        $antiguedad=(string)$difference->y;

        $date_1 = new DateTime($persona ->cm_fecha_notificacion_ingreso);
        // Todays date
        $date_2 = new DateTime(date('Y-m-d'));

        $difference = $date_2->diff( $date_1 );

        // Echo the as string to display in browser for testing
        $tiempo_cargo=(string)$difference->y;
        
        $foto              =  $persona ->foto;   
         $foto1             =  '../../nomina/paginas/'.$foto;
        $MiPDF->SetFont('times', 'B', 11);
        $altura=6;
       
         $MiPDF->SetFont('times', '', 9);

        $MiPDF->Multicell(70,$altura,'Institución:','TL',$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

        $MiPDF->Multicell(115,$altura,'Servicio Nacional de Migración','TR',$alineacion='L',$fondo=false,$salto_de_linea=1,$x='',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

       
        $MiPDF->Multicell(70,$altura,'Departamento:','L',$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

        $MiPDF->Multicell(115,$altura, $persona ->departamento,'R',$alineacion='L',$fondo=false,$salto_de_linea=1,$x='',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);
        $MiPDF->Multicell(185,6,'','RL',$alineacion='L',$fondo=false,$salto_de_linea=1,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=6,$alineacion_vertical='M', $fitcell=false);
                       
        $MiPDF->Multicell(185,6,'','TRL',$alineacion='L',$fondo=false,$salto_de_linea=1,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=6,$alineacion_vertical='M', $fitcell=false);
        
        
        $MiPDF->SetFont('times', '', 9);

        $MiPDF->Multicell(70,$altura,'Nombre del Servidor Público:','TL',$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

        $MiPDF->Multicell(115,$altura,$persona ->apenom,'TR',$alineacion='L',$fondo=false,$salto_de_linea=1,$x='',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

       
        $MiPDF->Multicell(70,$altura,'No. de Cédula:','L',$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

        $MiPDF->Multicell(115,$altura, $persona ->cedula,'R',$alineacion='L',$fondo=false,$salto_de_linea=1,$x='',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

       
        $MiPDF->Multicell(70,$altura,'No. de Posición:','L',$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

        $MiPDF->Multicell(115,$altura, $persona ->nomposicion_id,'R',$alineacion='L',$fondo=false,$salto_de_linea=1,$x='',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

       
        $MiPDF->Multicell(70,$altura,'Fecha de Nombramiento Permanente:','L',$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

        $MiPDF->Multicell(115,$altura,formato_fecha($persona ->fecha_permanencia,1),'R',$alineacion='L',$fondo=false,$salto_de_linea=1,$x='',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

        
        $MiPDF->Multicell(70,$altura,'Años de Servicio:','L',$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

        $MiPDF->Multicell(115,$altura, $antiguedad. " Año(s)",'R',$alineacion='L',$fondo=false,$salto_de_linea=1,$x='',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

       $MiPDF->Image($foto1, $x=160, $y=8, $w=35,   $h=35, $type='', $link='', $align='C', $resize=false, $dpi=300, $palign='', $ismask=false, $imgmask=false, $border=1, $fitbox=false, $hidden=false, $fitonpage=false);
        
       
       $MiPDF->Multicell(70,$altura,'Fecha de Acreditación en Carrera Migratoria: ','L',$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='T', $fitcell=false);

        $MiPDF->Multicell(115,$altura, formato_fecha($persona ->cm_fecha_notificacion_ingreso,1),'R',$alineacion='L',$fondo=false,$salto_de_linea=1,$x='',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='T', $fitcell=false);

        
        $MiPDF->Multicell(40,$altura,'Nivel: ','L',$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='T', $fitcell=false);

        $MiPDF->Multicell(45,$altura,$nivel,0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='55',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='T', $fitcell=false);

        $MiPDF->Multicell(45,$altura,'Cargo Actual:',0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='100',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='T', $fitcell=false);

        $MiPDF->Multicell(65,$altura,$persona ->des_car,'R',$alineacion='L',$fondo=false,$salto_de_linea=1,$x='135',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='T', $fitcell=false);

        $MiPDF->Multicell(40,$altura,'Salario Actual: ','L',$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='T', $fitcell=false);

        $MiPDF->Multicell(45,$altura, $persona ->suesal,0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='T', $fitcell=false);


        $MiPDF->Multicell(45,$altura,'Tiempo en el Cargo:',0,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='100',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

        $MiPDF->Multicell(65,$altura, $tiempo_cargo. " Año(s)",'R',$alineacion='L',$fondo=false,$salto_de_linea=1,$x='135',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);
      

        $MiPDF->SetFont('times', '', 9);

        $MiPDF->Multicell(50,$altura,'Nivel a Ascender: ','L',$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

        $MiPDF->Multicell(135,$altura,$expediente ->nombre,'R',$alineacion='L',$fondo=false,$salto_de_linea=1,$x='',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

       
        $MiPDF->Multicell(50,$altura,'Cargo a Ascender:','L',$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

        $MiPDF->Multicell(135,$altura, $expediente ->des_car,'R',$alineacion='L',$fondo=false,$salto_de_linea=1,$x='',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

      
        $MiPDF->Multicell(50,$altura,'Salario a Ascender:','L',$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

        $MiPDF->Multicell(135,$altura,$expediente ->ascenso_salario_nuevo,'R',$alineacion='L',$fondo=false,$salto_de_linea=1,$x='',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);
        
        $MiPDF->Multicell(185,6,'','LR',$alineacion='L',$fondo=false,$salto_de_linea=1,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=6,$alineacion_vertical='M', $fitcell=false);
        

        $MiPDF->SetFont('times', '', 9);
        $altura=6;
        $MiPDF->Multicell(40,$altura,'Antecedentes Vigentes: ','TL',$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=6,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(20,$altura,'','T',$alineacion='L',$fondo=false,$salto_de_linea=0,$x='',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=6,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(20,$altura,'','T',$alineacion='L',$fondo=false,$salto_de_linea=0,$x='',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=6,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(20,$altura,'','T',$alineacion='L',$fondo=false,$salto_de_linea=0,$x='',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=6,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(20,$altura,'','T',$alineacion='L',$fondo=false,$salto_de_linea=0,$x='',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=6,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(30,$altura,'Bajo Investigación:','T',$alineacion='R',$fondo=false,$salto_de_linea=0,$x='',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=6,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(35,$altura,$expediente ->ascenso_investigacion,'TR',$alineacion='C',$fondo=false,$salto_de_linea=1,$x='',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=6,$alineacion_vertical='M', $fitcell=false);
        
         $MiPDF->SetFont('times', '', 9);

        $MiPDF->Multicell(40,$altura,'Evaluación de Desempeño: ','L',$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=6,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(20,$altura,"2016: ".$expediente ->ascenso_desempenio_1_1,$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=6,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(20,$altura,"2016: ".$expediente ->ascenso_desempenio_1_2,$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=6,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(20,$altura,"2017: ".$expediente ->ascenso_desempenio_2_1,$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=6,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(20,$altura,"2017: ".$expediente ->ascenso_desempenio_2_2,$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=6,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(30,$altura,'Porcentaje         40%:',$b,$alineacion='R',$fondo=false,$salto_de_linea=0,$x='',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=6,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(35,$altura,$expediente ->ascenso_desempenio_porcentaje,'R',$alineacion='C',$fondo=false,$salto_de_linea=1,$x='',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=6,$alineacion_vertical='M', $fitcell=false);
        
        $MiPDF->Multicell(40,$altura,'Evaluación de Conducta: ','L',$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=6,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(20,$altura,"2016: ".$expediente ->ascenso_conducta_1,$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=6,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(20,$altura,"2017: ".$expediente ->ascenso_conducta_2,$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=6,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(20,$altura,'',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=6,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(20,$altura, "2018: ".$expediente ->ascenso_conducta_actual,$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=6,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(30,$altura,'30%:',$b,$alineacion='R',$fondo=false,$salto_de_linea=0,$x='',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=6,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(35,$altura,$expediente ->ascenso_conducta_porcentaje,'R',$alineacion='C',$fondo=false,$salto_de_linea=1,$x='',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=6,$alineacion_vertical='M', $fitcell=false);
        
         $MiPDF->Multicell(40,$altura,'Evaluación Participativa: ','L',$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=6,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(20,$altura,'',$b,$alineacion='C',$fondo=false,$salto_de_linea=0,$x='',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=6,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(20,$altura,'',$b,$alineacion='C',$fondo=false,$salto_de_linea=0,$x='',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=6,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(20,$altura,'',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=6,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(20,$altura, "2018: ".$expediente ->ascenso_participativa_actual,$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=6,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(30,$altura,'30%:',$b,$alineacion='R',$fondo=false,$salto_de_linea=0,$x='',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=6,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(35,$altura, $expediente ->ascenso_participativa_porcentaje,'R',$alineacion='C',$fondo=false,$salto_de_linea=1,$x='',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=6,$alineacion_vertical='M', $fitcell=false);
        
         $MiPDF->Multicell(40,$altura,'','L',$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=6,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(20,$altura,'',$b,$alineacion='C',$fondo=false,$salto_de_linea=0,$x='',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=6,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(20,$altura,'',$b,$alineacion='C',$fondo=false,$salto_de_linea=0,$x='',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=6,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(20,$altura,'',$b,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=6,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(20,$altura,'',$b,$alineacion='C',$fondo=false,$salto_de_linea=0,$x='',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=6,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(30,$altura,'Puntos:          100%:',$b,$alineacion='R',$fondo=false,$salto_de_linea=0,$x='',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=6,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(35,$altura,$expediente ->ascenso_puntaje_total,'R',$alineacion='C',$fondo=false,$salto_de_linea=1,$x='',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=6,$alineacion_vertical='M', $fitcell=false);
        
        $MiPDF->Multicell(185,6,'',1,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=6,$alineacion_vertical='M', $fitcell=false);
        
        $MiPDF->Multicell(185,6,'Fundamento legal de ascenso: Ley No. 3 de  22 de febrero de 2008 y Decreto Ejecutivo No. 138 de 4 de mayo de 2015.','RL',$alineacion='L',$fondo=false,$salto_de_linea=1,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(185,6,'','RL',$alineacion='L',$fondo=false,$salto_de_linea=1,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=6,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->SetFillColor(80, 220, 100);
        $MiPDF->SetFont('times', 'B', 11);
        $descripcion="CUMPLE CON LOS REQUISITOS";
        if($expediente ->ascenso_puntaje_total<71)
        {
            $MiPDF->SetFillColor(255,99,71);
            $descripcion="NO CUMPLE CON LOS REQUISITOS";
        }
        
        $MiPDF->Multicell(185,6,$descripcion,'RL',$alineacion='C',$fondo=true,$salto_de_linea=1,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=true,$autopadding=true,$maxh=6,$alineacion_vertical='M', $fitcell=false);
        $MiPDF->Multicell(185,6,'','RLB',$alineacion='L',$fondo=false,$salto_de_linea=1,$x='15',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=6,$alineacion_vertical='M', $fitcell=false);
        
    }
}
ob_end_clean();
$MiPDF->Output('resumen_expediente.pdf','I');
?>