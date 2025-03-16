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
                case 3:
	            $f = $dia." de ".$meses[$mes-1]." de ".$anio;
	            break;
	        default:
	            break;
	    }
	    return $f;
    }
    
		
	$sql1   = "SELECT A. * , B.des_car, B.cod_cargo, B.cod_car, C.descrip "
                . "FROM nompersonal AS A "
                . "LEFT JOIN nomcargos AS B ON A.codcargo = B.cod_car "
                . "LEFT JOIN nomnivel1 AS C ON A.codnivel1 = C.codorg "
                . "WHERE A.cedula =  '".$ced."'";
        $res1 = $db->query($sql1);
	$persona = $res1->fetch_object();
	
	$nombre_apellido  = strtoupper($persona ->apenom);
        $nombre  = strtoupper($persona ->nombres)." ".strtoupper($persona ->nombres2);
        $apellido  = strtoupper($persona ->apellidos)." ".strtoupper($persona ->apellido_materno);
	$cedula        = $persona ->cedula;
	$cargo         = strtoupper($persona ->des_car);
	$codcargo      = $persona ->cod_car;
	$posicion      = $persona ->nomposicion_id;
        
        $sql2   = "SELECT * FROM expediente  WHERE cod_expediente_det=".$codigo;
        $res2 = $db->query($sql2);
	$expediente = $res2->fetch_object();
        $fecha_resolucion = formato_fecha($expediente ->cm_fecha_resolucion,3);
        $numero_resolucion = $expediente ->cm_numero_resolucion;
        $tipo_proceso = $expediente ->cm_tipo_proceso;
        $proceso="Especial de Ingreso";
        if($tipo_proceso=='B')
        {
            $proceso="de Ingreso Ordinario";
        }
        
        //echo $numero_resolucion;
	\PhpOffice\PhpWord\Autoloader::register();
	\PhpOffice\PhpWord\Settings::loadConfig();
        
	$templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('plantillas/acreditacion_carrera_migratoria.docx');

	$templateProcessor->setValue('nombre_apellido', htmlspecialchars($nombre_apellido));
        $templateProcessor->setValue('nombre', htmlspecialchars($nombre));
        $templateProcessor->setValue('apellido', htmlspecialchars($apellido));
        $templateProcessor->setValue('cedula', htmlspecialchars($cedula));
        $templateProcessor->setValue('cargo', htmlspecialchars($cargo));
        $templateProcessor->setValue('cod_cargo', htmlspecialchars($codcargo));
        $templateProcessor->setValue('posicion', htmlspecialchars($posicion));
        
        $templateProcessor->setValue('numero_resolucion', htmlspecialchars($numero_resolucion));
        $templateProcessor->setValue('fecha_resolucion', htmlspecialchars($fecha_resolucion));
        $templateProcessor->setValue('proceso', htmlspecialchars($proceso));

	$filename = 'acreditacion_carrera_migratoria_'.$codigo.'.docx';
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