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

	\PhpOffice\PhpWord\Autoloader::register();
	\PhpOffice\PhpWord\Settings::loadConfig();

	//====================================================================================================
	// Consultar Datos Necesarios del Trabajador

	$sql = "SELECT n.nombres, n.apellidos, c.des_car as cargo
			FROM   nompersonal n
			LEFT JOIN nomcargos c ON c.cod_cargo=n.codcargo
			WHERE  n.ficha='{$ficha}' AND n.tipnom='{$tipnom}'";
	$res = $db->query($sql);

	$trabajador = $res->fetch_object();

	$primer_nombre   = $trabajador->nombres; 
	$primer_apellido = $trabajador->apellidos;

	if($trabajador->nombres!='')
	{
		$nombres = explode(' ', trim($trabajador->nombres));
		if(count($nombres)>0)
			$primer_nombre = $nombres[0];
	}

	if($trabajador->apellidos!='')
	{
		$apellidos = explode(' ', trim($trabajador->apellidos));
		if(count($apellidos)>0)
			$primer_apellido = $apellidos[0];
	}

	$nombre_completo = $primer_nombre . ' ' . $primer_apellido;
	//====================================================================================================

	$templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('templates/carta_periodo_probatorio.docx');

	$templateProcessor->setValue('trabajador', htmlspecialchars($nombre_completo));
	$templateProcessor->setValue('cargo', htmlspecialchars($trabajador->cargo));
	
	//=====================================================================================================
	$mes_letras = array("Enero","Febrero","Marzo","Abril","Mayo","Junio",
		                "Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");

	$dia  = date('j'); 
	$mes  = $mes_letras[date('n')-1];
	$anio = date('Y');

	$templateProcessor->setValue('dia', htmlspecialchars($dia));
	$templateProcessor->setValue('mes_lower', htmlspecialchars(strtolower($mes)));	
	$templateProcessor->setValue('mes_upper', htmlspecialchars(strtoupper($mes)));
	$templateProcessor->setValue('anio', htmlspecialchars($anio));
	//====================================================================================================

	$filename = 'CartaDespidoPeriodoProbatorio'.$ficha.'.docx';
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