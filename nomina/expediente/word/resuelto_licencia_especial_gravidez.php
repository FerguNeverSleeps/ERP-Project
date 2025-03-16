<?php
if (!isset($_SESSION)) {
  session_start();
  ob_start();
}

require_once('../../../includes/phpword/PhpWord/Autoloader.php');
date_default_timezone_set('America/Panama');
	require_once('../../lib/database.php');        
        $db = new Database($_SESSION['bd']);
	$ced         = $_GET['cedula'];
	$codigo        = $_GET['codigo'];
   
//    echo $cedula; echo " ";
//    echo $codigo;
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
	            $f = $dia." del mes ".$meses[$mes-1]." de ".$anio;
	            break;
	        default:
	            break;
	    }
	    return $f;
    }
    
		
	$sql1   = "SELECT A. * , B.des_car, B.cod_cargo, C.descrip "
                . "FROM nompersonal AS A "
                . "LEFT JOIN nomcargos AS B ON A.codcargo = B.cod_car "
                . "LEFT JOIN nomnivel1 AS C ON A.codnivel1 = C.codorg "
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
        
        $sql2   = "SELECT * FROM expediente  WHERE cod_expediente_det=".$codigo;
        $res2 = $db->query($sql2);
	$expediente = $res2->fetch_object();
        $fecha_resolucion = $expediente ->fecha_resolucion;
        $numero_resolucion = $expediente ->numero_resolucion;
        $fecha_efectividad = $expediente ->fecha_efectividad;
        
        //echo $numero_resolucion;
	\PhpOffice\PhpWord\Autoloader::register();
	\PhpOffice\PhpWord\Settings::loadConfig();
        
	$templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('plantillas/resuelto_licencia_especial_gravidez.docx');

	$templateProcessor->setValue('nombre_apellido', htmlspecialchars($nombre_apellido));
        $templateProcessor->setValue('cedula', htmlspecialchars($cedula));
        $templateProcessor->setValue('cargo', htmlspecialchars($cargo));
        $templateProcessor->setValue('cod_cargo', htmlspecialchars($codcargo));
        $templateProcessor->setValue('posicion', htmlspecialchars($posicion));
        $templateProcessor->setValue('planilla', htmlspecialchars($planilla));
        $templateProcessor->setValue('salario', htmlspecialchars($suesal));
        
        $templateProcessor->setValue('num_resolucion', htmlspecialchars($numero_resolucion));
        $templateProcessor->setValue('fecha_resolucion', htmlspecialchars($fecha_resolucion));
        $templateProcessor->setValue('fecha_efectividad', htmlspecialchars($fecha_efectividad));

	$filename = 'resuelto_licencia_especial_gravidez_'.$codigo.'.docx';
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
        ob_end_clean();
	flush();
	readfile($temp_file_uri);
	unlink($temp_file_uri);
?>