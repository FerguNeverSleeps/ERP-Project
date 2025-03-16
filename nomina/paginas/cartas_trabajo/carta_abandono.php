<?php
require_once 'vendor/phpword/PhpWord/Autoloader.php';
date_default_timezone_set('America/Panama');

if(isset($_GET['ficha']) && isset($_GET['tipnom']))
{
	require_once('../../lib/database.php');
	require ('numeros_letras_class.php');

	$ficha  = $_GET['ficha'];
	$tipnom = $_GET['tipnom'];
	$db = new Database($_SESSION['bd']);

	error_reporting(E_ALL);
	// define('CLI', (PHP_SAPI == 'cli') ? true : false);
	// define('EOL', CLI ? PHP_EOL : '<br />');
	// define('SCRIPT_FILENAME', basename($_SERVER['SCRIPT_FILENAME'], '.php'));
	// define('IS_INDEX', SCRIPT_FILENAME == 'index');

	\PhpOffice\PhpWord\Autoloader::register();
	\PhpOffice\PhpWord\Settings::loadConfig();

	//====================================================================================================
	// Consultar Datos Necesarios del Trabajador

	$sql = "SELECT n.nombres, n.apellidos, n.cedula, c.des_car as cargo
			FROM   nompersonal n
			LEFT JOIN nomcargos c ON c.cod_cargo=n.codcargo
			WHERE  n.ficha='{$ficha}' AND n.tipnom='{$tipnom}'";
	$res = $db->query($sql);

	$trabajador = $res->fetch_object();

	$fullname = mb_strtoupper($trabajador->nombres . ' ' . $trabajador->apellidos);

	$primer_apellido = '';

	if($trabajador->apellidos)
	{
		$apellidos = explode(' ', trim($trabajador->apellidos));
		if(count($apellidos)>0)
			$primer_apellido = $apellidos[0];
	}
	//====================================================================================================

	$templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('templates/carta_abandono.docx');

	$templateProcessor->setValue('trabajador', htmlspecialchars($fullname));
	$templateProcessor->setValue('cedula', htmlspecialchars($trabajador->cedula));
	$templateProcessor->setValue('ficha', htmlspecialchars($ficha));
	$templateProcessor->setValue('primer_apellido', htmlspecialchars($primer_apellido));
	$templateProcessor->setValue('cargo', htmlspecialchars($trabajador->cargo));

	//=====================================================================================================
	$mes_letras = array("Enero","Febrero","Marzo","Abril","Mayo","Junio",
		                "Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");

	$dia  = date('j'); 
	$mes  = strtolower($mes_letras[date('n')-1]);
	$anio = date('Y');

	$fecha = $dia .' de ' . $mes . ' de ' .  $anio;

	$templateProcessor->setValue('fecha', htmlspecialchars($fecha));
	//====================================================================================================

	$filename = 'CartaAbandono'.$ficha.'.docx';
	$temp_file_uri = tempnam('', $filename);
	$templateProcessor->saveAs($temp_file_uri);

	// The following offers file to user on client side: deletes temp version of file
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
else
{
   echo "<script>alert('Acceso Denegado');</script>";
   echo "<script>document.location.href = '../list_personal.php';</script>";
}

exit();
?>