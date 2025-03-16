<?php
require_once '../../includes/phpword/PhpWord/Autoloader.php';
date_default_timezone_set('America/Panama');
	require_once('../../lib/database.php');
	$ficha         = $_GET['ficha'];
	$tipnom        = $_GET['tipnom'];
	$tipo_contrato = $_GET['tipo_contrato'];

	$db = new Database($_SESSION['bd']);
	error_reporting(E_ALL);
    function formato_fecha($fecha,$formato)
    {
	    $meses = array("ENERO","FEBRERO","MARZO","ABRIL","MAYO","JUNIO","JULIO","AGOSTO","SEPTIEMBRE","OCTUBRE","NOVIEMBRE","DICIEMBRE");
        $separa = explode("-",$fecha);
        $dia = $separa[2];
	    $mes = $separa[1];
	    $anio = $separa[0];
	    switch ($formato)
	    {
	        case 1:
	            $f = "el dia ".$dia." de ".$meses[$mes-1]." de ".$anio;
	            break;
	        case 2:
	            $f = $dia." de ".$meses[$mes-1]." de ".$anio;
	            break;
	        default:
	            break;
	    }
	    return $f;
    }
    function condicion($codicion)
    {
    	switch ($codicion) {
    		case 'Titular':
    			$c = array('Titular' => 'xxx', 'Interino' => '___','Transitorio' => '___');
    			break;
    		case 'Interino':
    			$c = array('Titular' => '___', 'Interino' => 'xxx','Transitorio' => '___');
    			break;
    		case 'Transitorio':
    			$c = array('Titular' => '___', 'Interino' => '___','Transitorio' => 'xxx');
    			break;
    		default:
    			$c = array('Titular' => '_', 'Interino' => '_','Transitorio' => '_');
    			break;
    	}
    	return $c;
    }
	$sql2 = "SELECT * FROM nomempresa";
	$res2 = $db->query($sql2);
	$empresa = $res2->fetch_object();
	
	$sql = "SELECT a.apenom, a.cedula, a.seguro_social, a.lugarnac, a.fecnac, a.suesal,a.num_decreto, 
			a.fecha_decreto,a.condicion,a.nomposicion_id, a.tipo_empleado, a.codcargo, b.des_car,d.nombre AS provincia
			FROM nompersonal AS a 
			LEFT JOIN nomcargos AS b ON a.codcargo=b.cod_car
			LEFT JOIN nomposicion AS c ON a.nomposicion_id=c.nomposicion_id
			LEFT JOIN provincia AS d ON a.provincia=d.id_provincia
			WHERE a.ficha='$ficha'";
	$res = $db->query($sql);
	$persona = $res->fetch_object();

	$fecha         = formato_fecha(date('Y-m-d'),2);
	$nombre        = strtoupper($persona ->apenom);
	$seguro_social = $persona ->seguro_social;
	$dir_rrhh      = strtoupper($empresa ->ger_rrhh);
	$ced_rrhh      = $empresa ->ced_rrhh;
	$nom_empresa   = strtoupper($empresa ->nom_emp);
	$direccion     = $empresa ->dir_emp;
	$rif           = strtoupper($empresa ->rif);
	$num_patronal  = strtoupper($empresa ->numero_patronal);

	\PhpOffice\PhpWord\Autoloader::register();
	\PhpOffice\PhpWord\Settings::loadConfig();

	$templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('plantillas/carta_estado_gravidez.docx');

	$templateProcessor->setValue('fecha', htmlspecialchars($fecha));
	$templateProcessor->setValue('nombre', htmlspecialchars($nombre));
	$templateProcessor->setValue('nom_empresa', htmlspecialchars($nom_empresa));
	$templateProcessor->setValue('direccion', htmlspecialchars($direccion));
	$templateProcessor->setValue('rif', htmlspecialchars($rif));
	$templateProcessor->setValue('numero_patronal', htmlspecialchars($num_patronal));
	$templateProcessor->setValue('seguro_social', htmlspecialchars($seguro_social));
	$templateProcessor->setValue('dir_rrhh', utf8_decode($dir_rrhh));
	$templateProcessor->setValue('ced_rrhh', htmlspecialchars($ced_rrhh));

	$filename = 'Carta Estado de Gravidez '.$ficha.'.docx';
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