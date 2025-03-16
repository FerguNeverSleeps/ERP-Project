<?php 
//=============================================================================
// Validando fechas y variables
$input_fechas = array('fecnac', 'fecing', 'fecha_decreto', 'fecha_decreto_baja', 'inicio_periodo', 'fin_periodo');

foreach ($input_fechas as $campo_fecha) 
{
	if(isset($_POST[$campo_fecha]))
	{
		$$campo_fecha = str_replace('/', '-', $_POST[$campo_fecha]);
		$$campo_fecha = DateTime::createFromFormat('d-m-Y', $$campo_fecha); 
		$$campo_fecha = ($$campo_fecha !== false) ? $$campo_fecha->format('Y-m-d') : '';
	}
}

$niveles = array('codnivel1', 'codnivel2', 'codnivel3', 'codnivel4', 'codnivel5', 'codnivel6', 'codnivel7');

foreach ($niveles as $nivel) {
	$$nivel = 0;

	if(isset($_POST[$nivel])  &&  $_POST[$nivel] != '')
	{
		$$nivel = $_POST[$nivel];
	}
}

$suesal = (isset($_POST['suesal']) ? $_POST['suesal'] : 0);

$save = (isset($_POST['btn_guardar'])  || isset($_POST['btn_guardar1']) || isset($_POST['btn_guardar2']) ||
         isset($_POST['btn_guardar3']) || isset($_POST['btn_guardar4'])) ? true : false ;
//=============================================================================


