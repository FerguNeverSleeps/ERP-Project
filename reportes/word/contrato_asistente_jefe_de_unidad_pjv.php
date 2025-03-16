<?php
require_once '../../includes/phpword/PhpWord/Autoloader.php';
date_default_timezone_set('America/Panama');
    include('../../nomina/fpdf/numerosALetras.class.php');
	require_once('../../lib/database.php');
	$ficha         = $_GET['ficha'];
	$tipnom        = $_GET['tipnom'];
	$tipo_contrato = $_GET['tipo_contrato'];
    $n = new numerosALetras();
	//$montoLetras=$n->convertir($monto);
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
	            $f = $dia." dias de ".$meses[$mes-1]." de ".$anio;
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
	
	$sql = "SELECT a.apenom, a.cedula as ced_trabajador,a.sexo,a.sueldopro,a.estado_civil, a.seguro_social, a.lugarnac, a.fecnac,
	 a.suesal,a.num_decreto,a.fecha_decreto,a.condicion,a.nomposicion_id, a.tipo_empleado, a.codcargo,
	 b.des_car,d.nombre AS provincia,a.inicio_periodo as ini,a.fin_periodo as fin,e.descrip as donde,a.direccion
	FROM nompersonal AS a 
	LEFT JOIN nomcargos AS b ON a.codcargo=b.cod_car
	LEFT JOIN nomposicion AS c ON a.nomposicion_id=c.nomposicion_id
	LEFT JOIN provincia AS d ON a.provincia=d.id_provincia
	INNER JOIN nomnivel1 AS e ON  a.codnivel1=e.codorg
	WHERE a.ficha='$ficha'";
	$res = $db->query($sql);	
	$persona = $res->fetch_object();
	
//SELECCIONAR FAMILIARES
	$sql3="SELECT e.apellido as apellido_fam,e.nombre as nombre_fam,f.descrip as parentesco_fam
	FROM nompersonal AS a 
	LEFT JOIN nomfamiliares as e ON e.ficha=a.ficha		
	LEFT JOIN nomparentescos AS f ON f.codorg=e.codpar
	WHERE a.ficha='$ficha' and f.codorg in (1,2,3,5,6,7,8,9)";
	$res3=$db->query($sql3);
	//$familiaempleado=$res3->fetch_object();
	
	$familiares='';
	while($row = mysqli_fetch_array($res3))
	{
		$familiares .= $row['nombre_fam'].' '.$row['apellido_fam'].' '.$row['parentesco_fam'].', ';
	}
	$familiares .= '..';
	$familiares = str_replace(', ..', '.', $familiares);

	$sql3="SELECT e.apellido as apellido_padre,e.nombre as nombre_padre,f.descrip as parentesco_padre
	FROM nompersonal AS a 
	LEFT JOIN nomfamiliares as e ON e.ficha=a.ficha		
	LEFT JOIN nomparentescos AS f ON f.codorg=e.codpar
	WHERE a.ficha='$ficha' and f.codorg=2";
	$res3=$db->query($sql3);
	$familiapadre=$res3->fetch_object();

	$sql4="SELECT e.apellido as apellido_madre,e.nombre as nombre_madre,f.descrip as parentesco_madre
	FROM nompersonal AS a 
	LEFT JOIN nomfamiliares as e ON e.ficha=a.ficha		
	LEFT JOIN nomparentescos AS f ON f.codorg=e.codpar
	WHERE a.ficha='$ficha' and f.codorg=1";
	$res4=$db->query($sql4);
	$familiamadre=$res4->fetch_object();

	$sql5="SELECT e.apellido as apellido_hermana,e.nombre as nombre_hermana,f.descrip as parentesco_hermana
	FROM nompersonal AS a 
	LEFT JOIN nomfamiliares as e ON e.ficha=a.ficha		
	LEFT JOIN nomparentescos AS f ON f.codorg=e.codpar
	WHERE a.ficha='$ficha' and f.codorg=8";
	$res5=$db->query($sql5);
	$familiahermana=$res5->fetch_object();

	$sql6="SELECT e.apellido as apellido_hijo,e.nombre as nombre_hijo,f.descrip as parentesco_hijo
	FROM nompersonal AS a 
	LEFT JOIN nomfamiliares as e ON e.ficha=a.ficha		
	LEFT JOIN nomparentescos AS f ON f.codorg=e.codpar
	WHERE a.ficha='$ficha' and f.codorg=3";
	$res6=$db->query($sql6);
	$familiahijos=$res6->fetch_object();

	$sql7="SELECT e.apellido as apellido_conyugue,e.nombre as nombre_conyugue,f.descrip as parentesco_conyugue
        FROM nompersonal AS a 
        LEFT JOIN nomfamiliares as e ON e.ficha=a.ficha         
        LEFT JOIN nomparentescos AS f ON f.codorg=e.codpar
        WHERE a.ficha='$ficha' and f.codorg=4";
        $res7=$db->query($sql7);
        $familiaconyuge=$res7->fetch_object();

    $montoLetras=$n->convertir($monto);
