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
    
		
	$sql1   = "SELECT A.* , B.des_car, B.cod_cargo, C.descrip "
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
        
        $sql2   = "SELECT a.*,b.dias dias_vac FROM expediente a LEFT JOIN periodos_vacaciones b ON a.periodo_vacacion = b.id WHERE a.cod_expediente_det='".$codigo."'";
        $res2 = $db->query($sql2);
		$expediente = $res2->fetch_object();
        $fecha_resolucion = formato_fecha($expediente ->fecha_resolucion,1);
        $numero_resolucion = $expediente ->numero_resolucion;
        $fecha_inicio = formato_fecha($expediente ->fecha_inicio,1);
        $fecha_inicio_periodo = formato_fecha($expediente ->fecha_inicio_periodo,1);
        $fecha_fin_periodo = formato_fecha($expediente ->fecha_fin_periodo,1);
        $dias_vac = $expediente ->dias_vac;
        if ($dias_vac == 15) {
        	$dias_vac = "quince ($dias_vac)";
        }else{
        	$dias_vac = "treinta ($dias_vac)";
        }
        
        //echo $numero_resolucion;
	\PhpOffice\PhpWord\Autoloader::register();
	\PhpOffice\PhpWord\Settings::loadConfig();
        
	$templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('plantillas/resuelto_vacaciones.docx');

	$templateProcessor->setValue('nombre_apellido', htmlspecialchars($nombre_apellido));
        $templateProcessor->setValue('cedula', htmlspecialchars($cedula));
        $templateProcessor->setValue('dias', htmlspecialchars($dias_vac));
        $templateProcessor->setValue('cargo', htmlspecialchars($cargo));
        $templateProcessor->setValue('cod_cargo', htmlspecialchars($codcargo));
        $templateProcessor->setValue('posicion', htmlspecialchars($posicion));
        $templateProcessor->setValue('planilla', htmlspecialchars($planilla));
        $templateProcessor->setValue('salario', htmlspecialchars($suesal));
        
        $templateProcessor->setValue('num_resolucion', htmlspecialchars($numero_resolucion));
        $templateProcessor->setValue('fecha_inicio_periodo', htmlspecialchars($fecha_inicio_periodo));
        $templateProcessor->setValue('fecha_fin_periodo', htmlspecialchars($fecha_fin_periodo));
        $templateProcessor->setValue('fecha_inicio', htmlspecialchars($fecha_inicio));
        $templateProcessor->setValue('fecha_resolucion', htmlspecialchars($fecha_resolucion));
        
        $filename = 'resuelto_vacaciones_expediente.docx';
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