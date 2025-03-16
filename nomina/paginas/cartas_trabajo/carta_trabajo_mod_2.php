<?php
require_once 'vendor/phpword/PhpWord/Autoloader.php';
date_default_timezone_set('America/Panama');

if(isset($_POST['ficha']) && isset($_POST['tipnom']) && isset($_POST['aquien']))
{
	require_once('../../lib/database.php');
	require ('numeros_letras_class.php');

	$referencia  = $_POST['referencia'];
	$deudor = $_POST['deudor'];
	$codeudor = $_POST['codeudor'];
	$usuario = $_SESSION['nombre'];
	$ficha  = $_POST['ficha'];
	$tipnom = $_POST['tipnom'];
	$aquien = $_POST['aquien'];
	$db = new Database($_SESSION['bd']);

	error_reporting(E_ALL);

	\PhpOffice\PhpWord\Autoloader::register();
	\PhpOffice\PhpWord\Settings::loadConfig();

	//====================================================================================================
	// Consultar Datos Necesarios del Trabajador
	//----------------------------------------------------------------------------------------------------
	$sql = "SELECT n.nombres, n.apellidos, n.cedula,n.seguro_social,n.siacap,n.fecing,n.fecharetiro, c.des_car as cargo,
				   CASE WHEN sexo='Masculino' THEN ' el Sr. ' ELSE ' la Sra. ' END as sr,
				   n.suesal, n.hora_base
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
	$sql1 = "SELECT a.codcon,a.ficha,a.monto,a.descrip FROM nom_movimientos_nomina AS a WHERE a.ficha = '{$ficha}' AND a.tipcon = D";
	$res1 = $db->query($sql1);
	//----------------------------------------------------------------------------------------------------
	$sql2 = "SELECT a.descrip,b.monto FROM nomconceptos AS a,nom_movimientos_nomina AS b WHERE a.codcon=b.codcon AND a.tipcon = 'D' AND b.ficha = '{$ficha}' AND a.codcon not in (100,200,201,202,204)";
	$res2 = $db->query($sql2);
	//----------------------------------------------------------------------------------------------------
	$templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('templates/carta_trabajo_sm_2.docx');
	$j=0;
	while($data2 = mysqli_fetch_array($res2))
	{
		if ($j < 5)
		{
			$j=$j+1;
			$templateProcessor->setValue('descrip'.$j,validarDato($data2['descrip']));
			$templateProcessor->setValue('monto'.$j,validarDato($data2['monto']));
		}
	}
	if ($j < 5)
	{
		for ($x=$j; $x < 5; $x++)
		{
			$templateProcessor->setValue('descrip'.$x,'__________');
			$templateProcessor->setValue('monto'.$x,'0.00');	
		}
	}
	$dia  = date('j'); 
	$mes  = date('n');
	$anio = date('Y');

	$seguro_social     = obtenerDato($ficha,200);
	$seguro_educativo  = obtenerDato($ficha,201);
	$impuesto_st       = obtenerDato($ficha,202);
	$fondo_comp        = obtenerDato($ficha,204);

	$total_db = $seguro_social + $seguro_educativo + $impuesto_st + $fondo_comp;
	
	$descuento_vivienda = obtenerDato($ficha,201);
	$pensiones_alim     = obtenerDato($ficha,202);
	$secuentros_embar   = obtenerDato($ficha,204);

	$total_dv = $descuento_vivienda + $pensiones_alim + $secuentros_embar;
	$total_oblig = $total_db + $total_dv;
	if ($trabajador->fecharetiro == '0000-00-00')
		{$fecharetiro=date('Y-m-d');}
	//=====================================================================================================
	$mes_letras = array("Enero","Febrero","Marzo","Abril","Mayo","Junio",
		                "Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
	
	$mes  = $mes_letras[$mes - 1];	
	
	$templateProcessor->setValue('referencia', htmlspecialchars($referencia));
	$templateProcessor->setValue('deudor', htmlspecialchars($deudor));
	$templateProcessor->setValue('codeudor', htmlspecialchars($codeudor));
	$templateProcessor->setValue('usuario', htmlspecialchars($usuario));
	$templateProcessor->setValue('aquien', htmlspecialchars($aquien));
	$templateProcessor->setValue('sr', htmlspecialchars($trabajador->sr));
	$templateProcessor->setValue('trabajador', htmlspecialchars($nombre_completo));
	$templateProcessor->setValue('cedula', htmlspecialchars($trabajador->cedula));
	$templateProcessor->setValue('num_seguro',htmlspecialchars($trabajador->seguro_social));
	$templateProcessor->setValue('cargo', htmlspecialchars($trabajador->cargo));
	$templateProcessor->setValue('fecha_inicio',$trabajador->fecing);
	$templateProcessor->setValue('fecha_retiro',$fecharetiro);
	$templateProcessor->setValue('salario',$trabajador->suesal);
	$templateProcessor->setValue('numero_empleado',$ficha);
	$templateProcessor->setValue('salario_bruto',$trabajador->suesal);

	$templateProcessor->setValue('seguro_social',$seguro_social);
	$templateProcessor->setValue('seguro_educativo',$seguro_educativo);
	$templateProcessor->setValue('impuesto_st',$impuesto_st);
	$templateProcessor->setValue('fondo_comp',$fondo_comp);
	$templateProcessor->setValue('total_db',number_format($total_db, 2, '.', ''));

	$templateProcessor->setValue('descuento_vivienda',$descuento_vivienda);
	$templateProcessor->setValue('pensiones_alim',$pensiones_alim);
	$templateProcessor->setValue('secuentros_embar',$secuentros_embar);
	$templateProcessor->setValue('total_dv',$total_dv);
	
	$templateProcessor->setValue('total_oblig',$total_oblig);
	$templateProcessor->setValue('salario_neto',$trabajador->suesal);

	$templateProcessor->setValue('dia', htmlspecialchars($dia));
	$templateProcessor->setValue('mes_lower', htmlspecialchars(strtolower($mes)));	
	$templateProcessor->setValue('mes_upper', htmlspecialchars(strtoupper($mes)));
	$templateProcessor->setValue('anio', htmlspecialchars($anio));
	//====================================================================================================

	$filename = 'CartaTrabajo-sanmiguelito-mod2'.$ficha.'.docx';
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
function validarDato($dato)
{
	switch ($dato)
	{
		case '':
			return 'S/N';
			break;
		case FALSE:
			return 'S/N';
			break;
		default:
			return $dato;
			break;
	}
}
function obtenerDato($ficha,$cod)
{
	global $db;

	$dato = 0.0;

    // Consultar SEGURO SOCIAL (S.S.) - Tabla: nomconceptos - codcon: 200
    $sql = "SELECT monto
			FROM   nom_movimientos_nomina
			WHERE  ficha = '{$ficha}'
			AND    codcon = '{$cod}'";

	$res = $db->query($sql);

	if($data = $res->fetch_object())
	{
		$dato = $data->monto;
	}
	return $dato;
}
?>