if(isset($_POST['btn_guardar']))
{	ld($_POST);
	$sql = "UPDATE nompersonal SET
				nombres            = '".$_POST['nombres']."',
				apellidos          = '".$_POST['apellidos']."',
				cedula             = '".$_POST['cedula']."',
		        nacionalidad       = '".$_POST['nacionalidad']."',
		        sexo               = '".$_POST['sexo']."',
		        estado_civil       = '".$_POST['estado_civil']."',
		        fecnac             = '".$fecnac."',
		        lugarnac           = '".$_POST['lugarnac']."',
		        codpro             = '".$_POST['codpro']."',
		        direccion          = '".$_POST['direccion']."',
		        telefonos          = '".$_POST['telefonos']."',
		        email              = NULLIF('".$_POST['email']."',''),
				tipnom             = '".$_POST['tipnom']."', 
				ficha              = '".$_POST['ficha']."',  
				estado             = '".$_POST['estado']."',
				fecing             = '".$fecing."', 
				forcob             = '".$_POST['forcob']."',
	            codbancob          = NULLIF('". (isset($_POST['codbancob']) ? $_POST['codbancob'] : '') ."',''),
	            cuentacob          = NULLIF('". (isset($_POST['cuentacob']) ? $_POST['cuentacob'] : '') ."',''),
	            tipemp             = '".$_POST['tipemp']."',
	            inicio_periodo     = NULLIF('".$inicio_periodo."',''),
	            fin_periodo        = NULLIF('".$fin_periodo."',''),
	            puesto_id          = NULLIF('". (isset($_POST['puesto_id']) ? $_POST['puesto_id'] : '')."',''), 
	            turno_id           = NULLIF('".$_POST['turno_id']."',''),
	            nomposicion_id     = NULLIF('". (isset($_POST['nomposicion_id']) ? $_POST['nomposicion_id'] : '' )."',''),
	            hora_base          = '".$_POST['hora_base']."',
	            codcat             = '".$_POST['codcat']."',
	            codcargo           = '".$_POST['codcargo']."',
	            suesal             = '".$suesal."',  
	            codnivel1	       = '".$codnivel1."',
	            codnivel2	       = '".$codnivel2."',
	            codnivel3	       = '".$codnivel3."',
	            codnivel4          = '".$codnivel4."',
	            codnivel5          = '".$codnivel5."',
	            codnivel6          = '".$codnivel6."',
	            codnivel7          = '".$codnivel7."' ,
				dv                 = NULLIF('".$_POST['dv']."',''),
          		num_decreto        = NULLIF('".$_POST['num_decreto']."',''),
          		fecha_decreto      = NULLIF('{$fecha_decreto}','') ,
          		num_decreto_baja   = NULLIF('".$_POST['num_decreto_baja']."',''),
          		fecha_decreto_baja = NULLIF('{$fecha_decreto_baja}',''),
          		siacap             = NULLIF('".$_POST['siacap']."',''),
          		seguro_social      = NULLIF('".$_POST['seguro_social']."',''),
          		segurosocial_sipe  = NULLIF('".$_POST['segurosocial_sipe']."',''),
          		tipo_empleado      = '".$tipo_empleado."',
          		clave_ir           = '".$clave_ir."',
          		apellido_materno   = '".$apellido_materno."',
          		apellido_casada    = '".$apellido_casada."',
          		observaciones      = '".$observaciones."'
	            	                     
		    WHERE personal_id = '".$personal_id."'";

///echo $sql; exit;

	$res = $db->query($sql);
}
else if(isset($_POST['btn_guardar1']))
{
	$sql = "UPDATE nompersonal SET
				nombres            = '".$_POST['nombres']."',
				apellidos          = '".$_POST['apellidos']."',
				cedula             = '".$_POST['cedula']."',
		        nacionalidad       = '".$_POST['nacionalidad']."',
		        sexo               = '".$_POST['sexo']."',
		        estado_civil       = '".$_POST['estado_civil']."',
		        fecnac             = '".$fecnac."',
		        lugarnac           = '".$_POST['lugarnac']."',
		        codpro             = '".$_POST['codpro']."',
		        direccion          = '".$_POST['direccion']."',
		        telefonos          = '".$_POST['telefonos']."',
		        email              = NULLIF('".$_POST['email']."','')             
		    WHERE personal_id = '".$personal_id."'";
	$res = $db->query($sql);
}
else if(isset($_POST['btn_guardar2']))
{
	$sql = "UPDATE nompersonal SET
				tipnom             = '".$_POST['tipnom']."', 
				ficha              = '".$_POST['ficha']."',  
				estado             = '".$_POST['estado']."',
				fecing             = '".$fecing."', 
				forcob             = '".$_POST['forcob']."',
	            codbancob          = NULLIF('". (isset($_POST['codbancob']) ? $_POST['codbancob'] : '') ."',''),
	            cuentacob          = NULLIF('". (isset($_POST['cuentacob']) ? $_POST['cuentacob'] : '') ."',''),
	            tipemp             = '".$_POST['tipemp']."',
	            inicio_periodo     = NULLIF('".$inicio_periodo."',''),
	            fin_periodo        = NULLIF('".$fin_periodo."',''),
	            puesto_id          = NULLIF('". (isset($_POST['puesto_id']) ? $_POST['puesto_id'] : '')."',''), 
	            turno_id           = NULLIF('".$_POST['turno_id']."',''),
	            nomposicion_id     = NULLIF('". (isset($_POST['nomposicion_id']) ? $_POST['nomposicion_id'] : '' )."',''),
	            hora_base          = '".$_POST['hora_base']."',
	            codcat             = '".$_POST['codcat']."',
	            codcargo           = '".$_POST['codcargo']."',
	            suesal             = '".$suesal."'      
		    WHERE personal_id = '".$personal_id."'";
	$res = $db->query($sql);
}
else if(isset($_POST['btn_guardar3']))
{
	$sql = "UPDATE nompersonal SET
	            codnivel1	       = '".$codnivel1."',
	            codnivel2	       = '".$codnivel2."',
	            codnivel3	       = '".$codnivel3."',
	            codnivel4          = '".$codnivel4."',
	            codnivel5          = '".$codnivel5."',
	            codnivel6          = '".$codnivel6."',
	            codnivel7          = '".$codnivel7."'           
		    WHERE personal_id = '".$personal_id."'";
	$res = $db->query($sql);
}
else if(isset($_POST['btn_guardar4']))
{
	$sql = "UPDATE nompersonal SET
				dv                 = NULLIF('".$_POST['dv']."',''),
          		num_decreto        = NULLIF('".$_POST['num_decreto']."',''),
          		fecha_decreto      = NULLIF('{$fecha_decreto}','') ,
          		num_decreto_baja   = NULLIF('".$_POST['num_decreto_baja']."',''),
          		fecha_decreto_baja = NULLIF('{$fecha_decreto_baja}',''),
          		siacap             = NULLIF('".$_POST['siacap']."',''),
          		seguro_social      = NULLIF('".$_POST['seguro_social']."',''),
          		segurosocial_sipe  = NULLIF('".$_POST['segurosocial_sipe']."','')
		    WHERE personal_id = '".$personal_id."'";
	$res = $db->query($sql);
}

if($save)
{
	if($res)
		echo "<script>document.location.href = 'editar_integrante.php?personal_id=".$personal_id."&editar';</script>";
	else
		echo "<script>alert('¡Hay errores en el proceso!');</script>";
}
?>