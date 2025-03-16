<?php
//-------------------------------------------------
session_start();
//-------------------------------------------------
require_once "../../config/rhexpress_config.php";
//-------------------------------------------------
//-------------------------------------------------
require_once '../../../includes/phpword/PhpWord/Autoloader.php';
date_default_timezone_set('America/Panama');
$id   = ($_REQUEST['id']) ? $_REQUEST['id'] : '' ;
$tipo = ($_REQUEST['tipo']) ? $_REQUEST['tipo'] : '' ;
//-------------------------------------------------
/*
public function meses_v($mes)
{
	$meses = array("enero","febrero","marzo","abril","mayo","junio","julio","agosto","septiembre","octubre","noviembre","diciembre");
	return $meses[$mes-1];
}*/

//-------------------------------------------------
$sql_ = "SELECT *
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

$sql3          = "SELECT apenom FROM nompersonal WHERE cedula = '{$persona ->IdJefe}'";
$res3          = $conexion->query($sql3);
$departamento  = $res3->fetch_object();
$jefe_oirh     = $departamento -> apenom;
$nom           = $persona ->apenom;
$nombre        = strtoupper($nom);
$ficha         = $persona ->ficha;

$planilla      = $persona ->tipnom;
$cedula        = $persona ->cedula; 
$dias          = $persona ->dias; 
$posicion      = $persona ->nomposicion_id;

$car           = $persona ->des_car; 
$cargo         = strtoupper($car);

$suesal        = $persona ->suesal; 
$numdecreto    = $persona ->num_decreto;
$numpartida    = $persona ->partida;
$numpartida    = $persona ->partida;
$dia_inicio    = date('d', strtotime($persona ->fecha_inicio)) ; // resta 6 mes
$mes_inicio    = date('m', strtotime($persona ->fecha_inicio)) ; // resta 6 mes
$year_inicio   = date('Y', strtotime($persona ->fecha_inicio)) ; // resta 6 mes
$dia_fin       = date('d', strtotime($persona ->fecha_fin)) ; // resta 6 mes
$mes_fin       = date('m', strtotime($persona ->fecha_fin)) ; // resta 6 mes
$year_fin      = date('Y', strtotime($persona ->fecha_fin)) ; // resta 6 mes

$f_decreto     = $persona ->fecha_decreto;
$fecha_decreto = $f_decreto;

$dir           = $empresa ->dir_emp;
$nombre_empresa=$empresa->nom_emp;
$director      = strtoupper($dir);

\PhpOffice\PhpWord\Autoloader::register();
\PhpOffice\PhpWord\Settings::loadConfig();

$templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('plantillas/solicitud_vacaciones.docx');

$templateProcessor->setValue('nombre_empresa', htmlspecialchars($nombre_empresa));
$templateProcessor->setValue('nombre', htmlspecialchars($nombre));
$templateProcessor->setValue('nombres', htmlspecialchars($nom));
$templateProcessor->setValue('cedula', htmlspecialchars($cedula));
$templateProcessor->setValue('cargo', htmlspecialchars($cargo));
$templateProcessor->setValue('ficha', htmlspecialchars($ficha));
$templateProcessor->setValue('planilla', htmlspecialchars($planilla));
$templateProcessor->setValue('suesal', htmlspecialchars($suesal));
$templateProcessor->setValue('num', htmlspecialchars($numdecreto));
$templateProcessor->setValue('fecha_hoy', $fecha_hoy);
$templateProcessor->setValue('director', htmlspecialchars($director));
$templateProcessor->setValue('posicion', htmlspecialchars($posicion));
$templateProcessor->setValue('partida', htmlspecialchars($numpartida));
$templateProcessor->setValue('director', htmlspecialchars($director));
$templateProcessor->setValue('unidad_ad', htmlspecialchars($director));
$templateProcessor->setValue('dias', htmlspecialchars($dias));
$templateProcessor->setValue('mes', htmlspecialchars($mes));
$templateProcessor->setValue('year', htmlspecialchars($year));
$templateProcessor->setValue('jefe_oirh', htmlspecialchars($jefe_oirh));
$templateProcessor->setValue('dia_inicio', htmlspecialchars($dia_inicio));
$templateProcessor->setValue('mes_inicio', htmlspecialchars($mes_inicio));
$templateProcessor->setValue('year_inicio', htmlspecialchars($year_inicio));
$templateProcessor->setValue('dia_fin', htmlspecialchars($dia_fin));
$templateProcessor->setValue('mes_fin', htmlspecialchars($mes_fin));
$templateProcessor->setValue('year_fin', htmlspecialchars($year_fin));
$templateProcessor->setValue('dia1', htmlspecialchars($dia_fin));
$templateProcessor->setValue('mes1', htmlspecialchars($mes_fin));
$templateProcessor->setValue('year1', htmlspecialchars($year_fin));
$templateProcessor->setValue('pago1', htmlspecialchars($pago1));
$templateProcessor->setValue('pago2', htmlspecialchars($pago2));
$templateProcessor->setValue('quincena', htmlspecialchars($quincena));

$filename      = 'Solicitud Vacaciones '.$id.'.docx';
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