<?php
//-------------------------------------------------
session_start();
//-------------------------------------------------
require_once "../../assets/global/tcpdf/tcpdf.php";
//-------------------------------------------------
require_once "../../config/rhexpress_config.php";
//-------------------------------------------------

//-------------------------------------------------
require_once '../../../includes/phpword/PhpWord/Autoloader.php';
date_default_timezone_set('America/Panama');
error_reporting(1);
$id   = ($_REQUEST['id']) ? $_REQUEST['id'] : '' ;
$tipo = ($_REQUEST['tipo']) ? $_REQUEST['tipo'] : '' ;
//-------------------------------------------------
$sql_ = "SELECT * , dep.Descripcion as unidad_ad
    FROM solicitudes_casos as sc 
    LEFt JOIN tipo_justificacion as st on (sc.id_tipo_caso = st.idtipo)
    LEFT JOIN solicitudes_estatus as se on (sc.id_solicitudes_casos_status = se.id_solicitudes_estatus)
    LEFT JOIN nompersonal as np on (sc.cedula = np.cedula)
    LEFT JOIN departamento as dep on (dep.IdDepartamento = np.IdDepartamento)
    WHERE  sc.id_solicitudes_casos = '{$id}'";
$res           = $conexion->query($sql_);

$fecha_hoy     = date('d-m-Y');

$sql2          = "SELECT * FROM nomempresa";

$res2          = $conexion->query($sql2);

$persona       = $res->fetch_object();
$empresa       = $res2->fetch_object();
$fechainicio   = date('Y-m-d', strtotime($persona->fecha_inicio)) ; // resta 6 mes
//print_r($persona);exit;
$nom           = $persona ->apenom;
$nombre        = strtoupper($nom);
$idcarajo      = $persona ->ficha;

$planilla      = $persona ->tipnom;
$cedula        = $persona ->cedula; 
$posicion      = $persona ->nomposicion_id;

$car           = $persona ->des_car; 
$cargo         = strtoupper($car);

$dia_inicio    = date('d', strtotime($persona->fecha_inicio));
$mes_inicio    = date('m', strtotime($persona->fecha_inicio));
$year_inicio   = date('Y', strtotime($persona->fecha_inicio));

$dia_fin       = date('d', strtotime($persona->fecha_fin));
$mes_fin       = date('m', strtotime($persona->fecha_fin));
$year_fin      = date('Y', strtotime($persona->fecha_fin));

$hora_inicio   = $persona->hora_inicio;
$hora_fin      = $persona->hora_fin;
$suesal        = $persona ->suesal; 

$numdecreto    = $persona ->num_decreto;
$numpartida    = $persona ->partida;

$f_decreto     = $persona ->fecha_decreto;
$fecha_decreto = $f_decreto;
$nombre_empresa= $empresa->nom_emp;
$dir           = $empresa ->dir_emp;
$director      = strtoupper($dir);
$unidad_ad     = strtoupper($persona->unidad_ad);

\PhpOffice\PhpWord\Autoloader::register();
\PhpOffice\PhpWord\Settings::loadConfig();

$templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('plantillas/solicitud_permiso.docx');

$templateProcessor->setValue('nombre', htmlspecialchars($nombre));
$templateProcessor->setValue('nombre_empresa', htmlspecialchars($nombre_empresa));
$templateProcessor->setValue('cedula', htmlspecialchars($cedula));
$templateProcessor->setValue('cargo', htmlspecialchars($cargo));
$templateProcessor->setValue('ficha', htmlspecialchars($idcarajo));
$templateProcessor->setValue('planilla', htmlspecialchars($planilla));
$templateProcessor->setValue('suesal', htmlspecialchars($suesal));
$templateProcessor->setValue('num', htmlspecialchars($numdecreto));
$templateProcessor->setValue('fecha_hoy', htmlspecialchars($fecha_hoy));
$templateProcessor->setValue('director', htmlspecialchars($director));
$templateProcessor->setValue('posicion', htmlspecialchars($posicion));
$templateProcessor->setValue('partida', htmlspecialchars($numpartida));
$templateProcessor->setValue('director', htmlspecialchars($director));
$templateProcessor->setValue('unidad_ad', htmlspecialchars($unidad_ad));
$templateProcessor->setValue('dia', htmlspecialchars($dia));
$templateProcessor->setValue('mes', htmlspecialchars($director));
$templateProcessor->setValue('year', htmlspecialchars($director));
$templateProcessor->setValue('dia_inicio', htmlspecialchars($dia_inicio));
$templateProcessor->setValue('mes_inicio', htmlspecialchars($mes_inicio));
$templateProcessor->setValue('year_inicio', htmlspecialchars($year_inicio));
$templateProcessor->setValue('hora_inicio', htmlspecialchars($hora_inicio));
$templateProcessor->setValue('dia_fin', htmlspecialchars($dia_fin));
$templateProcessor->setValue('mes_fin', htmlspecialchars($mes_fin));
$templateProcessor->setValue('year_fin', htmlspecialchars($year_fin));
$templateProcessor->setValue('hora_fin', htmlspecialchars($hora_fin));

$filename = 'Solicitud Permiso '.$idcarajo.'.docx';
$temp_file_uri = tempnam('', $filename);
$templateProcessor->saveAs($temp_file_uri);


//The following offers file to user on client side: deletes temp version of file
header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename='.$filename);
header('Content-Transfer-Encoding: binary');
header('Expires: 0');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Pragma: public');
header('Content-Length: ' . filesize($temp_file_uri));
flush();
readfile($temp_file_uri);
unlink($temp_file_uri);

?>