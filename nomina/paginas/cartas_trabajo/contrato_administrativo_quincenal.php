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
            $f = $dia." DE ".$meses[$mes-1]." DE ".$anio;
            break;
        case 2:
            $f = dias($dia)."  DEL MES ".$meses[$mes-1]." DE ".$anio;
            break;
        case 3:
            $f = $dia."/".$mes."/".$anio;
            break;
        default:
            break;
    }
    return $f;
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
// Consultar Datos Empresa
	$sqle = "SELECT  * FROM   nomempresa";
    	$res_empresa         = $db->query($sqle);
    
    	$empresa             = $res_empresa->fetch_object();
	$jefe_planilla       = $empresa->jefe_planilla; 
	$nombre_empresa= $empresa->nom_emp;
	$ger_rrhh=$empresa->ger_rrhh;
	$representante_legal=$empresa->pre_sid;
	$representante_legal_cedula=$empresa->ced_presid;
	$direccion=$empresa->dir_emp;

	// Consultar Datos Necesarios del Trabajador
		$sql = "SELECT n.nombres, n.apellidos, n.cedula, n.seguro_social, n.direccion, n.suesal, n.codcat,
		 			   CASE n.sexo WHEN 'Masculino' THEN 'varón' WHEN 'Femenino' THEN 'mujer' END as sexo,
		 			   CASE n.nacionalidad WHEN 1 THEN 'panameño' WHEN 2 THEN 'extranjero' WHEN 3 THEN 'nacionalizado' 
		 			   END as nacionalidad, c.cod_car, c.des_car as cargo, n.estado_civil, n.fecnac,n.inicio_periodo, n.fin_periodo,
		 			   n.ficha,n.telefonos
				FROM   nompersonal n
				LEFT JOIN nomcargos c ON c.cod_cargo=n.codcargo
				WHERE  n.ficha='{$ficha}' AND n.tipnom='{$tipnom}'";
		$res = $db->query($sql);

		$trabajador = $res->fetch_object();
		$edad_d = date('d',strtotime($trabajador->fecnac));
		$edad_m = date('m',strtotime($trabajador->fecnac));
		$edad_a = date('Y',strtotime($trabajador->fecnac));
		$fechanac = $edad_a."-".$edad_m."-".$edad_d;
		$fechahoy = date('Y')."-".date('m')."-".date('d');
		$date1 = new DateTime($fechanac);
		$date2 = new DateTime($fechahoy);
		$diff = $date1->diff($date2);
		// will output 2 days
		$annos = $diff->format('de %Y años');
		$fullname = $trabajador->nombres . ' ' . $trabajador->apellidos;
		//$ultimo_char  = substr($trabajador->nacionalidad, -1);
		//$nacionalidad = ($trabajador->sexo=='mujer') ? str_lreplace($ultimo_char, 'a', $trabajador->nacionalidad) : $trabajador->nacionalidad;
		$nacionalidad = ($trabajador->sexo=='mujer') ? str_lreplace('o', 'a', $trabajador->nacionalidad) : $trabajador->nacionalidad;

	//====================================================================================================

	$templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('templates/contrato_administrativo_quincenal.docx');

	// Variables on different parts of document
	$templateProcessor->setValue('trabajador', htmlspecialchars($fullname)); // On header
	$templateProcessor->setValue('sexo', htmlspecialchars($trabajador->sexo));
	$templateProcessor->setValue('ficha', htmlspecialchars($trabajador->ficha));
	$templateProcessor->setValue('nacionalidad', htmlspecialchars($nacionalidad));
	$templateProcessor->setValue('cedula', htmlspecialchars($trabajador->cedula));
        $templateProcessor->setValue('seguro_social', htmlspecialchars($trabajador->seguro_social));
	$templateProcessor->setValue('domicilio', htmlspecialchars($trabajador->direccion));
	$templateProcessor->setValue('edocivil', htmlspecialchars($trabajador->estado_civil));
	$templateProcessor->setValue('telefono', htmlspecialchars($trabajador->telefonos));
	$templateProcessor->setValue('edad', htmlspecialchars($annos));
	$templateProcessor->setValue('inicioperiodo', htmlspecialchars(formato_fecha($trabajador->inicio_periodo,1)));
	$templateProcessor->setValue('nombre_empresa', htmlspecialchars($empresa->nom_emp));
	$templateProcessor->setValue('gerente', htmlspecialchars($empresa->ger_rrhh));
	$templateProcessor->setValue('representante_legal', htmlspecialchars($empresa->pre_sid));
	$templateProcessor->setValue('representante_legal_cedula', htmlspecialchars($empresa->ced_presid));
	$templateProcessor->setValue('direccion', htmlspecialchars($empresa->dir_emp));
	//=====================================================================================================
	// Familiares dependientes del trabajador

	$sql = "SELECT CONCAT_WS(' ', f.nombre, f.apellido) as nombre, p.descrip as parentesco, f.sexo
			FROM   nomfamiliares f
			INNER JOIN nomparentescos p ON p.codorg=f.codpar 
			WHERE  f.ficha='{$ficha}' AND f.tipnom='{$tipnom}'";
	$res = $db->query($sql);
        
        //$num_familiares = ($res->num_rows < 4) ? 4 : $res->num_rows;
	$num_familiares = $res->num_rows;

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

        //PTINCIPALES
        $sql = "SELECT CONCAT_WS(' ', f.nombre, f.apellido) as nombre, p.descrip as parentesco, f.sexo
			FROM   nomfamiliares f
			INNER JOIN nomparentescos p ON p.codorg=f.codpar 
			WHERE  f.ficha='{$ficha}' AND f.tipnom='{$tipnom}' AND beneficiario=1";
	$res_familiares1 = $db->query($sql);
        
        //$num_familiares1 = ($res_familiares1->num_rows < 4) ? 4 : $res_familiares1->num_rows;
	$num_familiares1 = $res_familiares1->num_rows;

	$templateProcessor->cloneRow('familiarNombre1', $num_familiares1);

	$i=1;

	while($familiar1 = $res_familiares1->fetch_object())
	{
		$parentesco = $familiar1->parentesco;

		if($familiar1->sexo == 'Masculino')
		{
			if($parentesco=='Hijo(a)'){ $parentesco='Hijo'; }
		}
		else
		{
			if($parentesco=='Hijo(a)'){ $parentesco='Hija'; }
		}

		$parentesco = strtoupper($parentesco);

		$templateProcessor->setValue('familiarNombre1#' . $i, htmlspecialchars($familiar1->nombre));
		$templateProcessor->setValue('familiarParentesco1#' . $i, htmlspecialchars($parentesco));

		$i++;
	}

	while($i<=4)
	{
		$templateProcessor->setValue('familiarNombre1#'.$i, htmlspecialchars(''));
		$templateProcessor->setValue('familiarParentesco1#' . $i, htmlspecialchars(''));
		$i++;
	}
        
        
        //CONTINGENTES
        $sql = "SELECT CONCAT_WS(' ', f.nombre, f.apellido) as nombre, p.descrip as parentesco, f.sexo
			FROM   nomfamiliares f
			INNER JOIN nomparentescos p ON p.codorg=f.codpar 
			WHERE  f.ficha='{$ficha}' AND f.tipnom='{$tipnom}' AND beneficiario=2";
	$res_familiares2 = $db->query($sql);
        
        //$num_familiares2 = ($res_familiares2->num_rows < 4) ? 4 : $res_familiares2->num_rows;
	$num_familiares2 = $res_familiares2->num_rows;

	$templateProcessor->cloneRow('familiarNombre2', $num_familiares2);

	$i=1;

	while($familiar2 = $res_familiares2->fetch_object())
	{
		$parentesco = $familiar2->parentesco;

		if($familiar2->sexo == 'Masculino')
		{
			if($parentesco=='Hijo(a)'){ $parentesco='Hijo'; }
		}
		else
		{
			if($parentesco=='Hijo(a)'){ $parentesco='Hija'; }
		}

		$parentesco = strtoupper($parentesco);

		$templateProcessor->setValue('familiarNombre2#' . $i, htmlspecialchars($familiar2->nombre));
		$templateProcessor->setValue('familiarParentesco2#' . $i, htmlspecialchars($parentesco));

		$i++;
	}

	while($i<=4)
	{
		$templateProcessor->setValue('familiarNombre2#'.$i, htmlspecialchars(''));
		$templateProcessor->setValue('familiarParentesco2#' . $i, htmlspecialchars(''));
		$i++;
	}
	//=====================================================================================================

	$templateProcessor->setValue('cargo', htmlspecialchars($trabajador->cargo));

	$numeroTexto = new NumerosLetras();
	$pago_texto  = mb_strtoupper(trim($numeroTexto->convertir($trabajador->suesal, '2')));
	$pago_numero = number_format($trabajador->suesal, 2, '.', ',');
	$frecuencia  = ($trabajador->codcat == 8) ? 'por hora' : 'por mes';
	$templateProcessor->setValue('sueldoletras', htmlspecialchars($pago_texto));
	$templateProcessor->setValue('suesal', htmlspecialchars($pago_numero));


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

	$fullname = str_replace(' ', '', $fullname);

	$filename = 'contrato_administrativo_quincenal_'.$ficha.'.docx';
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
	ob_clean();
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
