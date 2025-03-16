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
	    $meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
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
 
		
	$sql1   = "SELECT A.apenom,A.cedula ,B.fecha,B.seccion_anterior, B.tipo_estudio, B.seccion_nueva, B.nivel_actual, 
                    B.funcion_anterior, B.cargo_estructura, B.funcion_nueva, B.cargo_funcion, B.institucion_anterior, B.concepto, C.descrip "
                . "FROM nompersonal AS A "
                . "LEFT JOIN expediente AS B ON A.cedula = B.cedula "
                . "LEFT JOIN nomnivel1 AS C ON A.codnivel1 = C.codorg "
                . "WHERE A.cedula =  '".$ced."' AND B.cod_expediente_det='".$codigo."'";
        $res1 = $db->query($sql1);
	$persona = $res1->fetch_object();
	$fecha            = $persona ->fecha;
	$apenom           = strtoupper($persona ->apenom);
	$cedula           = $persona ->cedula;
	$sucursal         = strtoupper($persona ->descrip);
	$seccion_anterior = $persona ->seccion_anterior;
	$tipo_estudio     = $persona ->tipo_estudio;
	$seccion_nueva    = $persona ->seccion_nueva;
    $nivel_actual     = $persona ->nivel_actual;
    $funcion_anterior = $persona ->funcion_anterior;
    $cargo_estructura = $persona ->cargo_estructura;
    $funcion_nueva    = $persona ->funcion_nueva;
    $cargo_funcion    = $persona ->cargo_funcion;
    $institucion_anterior   = $persona ->institucion_anterior;
    $concepto   = $persona ->concepto;
    
    //echo $numero_resolucion;
	\PhpOffice\PhpWord\Autoloader::register();
	\PhpOffice\PhpWord\Settings::loadConfig();
        
	$templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('plantillas/formulario_entrega_kit.docx');

	$templateProcessor->setValue('fecha_doc', htmlspecialchars( date('d/m/Y',strtotime($fecha) ) ) );
	$templateProcessor->setValue('apenom', htmlspecialchars($apenom));
    $templateProcessor->setValue('sucursal', htmlspecialchars($sucursal));
    
    $templateProcessor->setValue('seccion_anterior', htmlspecialchars($seccion_anterior));
    $templateProcessor->setValue('tipo_estudio', htmlspecialchars($tipo_estudio));
    $templateProcessor->setValue('seccion_nueva', htmlspecialchars($seccion_nueva));
    $templateProcessor->setValue('nivel_actual', htmlspecialchars($nivel_actual));
    $templateProcessor->setValue('funcion_anterior', htmlspecialchars($funcion_anterior));
    $templateProcessor->setValue('cargo_estructura', htmlspecialchars($cargo_estructura));
    $templateProcessor->setValue('funcion_nueva', htmlspecialchars($funcion_nueva));
    $templateProcessor->setValue('cargo_funcion', htmlspecialchars($cargo_funcion));
    $templateProcessor->setValue('institucion_anterior', htmlspecialchars($institucion_anterior));
    $templateProcessor->setValue('concepto', htmlspecialchars($concepto));

    $filename = 'formulario_entrega_kit.docx';
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