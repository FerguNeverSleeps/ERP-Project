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
	
	$sql = "SELECT a.apenom, a.cedula, a.suesal, a.num_decreto, a.tipnom, a.fecha_decreto, a.ficha, a.nomposicion_id, b.des_car,c.partida FROM nompersonal as a, nomcargos as b, nomposicion as c WHERE a.codcargo=b.cod_car AND a.nomposicion_id=c.nomposicion_id AND  a.ficha='$ficha' AND a.tipnom='$tipnom'";

	$res = $db->query($sql);

	$sql2 = "SELECT dir_emp,ced_rrhh FROM nomempresa";

	$res2 = $db->query($sql2);


	$persona = $res->fetch_object();
	$empresa = $res2->fetch_object();

	$nom  =  $persona ->apenom;
	$nombre   = strtoupper($nom);
	$idcarajo  =  $persona ->ficha;

	$planilla   = $persona ->tipnom;
	$cedula   = $persona ->cedula; 
	$posicion = $persona ->nomposicion_id;
	
	$car = $persona ->des_car; 
	$cargo = strtoupper($car);
	
	$suesal = $persona ->suesal; 
	$numdecreto = $persona ->num_decreto;
	$numpartida = $persona ->partida;
    
    $f_decreto = $persona ->fecha_decreto;
	$fecha_decreto=fechas($f_decreto,3);
	
	$dir = $empresa ->dir_emp;
	$director = strtoupper($dir);

	\PhpOffice\PhpWord\Autoloader::register();
	\PhpOffice\PhpWord\Settings::loadConfig();

	$templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('plantillas/nombramiento_m.docx');

	$templateProcessor->setValue('nombre', htmlspecialchars($nombre));
	$templateProcessor->setValue('cedula', htmlspecialchars($cedula));
	$templateProcessor->setValue('cargo', htmlspecialchars($cargo));
	$templateProcessor->setValue('ficha', htmlspecialchars($idcarajo));
	$templateProcessor->setValue('planilla', htmlspecialchars($planilla));
	$templateProcessor->setValue('suesal', htmlspecialchars($suesal));
	$templateProcessor->setValue('num', htmlspecialchars($numdecreto));
	$templateProcessor->setValue('fdecreto', htmlspecialchars($fecha_decreto));
	$templateProcessor->setValue('director', htmlspecialchars($director));
	$templateProcessor->setValue('posicion', htmlspecialchars($posicion));
	$templateProcessor->setValue('factual', htmlspecialchars($f));
	$templateProcessor->setValue('partida', htmlspecialchars($numpartida));
	$templateProcessor->setValue('director', htmlspecialchars($director));

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