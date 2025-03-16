<?php
require ('mypdf.php');
require ('numerosALetras.class.php');
$conexion =  mysqli_connect( DB_HOST, DB_USUARIO, DB_CLAVE, $_SESSION['bd'] ) or die( 'No Hay Conexión con el Servidor de Mysql' );
        mysqli_query($conexion, 'SET CHARACTER SET utf8');
//require_once ('../../lib/database.php');
ob_start();
error_reporting(E_ALL & ~E_NOTICE);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
//echo "Peroooo";

// require ('numeros_letras_class.php');
//$conexion = new Database($_SESSION['bd']);
if( isset($_GET['tipo']))
{
	$cargo=$_SESSION['cargo_rhexpress'];
	$cedula=$_SESSION['cedula_rhexpress'];
	$ficha  = $_SESSION['ficha_rhexpress'];
	$nivel = $_SESSION['gerencia_rhexpress'];
	$tipo = $_GET['tipo'];
	$id_solicitud_caso = $_GET['id'];	
	// $tipnom        = 1;//(int) $_GET['tipnom'];
	// $constancia_id = 2;//(int) $_GET['constancia_id']; // id de la constancia/carta de trabajo

	if(!isset($_GET['cove']))
		$CODIGO_VERIFICACION .= str_pad($ficha, 4, "0", STR_PAD_LEFT);
	else
		$CODIGO_VERIFICACION = $_GET['cove'];
	//==============================================================================================
	$sql="SELECT * from nomempresa";
	$datos_empresa = $conexion->query($sql);
	$image = fetch_array($datos_empresa);	
	$var_izquierda   = '../../../includes/imagenes/'.$image['imagen_izq'];
	$nombre_empresa_titular=$image['nom_emp'];
	//=========================================================================
	$sql="SELECT id_solicitudes_campos,nombre
	FROM solicitudes_campos WHERE solicitudes_tipo_id = '$tipo'";
	$cantidad_campos = $conexion->query($sql);
	//================================================================================
	$sql="SELECT fecha_registro FROM solicitudes_casos WHERE id_solicitudes_casos='id_solicitud_caso'";
	$fecha = $conexion->query($sql);	
	// //===============================================================================================
	$sql="SELECT val.solicitudes_campos_id,cas.fecha_registro,cam.nombre,val.valores FROM solicitudes_valores as val
	LEFT JOIN solicitudes_casos as cas on cas.id_solicitudes_casos=val.solicitudes_casos_id
	LEFT JOIN solicitudes_campos as cam on cam.id_solicitudes_campos=val.solicitudes_campos_id
	WHERE solicitudes_casos_id='$id_solicitud_caso' ";
	$sol_reclamo = $conexion->query($sql);
	//=============================================================================================
	$sql="SELECT *,pro.descrip FROM nompersonal
	LEFT JOIN nomprofesiones as pro ON nompersonal.codpro=pro.codorg	
	WHERE cedula='$cedula'";
	$datos=$conexion->query($sql);
	//===================================================================================================
	$sql="SELECT *,nivel.descrip as sucursal,pro.descrip as profesion,car.des_car as cargo 
	FROM solicitudes_casos as s
	LEFT JOIN nompersonal as n ON n.cedula=s.cedula
	LEFT JOIN nomnivel1 as nivel ON n.codnivel1=nivel.codorg
	left join nomcargos as car on car.cod_car=n.codcargo
	LEFT JOIN nomprofesiones as pro ON n.codpro=pro.codorg
	WHERE id_solicitudes_casos='$id_solicitud_caso'";
	$datos_general = $conexion->query($sql);
	$info_colaborador=fetch_array($datos_general);
	$nombre_colaborador=$info_colaborador['apenom'];
	$sucursal=$info_colaborador['sucursal'];
	$nacionalidad=$info_colaborador['nacionalidad'];
	$primer_nombre=$info_colaborador['nombres'];
	$segundo_nombre=$info_colaborador['nombres2'];
	$apellido_materno=$info_colaborador['apellido_materno'];
	$apellido_casada=$info_colaborador['apellido_casada'];
	$primer_apellido=$info_colaborador['apellidos'];
	$correo=$info_colaborador['email'];
	$profesion=$info_colaborador['profesion'];
	$cargo=$info_colaborador['cargo'];
	$fichass=$info_colaborador['ficha'];
	$direccion2=$info_colaborador['direccion'];
	$estado_civil2=$info_colaborador['estado_civil'];
	$cedula2=$info_colaborador['cedula'];
	$fecha_ingres=$info_colaborador['fecing'];
	$telefonos=$info_colaborador['telefonos'];
	//$full_name=$info_colaborador['apenom'];
	//=====================================================================================
	// Crear reporte PDF
	$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, 'LETTER', true, 'UTF-8', false);
	$pdf->SetCreator(PDF_CREATOR);
	$pdf->SetAuthor('Leon Sosa');
	$pdf->SetTitle('Solicitud de reclamo de pago');

	$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
	$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

	$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

	$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
	
	$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

	$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

	$calibri      = TCPDF_FONTS::addTTFfont('vendor/tcpdf/fonts/ttf/calibri.ttf',  'TrueTypeUnicode', '', 32);
	$calibri_bold = TCPDF_FONTS::addTTFfont('vendor/tcpdf/fonts/ttf/calibrib.ttf', 'TrueTypeUnicode', '', 32);
	$calibri_bi   = TCPDF_FONTS::addTTFfont('vendor/tcpdf/fonts/ttf/calibriz.ttf', 'TrueTypeUnicode', '', 32);
	
	$pdf->AddPage();
	$pdf->Cell(65,5,' ','LT',0,'L',0);   // empty cell with left,top, and right borders
	$pdf->Cell(64,5,'' ,'T',0,'C',0);	
	$pdf->Cell(56,5,'','TR',0,'L',0);
    $pdf->Ln();
	//$pdf->Image('../../../includes/assets/img/logo_amaxonia.png', 8, 3, 120);
	$pdf->Image($var_izquierda,24,28,45,14); 
	$pdf->SetFont('helvetica','B',16);
	$pdf->Cell(65,6,'','L',0,'C',0);  // cell with left and right borders
	$pdf->Cell(64,5,$nombre_empresa_titular,'',0,'C',0);
	$pdf->Cell(56,5,'','R',0,'C',0);
    $pdf->Ln();
	$pdf->Cell(65,5,'','LB',0,'L',0);   // empty cell with left,bottom, and right borders
	$pdf->Cell(64,5,'','B',0,'C',0);
	$pdf->Cell(56,5,'','BR',0,'L',0);
	$pdf->Ln();
	$pdf->SetFont('helvetica','B',10);
	$pdf->Cell(185,5,'ACTUALIZACION ANUAL - DECLARACION DE BIENES PATRIMONIALES DE COLABORADORES','LRB',0,'C',0);
	$pdf->Ln();	
	$pdf->Cell(95,5,'I. DATOS GENERALES COLABORADOR','LRB',0,'L',0);
	$pdf->Cell(90,5,'FECHA','LRB',0,'L',0);
	$pdf->Ln();
	$pdf->Cell(185,5,date('d-m-Y'),'LRB',0,'R',0);
	$pdf->Ln();
	// $str = $full_name;
	// $names= (explode(" ",$str));
		
		$nacionalidad=$nacionalidad=='1' ? "Panamameño": "Extranjero";		

	$pdf->Cell(37,5,'Primer Apellido: ','LRB',0,'C',0);	
	$pdf->Cell(37,5,'Segundo Apellido:','LRB',0,'C',0);	
	$pdf->Cell(37,5,'Apellido de casada:','LRB',0,'C',0);	
	$pdf->Cell(37,5,'Primer Nombre:','LRB',0,'C',0);	
	$pdf->Cell(37,5,'Segundo Nombre:','LRB',0,'C',0);	
	$pdf->Ln();
	$pdf->Cell(37,5,$primer_apellido,'LBR',0,'C',0);	
	$pdf->Cell(37,5,$apellido_materno,'RB',0,'C',0);	
	$pdf->Cell(37,5,$apellido_casada,'RB',0,'C',0);	
	$pdf->Cell(37,5,$primer_nombre,'RB',0,'C',0);	
	$pdf->Cell(37,5,$segundo_nombre,'RB',0,'C',0);	
	$pdf->Ln();
	$pdf->Cell(37,5,'Nacionalidad: ','LRB',0,'C',0);	
	$pdf->Cell(37,5,'No.de Cedula:','LRB',0,'C',0);	
	$pdf->Cell(37,5,'Estado Civil:','LRB',0,'C',0);	
	$pdf->Cell(37,5,'','B',0,'C',0);	
	$pdf->Cell(37,5,'','RB',0,'C',0);	
	$pdf->Ln();
	$pdf->Cell(37,5,$nacionalidad,'LBR',0,'C',0);	
	$pdf->Cell(37,5,$cedula2,'RB',0,'C',0);	
	$pdf->Cell(37,5,$estado_civil2,'RB',0,'C',0);	
	$pdf->Cell(37,5,'','RB',0,'C',0);	
	$pdf->Cell(37,5,'','RB',0,'C',0);	
	$pdf->Ln();
	$pdf->Cell(74,5,'Profesion: ','LRB',0,'C',0);	
	$pdf->Cell(74,5,'Cargo actual:','LRB',0,'C',0);	
	$pdf->Cell(37,5,'Fecha ingreso:','LRB',0,'C',0);		
	$pdf->Ln();
	$pdf->Cell(74,5,$profesion,'LRB',0,'C',0);	
	$pdf->Cell(74,5,$cargo,'LRB',0,'C',0);	
	$pdf->Cell(37,5,$fecha_ingres,'LRB',0,'C',0);		
	$pdf->Ln();
	$pdf->Cell(37,5,'Correo electronico:','LRB',0,'C',0);	
	$pdf->Cell(37,5,'Telefono:','LRB',0,'C',0);		
	$pdf->Cell(37,5,'Direccion:','LRB',0,'C',0);		
	$pdf->SetFont('helvetica','B',7);	
	$tamano=strlen($direccion2);
	if ($tamano>=45) {
		$cadena1=substr($direccion2,0,$tamano/2);
		$cadena2=substr($direccion2,$tamano/2);		
		//$pdf->Cell(40,5,$cadena1.'-','LR',0,'L',0);
		$pdf->Cell(74,5,$cadena1.'-','LRB',0,'L',0);
	} else {
		$cadena1=$direccion2;
		$cadena2='';
		$pdf->Cell(74,5,$cadena1,'LRB',0,'L',0);
	}
	
	//$pdf->Cell(74,5,$cadena1,'LRB',0,'C',0);	
	//$pdf->Cell(37,5,'Direccion residencial: ','LRB',0,'C',0);	
	$pdf->Ln();
	//$pdf->SetFont('helvetica','B',7);	
	$pdf->Cell(37,5,$correo,'LRB',0,'C',0);	
	$pdf->Cell(37,5,$telefonos,'LRB',0,'C',0);		
	$pdf->Cell(111,5,$cadena2,'LRB',0,'C',0);
	//$pdf->Cell(37,5,'','LRB',0,'C',0);
	// $pdf->SetFont('helvetica','B',10);
	// //$pdf->Cell(111,5,'','RB',0,'C',0);	
    // $pdf->Ln();
	// $pdf->SetFont('helvetica','B',7);	
	// $pdf->Cell(185,5,'','LRB',0,'L',0);		
	$pdf->SetFont('helvetica','B',10);
	//}
	$datos=array();	
	while ($filas = mysqli_fetch_array($sol_reclamo)) {
		$datos[]=$filas['valores'];
		$empresa=$datos['23']!='' ? $datos['23'] : "N/A";
		$cargo=$datos['24']!='' ? $datos['24'] : "N/A";
	}

	$pdf->Ln();
	$pdf->Cell(185,5,'II. BALANCE DE LOS ACTIVOS & INGRESOS  DEL COLABORADOR ','LRB',0,'L',0);
	$pdf->Ln();
	$pdf->Cell(37,5,'Salario empresa: ','LRB',0,'C',0);	
	$pdf->Cell(37,5,$datos['0'],'LRB',0,'C',0);	
	$pdf->Cell(37,5,'','LRB',0,'C',0);	
	$pdf->Cell(37,5,'','LB',0,'C',0);	
	$pdf->Cell(37,5,'','RB',0,'C',0);	
	$pdf->Ln();
	$pdf->Cell(37,5,'Vivienda principal: ','LRB',0,'C',0);	
	$pdf->Cell(37,5,$datos['1'],'LRB',0,'C',0);	
	$pdf->Cell(37,5,'Valor vivienda:','LRB',0,'C',0);	
	$pdf->Cell(37,5,$datos['2'],'LRB',0,'C',0);	
	$pdf->Cell(37,5,'','LRB',0,'C',0);	
	$pdf->Ln();
	$pdf->Cell(37,5,'Automovil: ','LRB',0,'C',0);	
	$pdf->Cell(37,5,$datos['3'],'LRB',0,'C',0);	
	$pdf->Cell(37,5,'Tipo adquiriencia','LRB',0,'C',0);	
	$pdf->Cell(37,5,$datos['4'],'LRB',0,'C',0);	
	$pdf->Cell(37,5,'','LRB',0,'C',0);	
	$pdf->Ln();
	$pdf->Cell(37,5,'Valor automovil: ','LRB',0,'C',0);	
	$pdf->Cell(37,5,$datos['5'],'LRB',0,'C',0);	
	$pdf->Cell(37,5,'','LRB',0,'C',0);	
	$pdf->Cell(37,5,'','LRB',0,'C',0);	
	$pdf->Cell(37,5,'','LRB',0,'C',0);	
	$pdf->Ln();
	$pdf->Cell(37,5,'Terrenos: ','LRB',0,'C',0);	
	$pdf->Cell(37,5,$datos['6'],'LRB',0,'C',0);	
	$pdf->Cell(37,5,'Monto:','LRB',0,'C',0);	
	$pdf->Cell(37,5,$datos['7'],'LRB',0,'C',0);	
	$pdf->Cell(37,5,'','LRB',0,'C',0);	
	$pdf->Ln();
	$pdf->Cell(45,5,'Dividendos por Acciones: ','LRB',0,'C',0);	
	$pdf->Cell(29,5,$datos['8'],'LRB',0,'C',0);	
	$pdf->Cell(37,5,'Monto:','LRB',0,'C',0);	
	$pdf->Cell(37,5,$datos['9'],'LRB',0,'C',0);	
	$pdf->Cell(37,5,'','LRB',0,'C',0);	
	$pdf->Ln();
	$pdf->Cell(37,5,'Intereses de cuentas: ','LRB',0,'C',0);	
	$pdf->Cell(37,5,$datos['10'],'LRB',0,'C',0);	
	$pdf->Cell(37,5,'Monto:','LRB',0,'C',0);	
	$pdf->Cell(37,5,$datos['11'],'LRB',0,'C',0);	
	$pdf->Cell(37,5,'','LRB',0,'C',0);	
	$pdf->Ln();
	$pdf->Cell(37,5,'Herencias: ','LRB',0,'C',0);	
	$pdf->Cell(37,5,$datos['12'],'LRB',0,'C',0);	
	$pdf->Cell(37,5,'Monto:','LRB',0,'C',0);	
	$pdf->Cell(37,5,$datos['13'],'LRB',0,'C',0);	
	$pdf->Cell(37,5,'','LRB',0,'C',0);	
	$pdf->Ln();
	$pdf->Cell(37,5,'Comisiones: ','LRB',0,'C',0);	
	$pdf->Cell(37,5,$datos['14'],'LRB',0,'C',0);	
	$pdf->Cell(37,5,'Monto:','LRB',0,'C',0);	
	$pdf->Cell(37,5,$datos['15'],'LRB',0,'C',0);	
	$pdf->Cell(37,5,'','LRB',0,'C',0);	
	$pdf->Ln();
	$pdf->Cell(37,5,'Bonificaciones: ','LRB',0,'C',0);	
	$pdf->Cell(37,5,$datos['16'],'LRB',0,'C',0);	
	$pdf->Cell(37,5,'Monto:','LRB',0,'C',0);	
	$pdf->Cell(37,5,$datos['17'],'LRB',0,'C',0);	
	$pdf->Cell(37,5,'','LRB',0,'C',0);	
	$pdf->Ln();
	$pdf->Cell(42,5,'Servicios profesionales: ','LRB',0,'C',0);	
	$pdf->Cell(32,5,$datos['18'],'LRB',0,'C',0);	
	$pdf->Cell(37,5,'Monto:','LRB',0,'C',0);	
	$pdf->Cell(37,5,$datos['19'],'LRB',0,'C',0);	
	$pdf->Cell(37,5,'','LRB',0,'C',0);	
	$pdf->Ln();
	$pdf->Cell(185,5,'III.  USTED PRESTA SERVICIOS PROFESIONALES, FUERA DE LA EMPRESA POR FAVOR INDIQUE:','LRB',0,'L',0);
	$pdf->Ln();
	$pdf->Cell(42,5,'Empresa: ','LRB',0,'C',0);	
	$pdf->Cell(32,5,$empresa,'LRB',0,'C',0);	
	$pdf->Cell(37,5,'','LRB',0,'C',0);	
	$pdf->Cell(37,5,'','LRB',0,'C',0);	
	$pdf->Cell(37,5,'','LRB',0,'C',0);	
	$pdf->Ln();
	$pdf->Cell(42,5,'Cargo desempeñado: ','LRB',0,'C',0);	
	$pdf->Cell(32,5,$cargo,'LRB',0,'C',0);	
	$pdf->Cell(37,5,'','LRB',0,'C',0);	
	$pdf->Cell(37,5,'','LRB',0,'C',0);	
	$pdf->Cell(37,5,'','LRB',0,'C',0);	
	$pdf->Ln();	
	$pdf->Cell(148,5,'Dicha función presenta conflicto de Interés con el papel desempeñado en la Empresa ','LRB',0,'C',0);	
	$pdf->Cell(37,5,$datos['20'],'LRB',0,'C',0);		
	$pdf->Ln();
	$pdf->Cell(60,5,'Monto aproximado que devenga','LB',0,'C',0);	
	$pdf->Cell(14,5,'','RB',0,'C',0);	
	$pdf->Cell(37,5,'','LRB',0,'C',0);	
	$pdf->Cell(37,5,$datos['21'],'LRB',0,'C',0);	
	$pdf->Cell(37,5,'','LRB',0,'C',0);	
	$pdf->Ln();
	$pdf->Cell(60,5,'¿Presenta usted Declaracion de','LR',0,'L',0);	
	$pdf->Cell(37,5,'','LR',0,'L',0);	
	$pdf->Cell(37,5,'','L',0,'L',0);	
	$pdf->Cell(51,5,'','R',0,'L',0);	
	$pdf->Ln();
	$pdf->Cell(14,5,'renta?','LB',0,'C',0);	
	$pdf->Cell(46,5,'','RB',0,'C',0);	
	$pdf->Cell(37,5,$datos['22'],'LRB',0,'C',0);	
	$pdf->Cell(37,5,'','B',0,'C',0);
	$pdf->Cell(51,5,'','RB',0,'C',0);	
	$pdf->Ln();
	$pdf->Cell(185,5,'Si señalo que si declara favor proporcionar anualmente copia al Area de Recursos Humanos','LRB',0,'L',0);	
	$pdf->Ln();
	$pdf->Cell(42,5,'Declaracion jurada ','LRB',0,'C',0);	
	$pdf->Cell(32,5,'','B',0,'C',0);	
	$pdf->Cell(37,5,'','B',0,'C',0);	
	$pdf->Cell(37,5,'','B',0,'C',0);	
	$pdf->Cell(37,5,'','RB',0,'C',0);	
	$pdf->Ln();
	$pdf->SetFont('helvetica','B',10);
	$pdf->Cell(37,5,'','L',0,'C',0);	
	$pdf->Cell(37,5,'','R',0,'C',0);	
	$pdf->Cell(37,5,'','L',0,'C',0);	
	$pdf->Cell(37,5,'','R',0,'C',0);	
	$pdf->Cell(37,5,'','LRB',0,'C',0);	
	$pdf->Ln();
	$pdf->Cell(37,5,'yo: ','LB',0,'L',0);	
	$pdf->Cell(37,5,'','B',0,'C',0);	
	$pdf->Cell(40,5,'No. identidad personal:','L	B',0,'L',0);	
	$pdf->Cell(34,5,'','B',0,'C',0);	
	$pdf->Cell(37,5,'','LRB',0,'C',0);	
	$pdf->Ln();
	$pdf->SetFont('helvetica','B',9);
	$pdf->Cell(185,5,'Declaro que no ejerzo funciones que presenten conflicto de interes con mis labores desempeñadas en  ','LR',0,'L',0);				
	$pdf->Ln();
	$pdf->Cell(185,5,$nombre_empresa_titular.' de igual manera certificó que los activos aquí declarados no provienen de fuentes ilicitas.','LRB',0,'L',0);			
	$pdf->Ln();
	$pdf->SetFont('helvetica','B',10);
	$pdf->Cell(74,5,'','LRB',0,'C',0);	
	$pdf->Cell(44,5,'','LRB',0,'C',0);	
	$pdf->Cell(67,5,'','RB',0,'C',0);		
	$pdf->Ln();
	$pdf->SetFont('helvetica','B',10);
	$pdf->Cell(74,5,'FIRMA DEL COLABORADOR','LRB',0,'C',0);	
	$pdf->Cell(44,5,'CARGO','LRB',0,'C',0);	
	$pdf->Cell(67,5,'REVISION POR CUMPLIMIENTO','RB',0,'C',0);		
	$pdf->Ln();
	

	
	//$pdf->cell(60,5,'quincena que reclama')
	//5 y 14 del Código de Trabajo de la República de Panamá.

	// Consultar datos generales de las constancias
	// $sql = "SELECT titulo, slogan, cargo_gerente, abreviatura, observaciones, validar_constancias, codigo_verificacion FROM nomconf_constancia";
	// $res = $conexion->query($sql);
	// $conf_general = $res->fetch_object();
	// $titulo = ($conf_general->titulo != '') ? $conf_general->titulo : '';  

	// Consultar datos específicos del modelo de constancia/carta de trabajo
	// $sql = "SELECT nombre, contenido1, contenido2, contenido3, titulo, observaciones, formula, fuente, 
	//                margen_sup, margen_izq, margen_der
	// 		FROM   nomtipos_constancia
	// 		WHERE  codigo='{$constancia_id}'";
	// $res = $conexion->query($sql);
	// $constancia = $res->fetch_object();

	// $margen_sup = ($constancia->margen_sup!=null) ? $constancia->margen_sup : PDF_MARGIN_TOP; // PDF_MARGIN_TOP 27
	// $margen_izq = ($constancia->margen_izq!=null) ? $constancia->margen_izq : 30; // PDF_MARGIN_LEFT  15
	// $margen_der = ($constancia->margen_der!=null) ? $constancia->margen_der : 29; // PDF_MARGIN_RIGHT 15

	// $nombre_constancia = utf8_encode($constancia->nombre);

	// if($constancia->titulo!=''){ $titulo = $constancia->titulo; }

	// Consultar datos del colaborador
	//  $sql = "SELECT n.nombres, n.apellidos, n.apenom, n.cedula, n.fecing, n.fecharetiro, n.suesal, n.sexo,
	//                c.cod_car as codcargo, c.des_car as cargo
	// 		FROM   nompersonal n
	// 		LEFT JOIN nomcargos c ON c.cod_cargo=n.codcargo
	// 		WHERE  n.ficha='{$ficha}' ";
	// $res = $conexion->query($sql);

	// $colaborador = $res->fetch_object();

	$CEDULA = $colaborador->cedula;
	$NOMBRE = $NOMBRE_COMPLETO = strtoupper($colaborador->nombres .' ' . $colaborador->apellidos);
	$NOMBRE_NORMAL = ucwords(strtolower($NOMBRE));
	$APELLIDOS = $colaborador->apellidos;

	$PRIMER_NOMBRE = trim($colaborador->nombres);
	$nombres = explode(' ', $PRIMER_NOMBRE);
	if(count($nombres)>0) $PRIMER_NOMBRE = $nombres[0];

	$PRIMER_APELLIDO = trim($colaborador->apellidos);
	$apellidos = explode(' ', $PRIMER_APELLIDO);
	if(count($apellidos)>0) $PRIMER_APELLIDO = $apellidos[0];

	$SUELDO = $colaborador->suesal;
	$CARGO  = $colaborador->cargo;
	$CARGO_MAYUS = strtoupper($colaborador->cargo);
	$FICHA = $ficha;

	$mes_letras = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio",
		                "Agosto","Septiembre","Octubre","Noviembre","Diciembre");

	// Fecha de ingreso
	list($anioing, $mesing, $diaing) = explode('-', $colaborador->fecing);
	$FECHA_INGRESO = $diaing .' de '. ucfirst(strtolower($mes_letras[$mesing-1])) .' de '. $anioing;

	// Fecha de egreso
	if(isset($colaborador->fecharetiro))
	{
		list($anioegr, $mesegr, $diaegr) = explode('-', $colaborador->fecharetiro);
		$FECHA_EGRESO = $diaegr .'/'. $mesegr .'/'. $anioegr;
	}

    //=============================================================================================
	// Día, mes y año actual
	$DIA  = date('d');
	$MES  = strtolower($mes_letras[date('n')-1]);
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

    if($fila = $conexion->query($sql)->fetch_array())
    {
    	$mes_actual  = ($fila['mes']  != null) ? $fila['mes']  : $mes_actual;
    	$anio_actual = ($fila['anio'] != null) ? $fila['anio'] : $anio_actual;
    }

    // Primero voy a consultar si ya se cancelo alguna quincena del mes actual
    $sql = "SELECT DATE_FORMAT(n.periodo_ini,'%e') AS dia_inicio, DATE_FORMAT(n.periodo_fin,'%e') AS dia_fin
			FROM   nom_nominas_pago n 
			WHERE  n.periodo_fin=(SELECT MAX(periodo_fin) 
				                  FROM   nom_nominas_pago 
				                  WHERE  anio={$anio_actual} AND mes={$mes_actual} AND tipnom='{$tipnom}')";

	if($fila = $conexion->query($sql)->fetch_array())
	{
		$dia_inicio_quincena = $fila['dia_inicio']; // Comienzo de la quincena
		$dia_fin_quincena    = $fila['dia_fin'];

		if($dia_inicio_quincena==1)
		{
			$ini_ultima_quincena = date("Y-m-d", mktime(0, 0, 0, $mes_actual, $dia_fin_quincena+1, $anio_actual));
			$fin_ultima_quincena = date("Y-m-d",(mktime(0, 0, 0, $mes_actual+1, 1, $anio_actual)-1));			
		}
	}

    // Consultar GASTOS DE REPRESENTACION 
	$sql = "SELECT nap.valor AS monto 
	        FROM   nomcampos_adic_personal nap 
			INNER  JOIN nomcampos_adicionales na ON nap.id =  na.id 
			WHERE  nap.ficha='{$FICHA}' AND UPPER(na.descrip)='GASTOS DE REPRESENTACION'";

	$GASTOS_REPRESENTACION = 0;

	if($fila = $conexion->query($sql)->fetch_array())
	{
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

	if($fila = $conexion->query($sql)->fetch_array())
	{
		$SEGURO_SOCIAL = $fila['monto'];

		if($dia_inicio_quincena==1) // Es la primera quincena
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

	if($fila = $conexion->query($sql)->fetch_array())
	{
		$IMPUESTO_SOBRE_RENTA = $fila['monto'];

		if($dia_inicio_quincena==1) // Es la primera quincena
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

	if($fila = $conexion->query($sql)->fetch_array())
	{
		$SEGURO_EDUCATIVO = $fila['monto'];

		if($dia_inicio_quincena==1) // Es la primera quincena
		{
			$SEGURO_EDUCATIVO *= 2;
		}
	}

	// Consultar Casa Comercial - Tabla nomconceptos - codcon del 501 al 599
	$sql = "SELECT SUM(n.mtocuota) as monto
	     FROM   nomprestamos_cabecera n
	     WHERE  n.ficha='{$FICHA}' AND n.estadopre='Pendiente'";

	if($fila = $conexion->query($sql)->fetch_array())
	{
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

	if ($IMPUESTO_SOBRE_RENTA_SALARIO >= 11001 && $IMPUESTO_SOBRE_RENTA_SALARIO <= 50000) 
	{
    	$IMPUESTO_SOBRE_RENTA_SALARIO  = (($IMPUESTO_SOBRE_RENTA_SALARIO - 11000) * 0.15)/13;
	} 
	elseif ($IMPUESTO_SOBRE_RENTA_SALARIO >= 50001 && $IMPUESTO_SOBRE_RENTA_SALARIO <= 500000) 
	{
		$IMPUESTO_SOBRE_RENTA_SALARIO  =   $SUELDO * 0.25;
	} 
	else 
	{
		$IMPUESTO_SOBRE_RENTA_SALARIO  =  0;
	}

    $IMPUESTO_SOBRE_RENTA_GASTOR   =  $GASTOS_REPRESENTACION*12;
    if($IMPUESTO_SOBRE_RENTA_GASTOR <= 25000)
    {
       $IMPUESTO_SOBRE_RENTA_GASTOR  =  $GASTOS_REPRESENTACION * 0.10;
    }
	else
	{
	  $IMPUESTO_SOBRE_RENTA_GASTOR  =  (($IMPUESTO_SOBRE_RENTA_GASTOR - 25000)* 0.10)/12;
	  $IMPUESTO_SOBRE_RENTA_GASTOR  =  $GASTOS_REPRESENTACION + (2500/12); 
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

	if($constancia->formula!='')
	{
		eval($constancia->formula);
	}
	//===================================================================================================
	// Variables para las que se necesita conocer el año fiscal

	$sql = "SELECT SUM(nm.monto) as salario_anual 
			FROM   nom_movimientos_nomina nm
			WHERE  nm.ficha='{$FICHA}' AND nm.anio={$ANIO_FISCAL} 
			AND    nm.codcon=100";

	if($fila = $conexion->query($sql)->fetch_array())
	{
		$SALARIOS_ANIO_FISCAL = $fila['salario_anual'];
		$DECIMO_TERCER_MES_SALARIO = ($SALARIOS_ANIO_FISCAL / 3) / 4;
		$SALARIOS_ANIO_FISCAL = number_format($SALARIOS_ANIO_FISCAL, 2, '.', ',');
		$DECIMO_TERCER_MES_SALARIO = number_format($DECIMO_TERCER_MES_SALARIO, 2, '.', ',');
	}

	$sql = "SELECT SUM(nm.monto) as isr_salario_anual
			FROM   nom_movimientos_nomina nm
			WHERE  nm.ficha='{$FICHA}' AND nm.anio={$ANIO_FISCAL}  
			AND    nm.codcon=202"; // AND UPPER(nm.descrip) LIKE 'IMPUESTO SOBRE LA RENTA (I.S.R.)%' 

	if($fila = $conexion->query($sql)->fetch_array())
	{
		$ISR_SALARIOS = $fila['isr_salario_anual'];
		$ISR_SALARIOS = number_format($ISR_SALARIOS, 2, '.', ',');
	}

	$sql = "SELECT SUM(nm.monto) as gastos_anual 
			FROM   nom_movimientos_nomina nm
			WHERE  nm.ficha='{$FICHA}' AND nm.anio={$ANIO_FISCAL} 
			AND    nm.codcon=145"; // UPPER(nm.descrip) LIKE 'GASTOS DE REPRESENTACION'

	if($fila = $conexion->query($sql)->fetch_array())
	{
		$GASTOS_REPR_ANIO_FISCAL  = $fila['gastos_anual'];
		$DECIMO_TERCER_MES_GASTOS = ($GASTOS_REPR_ANIO_FISCAL / 3) / 4;
		$GASTOS_REPR_ANIO_FISCAL  = number_format($GASTOS_REPR_ANIO_FISCAL, 2, '.', ',');
		$DECIMO_TERCER_MES_GASTOS = number_format($DECIMO_TERCER_MES_GASTOS, 2, '.', ',');
	}

	$sql = "SELECT SUM(nm.monto) as isr_gastos_anual
			FROM   nom_movimientos_nomina nm
			WHERE  nm.ficha='{$FICHA}' AND nm.anio={$ANIO_FISCAL} 
			AND    nm.codcon=207"; // 207 IMPUESTO SOBRE LA RENTA GR

	if($fila = $conexion->query($sql)->fetch_array())
	{
		$ISR_GASTOS_REP = $fila['isr_gastos_anual'];
		$ISR_GASTOS_REP = number_format($ISR_GASTOS_REP, 2, '.', ',');
	}	
	//===================================================================================================
	//===================================================================================================
	// Mostrar titulo
	if($titulo!='')
	{
		$pdf->SetXY(29, 55);

		if($constancia->fuente == 'Calibri')
		{ 
			$pdf->SetFont($calibri, 'B', 16); 
		}

		$pdf->MultiCell(156, 12, $titulo, 0, 'C', false);

		$nombre_constancia=$titulo;

		$pdf->SetXY(29, 68);
	}
	else
	{
		// Esto se coloco para la certificacion de salarios
		if( strpos($constancia->nombre, 'Certificación de Salarios') !== FALSE )
			$pdf->SetXY(29, 55); 
	}

	$pdf->setCellHeightRatio(1.5);

	if($constancia->fuente == 'Calibri')
	{
		$pdf->SetFont($calibri, '', 12); 
	}

	if($constancia->contenido2!='')
	{
		$pdf->SetMargins($margen_izq, $margen_sup, $margen_der);

    	$html = str_replace('"', "'", $constancia->contenido2);

    	eval("\$html = \"$html\";");

    	$html = str_replace("'", '"', $html);

    	if($colaborador->sexo=='Femenino')
    	{
    		$html = str_replace("El <strong>Sr.", 'La <strong>Sra.', $html);
    		$html = str_replace("var&oacute;n", 'mujer', $html); 
    		$html = str_replace("Paname&ntilde;o", 'Paname&ntilde;a', $html); 
    		$html = str_replace("trabajador activo", 'trabajadora activa', $html); 
    		$html = str_replace("El Sr.", 'La Sra.', $html);
    		$html = str_replace("El Se&ntilde;or", 'La Se&ntilde;ora', $html);
    		$html = str_replace("el se&ntilde;or", 'la se&ntilde;ora', $html);
    	}

		$pdf->writeHTML($html, true, false, false, false, 'J');		
	}
	//===================================================================================================
	$conAcentos = array('á','é','í','ó','ú',' ','&aacute;','&eacute;','&iacute;','&oacute;','&uacute;', '(pdf)');
	$sinAcentos = array('a','e','i','o','u','_','a','e','i','o','u','');
	
	$nombre_constancia = strtolower($nombre_constancia);
	$nombre_constancia = str_replace($conAcentos, $sinAcentos, $nombre_constancia);
	
	$fichero = $nombre_constancia .'_'. $colaborador->cedula .'.pdf';


    ob_clean();
	$pdf->Output($fichero, 'I');

	if($conf_general->codigo_verificacion=='Si')
	{

		$sql = "SELECT COUNT(*) as existe
				FROM nompersonal_constancias n
				WHERE n.codigo_validacion='{$CODIGO_VERIFICACION}' AND n.ficha='{$ficha}'";

		$res = $conexion->query($sql)->fetch_object();

		if($res->existe==0)
		{
			$fecha_emision = date("Y-m-d H:i:s");

			$sql = "INSERT INTO nompersonal_constancias (codigo_validacion, tipo_constancia, ficha, fecha_emision)
	                VALUES
	                ('{$CODIGO_VERIFICACION}', '{$constancia_id}', '{$ficha}', '{$fecha_emision}')";

			$conexion->query($sql);
		}
	}
}
else
{
   echo "<script>alert('Acceso Denegado');</script>";
   echo "<script>document.location.href = '../list_personal.php';</script>";	
}
?>
