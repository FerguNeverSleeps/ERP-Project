<?php
require_once '../../includes/phpword/PhpWord/Autoloader.php';
date_default_timezone_set('America/Panama');
	require_once('../../lib/database.php');
	$ficha    = $_POST['ficha'];
	$tipnom   = $_POST['tipnom'];
	$fecha    = $_POST['fecha'];
	$quincena = $_POST['quincena']." de ".formato_fecha($fecha,4);

	$db = new Database($_SESSION['bd']);
	error_reporting(E_ALL);
	function ano($fecha)
	{
		$separa = explode("-",$fecha);
	    return $separa[0];
	}
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
	            $f = $dia." de ".strtolower($meses[$mes-1])." de ".$anio;
	            break;
	        case 2:
	            $f = $dia." del mes ".strtolower($meses[$mes-1])." de ".$anio;
	            break;
	        case 3:
	            $f = $dia." de ".strtolower($meses[$mes-1]);
	            break;
	        case 4:
	            $f = strtolower($meses[$mes-1])." de ".$dia;
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
			a.fecha_decreto,a.tipo_empleado,a.nomposicion_id, a.codcargo, b.des_car 
			FROM nompersonal AS a 
			LEFT JOIN nomcargos AS b ON a.codcargo=b.cod_car
			LEFT JOIN nomposicion AS c ON a.nomposicion_id=c.nomposicion_id
			WHERE a.ficha='$ficha'";
	$res = $db->query($sql);
	$persona = $res->fetch_object();

	$fecha         = formato_fecha($persona ->fecha_decreto,1);
	$codigo        = $persona ->num_decreto."-".ano($persona ->fecha_decreto);
	$contralor     = $empresa ->contralor;
	$mes           = formato_fecha($persona ->fecha_decreto,3);
	$num_decreto   = $persona ->num_decreto;
	$tipo_empleado = $persona ->tipo_empleado;
	$quinc         = $persona ->codcargo;
	$sueldo        = $persona ->suesal;
	$presidente    = $empresa ->pre_sid;
	$jefe_planilla = $empresa ->jefe_planilla;

	\PhpOffice\PhpWord\Autoloader::register();
	\PhpOffice\PhpWord\Settings::loadConfig();

	$templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('plantillas/nota_contraloria_asamblea.docx');

	$templateProcessor->setValue('fecha', htmlspecialchars($fecha));
	$templateProcessor->setValue('codigo', htmlspecialchars($codigo));
	$templateProcessor->setValue('contralor', strtoupper(utf8_decode($contralor)));
	$templateProcessor->setValue('mes', htmlspecialchars($mes));
	$templateProcessor->setValue('num_decreto1', htmlspecialchars($num_decreto));
	$templateProcessor->setValue('tipo_empleado', htmlspecialchars($tipo_empleado));
	$templateProcessor->setValue('quincena', htmlspecialchars($quincena));
	$templateProcessor->setValue('num_decreto2', htmlspecialchars($num_decreto));
	$templateProcessor->setValue('sueldo', htmlspecialchars($sueldo));
	$templateProcessor->setValue('presidente', htmlspecialchars($presidente));
	$templateProcessor->setValue('jefe_planilla', htmlspecialchars($jefe_planilla));


	$filename = 'Modelo de Nota de Contraloria '.$ficha.'.docx';
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