<?php
require_once 'vendor/phpword/PhpWord/Autoloader.php';
date_default_timezone_set('America/Panama');

if(isset($_GET['ficha']) && isset($_GET['tipnom']))
{
	require_once('../../lib/database.php');
    require_once("../../../includes/clases/EnLetras.php");

	$ficha  = $_GET['ficha'];
	$tipnom = $_GET['tipnom'];
	$anio = $_GET['anio'];
	$db = new Database($_SESSION['bd']);
	\PhpOffice\PhpWord\Autoloader::register();
	\PhpOffice\PhpWord\Settings::loadConfig();

	//====================================================================================================
	// Consultar Datos Necesarios del Trabajador
	$sql = "SELECT  *                       
			FROM   nomempresa";
    $res_empresa         = $db->query($sql);
    
    $empresa             = $res_empresa->fetch_object();
    $jefe_planilla       = $empresa->jefe_planilla; 
    $cedula_jefe_planilla       = $empresa->ced_jefe_planilla; 

	$sql = "SELECT n.nombres, n.apellidos, n.cedula,n.seguro_social, c.des_car as cargo, 
				   CASE WHEN sexo='Masculino' THEN ' El Sr. ' ELSE ' La Sra. ' END as sr,
				   n.suesal, n.hora_base, n.fecing inicio_periodo, n.fecharetiro fin_periodo, p.descripcionLarga as proyecto
			FROM   nompersonal n
			LEFT JOIN nomcargos c ON c.cod_cargo=n.codcargo
            LEFT JOIN proyectos p ON p.IdProyecto=n.proyecto
			WHERE  n.ficha='{$ficha}' AND n.tipnom='{$tipnom}' ";
	$res = $db->query($sql);
	

	$trabajador = $res->fetch_object();

	$sql_periodos= "SELECT MIN(MONTH(fecha_pago)) as mes_inicio, MAX(MONTH(fecha_pago)) as mes_fin ,MIN(fecha_pago) as fecha_inicio, MAX(fecha_pago) as fecha_final
	FROM salarios_acumulados 
	WHERE ficha ='{$ficha}' and YEAR(fecha_pago)='{$anio}'";
	
	$res_periodos = $db->query($sql_periodos);
	
	$periodos_acum = $res_periodos->fetch_object();

	$primer_nombre   = $trabajador->nombres; 
	$primer_apellido = $trabajador->apellidos;
	$inicio_periodo = formato_fecha($periodos_acum->inicio_periodo,3); 
	$fin_periodo = formato_fecha($periodos_acum->fecha_final,3);
	$fecha_inicio = date('Y-m-d',strtotime($periodos_acum->inicio_periodo));
	$fecha_fin = date('Y-m-d',strtotime($periodos_acum->fecha_final));
	$mes_inicio = date('m',strtotime($periodos_acum->inicio_periodo));
	$mes_fin = date('m',strtotime($periodos_acum->fin_periodo));
	$anio_inicio = date('Y',strtotime($anio));
	$anio_fin = date('Y',strtotime($periodos_acum->fin_periodo));
	$fecha = formato_fecha('',1);

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
	$sr = $trabajador->primer_nombre." ".$primer_apellido;

	$nombre_completo =  $trabajador->sr." ".$primer_nombre . ' ' . $primer_apellido;
	//====================================================================================================

	
        
        $sql_campo_adicional = "SELECT valor
                                    FROM   nomcampos_adic_personal n
                                    WHERE  ficha='{$ficha}' AND tiponom='{$tipnom}' AND id=4";

	$res_campo_adicional = $db->query($sql_campo_adicional);

	$campo_adicional = $res_campo_adicional->fetch_object();

	/*$sql_acumulado = "
	SELECT 
		sum(islr) as isr, 
		sum(salario_bruto) as salario_bruto, 
		sum(Neto) as Neto 
	from 
		salarios_acumulados 
	where 
		ficha ='{$ficha}' and fecha_pago between '{$fecha_inicio}' AND '${fecha_fin}' AND tipo_planilla='{$tipnom}' ";*/

		$sql_acumulado = "SELECT ficha,
		sum(islr) as isr, 
		SUM(salario_bruto) salario_bruto,
		SUM(vacac) vacac,
		SUM(xiii) xiii,
		SUM(gtorep) gtorep,
		SUM(xiii_gtorep) xiii_gtorep,
		SUM(bono) bono,
		SUM(otros_ing) otros_ing,
		sum(Neto) as Neto 
	FROM 
		salarios_acumulados 
	WHERE 
		ficha ='{$ficha}' AND tipo_planilla='{$tipnom}' AND YEAR(fecha_pago) = '{$anio}' 
	GROUP BY ficha";


	$res_acumulado = $db->query($sql_acumulado);

	$acumulado = $res_acumulado->fetch_object();
        
    $devengado=$acumulado->salario_bruto+$acumulado->vacac+$acumulado->xiii+$acumulado->bono+$acumulado->xiii_gtorep+$acumulado->otros_ing; 
       
	$salario_mensual   = ($trabajador->suesal*45)*4.33;
	$salario_bruto     = $trabajador->suesal;
	$seguro_social     = obtenerSeguroSocial($ficha, $tipnom, $mes, $anio);
	$seguro_educativo  = obtenerSeguroEducativo($ficha, $tipnom, $mes, $anio);
	$seguro_social     = $trabajador->suesal*0.0975;
	$seguro_educativo  = $trabajador->suesal*0.0125;
	$total_deducciones = $seguro_social + $seguro_educativo; 
	$salario_neto      = $acumulado->Neto;
        
    $seguro_vida   = $campo_adicional->valor;
    $seguro_colectivo = 2*$seguro_vida;
    
    $isr=((($salario_mensual*13)-11000)*0.15)/13;
    $isr = $acumulado->isr;

	$salario_mensual   = number_format($salario_mensual, 2, '.', '');
	$salario_bruto     = number_format($salario_bruto, 2, '.', '');
	$seguro_social     = number_format($seguro_social, 2, '.', '');
	$seguro_educativo  = number_format($seguro_educativo, 2, '.', '');
	$seguro_colectivo  = number_format($seguro_colectivo, 2, '.', '');
	$isr               = number_format($isr, 2, '.', '');
	$total_deducciones = number_format($total_deducciones, 2, '.', '');
	$total_deducciones = ($total_deducciones==0.00) ? '  '.$total_deducciones : $total_deducciones;	
	$seguro_colectivo  = ($seguro_colectivo==0.00) ? '-' : $seguro_colectivo;
	$isr               = ($isr==0.00) ? '-' : $isr;		
	$salario_neto      = number_format($salario_neto, 2, '.', '');
	$devengado	       = number_format($devengado	, 2, '.', '');
    $proyecto		   = $trabajador->proyecto; 
    $V                 = new EnLetras(); 
    $monto_letras      = strtoupper($V->ValorEnLetras(($trabajador->suesal),"balboas"));
    
    $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('templates/carta_certificacion_isr.docx');

	$templateProcessor->setValue('trabajador', htmlspecialchars($nombre_completo));
	$templateProcessor->setValue('sr', htmlspecialchars($sr));
	$templateProcessor->setValue('cedula', htmlspecialchars($trabajador->cedula));
        $templateProcessor->setValue('ficha', htmlspecialchars($ficha));
	$templateProcessor->setValue('cargo', htmlspecialchars($trabajador->cargo));

	$dia  = date('j'); 
	$mes  = date('n');
	//$anio = date('Y');
	$templateProcessor->setValue('salario_letras',    htmlspecialchars($monto_letras));
	$templateProcessor->setValue('salario_mensual',   htmlspecialchars($salario_mensual));
	$templateProcessor->setValue('seguro_colectivo',   htmlspecialchars($seguro_colectivo));
	$templateProcessor->setValue('isr',   htmlspecialchars($isr));
	$templateProcessor->setValue('devengado',     htmlspecialchars($devengado));
	$templateProcessor->setValue('salario_bruto',     htmlspecialchars($salario_bruto));
	$templateProcessor->setValue('seguro_social',     htmlspecialchars($seguro_social));
	$templateProcessor->setValue('seguro_educativo',  htmlspecialchars($seguro_educativo));
	$templateProcessor->setValue('deduccion',         htmlspecialchars($total_deducciones));
	$templateProcessor->setValue('salario_neto',      htmlspecialchars($salario_neto));
	$templateProcessor->setValue('inicio_periodo',    htmlspecialchars($inicio_periodo));
	$templateProcessor->setValue('fin_periodo',       htmlspecialchars($fin_periodo));
	$templateProcessor->setValue('jefe_planilla',     htmlspecialchars($jefe_planilla));
    $templateProcessor->setValue('cedula_jefe_planilla',     htmlspecialchars($cedula_jefe_planilla));
	$templateProcessor->setValue('proyecto', htmlspecialchars($proyecto));
	//=====================================================================================================
	$mes_letras = array("Enero","Febrero","Marzo",
						"Abril","Mayo","Junio",
		                "Julio","Agosto","Septiembre",
		                "Octubre","Noviembre","Diciembre");
	$datetime1 = new DateTime($periodos_acum->fecha_inicio);
	$datetime2 = new DateTime($periodos_acum->fecha_final);
	$interval = $datetime1->diff($datetime2);
	$meses= $interval->format('%m');
	$meses+=1;
	
	$mes  = $mes_letras[$mes - 1];
	$mes_inicio  = $mes_letras[$periodos_acum->mes_inicio - 1];
	$mes_fin  = $mes_letras[$periodos_acum->mes_fin - 1];

	$templateProcessor->setValue('dia', htmlspecialchars($dia));
	$templateProcessor->setValue('mes_lower', htmlspecialchars(strtolower($mes)));	
	$templateProcessor->setValue('mes_upper', htmlspecialchars(strtoupper($mes)));
	$templateProcessor->setValue('anio', htmlspecialchars($anio));
    $templateProcessor->setValue('mes_inicio', htmlspecialchars($mes_inicio));
    $templateProcessor->setValue('mes_fin', htmlspecialchars($mes_fin));
    $templateProcessor->setValue('anio_inicio', htmlspecialchars($anio));
    $templateProcessor->setValue('anio_fin', htmlspecialchars($anio));
    $templateProcessor->setValue('meses', htmlspecialchars($meses));
    $templateProcessor->setValue('neto', htmlspecialchars($salario_neto));
	$templateProcessor->setValue('fecha', htmlspecialchars($fecha));
	//====================================================================================================

	$filename = 'carta_certificacion_isr_'.$trabajador->cedula.'.docx';
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
	ob_clean();
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

function obtenerSalarioBruto($ficha, $sueldo)
{
	global $db;

	$salario_bruto = $gastos_rep = 0.0;

    // Consultar GASTOS DE REPRESENTACION 
	$sql = "SELECT nap.valor AS monto 
	        FROM   nomcampos_adic_personal nap 
			INNER  JOIN nomcampos_adicionales na ON nap.id =  na.id 
			WHERE  nap.ficha = '{$ficha}' AND UPPER(na.descrip)='GASTOS DE REPRESENTACION'";

	$res =$db->query($sql);

	if( $data = $res->fetch_object() )
		$gastos_rep = $data->monto;

	$salario_bruto = $sueldo + $gastos_rep;

	return $salario_bruto;
}

function obtenerSeguroEducativo($ficha, $tipnom, $mes, $anio)
{
	global $db;

	$seguro_educativo = 0.0;

    // Consultar SEGURO EDUCATIVO (S.E.) - Tabla: nomconceptos - codcon: 201
    $sql = "SELECT SUM(monto) AS monto 
			FROM   nom_movimientos_nomina
			WHERE  codcon=201
			AND    ficha='{$ficha}' AND tipnom='{$tipnom}'
			AND    codnom IN (SELECT codnom FROM nom_nominas_pago WHERE mes='{$mes}' AND anio='{$anio}')";

	$res = $db->query($sql);

	if( $data = $res->fetch_object() )
		$seguro_educativo = $data->monto;

	return $seguro_educativo;
}

function obtenerSeguroSocial($ficha, $tipnom, $mes, $anio)
{
	global $db;

	$seguro_social = 0.0;

    // Consultar SEGURO SOCIAL (S.S.) - Tabla: nomconceptos - codcon: 200
    $sql = "SELECT SUM(monto) AS monto 
			FROM   nom_movimientos_nomina
			WHERE  codcon=200
			AND    ficha='{$ficha}' AND tipnom='{$tipnom}'
			AND    codnom IN (SELECT codnom FROM nom_nominas_pago WHERE mes='{$mes}' AND anio='{$anio}')";

	$res = $db->query($sql);

	if( $data = $res->fetch_object() )
		$seguro_social = $data->monto;

	return $seguro_social;
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
?>