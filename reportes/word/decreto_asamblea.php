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
	    if (empty($fecha))
	    {
	    	$fecha = date('Y-m-d');
	    }
	    $meses = array("ENERO","FEBRERO","MARZO","ABRIL","MAYO","JUNIO","JULIO","AGOSTO","SEPTIEMBRE","OCTUBRE","NOVIEMBRE","DICIEMBRE");
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
    function validar_decreto($num_decreto)
    {
    	if (empty($num_decreto))
    	{
    		return "____";
    	}else{
    		return $num_decreto;
    	}
    }
	$sql2 = "SELECT * FROM nomempresa";
	$res2 = $db->query($sql2);
	$empresa = $res2->fetch_object();
	
	$sql = "SELECT a.apenom, a.cedula, a.seguro_social, a.lugarnac, a.fecnac, a.suesal,a.num_decreto, 
			a.fecha_decreto,a.condicion,a.nomposicion_id, b.cod_car, b.des_car ,a.fecing, c.cargo_id
			FROM nompersonal AS a 
			
			LEFT JOIN nomposicion AS c ON a.nomposicion_id=c.nomposicion_id
			LEFT JOIN nomcargos AS b ON b.cod_car=c.cargo_id
			WHERE a.ficha='$ficha'";
	$res = $db->query($sql);
	$persona = $res->fetch_object();

	$num_decreto   = validar_decreto($persona ->num_decreto);
	$fecha         = formato_fecha($persona ->fecha_decreto,1);
	$fechaing         = formato_fecha($persona ->fecing,1);
	$nombre        = strtoupper($persona ->apenom);
	$cedula        = $persona ->cedula;
	$cargo         = $persona ->des_car;
	$codigo        = $persona ->cargo_id;
	$suesal        = $persona ->suesal;
	$planilla      = $persona ->plantilla;
	$posicion      = $persona ->nomposicion_id;
	$condicion     = ( (isset($persona ->condicion) )&&(!empty($persona ->condicion)) ) ? $persona ->condicion : "";
	$opcion        = (($condicion == "Interino")&&(!empty($condicion)) ) ? "(Mientras dure la Licencia del Titular)" : "";
	$presidente    = $empresa ->pre_sid;

	\PhpOffice\PhpWord\Autoloader::register();
	\PhpOffice\PhpWord\Settings::loadConfig();

	$templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('plantillas/decreto_asamblea.docx');

	$templateProcessor->setValue('num_decreto', htmlspecialchars($num_decreto));
	$templateProcessor->setValue('fecha1', htmlspecialchars($fecha));
	$templateProcessor->setValue('nombre', htmlspecialchars($nombre));
	$templateProcessor->setValue('cedula', htmlspecialchars($cedula));
	$templateProcessor->setValue('cargo', htmlspecialchars($cargo));
	$templateProcessor->setValue('codigo', htmlspecialchars($codigo));
	$templateProcessor->setValue('sueldo', htmlspecialchars($suesal));
	$templateProcessor->setValue('planilla', htmlspecialchars($tipnom));
	$templateProcessor->setValue('posicion', htmlspecialchars($posicion));
	$templateProcessor->setValue('fecha2', htmlspecialchars($fechaing));
	$templateProcessor->setValue('cond', htmlspecialchars($condicion));
	$templateProcessor->setValue('opcion', htmlspecialchars($opcion));
	$templateProcessor->setValue('presidente', strtoupper(utf8_decode($presidente)));


	$filename = 'Modelo de Decreto '.$ficha.'.docx';
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