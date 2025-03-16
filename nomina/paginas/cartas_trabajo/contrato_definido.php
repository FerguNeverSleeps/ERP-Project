<?php
require_once 'vendor/phpword/PhpWord/Autoloader.php';
date_default_timezone_set('America/Panama');

use PhpOffice\PhpWord\Autoloader; // (PHP 5 >= 5.3.0)
use PhpOffice\PhpWord\Settings;   // (PHP 5 >= 5.3.0)

function str_lreplace($search, $replace, $subject)
{
    $pos = strrpos($subject, $search);

    if($pos !== false)
    {
        $subject = substr_replace($subject, $replace, $pos, strlen($search));
    }

    return $subject;
}

if(isset($_GET['ficha']) && isset($_GET['tipnom']))
{
	require_once('../../lib/database.php');
	require ('numeros_letras_class.php');

	$ficha  = $_GET['ficha'];
	$tipnom = $_GET['tipnom'];
	$db = new Database($_SESSION['bd']);

	error_reporting(E_ALL);
	define('CLI', (PHP_SAPI == 'cli') ? true : false);
	define('EOL', CLI ? PHP_EOL : '<br />');
	define('SCRIPT_FILENAME', basename($_SERVER['SCRIPT_FILENAME'], '.php'));
	define('IS_INDEX', SCRIPT_FILENAME == 'index');

	Autoloader::register();
	Settings::loadConfig();

	//====================================================================================================
	// Consultar Datos Necesarios del Trabajador
		$sql = "SELECT n.nombres, n.apellidos, n.cedula, n.direccion, n.suesal, n.codcat,
		 			   CASE n.sexo WHEN 'Masculino' THEN 'varón' WHEN 'Femenino' THEN 'mujer' END as sexo,
		 			   CASE n.nacionalidad WHEN 1 THEN 'panameño' WHEN 2 THEN 'extranjero' WHEN 3 THEN 'nacionalizado' 
		 			   END as nacionalidad, c.cod_car, c.des_car as cargo,
		 			   DATE_FORMAT(n.inicio_periodo,'%e-%c-%Y') as inicio_periodo, 
		 			   DATE_FORMAT(n.fin_periodo,'%e-%c-%Y') as fin_periodo
				FROM   nompersonal n
				LEFT JOIN nomcargos c ON c.cod_cargo=n.codcargo
				WHERE  n.ficha='{$ficha}' AND n.tipnom='{$tipnom}'";
		$res = $db->query($sql);

		$trabajador = $res->fetch_object();

		$fullname = $trabajador->nombres . ' ' . $trabajador->apellidos;

		//$ultimo_char  = substr($trabajador->nacionalidad, -1);
		//$nacionalidad = ($trabajador->sexo=='mujer') ? str_lreplace($ultimo_char, 'a', $trabajador->nacionalidad) : $trabajador->nacionalidad;
		$nacionalidad = ($trabajador->sexo=='mujer') ? str_lreplace('o', 'a', $trabajador->nacionalidad) : $trabajador->nacionalidad;

	//====================================================================================================

	$templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('templates/contrato_definido.docx');

	// Variables on different parts of document
	$templateProcessor->setValue('trabajador', htmlspecialchars($fullname)); // On header
	$templateProcessor->setValue('sexo', htmlspecialchars($trabajador->sexo));
	$templateProcessor->setValue('nacionalidad', htmlspecialchars($nacionalidad));
	$templateProcessor->setValue('cedula', htmlspecialchars($trabajador->cedula));
	//$templateProcessor->setValue('domicilio', htmlspecialchars($trabajador->direccion));

	//=====================================================================================================
	// Familiares dependientes del trabajador
	$sql = "SELECT CONCAT_WS(' ', f.nombre, f.apellido) as nombre, p.descrip as parentesco, f.sexo
			FROM   nomfamiliares f
			INNER JOIN nomparentescos p ON p.codorg=f.codpar 
			WHERE  f.ficha='{$ficha}' AND f.tipnom='{$tipnom}'";
	$res = $db->query($sql);

	$num_familiares = ($res->num_rows < 4) ? 4 : $res->num_rows;

	$templateProcessor->cloneRow('familiarNombre', $num_familiares);

	$i=1;

	while($familiar = $res->fetch_object())
	{
		$parentesco = $familiar->parentesco;

		if($familiar->sexo == 'Masculino')
		{
			if($parentesco=='Hijo(a)'){ $parentesco='Hijo'; }
		}
		else
		{
			if($parentesco=='Hijo(a)'){ $parentesco='Hija'; }
		}

		$parentesco = strtoupper($parentesco);

		$templateProcessor->setValue('familiarNombre#' . $i, htmlspecialchars($familiar->nombre));
		$templateProcessor->setValue('familiarParentesco#' . $i, htmlspecialchars($parentesco));

		$i++;
	}

	while($i<=4)
	{
		$templateProcessor->setValue('familiarNombre#'.$i, htmlspecialchars(''));
		$templateProcessor->setValue('familiarParentesco#' . $i, htmlspecialchars(''));
		$i++;
	}
	//=====================================================================================================

	$templateProcessor->setValue('cargo', htmlspecialchars($trabajador->cargo));

	$numeroTexto = new NumerosLetras();
	$pago_texto  = mb_strtoupper(trim($numeroTexto->convertir($trabajador->suesal, '2')));
	$pago_numero = number_format($trabajador->suesal, 2, '.', ',');
	$frecuencia  = ($trabajador->codcat == 8) ? 'por hora' : 'por mes';

	//$templateProcessor->setValue('pago_hora', htmlspecialchars('DOS DOLARES AMERICANOS CON 35/100 (USS2.35) por hora'));
	$templateProcessor->setValue('pago_hora', htmlspecialchars( $pago_texto . ' (USS'.$pago_numero.') ' . $frecuencia));

	//=====================================================================================================
	$dia_letras = array('un', 'dos', 'tres', 'cuatro', 'cinco', 'seis', 'siete', 'ocho', 'nueve', 'diez', 'once', 'doce', 'trece',
						'catorce', 'quince', 'dieciséis', 'diecisiete', 'dieciocho', 'diecinueve', 'veinte', 'veintiún', 
						'veintidós', 'veintitrés', 'veinticuatro', 'veinticinco', 'veintiséis', 'veintisiete', 'veintiocho', 
						'veintinueve', 'treinta', 'treintaiún');

	$mes_letras = array("Enero","Febrero","Marzo","Abril","Mayo","Junio",
		                "Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");

	$dia  = mb_strtoupper($dia_letras[date('j')-1]); // Tener activada extension "php_mbstring"
	$mes  = strtoupper($mes_letras[date('n')-1]);
	$anio = date('Y');

	$fecha = $dia .' ('. date('j') .') días del mes de ' . $mes . ' de ' .  $anio;

	$templateProcessor->setValue('fecha', htmlspecialchars($fecha));

	$inicio_contrato = $fin_contrato = "";

	if($trabajador->inicio_periodo!='')
	{
		list($dia, $mes, $anio) = explode('-', $trabajador->inicio_periodo);

		$inicio_contrato = $dia.' de ' . strtolower($mes_letras[$mes-1]). ' de ' . $anio;
	}

	if($trabajador->fin_periodo!='')
	{
		list($dia, $mes, $anio) = explode('-', $trabajador->fin_periodo);

		$fin_contrato = $dia.' de ' . strtolower($mes_letras[$mes-1]). ' de ' . $anio;
	}

	$templateProcessor->setValue('inicio_contrato', htmlspecialchars($inicio_contrato));
	$templateProcessor->setValue('fin_contrato',    htmlspecialchars($fin_contrato));


	$fullname = str_replace(' ', '', $fullname);

	$filename = 'ContratoDefinido'.$ficha.'.docx';
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