//calculando la edad
    $fecnaca=$persona->fecnac;
    $edad3=explode("-",$fecnaca);
 
	$var=1;
	if( strtotime( date("Y-m-d") ) > strtotime( date("Y-m-d",strtotime( date("Y").'-'.$edad3[1].'-'.$edad3[2] )) ) )
		$var=0;
    $fecnac=(date("Y")-$edad3[0])-$var;

	$direccion     = $persona->direccion;

    $fecnac=(date("Y")-$edad3[0]);
    $fecha1         = formato_fecha(date('Y-m-d'),1);
    $fecha2         = formato_fecha(date('Y-m-d'),2);
    $nombre        = strtoupper($persona ->apenom);
	$des_car =$persona->des_car;
	$ced_trabajador=$persona->ced_trabajador;
	$seguro_social = $persona ->seguro_social;
    $dir_rrhh      = strtoupper($empresa ->ger_rrhh);
	$sexo =$persona->sexo;
	$donde=$persona->donde;
	
    $estado_civil=$persona->estado_civil;
    $lugarnac=$persona->lugarnac;
	if ($lugarnac=="Panamá") {
		$lugarnac="Panameña";
	}
    $sueldopro=$persona->sueldopro;
    $montoLetras=$n->convertir($sueldopro);
	$ced_rrhh      = $empresa ->ced_rrhh;
	$nombre_padres   = $familiapadre->nombre_padre;
	$apellido_padre   = $familiapadre->apellido_padre;
	$parentesco_padre   = $familiapadre->parentesco_padre;
	/*if (empty($parentesco_padre)) {
		$nombre_padres="N/A";
		$apellido_padre="N/A";
	}*/
	$nombre_madre   = $familiamadre->nombre_madre;
	$apellido_madre   = $familiamadre->apellido_madre;
	$parentesco_madre   = $familiamadre->parentesco_madre;
	/*if (empty($parentesco_madre)) {
		$nombre_madre="N/A";
		$apellido_madre="N/A";
	}*/
	$nombre_hermana   = $familiahermana->nombre_hermana;
	$apellido_hermana   = $familiahermana->apellido_hermana;
	$parentesco_hermana   = $familiahermana->parentesco_hermana;
	/*if (empty($parentesco_hermana)) {
		$nombre_hermana="N/A";
		$apellido_hermana="N/A";
	}*/
	$nombre_hijo   = $familiahijos->nombre_hijo;
	$apellido_hijo   = $familiahijos->apellido_hijo;
	$parentesco_hijo   = $familiahijos->parentesco_hijo;
	/*if (empty($parentesco_hijo)) {
		$nombre_hijo="N/A";
		$apellido_hijo="N/A";
	}*/

	$nombre_conyugue   = $familiaconyuge->nombre_conyugue;
        $apellido_conyugue   = $familiaconyuge->apellido_conyugue;
        $parentesco_conyugue   = $familiaconyuge->parentesco_conyugue;  

	$ini=$persona->ini;
	if ($ini) {
		$ini2=explode("-",$ini);
		$ini=" ".$ini2[2]." de ".$ini2[1]."de ".$ini2[0];
	}
	$fin=$persona->fin;
	if ($fin) {
		$fin2=explode("-",$fin);
		$fin=" hasta el ".$fin2[2]." de ".$fin2[1]." de ".$fin2[0];
	}
	$direccionempresa     = $empresa ->dir_emp;
	// $rif           = strtoupper($empresa ->rif);
	// $num_patronal  = strtoupper($empresa ->numero_patronal);

	\PhpOffice\PhpWord\Autoloader::register();
	\PhpOffice\PhpWord\Settings::loadConfig();

	$templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('plantillas/contrato_asistente_jefe_de_unidad_pjv.docx');

    $templateProcessor->setValue('fecha1', htmlspecialchars($fecha1));
    $templateProcessor->setValue('fecha2', htmlspecialchars($fecha2));
    $templateProcessor->setValue('nombre', htmlspecialchars($nombre));
    $templateProcessor->setValue('montoLetras', htmlspecialchars($montoLetras));
	$templateProcessor->setValue('sueldopro', htmlspecialchars($sueldopro));
    $templateProcessor->setValue('direccion', htmlspecialchars($direccion));
 $templateProcessor->setValue('direccionempresa', htmlspecialchars($direccionempresa));
    $templateProcessor->setValue('des_car', htmlspecialchars($des_car));
    $templateProcessor->setValue('sexo', htmlspecialchars($sexo));
    $templateProcessor->setValue('fecnac', htmlspecialchars($fecnac));
    $templateProcessor->setValue('lugarnac', htmlspecialchars($lugarnac));
    $templateProcessor->setValue('estado_civil', htmlspecialchars($estado_civil));
	$templateProcessor->setValue('nombre_padres', htmlspecialchars($nombre_padres));
	$templateProcessor->setValue('apellido_padre', htmlspecialchars($apellido_padre));
	$templateProcessor->setValue('parentesco_padre', htmlspecialchars($parentesco_padre));
	$templateProcessor->setValue('donde', htmlspecialchars($donde));
	$templateProcessor->setValue('nombre_madre', htmlspecialchars($nombre_madre));
	$templateProcessor->setValue('apellido_madre', htmlspecialchars($apellido_madre));
	$templateProcessor->setValue('parentesco_madre', htmlspecialchars($parentesco_madre));
	$templateProcessor->setValue('nombre_hermana', htmlspecialchars($nombre_hermana));
	$templateProcessor->setValue('apellido_hermana', htmlspecialchars($apellido_hermana));
	$templateProcessor->setValue('parentesco_hermana', htmlspecialchars($parentesco_hermana));
	$templateProcessor->setValue('nombre_hijo', htmlspecialchars($nombre_hijo));
	$templateProcessor->setValue('apellido_hijo', htmlspecialchars($apellido_hijo));
	$templateProcessor->setValue('parentesco_hijo', htmlspecialchars($parentesco_hijo));
	$templateProcessor->setValue('nombre_conyugue', htmlspecialchars($nombre_conyugue));
        $templateProcessor->setValue('apellido_conyugue', htmlspecialchars($apellido_conyugue));
        $templateProcessor->setValue('parentesco_conyugue', htmlspecialchars($parentesco_conyugue));
 	$templateProcessor->setValue('dir_rrhh', utf8_decode($dir_rrhh));
	$templateProcessor->setValue('ced_rrhh', htmlspecialchars($ced_rrhh));
	$templateProcessor->setValue('ced_trabajador', htmlspecialchars($ced_trabajador));
	$templateProcessor->setValue('ini', htmlspecialchars($ini));
	$templateProcessor->setValue('fin', htmlspecialchars($fin));
	// $templateProcessor->setValue('numero_patronal', htmlspecialchars($num_patronal));
	$templateProcessor->setValue('seguro_social', htmlspecialchars($seguro_social));
$templateProcessor->setValue('familiares', utf8_decode($familiares));
	$filename = 'Contrato asistente jefe de unidad pjv'.$ficha.'.docx';
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
