<?php
require_once '../../includes/phpword/PhpWord/Autoloader.php';
date_default_timezone_set('America/Panama');
if(isset($_GET['ficha']) && isset($_GET['tipnom']))
{
	require_once('../../lib/database.php');
	$ficha=$_GET['ficha'];
	$tipnom=$_GET['tipnom'];
	$db = new Database($_SESSION['bd']);

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
		            $dia = $separa[2];
				    $mes = $separa[1];
				    $anio = $separa[0];
		            $f="el dia ".$dia." de ".$meses[$mes-1]." de ".$anio;
		            break;
		        case 3:
		            $separa = explode("-",$fecha);
		            $dia = $separa[2];
				    $mes = $separa[1];
				    $anio = $separa[0];
		            $f="del ".$dia." de ".$meses[$mes-1]." de ".$anio;
		            break;
		        default:
		            break;
		    }
		    return $f;
    }
 	$fecha_hoy=date('d-m-Y-G:i');
 	$f=fechas($fecha_hoy,1);
	$sql = "SELECT a.apenom, a.cedula, a.seguro_social, a.lugarnac, a.fecnac, a.suesal, a.num_decreto, a.fecha_decreto, a.condicion, a.nomposicion_id, b.des_car FROM nompersonal as a, nomcargos as b WHERE a.codcargo=b.cod_car AND  a.ficha='$ficha' AND a.tipnom='$tipnom'";

	$res = $db->query($sql);

	$sql2 = "SELECT dir_emp,ced_rrhh FROM nomempresa";

	$res2 = $db->query($sql2);


	$persona = $res->fetch_object();
	$empresa = $res2->fetch_object();

	$nom  =  $persona ->apenom;
	$nombre   = strtoupper($nom);

	$cedula   = $persona ->cedula; 
	$seguro_social   = $persona ->seguro_social;
	$lugar   = $persona ->lugarnac; 
	$posicion = $persona ->nomposicion_id;
	$condicion = $persona ->condicion;
	
	$f_nac = $persona ->fecnac; 
	$nacimiento=fechas($f_nac,2);
	
	$car = $persona ->des_car; 
	$cargo = strtoupper($car);
	
	$suesal = $persona ->suesal; 
	$numdecreto = $persona ->num_decreto;
    
    $f_decreto = $persona ->fecha_decreto;
	$fecha_decreto=fechas($f_decreto,3);
	
	$dir = $empresa ->dir_emp;
	$director = strtoupper($dir);

	$ced_rh = $empresa ->ced_rrhh;


	\PhpOffice\PhpWord\Autoloader::register();
	\PhpOffice\PhpWord\Settings::loadConfig();

	$templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('plantillas/modelo_ajus_h.docx');

	$templateProcessor->setValue('mensaje', htmlspecialchars($f));
	$templateProcessor->setValue('nombre', htmlspecialchars($nombre));
	$templateProcessor->setValue('cedula', htmlspecialchars($cedula));
	$templateProcessor->setValue('seguro', htmlspecialchars($seguro_social));
	$templateProcessor->setValue('lugar', htmlspecialchars($lugar));
	$templateProcessor->setValue('nacimiento', htmlspecialchars($nacimiento));
	$templateProcessor->setValue('cargo', htmlspecialchars($cargo));
	$templateProcessor->setValue('suesal', htmlspecialchars($suesal));
	$templateProcessor->setValue('num', htmlspecialchars($numdecreto));
	$templateProcessor->setValue('fdecreto', htmlspecialchars($fecha_decreto));
	$templateProcessor->setValue('director', htmlspecialchars($director));
	$templateProcessor->setValue('ced_rh', htmlspecialchars($ced_rh));
	$templateProcessor->setValue('condicion', htmlspecialchars($condicion));
	$templateProcessor->setValue('posicion', htmlspecialchars($posicion));

	$filename = 'Modelo de Ajuste de '.$ficha.'.docx';
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