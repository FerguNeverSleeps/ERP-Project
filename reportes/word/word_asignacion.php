<?php
require_once '../../includes/phpword/PhpWord/Autoloader.php';
date_default_timezone_set('America/Panama');
if(isset($_GET['id']))
{
	require_once('../../ginteven/conexion.php');
	$id=$_GET['id'];
	
	//$db = new Database($_SESSION['bd']);

	error_reporting(E_ALL);
    function fechas($fecha,$formato)
    {
		    $meses = array("ENERO","FEBRERO","MARZO","ABRIL","MAYO","JUNIO","JULIO","AGOSTO","SEPTIEMBRE","OCTUBRE","NOVIEMBRE","DICIEMBRE");
		    switch ($formato)
		    {
		        case 1:
		        	$separa = explode("-",$fecha);
		            $dia = $separa[0];
				    $mes = $separa[1];
				    $anio = $separa[2];
				    $hora = $separa[3]; 
		            $separa2 = explode(":",$hora);
		            $numhora = $separa2[0];
		            if($numhora<12)
		            {
		            	$menj="mañana";
		            }
		            else
		            {
		            	$menj="tarde";
		            }
		            $f=$hora." de la ".$menj." del dia ".$dia." del mes de ".$meses[$mes-1]." de ".$anio;
		            break;
		        case 2:
		            $separa = explode("-",$fecha);
		            $dia = $separa[0];
				    $mes = $separa[1];
				    $anio = $separa[2];
		            $f=$dia." dias del mes de ".$meses[$mes-1]." de ".$anio;
		            break;
		        case 3:
		            $separa = explode("-",$fecha);
		            $dia = $separa[2];
				    $mes = $separa[1];
				    $anio = $separa[0];
		            $f="DEL ".$dia." DE ".$meses[$mes-1]." DE ".$anio;
		            break;
		        default:
		            break;
		    }
		    return $f;
    }
 	$fecha_hoy=date('d-m-Y');
 	$f=fechas($fecha_hoy,2);
	/*
	$sql = "SELECT a.apenom, a.cedula, a.suesal, a.num_decreto, a.tipnom, a.fecha_decreto, a.ficha, a.nomposicion_id, b.des_car,c.partida FROM nompersonal as a, nomcargos as b, nomposicion as c WHERE a.codcargo=b.cod_car AND a.nomposicion_id=c.nomposicion_id AND  a.ficha='$ficha' AND a.tipnom='$tipnom'";
*/
	$sql = "SELECT nombre_institucion,director FROM param_ws WHERE usuario = 'admin'";
	$res = mysql_query($sql);
	$institucion = mysql_fetch_array($res);
		$institucion = utf8_encode($institucion["nombre_institucion"]);
		$director = utf8_encode($institucion["director"]);

	$sql = "SELECT IdEmpleado,IdDepartamento FROM empleado_cargo WHERE ID='$id'";
	$res = mysql_query($sql);
	$empleado = mysql_fetch_array($res);
		$ide = $empleado["IdEmpleado"];
		$idd = $empleado["IdDepartamento"];

	$sql2 = "SELECT ApellidoPaterno,PrimerNombre,Cedula FROM empleado WHERE IdEmpleado='$ide'";
	$res2 = mysql_query($sql2);
	$persona = mysql_fetch_array($res2);
		$cedula = $persona["Cedula"];
		$nombre = $persona["ApellidoPaterno"].' '.$persona["PrimerNombre"];
	
	$sql3 = "SELECT Posicion FROM posicionempleado WHERE IdEmpleado='$ide'";
	$res3 = mysql_query($sql3);
	$pos = mysql_fetch_array($res3);
		$posicion = $pos["Posicion"];
//------------------------------------------------------------------------------------------------------------
//Departamento donde se le esta asignando
	$sql4 = "SELECT Descripcion,IdJefe FROM departamento WHERE IdDepartamento='$idd'";
	$res4 = mysql_query($sql4);
	$dep = mysql_fetch_array($res4);
		$departamento = $dep["Descripcion"];
		$idjefe = $dep["IdJefe"];
//------------------------------------------------------------------------------------------------------------
// Jefe del Departamento donde se le esta asignando
	$sql5 = "SELECT ApellidoPaterno,PrimerNombre FROM empleado WHERE useruid='$idjefe'";
	$res5 = mysql_query($sql5);
	$jef = mysql_fetch_array($res5);
		$jefe_dpto = $jef["ApellidoPaterno"].' '.$jef["PrimerNombre"];
//------------------------------------------------------------------------------------------------------------

	\PhpOffice\PhpWord\Autoloader::register();
	\PhpOffice\PhpWord\Settings::loadConfig();

	$templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('plantillas/asignacion_ginteven.docx');

	$templateProcessor->setValue('nombre', htmlspecialchars($nombre));
	$templateProcessor->setValue('cedula', htmlspecialchars($cedula));
	$templateProcessor->setValue('posicion', htmlspecialchars($posicion));
	$templateProcessor->setValue('factual', htmlspecialchars($f));
	$templateProcessor->setValue('director', htmlspecialchars($director));
	$templateProcessor->setValue('institucion', htmlspecialchars($institucion));
	$templateProcessor->setValue('jefe', htmlspecialchars(utf8_encode($jefe_dpto)));
	$templateProcessor->setValue('dpto', htmlspecialchars(utf8_encode($departamento)));

	$filename = 'Asignacion.docx';
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
}
?>