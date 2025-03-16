<?php
if (!isset($_SESSION)) {
  session_start();
  ob_start();
}
error_reporting(E_ALL);
require_once('../../../includes/phpword/PhpWord/Autoloader.php');
date_default_timezone_set('America/Panama');
	require_once('../../lib/database.php');        
        $db = new Database($_SESSION['bd']);
	$ced         = $_GET['cedula'];
	$codigo        = $_GET['codigo'];
   
//    echo $cedula; echo " ";
//    echo $codigo;
	
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
	$sql = "select * from nomempresa";
	$res = $db->query($sql);
	$empresa = $res->fetch_object();
		
	$sql1   = "SELECT A. * , B.des_car, B.cod_cargo, C.descrip "
                . "FROM nompersonal AS A "
                . "LEFT JOIN nomcargos AS B ON A.codcargo = B.cod_car "
                . "LEFT JOIN nomnivel1 AS C ON A.codnivel1 = C.codorg "
                . "WHERE A.cedula =  '".$ced."'";
	$res1 = $db->query($sql1);
	$persona = $res1->fetch_object();
	
	$nombre_empresa  = strtoupper($empresa ->nom_emp);
	$nombre_apellido  = strtoupper($persona ->apenom);
	$cedula        = $persona ->cedula;
	$cargo         = strtoupper($persona ->des_car);
	$codcargo      = $persona ->cod_cargo;
	$posicion      = $persona ->nomposicion_id;
	$departamento  = $persona ->descrip;
	$categoria     = $persona ->codcat;
	
	$sql2   = "SELECT * FROM expediente  WHERE cod_expediente_det=".$codigo;
	$res2 = $db->query($sql2);
	$expediente = $res2->fetch_object();
	$fecha_amonestacion = $expediente ->fecha;
	$fecha_amonestacion_completa = formato_fecha($expediente ->fecha,2);
	$articulo = $expediente ->articulo;
	$numeral = $expediente ->numeral;
	$numeral_descripcion = $expediente ->numeral_descripcion;
	$motivo = $expediente ->descripcion;
	
	//echo $numero_resolucion;
	\PhpOffice\PhpWord\Autoloader::register();
	\PhpOffice\PhpWord\Settings::loadConfig();

	$templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('plantillas/amonestacion_escrita_amx.docx');
	//add image to selector
	$path = "../../imagenes/" . $empresa ->imagen_izq;
	
	//$templateProcessor->setImageValue('logo', $path);
	//$templateProcessor->setImageValue('logo', array('logo' => $path, 'width' => 100, 'height' => 100, 'ratio' => false));
	
	$templateProcessor->setValue('nombre_empresa', htmlspecialchars($nombre_empresa));
	$templateProcessor->setValue('nombre_apellido', htmlspecialchars($nombre_apellido));
	$templateProcessor->setValue('cedula', htmlspecialchars($cedula));
	$templateProcessor->setValue('departamento', htmlspecialchars($departamento));
	$templateProcessor->setValue('posicion', htmlspecialchars($posicion));
	
	$templateProcessor->setValue('fecha_amonestacion', htmlspecialchars($fecha_amonestacion));
	$templateProcessor->setValue('fecha_amonestacion_completa', htmlspecialchars($fecha_amonestacion_completa));
	$templateProcessor->setValue('articulo', htmlspecialchars($articulo));
	$templateProcessor->setValue('numeral', htmlspecialchars($numeral));
	$templateProcessor->setValue('numeral_descripcion', htmlspecialchars(utf8_decode($numeral_descripcion)));
	$templateProcessor->setValue('motivo', htmlspecialchars(utf8_decode($motivo)));

	$filename = 'amonestacion_verbal_amx_'.$codigo.'.docx';
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