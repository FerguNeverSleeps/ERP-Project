<?php
require('mypdf.php');
require('numerosALetras.class.php');
$conexion =  mysqli_connect(DB_HOST, DB_USUARIO, DB_CLAVE, $_SESSION['bd']) or die('No Hay Conexión con el Servidor de Mysql');
mysqli_query($conexion, 'SET CHARACTER SET utf8');
//require_once ('../../lib/database.php');
ob_start();
error_reporting(E_ALL & ~E_NOTICE);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
//echo "Peroooo";

// require ('numeros_letras_class.php');
//$conexion = new Database($_SESSION['bd']);
if (isset($_GET['ficha_rhexpress'])) {
	$ficha         = (int) $_GET['ficha_rhexpress'];
	$tipnom        = 1; //(int) $_GET['tipnom'];
	$constancia_id = 2; //(int) $_GET['constancia_id']; // id de la constancia/carta de trabajo

	if (!isset($_GET['cove']))
		$CODIGO_VERIFICACION .= str_pad($ficha, 4, "0", STR_PAD_LEFT);
	else
		$CODIGO_VERIFICACION = $_GET['cove'];

	//===================================================================================================
	// Crear reporte PDF
	$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, 'LETTER', true, 'UTF-8', false);
	$pdf->SetCreator(PDF_CREATOR);
	$pdf->SetAuthor('Selectra Planilla');
	$pdf->SetTitle('Constancia de trabajo');

	$pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
	$pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

	$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

	$pdf->SetMargins(PDF_MARGIN_LEFT, 10, PDF_MARGIN_RIGHT);
	// $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);

	$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

	$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

	$calibri      = TCPDF_FONTS::addTTFfont('vendor/tcpdf/fonts/ttf/calibri.ttf',  'TrueTypeUnicode', '', 32);
	$calibri_bold = TCPDF_FONTS::addTTFfont('vendor/tcpdf/fonts/ttf/calibrib.ttf', 'TrueTypeUnicode', '', 32);
	$calibri_bi   = TCPDF_FONTS::addTTFfont('vendor/tcpdf/fonts/ttf/calibriz.ttf', 'TrueTypeUnicode', '', 32);

	$pdf->AddPage();

	// Consultar datos generales de las constancias
	$sql = "SELECT titulo, slogan, cargo_gerente, abreviatura, observaciones, validar_constancias, codigo_verificacion FROM nomconf_constancia";
	$res = $conexion->query($sql);
	$conf_general = $res->fetch_object();
	$titulo = ($conf_general->titulo != '') ? $conf_general->titulo : '';
	////datios empresa
	$sql = "SELECT * FROM nomempresa";
	$res_datos = $conexion->query($sql);
	$datos_empresa = $res_datos->fetch_object();
	$gerente_rrhh = $datos_empresa->ger_rrhh;
	// Consultar datos específicos del modelo de constancia/carta de trabajo
	$sql = "SELECT nombre, contenido1, contenido2, contenido3, titulo, observaciones, formula, fuente, 
	               margen_sup, margen_izq, margen_der
			FROM   nomtipos_constancia
			WHERE  codigo='{$constancia_id}'";
	$res = $conexion->query($sql);
	$constancia = $res->fetch_object();

	// Antigua margen
	$margen_sup = ($constancia->margen_sup != null) ? $constancia->margen_sup : PDF_MARGIN_TOP; // PDF_MARGIN_TOP 27
	$margen_izq = ($constancia->margen_izq != null) ? $constancia->margen_izq : 30; // PDF_MARGIN_LEFT  15
	$margen_der = ($constancia->margen_der != null) ? $constancia->margen_der : 29; // PDF_MARGIN_RIGHT 15

	$nombre_constancia = utf8_encode($constancia->nombre);

	if ($constancia->titulo != '') {
		$titulo = $constancia->titulo;
	}

	// Consultar datos del colaborador
	$sql = "SELECT n.nombres, n.nombres2, n.apellidos, n.apellido_materno, n.apenom, n.cedula, n.fecing, n.fecharetiro, n.suesal, n.sexo,
	               c.cod_car as codcargo, c.des_car as cargo, n.tipemp
			FROM   nompersonal n
			LEFT JOIN nomcargos c ON c.cod_cargo=n.codcargo
			WHERE  n.ficha='{$ficha}' ";
	$res = $conexion->query($sql);

	$colaborador = $res->fetch_object();

	// Seteamos algunos datos, en este caso fecha de cumpleaños
	$FECHA_INGRESO = DateTime::createFromFormat('Y-m-d', $colaborador->fecing);

	$CEDULA = $colaborador->cedula;
	$NOMBRE = $NOMBRE_COMPLETO = strtoupper($colaborador->nombres . ' ' . $colaborador->nombres2 . ' '  . $colaborador->apellidos . ' '  . $colaborador->apellido_materno);
	$NOMBRE_NORMAL = ucwords(strtolower($NOMBRE));
	$APELLIDOS = $colaborador->apellidos;

	$PRIMER_NOMBRE = trim($colaborador->nombres);
	$nombres = explode(' ', $PRIMER_NOMBRE);
	if (count($nombres) > 0) $PRIMER_NOMBRE = $nombres[0];

	$PRIMER_APELLIDO = trim($colaborador->apellidos);
	$apellidos = explode(' ', $PRIMER_APELLIDO);
	if (count($apellidos) > 0) $PRIMER_APELLIDO = $apellidos[0];

	$SUELDO = $colaborador->suesal;
	$CARGO  = $colaborador->cargo;
	$CARGO_MAYUS = strtoupper($_SESSION['cargo_rhexpress']);
	$FICHA = $ficha;

	$mes_letras = array(
		"Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio",
		"Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"
	);

	// Fecha de ingreso
	list($anioing, $mesing, $diaing) = explode('-', $colaborador->fecing);
	$FECHA_INGRESO = $diaing . ' de ' . ucfirst(strtolower($mes_letras[$mesing - 1])) . ' de ' . $anioing;

	$TIPO_CONTRATO = strtoupper($colaborador->tipemp);

	// Fecha de egreso
	if (isset($colaborador->fecharetiro)) {
		list($anioegr, $mesegr, $diaegr) = explode('-', $colaborador->fecharetiro);
		$FECHA_EGRESO = $diaegr . '/' . $mesegr . '/' . $anioegr;
	}

	//=============================================================================================
	// Día, mes y año actual
	$DIA  = date('d');
	$MES  = strtolower($mes_letras[date('n') - 1]);
	$ANIO = date('Y');
	$ANIO_FISCAL = $ANIO - 1;
	//===================================================================================================
	//===================================================================================================
	// Consultas realizadas inicialmente para EISA y con el fin de hallar los valores de algunas
	// de las variables utilizadas en las constancias/cartas de trabajo
	$mes_actual  = date('n');
	$anio_actual = date('Y');
	$dia_inicio_quincena = 0;

	$sql = "SELECT MAX(mes) as mes, Max(anio) as anio 
            FROM   nom_movimientos_nomina n 
            WHERE  ficha='{$FICHA}' AND tipnom='{$tipnom}' 
            AND    n.anio=(SELECT MAX(anio) 
                           FROM nom_movimientos_nomina WHERE ficha=n.ficha)";

	if ($fila = $conexion->query($sql)->fetch_array()) {
		$mes_actual  = ($fila['mes']  != null) ? $fila['mes']  : $mes_actual;
		$anio_actual = ($fila['anio'] != null) ? $fila['anio'] : $anio_actual;
	}

	// Primero voy a consultar si ya se cancelo alguna quincena del mes actual
	$sql = "SELECT DATE_FORMAT(n.periodo_ini,'%e') AS dia_inicio, DATE_FORMAT(n.periodo_fin,'%e') AS dia_fin
			FROM   nom_nominas_pago n 
			WHERE  n.periodo_fin=(SELECT MAX(periodo_fin) 
				                  FROM   nom_nominas_pago 
				                  WHERE  anio={$anio_actual} AND mes={$mes_actual} AND tipnom='{$tipnom}')";

	if ($fila = $conexion->query($sql)->fetch_array()) {
		$dia_inicio_quincena = $fila['dia_inicio']; // Comienzo de la quincena
		$dia_fin_quincena    = $fila['dia_fin'];

		if ($dia_inicio_quincena == 1) {
			$ini_ultima_quincena = date("Y-m-d", mktime(0, 0, 0, $mes_actual, $dia_fin_quincena + 1, $anio_actual));
			$fin_ultima_quincena = date("Y-m-d", (mktime(0, 0, 0, $mes_actual + 1, 1, $anio_actual) - 1));
		}
	}

	// Consultar GASTOS DE REPRESENTACION 
	$sql = "SELECT nap.valor AS monto 
	        FROM   nomcampos_adic_personal nap 
			INNER  JOIN nomcampos_adicionales na ON nap.id =  na.id 
			WHERE  nap.ficha='{$FICHA}' AND UPPER(na.descrip)='GASTOS DE REPRESENTACION'";

	$GASTOS_REPRESENTACION = 0;

	if ($fila = $conexion->query($sql)->fetch_array()) {
		$GASTOS_REPRESENTACION = $fila['monto'];
	}

	// Consultar SEGURO SOCIAL (S.S.) - Tabla: nomconceptos - codcon: 200
	$sql = "SELECT SUM(nm.monto) AS monto 
			FROM   nomconceptos nc, nom_movimientos_nomina nm
			WHERE  nc.codcon=nm.codcon
			AND    nc.codcon=200
			AND    nm.ficha='{$FICHA}' AND nm.tipnom='{$tipnom}'
			AND    nm.codnom IN (SELECT codnom 
			                     FROM   nom_nominas_pago 
			                     WHERE  mes='{$mes_actual}' AND anio='{$anio_actual}')";
	// nc.descrip LIKE '%SEGURO SOCIAL (S.S.)%'

	if ($fila = $conexion->query($sql)->fetch_array()) {
		$SEGURO_SOCIAL = $fila['monto'];

		if ($dia_inicio_quincena == 1) // Es la primera quincena
		{
			$SEGURO_SOCIAL *= 2;
		}
	}

	// Consultar IMPUESTO SOBRE LA RENTA
	// IMPUESTO SOBRE LA RENTA (I.S.R.) - Tabla: nomconceptos - codcon: 202
	// IMPUESTO SOBRE LA RENTA GASTOS DE REPRESENTACION - Tabla: nomconceptos - codcon: 207 
	$sql = "SELECT SUM(nm.monto) AS monto 
			FROM   nomconceptos nc, nom_movimientos_nomina nm
			WHERE  nc.codcon=nm.codcon
			AND    nc.codcon IN (202, 207) -- nc.descrip LIKE '%IMPUESTO SOBRE LA RENTA%' 
			AND    nm.ficha='{$FICHA}' AND nm.tipnom='{$tipnom}' 
			AND    nm.codnom IN (SELECT codnom 
								 FROM   nom_nominas_pago 
								 WHERE  mes='{$mes_actual}' AND anio='{$anio_actual}')";

	if ($fila = $conexion->query($sql)->fetch_array()) {
		$IMPUESTO_SOBRE_RENTA = $fila['monto'];

		if ($dia_inicio_quincena == 1) // Es la primera quincena
		{
			$IMPUESTO_SOBRE_RENTA *= 2;
		}
	}

	// Consultar SEGURO EDUCATIVO (S.E.) - Tabla: nomconceptos - codcon: 201
	$sql = "SELECT SUM(nm.monto) AS monto
			FROM   nomconceptos nc, nom_movimientos_nomina nm
			WHERE  nc.codcon=nm.codcon
			AND    nc.descrip LIKE 'SEGURO EDUCATIVO (S.E.)' 
		    AND    nm.ficha='{$FICHA}' AND nm.tipnom='{$tipnom}'
			AND    nm.codnom IN (SELECT codnom
			                     FROM   nom_nominas_pago 
			                     WHERE  mes='{$mes_actual}' AND anio='{$anio_actual}')";

	if ($fila = $conexion->query($sql)->fetch_array()) {
		$SEGURO_EDUCATIVO = $fila['monto'];

		if ($dia_inicio_quincena == 1) // Es la primera quincena
		{
			$SEGURO_EDUCATIVO *= 2;
		}
	}

	// Consultar Casa Comercial - Tabla nomconceptos - codcon del 501 al 599
	$sql = "SELECT SUM(n.mtocuota) as monto
	     FROM   nomprestamos_cabecera n
	     WHERE  n.ficha='{$FICHA}' AND n.estadopre='Pendiente'";

	if ($fila = $conexion->query($sql)->fetch_array()) {
		$CASA_COMERCIAL = $fila['monto'];
	}
	//===================================================================================================
	//===================================================================================================
	// VARIABLES DE LAS CONSTANCIAS - APLICAR FORMULAS
	$numeroTexto = new numerosALetras();

	$SUELDO_LETRAS = strtoupper(trim($numeroTexto->convertir($SUELDO, '1')));

	$SALARIO_BRUTO    =  $SUELDO + $GASTOS_REPRESENTACION;

	//esto es nuevo si lo quitan funciona como antes
	$SEGURO_SOCIAL    = $SALARIO_BRUTO * 0.0975;
	$SEGURO_EDUCATIVO = $SUELDO * 0.0125;

	$IMPUESTO_SOBRE_RENTA_SALARIO  =  $SUELDO * 13;

	if ($IMPUESTO_SOBRE_RENTA_SALARIO >= 11001 && $IMPUESTO_SOBRE_RENTA_SALARIO <= 50000) {
		$IMPUESTO_SOBRE_RENTA_SALARIO  = (($IMPUESTO_SOBRE_RENTA_SALARIO - 11000) * 0.15) / 13;
	} elseif ($IMPUESTO_SOBRE_RENTA_SALARIO >= 50001 && $IMPUESTO_SOBRE_RENTA_SALARIO <= 500000) {
		$IMPUESTO_SOBRE_RENTA_SALARIO  =   $SUELDO * 0.25;
	} else {
		$IMPUESTO_SOBRE_RENTA_SALARIO  =  0;
	}

	$IMPUESTO_SOBRE_RENTA_GASTOR   =  $GASTOS_REPRESENTACION * 12;
	if ($IMPUESTO_SOBRE_RENTA_GASTOR <= 25000) {
		$IMPUESTO_SOBRE_RENTA_GASTOR  =  $GASTOS_REPRESENTACION * 0.10;
	} else {
		$IMPUESTO_SOBRE_RENTA_GASTOR  =  (($IMPUESTO_SOBRE_RENTA_GASTOR - 25000) * 0.10) / 12;
		$IMPUESTO_SOBRE_RENTA_GASTOR  =  $GASTOS_REPRESENTACION + (2500 / 12);
	}

	$IMPUESTO_SOBRE_RENTA  =  $IMPUESTO_SOBRE_RENTA_SALARIO + $IMPUESTO_SOBRE_RENTA_GASTOR;

	$TOTAL_DESCUENTOS =  $SEGURO_SOCIAL + $SEGURO_EDUCATIVO + $IMPUESTO_SOBRE_RENTA + $CASA_COMERCIAL;
	$SALARIO_NETO     =  $SALARIO_BRUTO - $TOTAL_DESCUENTOS;

	$SUELDO_LETRAS_SALARIOBRUTO = strtoupper(trim($numeroTexto->convertir($SALARIO_BRUTO, '1')));

	$SUELDO                = number_format($SUELDO, 2, ',', '.');
	$GASTOS_REPRESENTACION = number_format($GASTOS_REPRESENTACION, 2, ',', '.');
	$SALARIO_BRUTO         = number_format($SALARIO_BRUTO, 2, ',', '.');
	$SEGURO_SOCIAL         = number_format($SEGURO_SOCIAL, 2, ',', '.');
	$SEGURO_EDUCATIVO      = number_format($SEGURO_EDUCATIVO, 2, ',', '.');
	$IMPUESTO_SOBRE_RENTA  = number_format($IMPUESTO_SOBRE_RENTA, 2, ',', '.');
	$CASA_COMERCIAL        = number_format($CASA_COMERCIAL, 2, ',', '.');
	$TOTAL_DESCUENTOS      = number_format($TOTAL_DESCUENTOS, 2, ',', '.');
	$SALARIO_NETO          = number_format($SALARIO_NETO, 2, ',', '.');
	//===================================================================================================
	//===================================================================================================

	if ($constancia->formula != '') {
		eval($constancia->formula);
	}
	//===================================================================================================
	// Variables para las que se necesita conocer el año fiscal

	$sql = "SELECT SUM(nm.monto) as salario_anual 
			FROM   nom_movimientos_nomina nm
			WHERE  nm.ficha='{$FICHA}' AND nm.anio={$ANIO_FISCAL} 
			AND    nm.codcon=100";

	if ($fila = $conexion->query($sql)->fetch_array()) {
		$SALARIOS_ANIO_FISCAL = $fila['salario_anual'];
		$DECIMO_TERCER_MES_SALARIO = ($SALARIOS_ANIO_FISCAL / 3) / 4;
		$SALARIOS_ANIO_FISCAL = number_format($SALARIOS_ANIO_FISCAL, 2, '.', ',');
		$DECIMO_TERCER_MES_SALARIO = number_format($DECIMO_TERCER_MES_SALARIO, 2, '.', ',');
	}

	$sql = "SELECT SUM(nm.monto) as isr_salario_anual
			FROM   nom_movimientos_nomina nm
			WHERE  nm.ficha='{$FICHA}' AND nm.anio={$ANIO_FISCAL}  
			AND    nm.codcon=202"; // AND UPPER(nm.descrip) LIKE 'IMPUESTO SOBRE LA RENTA (I.S.R.)%' 

	if ($fila = $conexion->query($sql)->fetch_array()) {
		$ISR_SALARIOS = $fila['isr_salario_anual'];
		$ISR_SALARIOS = number_format($ISR_SALARIOS, 2, '.', ',');
	}

	$sql = "SELECT SUM(nm.monto) as gastos_anual 
			FROM   nom_movimientos_nomina nm
			WHERE  nm.ficha='{$FICHA}' AND nm.anio={$ANIO_FISCAL} 
			AND    nm.codcon=145"; // UPPER(nm.descrip) LIKE 'GASTOS DE REPRESENTACION'

	if ($fila = $conexion->query($sql)->fetch_array()) {
		$GASTOS_REPR_ANIO_FISCAL  = $fila['gastos_anual'];
		$DECIMO_TERCER_MES_GASTOS = ($GASTOS_REPR_ANIO_FISCAL / 3) / 4;
		$GASTOS_REPR_ANIO_FISCAL  = number_format($GASTOS_REPR_ANIO_FISCAL, 2, '.', ',');
		$DECIMO_TERCER_MES_GASTOS = number_format($DECIMO_TERCER_MES_GASTOS, 2, '.', ',');
	}

	$sql = "SELECT SUM(nm.monto) as isr_gastos_anual
			FROM   nom_movimientos_nomina nm
			WHERE  nm.ficha='{$FICHA}' AND nm.anio={$ANIO_FISCAL} 
			AND    nm.codcon=207"; // 207 IMPUESTO SOBRE LA RENTA GR

	if ($fila = $conexion->query($sql)->fetch_array()) {
		$ISR_GASTOS_REP = $fila['isr_gastos_anual'];
		$ISR_GASTOS_REP = number_format($ISR_GASTOS_REP, 2, '.', ',');
	}
	//===================================================================================================
	//===================================================================================================
	// Mostrar titulo
	if ($titulo != '') {
		$pdf->SetXY(29, 55);

		if ($constancia->fuente == 'Calibri') {
			$pdf->SetFont($calibri, 'B', 16);
		}

		$pdf->MultiCell(156, 12, $titulo, 0, 'C', false);

		$nombre_constancia = $titulo;

		$pdf->SetXY(29, 68);
	} else {
		// Esto se coloco para la certificacion de salarios
		if (strpos($constancia->nombre, 'Certificación de Salarios') !== FALSE)
			$pdf->SetXY(29, 55);
	}

	$pdf->setCellHeightRatio(1.5);

	if ($constancia->fuente == 'Calibri') {
		$pdf->SetFont($calibri, '', 12);
	}

	if ($constancia->contenido2 != '') {

		// Seteamos la imagen de la empresa
		$ruta_img_empresa = "../../../includes/imagenes/$datos_empresa->imagen_der";
		$html_con_imagen = '<img src="' . $ruta_img_empresa . '" alt="Imagen derecha empresa" width="180px" height="80px" style="position: absolute;top: 80px;right: 0;"/>';
		$pdf->writeHTML($html_con_imagen, true, false, false, false, 'H');

		// Agregamos margenes
		$pdf->SetMargins($margen_izq, $margen_sup, $margen_der);

		$html = str_replace('"', "'", $constancia->contenido2);

		eval("\$html = \"$html\";");

		$html = str_replace("'", '"', $html);

		if ($colaborador->sexo == 'Femenino') {
			$html = str_replace("el&nbsp;<strong>Sr.", 'la&nbsp;<strong>Sra.', $html);
			$html = str_replace("el&nbsp;<strong>Sr.", 'la&nbsp;<strong>Sra.', $html);
			$html = str_replace("var&oacute;n", 'mujer', $html);
			$html = str_replace("Paname&ntilde;o", 'Paname&ntilde;a', $html);
			$html = str_replace("trabajador activo", 'trabajadora activa', $html);
			$html = str_replace("El Sr.", 'La Sra.', $html);
			$html = str_replace("El Se&ntilde;or", 'La Se&ntilde;ora', $html);
			$html = str_replace("el se&ntilde;or", 'la se&ntilde;ora', $html);
		}

		// Setemos todo el texto de la carta
		$pdf->writeHTML($html, true, false, false, false, 'J');
	}
	//===================================================================================================
	$conAcentos = array('á', 'é', 'í', 'ó', 'ú', ' ', '&aacute;', '&eacute;', '&iacute;', '&oacute;', '&uacute;', '(pdf)');
	$sinAcentos = array('a', 'e', 'i', 'o', 'u', '_', 'a', 'e', 'i', 'o', 'u', '');

	$nombre_constancia = strtolower($nombre_constancia);
	$nombre_constancia = str_replace($conAcentos, $sinAcentos, $nombre_constancia);

	$fichero = $nombre_constancia . '_' . $colaborador->cedula . '.pdfa';


	ob_clean();
	$pdf->Output($fichero, 'I');

	if ($conf_general->codigo_verificacion == 'Si') {

		$sql = "SELECT COUNT(*) as existe
				FROM nompersonal_constancias n
				WHERE n.codigo_validacion='{$CODIGO_VERIFICACION}' AND n.ficha='{$ficha}'";

		$res = $conexion->query($sql)->fetch_object();

		if ($res->existe == 0) {
			$fecha_emision = date("Y-m-d H:i:s");

			$sql = "INSERT INTO nompersonal_constancias (codigo_validacion, tipo_constancia, ficha, fecha_emision)
	                VALUES
	                ('{$CODIGO_VERIFICACION}', '{$constancia_id}', '{$ficha}', '{$fecha_emision}')";

			$conexion->query($sql);
		}
	}
} else {
	echo "<script>alert('Acceso Denegado');</script>";
	echo "<script>document.location.href = '../list_personal.php';</script>";
}
