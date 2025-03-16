<?php
session_start();
ob_start();
?>
<?php
require_once '../lib/config.php';
require_once '../lib/common.php';
include ("../header.php");
include ("func_bd.php");
require("../procesos/PHPMailer_5.2.4/class.phpmailer.php");
include("../procesos/PHPMailer_5.2.4/class.smtp.php");

$conexion=conexion();
$host_ip = $_SERVER['REMOTE_ADDR'];

$reg=$_GET[reg];

$query               = "select * from nomempresa";
$result              = query($query,$conexion);
$row                 = fetch_array($result);
global $fecha_reg, $fecha_ini, $fecha_fin;
$correo_rrhh     = $row[correo_rrhh];
$correo_planilla     = $row[correo_planilla];
$correo_sistemas1     = $row[correo_sistemas1];
$correo_sistemas2     = $row[correo_sistemas2];
$correo_sistemas2_password     = $row[correo_sistemas2_password];
//PARAMETROS ENVIO CORREO
$mail             = new PHPMailer();
$mail->IsSMTP();
$email1            =$correo_rrhh;
$email2           =$correo_planilla;
$email3           =$correo_sistemas1;
//$email4           ='epoperez@gmail.com';

$mail->SMTPAuth   = true;
$mail->isSMTP();
$mail->SMTPDebug  = 0;
$mail->Host       = "smtp.gmail.com";
$mail->Port       = 465;
$mail->SMTPSecure = 'ssl';
$mail->Username   = $correo_sistemas2;
$mail->Password   = $correo_sistemas2_password;
$mail->From       = $correo_sistemas2;
$mail->FromName   = "Amaxonia Planilla";
$tit='Control de Asistencias - Aaprobacion para Planilla';

$mail->Subject = $tit;

//$mail->AddAddress ($email1);
if($email1!=="" && $email1!==0)
{
//    echo "AQUI";
//    exit;
    $mail->AddAddress ($email1);
}
//echo $email1;
//exit;
if($email2!=="" && $email2!==0)
{
    $mail->AddAddress ($email2);
}
if($email3!=="" && $email3!==0)
{
    $mail->AddAddress ($email3);
}


$datos="SELECT
     reloj_encabezado.cod_enca,
     reloj_encabezado.fecha_reg,
     reloj_encabezado.fecha_ini,
     reloj_encabezado.fecha_fin,
     nomturnos.turno_id,
     nomturnos.descripcion,
     nomturnos.entrada,
     nomturnos.tolerancia_entrada,
     nomturnos.inicio_descanso,
     nomturnos.salida_descanso,
     nomturnos.tolerancia_descanso,
     nomturnos.salida,
     nomturnos.tolerancia_salida,
     nomturnos.nocturno,
     nomturnos.tipo,
     nomcalendarios_personal.dia_fiesta,
     reloj_detalle.id,
     reloj_detalle.id_encabezado,
     reloj_detalle.ficha,
     reloj_detalle.fecha,     
     reloj_detalle.entrada,
     reloj_detalle.salmuerzo,
     reloj_detalle.ealmuerzo,
     reloj_detalle.salida,
     reloj_detalle.ordinaria,
     reloj_detalle.extra,
     reloj_detalle.extraext,
     reloj_detalle.extranoc,
     reloj_detalle.extraextnoc,
     reloj_detalle.domingo,
     reloj_detalle.tardanza,
     reloj_detalle.nacional,
     reloj_detalle.extranac,
     reloj_detalle.extranocnac,
     reloj_detalle.descextra1,
     reloj_detalle.mixtodiurna,
     reloj_detalle.mixtonoc,
     reloj_detalle.mixtoextdiurna,
     reloj_detalle.mixtoextnoc,
     reloj_detalle.dialibre,
     reloj_detalle.emergencia,
     reloj_detalle.descansoincompleto,
     reloj_detalle.tarea,
     reloj_detalle.lluvia,
     reloj_detalle.paralizacion_lluvia,
     reloj_detalle.altura_menor,
     reloj_detalle.altura_mayor,
     reloj_detalle.profundidad,
     reloj_detalle.tunel,
     reloj_detalle.martillo,
     reloj_detalle.rastrilleo,
     reloj_detalle.otras,
     reloj_detalle.descanso_contrato
FROM
     reloj_detalle 
     INNER JOIN  nomcalendarios_personal ON (reloj_detalle.ficha = nomcalendarios_personal.ficha AND reloj_detalle.fecha = nomcalendarios_personal.fecha)
     INNER JOIN  nomturnos ON reloj_detalle.turno = nomturnos.turno_id
     INNER JOIN reloj_encabezado ON reloj_detalle.id_encabezado = reloj_encabezado.cod_enca
where reloj_encabezado.cod_enca='$reg' order by ficha, fecha";
$rs = query($datos,$conexion);
//if((num_rows($rs)%2)==0)
//{
$i=1;
$fichaaux='';
$color=0;
$conv = 0 ;
$insert ="insert into reloj_procesar (ficha, fecha, minutos, concepto, id_encabezado) values " ;
while($fila=fetch_array($rs))
{
	
	if(($fila[entrada]=="")||($fila[salida]==""))
        {
		$color=1;
                continue;
        }
	if((($fila[salmuerzo]!="")&&($fila[ealmuerzo]==""))||(($fila[salmuerzo]=="")&&($fila[ealmuerzo]!="")))
        {
		$color=1;
                continue;
        }
        $conv = 1 ;
        $fecha = $fila[fecha];
        $dia = date("N",strtotime($fecha));
        $dia_fiesta = $fila[dia_fiesta];
        $ficha = $fila[ficha];
        $encabezado = $fila[id_encabezado];
        
	$entrada=$fila[entrada];
	$tolerancia_entrada=$fila[tolerancia_entrada];
	$inicio_descanso=$fila[inicio_descanso];
 	$salida_descanso=$fila[salida_descanso];
 	$tolerancia_descanso=$fila[tolerancia_descanso];
 	$salida=$fila[salida];
 	$tolerancia_salida=$fila[tolerancia_salida];
 	
 	$ordinaria=$fila[ordinaria];
 	$extra=$fila[extra];
 	$extraext=$fila[extraext];
 	$extranoc=$fila[extranoc];
 	$extraextnoc=$fila[extraextnoc];
 	$dom=$fila[domingo]; 	
 	$tardanza = $fila[tardanza];
 	$nacional = $fila[nacional];
 	$extranac = $fila[extranac];
 	$extranocnac = $fila[extranocnac];
 	$descextra1 = $fila[descextra1];

 	$dialibre=$fila[dialibre];

 	$mixtodiurna = $fila[mixtodiurna];
        $mixtonoc = $fila[mixtonoc];
        $mixtoextdiurna = $fila[mixtoextdiurna];
        $mixtoextnoc = $fila[mixtoextnoc];
        $dialibre = $fila[dialibre];
        $emergencia = $fila[emergencia];
        $descansoincompleto = $fila[descansoincompleto];
        $tarea = $fila[tarea];
        $lluvia = $fila[lluvia];
        $paralizacion_lluvia = $fila[paralizacion_lluvia];
        $altura_menor = $fila[altura_menor];
        $altura_mayor = $fila[altura_mayor];
        $profundidad = $fila[profundidad];
        $tunel = $fila[tunel];
        $martillo = $fila[martillo];
        $rastrilleo = $fila[rastrilleo];
        $otras = $fila[otras];
        $descanso_contrato = $fila[descanso_contrato];
        
 	if($dia==7)
	{
		if(($dom!="00:00")&&($dom!=""))
		{
                        $min=explode(":",$dom);
                        $minutos=($min[0]*60)+$min[1];
                        $concepto="domingo";
                        if(($minutos>=1)&&($minutos!=''))
                                $insert .="('".$ficha."','".$fecha."','".$minutos."','".$concepto."', '".$encabezado."'),";
                        //$guardar = query($insert,$conexion);
		}
		if(($extra!="00:00")&&($extra!=""))
		{
			$min=explode(":",$extra);
			$minutos=($min[0]*60)+$min[1];
                        $concepto="1.25x1.50";
			if(($minutos>=1)&&($minutos!=''))
				$insert .="('".$ficha."','".$fecha."','".$minutos."','".$concepto."', '".$encabezado."'),";
			//$guardar = query($insert,$conexion);	
		}
		if(($extranoc!="00:00")&&($extranoc!=""))
		{
			$min=explode(":",$extranoc);
			$minutos=($min[0]*60)+$min[1];
                        $concepto="d1.50x1.75";
			if(($minutos>=1)&&($minutos!=''))
				$insert .="('".$ficha."','".$fecha."','".$minutos."','".$concepto."', '".$encabezado."'),";
			//$guardar = query($insert,$conexion);	
		}
		if(($extraext!="00:00")&&($extraext!=""))
		{
			$min=explode(":",$extraext);
			$minutos=($min[0]*60)+$min[1];
                        $concepto="1.25x1.50x1.75";
			if(($minutos>=1)&&($minutos!=''))
				$insert .="('".$ficha."','".$fecha."','".$minutos."','".$concepto."', '".$encabezado."'),";
			//$guardar = query($insert,$conexion);	
		}
		if(($extraextnoc!="00:00")&&($extraextnoc!=""))
		{
			$min=explode(":",$extraextnoc);
			$minutos=($min[0]*60)+$min[1];
                        $concepto="1.50x1.75x1.75";
			if(($minutos>=1)&&($minutos!=''))
				$insert .="('".$ficha."','".$fecha."','".$minutos."','".$concepto."', '".$encabezado."'),";
			//$guardar = query($insert,$conexion);	
		}
		if(($tardanza!="00:00")&&($tardanza!=""))
		{
			$min=explode(":",$tardanza);
			$minutos=($min[0]*60)+$min[1];
                        $concepto="tardanza";
			if(($minutos>=1)&&($minutos!=''))
				$insert .="('".$ficha."','".$fecha."','".$minutos."','".$concepto."', '".$encabezado."'),";
			//$guardar = query($insert,$conexion);	
		}
		if(($mixtodiurna!="00:00")&&($mixtodiurna!=""))
		{
			$min=explode(":",$mixtodiurna);
			$minutos=($min[0]*60)+$min[1];
                        $concepto="1.50x1.50";
			if(($minutos>=1)&&($minutos!=''))
				$insert .="('".$ficha."','".$fecha."','".$minutos."','".$concepto."', '".$encabezado."'),";
			//$guardar = query($insert,$conexion);	
		}
		if(($mixtoextdiurna!="00:00")&&($mixtoextdiurna!=""))
		{
			$min=explode(":",$mixtoextdiurna);
			$minutos=($min[0]*60)+$min[1];
                        $concepto="1.50x1.50x1.75";
			if(($minutos>=1)&&($minutos!=''))
				$insert .="('".$ficha."','".$fecha."','".$minutos."','".$concepto."', '".$encabezado."'),";
			//$guardar = query($insert,$conexion);	
		}
		/*if(($mixtonoc!="00:00")&&($mixtonoc!=""))
		{
			$min=explode(":",$mixtonoc);
			$minutos=($min[0]*60)+$min[1];
                        $concepto="d1.50x1.75";
			if(($minutos>=1)&&($minutos!=''))
				$insert .="('".$ficha."','".$fecha."','".$minutos."','".$concepto."', '".$encabezado."'),";
			//$guardar = query($insert,$conexion);	
		}*/
		if(($mixtoextnoc!="00:00")&&($mixtoextnoc!=""))
		{
			$min=explode(":",$mixtoextnoc);
			$minutos=($min[0]*60)+$min[1];
                        $concepto="1.50x1.75x1.75";
			if(($minutos>=1)&&($minutos!=''))
				$insert .="('".$ficha."','".$fecha."','".$minutos."','".$concepto."', '".$encabezado."'),";
			//$guardar = query($insert,$conexion);	
		}
		if(($dialibre!="00:00")&&($dialibre!=""))
		{
			$min=explode(":",$dialibre);
			$minutos=($min[0]*60)+$min[1];
                        $concepto="1.50";
			if(($minutos>=1)&&($minutos!=''))
				$insert .="('".$ficha."','".$fecha."','".$minutos."','".$concepto."', '".$encabezado."'),";
			//$guardar = query($insert,$conexion);	
		}
		if(($emergencia!="00:00")&&($emergencia!=""))
		{
			$min=explode(":",$emergencia);
			$minutos=($min[0]*60)+$min[1];
                        $concepto="emergenciadom";
			if(($minutos>=1)&&($minutos!=''))
				$insert .="('".$ficha."','".$fecha."','".$minutos."','".$concepto."', '".$encabezado."'),";
			//$guardar = query($insert,$conexion);	
		}
		if(($descansoincompleto!="00:00")&&($descansoincompleto!=""))
		{
			$min=explode(":",$descansoincompleto);
			$minutos=($min[0]*60)+$min[1];
                        $concepto="descansoincompletodom";
			if(($minutos>=1)&&($minutos!=''))
				$insert .="('".$ficha."','".$fecha."','".$minutos."','".$concepto."', '".$encabezado."'),";
			//$guardar = query($insert,$conexion);	
		}
                
                if(($tarea!="00:00")&&($tarea!=""))
		{
			$min=explode(":",$tarea);
			$minutos=($min[0]*60)+$min[1];
			if(($minutos>=1)&&($minutos!=''))
				$insert .="('".$ficha."','".$fecha."','".$minutos."','tarea', '".$encabezado."'),";
			//$guardar = query($insert,$conexion);	
		}
                
                if(($lluvia!="00:00")&&($lluvia!=""))
		{
			$min=explode(":",$lluvia);
			$minutos=($min[0]*60)+$min[1];
			if(($minutos>=1)&&($minutos!=''))
				$insert .="('".$ficha."','".$fecha."','".$minutos."','lluvia', '".$encabezado."'),";
			//$guardar = query($insert,$conexion);	
		}
                
                if(($paralizacion_lluvia!="00:00")&&($paralizacion_lluvia!=""))
		{
			$min=explode(":",$paralizacion_lluvia);
			$minutos=($min[0]*60)+$min[1];
			if(($minutos>=1)&&($minutos!=''))
				$insert .="('".$ficha."','".$fecha."','".$minutos."','paralizacionlluvia', '".$encabezado."'),";
			//$guardar = query($insert,$conexion);	
		}
                
                if(($altura_menor!="00:00")&&($altura_menor!=""))
		{
			$min=explode(":",$altura_menor);
			$minutos=($min[0]*60)+$min[1];
			if(($minutos>=1)&&($minutos!=''))
				$insert .="('".$ficha."','".$fecha."','".$minutos."','alturamenor', '".$encabezado."'),";
			//$guardar = query($insert,$conexion);	
		}
                
                if(($altura_mayor!="00:00")&&($altura_mayor!=""))
		{
			$min=explode(":",$altura_mayor);
			$minutos=($min[0]*60)+$min[1];
			if(($minutos>=1)&&($minutos!=''))
				$insert .="('".$ficha."','".$fecha."','".$minutos."','alturamayor', '".$encabezado."'),";
			//$guardar = query($insert,$conexion);	
		}
                
                if(($profundidad!="00:00")&&($profundidad!=""))
		{
			$min=explode(":",$profundidad);
			$minutos=($min[0]*60)+$min[1];
			if(($minutos>=1)&&($minutos!=''))
				$insert .="('".$ficha."','".$fecha."','".$minutos."','profundidad', '".$encabezado."'),";
			//$guardar = query($insert,$conexion);	
		}
                
                if(($tunel!="00:00")&&($tunel!=""))
		{
			$min=explode(":",$tunel);
			$minutos=($min[0]*60)+$min[1];
			if(($minutos>=1)&&($minutos!=''))
				$insert .="('".$ficha."','".$fecha."','".$minutos."','tunel', '".$encabezado."'),";
			//$guardar = query($insert,$conexion);	
		}
                
                if(($martillo!="00:00")&&($martillo!=""))
		{
			$min=explode(":",$martillo);
			$minutos=($min[0]*60)+$min[1];
			if(($minutos>=1)&&($minutos!=''))
				$insert .="('".$ficha."','".$fecha."','".$minutos."','martillo', '".$encabezado."'),";
			//$guardar = query($insert,$conexion);	
		}
                
                if(($rastrilleo!="00:00")&&($rastrilleo!=""))
		{
			$min=explode(":",$rastrilleo);
			$minutos=($min[0]*60)+$min[1];
			if(($minutos>=1)&&($minutos!=''))
				$insert .="('".$ficha."','".$fecha."','".$minutos."','rastrilleo', '".$encabezado."'),";
			//$guardar = query($insert,$conexion);	
		}
                
                if(($otras!="00:00")&&($otras!=""))
		{
			$min=explode(":",$otras);
			$minutos=($min[0]*60)+$min[1];
			if(($minutos>=1)&&($minutos!=''))
				$insert .="('".$ficha."','".$fecha."','".$minutos."','otras', '".$encabezado."'),";
			//$guardar = query($insert,$conexion);	
		}
                
                if(($descanso_contrato!="00:00")&&($descanso_contrato!=""))
		{
			$min=explode(":",$descanso_contrato);
			$minutos=($min[0]*60)+$min[1];
			if(($minutos>=1)&&($minutos!=''))
				$insert .="('".$ficha."','".$fecha."','".$minutos."','descansocontrato', '".$encabezado."'),";
			//$guardar = query($insert,$conexion);	
		}
	}
        
        //DIA NACIONAL
	elseif($dia_fiesta==3)
	{
		if(($nacional!="00:00")&&($nacional!=""))
		{
			$min=explode(":",$nacional);
			$minutos=($min[0]*60)+$min[1];
                        $concepto="nacional";
			if(($minutos>=1)&&($minutos!=''))
				$insert .=" ('".$ficha."','".$fecha."','".$minutos."','".$concepto."', '".$encabezado."'),";
			//$guardar = query($insert,$conexion);
		}
		
		if(($extra!="00:00")&&($extra!=""))
		{
			$min=explode(":",$extra);
			$minutos=($min[0]*60)+$min[1];
                        $concepto="1.25x2.50";
			if(($minutos>=1)&&($minutos!=''))
				$insert .="('".$ficha."','".$fecha."','".$minutos."','".$concepto."', '".$encabezado."'),";
			//$guardar = query($insert,$conexion);	
		}
		if(($extraext!="00:00")&&($extraext!=""))
		{
			$min=explode(":",$extraext);
			$minutos=($min[0]*60)+$min[1];
                        $concepto="1.25x1.75x2.50";
			if(($minutos>=1)&&($minutos!=''))
				$insert .="('".$ficha."','".$fecha."','".$minutos."','".$concepto."', '".$encabezado."'),";
			//$guardar = query($insert,$conexion);	
		}
		if(($extranoc!="00:00")&&($extranoc!=""))
		{
			$min=explode(":",$extranoc);
			$minutos=($min[0]*60)+$min[1];
                        $concepto="1.75x2.50";
			if(($minutos>=1)&&($minutos!=''))
				$insert .="('".$ficha."','".$fecha."','".$minutos."','".$concepto."', '".$encabezado."'),";
			//$guardar = query($insert,$conexion);	
		}
		if(($extraextnoc!="00:00")&&($extraextnoc!=""))
		{
			$min=explode(":",$extraextnoc);
			$minutos=($min[0]*60)+$min[1];
                        $concepto="1.75x1.75x2.50";
			if(($minutos>=1)&&($minutos!=''))
				$insert .="('".$ficha."','".$fecha."','".$minutos."','".$concepto."', '".$encabezado."'),";
			//$guardar = query($insert,$conexion);	
		}
		if(($tardanza!="00:00")&&($tardanza!=""))
		{
			$min=explode(":",$tardanza);
			$minutos=($min[0]*60)+$min[1];
                        $concepto="tardanza";
			if(($minutos>=1)&&($minutos!=''))
				$insert .="('".$ficha."','".$fecha."','".$minutos."','".$concepto."', '".$encabezado."'),";
			//$guardar = query($insert,$conexion);	
		}		
		if(($descextra1!="00:00")&&($descextra1!=""))
		{
			$min=explode(":",$descextra1);
			$minutos=($min[0]*60)+$min[1];
                        $concepto="descextra1";
			if(($minutos>=1)&&($minutos!=''))
				$insert .=" ('".$ficha."','".$fecha."','".$minutos."','".$concepto."', '".$encabezado."'),";
			
		}
		if(($mixtodiurna!="00:00")&&($mixtodiurna!=""))
		{
			$min=explode(":",$mixtodiurna);
			$minutos=($min[0]*60)+$min[1];
                        $concepto="1.50x2.50";
			if(($minutos>=1)&&($minutos!=''))
				$insert .="('".$ficha."','".$fecha."','".$minutos."','".$concepto."', '".$encabezado."'),";
			//$guardar = query($insert,$conexion);	
		}
		if(($mixtoextdiurna!="00:00")&&($mixtoextdiurna!=""))
		{
			$min=explode(":",$mixtoextdiurna);
			$minutos=($min[0]*60)+$min[1];
                        $concepto="1.50x1.75x2.50";
			if(($minutos>=1)&&($minutos!=''))
				$insert .="('".$ficha."','".$fecha."','".$minutos."','".$concepto."', '".$encabezado."'),";
			//$guardar = query($insert,$conexion);	
		}
		if(($mixtonoc!="00:00")&&($mixtonoc!=""))
		{
			$min=explode(":",$mixtonoc);
			$minutos=($min[0]*60)+$min[1];
                        $concepto="1.75x2.50";
			if(($minutos>=1)&&($minutos!=''))
				$insert .="('".$ficha."','".$fecha."','".$minutos."','".$concepto."', '".$encabezado."'),";
			//$guardar = query($insert,$conexion);	
		}
		if(($mixtoextnoc!="00:00")&&($mixtoextnoc!=""))
		{
			$min=explode(":",$mixtoextnoc);
			$minutos=($min[0]*60)+$min[1];
                        $concepto="1.75x1.75x2.50";
			if(($minutos>=1)&&($minutos!=''))
				$insert .="('".$ficha."','".$fecha."','".$minutos."','".$concepto."', '".$encabezado."'),";
			//$guardar = query($insert,$conexion);	
		}
		if(($dialibre!="00:00")&&($dialibre!=""))
		{
			$min=explode(":",$dialibre);
			$minutos=($min[0]*60)+$min[1];
                        $concepto="dialibrenac";
			if(($minutos>=1)&&($minutos!=''))
				$insert .="('".$ficha."','".$fecha."','".$minutos."','".$concepto."', '".$encabezado."'),";
			//$guardar = query($insert,$conexion);	
		}
		if(($emergencia!="00:00")&&($emergencia!=""))
		{
			$min=explode(":",$emergencia);
			$minutos=($min[0]*60)+$min[1];
                        $concepto="emergencianac";
			if(($minutos>=1)&&($minutos!=''))
				$insert .="('".$ficha."','".$fecha."','".$minutos."','".$concepto."', '".$encabezado."'),";
			//$guardar = query($insert,$conexion);	
		}
		if(($descansoincompleto!="00:00")&&($descansoincompleto!=""))
		{
			$min=explode(":",$descansoincompleto);
			$minutos=($min[0]*60)+$min[1];
                        $concepto="descansoincompletonac";
			if(($minutos>=1)&&($minutos!=''))
				$insert .="('".$ficha."','".$fecha."','".$minutos."','".$concepto."', '".$encabezado."'),";
			//$guardar = query($insert,$conexion);	
		}
                
                if(($tarea!="00:00")&&($tarea!=""))
		{
			$min=explode(":",$tarea);
			$minutos=($min[0]*60)+$min[1];
			if(($minutos>=1)&&($minutos!=''))
				$insert .="('".$ficha."','".$fecha."','".$minutos."','tarea', '".$encabezado."'),";
			//$guardar = query($insert,$conexion);	
		}
                
                if(($lluvia!="00:00")&&($lluvia!=""))
		{
			$min=explode(":",$lluvia);
			$minutos=($min[0]*60)+$min[1];
			if(($minutos>=1)&&($minutos!=''))
				$insert .="('".$ficha."','".$fecha."','".$minutos."','lluvia', '".$encabezado."'),";
			//$guardar = query($insert,$conexion);	
		}
                
                if(($paralizacion_lluvia!="00:00")&&($paralizacion_lluvia!=""))
		{
			$min=explode(":",$paralizacion_lluvia);
			$minutos=($min[0]*60)+$min[1];
			if(($minutos>=1)&&($minutos!=''))
				$insert .="('".$ficha."','".$fecha."','".$minutos."','paralizacionlluvia', '".$encabezado."'),";
			//$guardar = query($insert,$conexion);	
		}
                
                if(($altura_menor!="00:00")&&($altura_menor!=""))
		{
			$min=explode(":",$altura_menor);
			$minutos=($min[0]*60)+$min[1];
			if(($minutos>=1)&&($minutos!=''))
				$insert .="('".$ficha."','".$fecha."','".$minutos."','alturamenor', '".$encabezado."'),";
			//$guardar = query($insert,$conexion);	
		}
                
                if(($altura_mayor!="00:00")&&($altura_mayor!=""))
		{
			$min=explode(":",$altura_mayor);
			$minutos=($min[0]*60)+$min[1];
			if(($minutos>=1)&&($minutos!=''))
				$insert .="('".$ficha."','".$fecha."','".$minutos."','alturamayor', '".$encabezado."'),";
			//$guardar = query($insert,$conexion);	
		}
                
                if(($profundidad!="00:00")&&($profundidad!=""))
		{
			$min=explode(":",$profundidad);
			$minutos=($min[0]*60)+$min[1];
			if(($minutos>=1)&&($minutos!=''))
				$insert .="('".$ficha."','".$fecha."','".$minutos."','profundidad', '".$encabezado."'),";
			//$guardar = query($insert,$conexion);	
		}
                
                if(($tunel!="00:00")&&($tunel!=""))
		{
			$min=explode(":",$tunel);
			$minutos=($min[0]*60)+$min[1];
			if(($minutos>=1)&&($minutos!=''))
				$insert .="('".$ficha."','".$fecha."','".$minutos."','tunel', '".$encabezado."'),";
			//$guardar = query($insert,$conexion);	
		}
                
                if(($martillo!="00:00")&&($martillo!=""))
		{
			$min=explode(":",$martillo);
			$minutos=($min[0]*60)+$min[1];
			if(($minutos>=1)&&($minutos!=''))
				$insert .="('".$ficha."','".$fecha."','".$minutos."','martillo', '".$encabezado."'),";
			//$guardar = query($insert,$conexion);	
		}
                
                if(($rastrilleo!="00:00")&&($rastrilleo!=""))
		{
			$min=explode(":",$rastrilleo);
			$minutos=($min[0]*60)+$min[1];
			if(($minutos>=1)&&($minutos!=''))
				$insert .="('".$ficha."','".$fecha."','".$minutos."','rastrilleo', '".$encabezado."'),";
			//$guardar = query($insert,$conexion);	
		}
                
                if(($otras!="00:00")&&($otras!=""))
		{
			$min=explode(":",$otras);
			$minutos=($min[0]*60)+$min[1];
			if(($minutos>=1)&&($minutos!=''))
				$insert .="('".$ficha."','".$fecha."','".$minutos."','otras', '".$encabezado."'),";
			//$guardar = query($insert,$conexion);	
		}
                
                if(($descanso_contrato!="00:00")&&($descanso_contrato!=""))
		{
			$min=explode(":",$descanso_contrato);
			$minutos=($min[0]*60)+$min[1];
			if(($minutos>=1)&&($minutos!=''))
				$insert .="('".$ficha."','".$fecha."','".$minutos."','descansocontrato', '".$encabezado."'),";
			//$guardar = query($insert,$conexion);	
		}
	}
        
        //DIA ORDINARIO
	else
	{
		if(($dom!="00:00")&&($dom!=""))
                {
                        $min=explode(":",$dom);
                        $minutos=($min[0]*60)+$min[1];
                        $concepto="domingo";
                        if(($minutos>=1)&&($minutos!=''))
                                $insert .="('".$ficha."','".$fecha."','".$minutos."','".$concepto."', '".$encabezado."'),";
                        //$guardar = query($insert,$conexion);
                }

		if(($ordinaria!="00:00")&&($ordinaria!=""))
		{
			$min=explode(":",$ordinaria);
			$minutos=($min[0]*60)+$min[1];
			if(($minutos>=1)&&($minutos!=''))
				$insert .=" ('".$ficha."','".$fecha."','".$minutos."','1', '".$encabezado."'),";
			//$guardar = query($insert,$conexion);
		}
		
		if(($extra!="00:00")&&($extra!=""))
		{
			$min=explode(":",$extra);
			$minutos=($min[0]*60)+$min[1];
			if(($minutos>=1)&&($minutos!=''))
                        {
                            $concepto="1.25";
//                            if($dia==6)
//                            {
//                                $concepto="extrassab";
//                            }
                                
                            $insert .="('".$ficha."','".$fecha."','".$minutos."','".$concepto."', '".$encabezado."'),";
                        }
			//$guardar = query($insert,$conexion);	
		}
		if(($extraext!="00:00")&&($extraext!=""))
		{
			$min=explode(":",$extraext);
			$minutos=($min[0]*60)+$min[1];
                        if(($minutos>=1)&&($minutos!=''))
                        {
                            $concepto="1.25x1.75";
//                            if($dia==6)
//                            {
//                                $concepto="extrasextsab";
//                            }
                                
                            $insert .="('".$ficha."','".$fecha."','".$minutos."','".$concepto."', '".$encabezado."'),";
                        }
			
			//$guardar = query($insert,$conexion);	
		}
		if(($extranoc!="00:00")&&($extranoc!=""))
		{
			$min=explode(":",$extranoc);
			$minutos=($min[0]*60)+$min[1];
                        if(($minutos>=1)&&($minutos!=''))
                        {
                            $concepto="1.75";
                            if($dia==6)
                            {
                                $concepto="d1.50x1.75";
                            }
                                
                            $insert .="('".$ficha."','".$fecha."','".$minutos."','".$concepto."', '".$encabezado."'),";
                        }
			
		}
		if(($extraextnoc!="00:00")&&($extraextnoc!=""))
		{
			$min=explode(":",$extraextnoc);
			$minutos=($min[0]*60)+$min[1];
                        if(($minutos>=1)&&($minutos!=''))
                        {
                            $concepto="1.75x1.75";
//                            if($dia==6)
//                            {
//                                $concepto="extrasextnocsab";
//                            }
                                
                            $insert .="('".$ficha."','".$fecha."','".$minutos."','".$concepto."', '".$encabezado."'),";
                        }
			
		}
		if(($tardanza!="00:00")&&($tardanza!=""))
		{
			$min=explode(":",$tardanza);
			$minutos=($min[0]*60)+$min[1];
			if(($minutos>=1)&&($minutos!=''))
				$insert .="('".$ficha."','".$fecha."','".$minutos."','tardanza', '".$encabezado."'),";
			//$guardar = query($insert,$conexion);	
		}
		if(($nacional!="00:00")&&($nacional!=""))
		{
			$min=explode(":",$nacional);
			$minutos=($min[0]*60)+$min[1];
                        $concepto="nacional";
			if(($minutos>=1)&&($minutos!=''))
				$insert .="('".$ficha."','".$fecha."','".$minutos."','".$concepto."', '".$encabezado."'),";
			//$guardar = query($insert,$conexion);	
		}
		if(($extranac!="00:00")&&($extranac!=""))
		{
			$min=explode(":",$extranac);
			$minutos=($min[0]*60)+$min[1];
                        if(($minutos>=1)&&($minutos!=''))
                        {
                            $concepto="1.25x2.50";
//                            if($dia==6)
//                            {
//                                $concepto="extranacsab";
//                            }
                                
                            $insert .="('".$ficha."','".$fecha."','".$minutos."','".$concepto."', '".$encabezado."'),";
                        }
			
		}
		if(($extranocnac!="00:00")&&($extranocnac!=""))
		{
			$min=explode(":",$extranocnac);
			$minutos=($min[0]*60)+$min[1];
                        if(($minutos>=1)&&($minutos!=''))
                        {
                            $concepto="1.75x2.50";
//                            if($dia==6)
//                            {
//                                $concepto="extranocnacsab";
//                            }
                                
                            $insert .="('".$ficha."','".$fecha."','".$minutos."','".$concepto."', '".$encabezado."'),";
                        }
			
		}
		if(($mixtonoc!="00:00")&&($mixtonoc!=""))
		{
			$min=explode(":",$mixtonoc);
			$minutos=($min[0]*60)+$min[1];
                        $concepto="1.75x2.50";
			if(($minutos>=1)&&($minutos!=''))
				$insert .="('".$ficha."','".$fecha."','".$minutos."','".$concepto."', '".$encabezado."'),";
			//$guardar = query($insert,$conexion);	
		}
		if(($descextra1!="00:00")&&($descextra1!=""))
		{
			$min=explode(":",$descextra1);
			$minutos=($min[0]*60)+$min[1];
                        if(($minutos>=1)&&($minutos!=''))
                        {
                            $concepto="descextra1";
//                            if($dia==6)
//                            {
//                                $concepto="descextra1sab";
//                            }
                                
                            $insert .="('".$ficha."','".$fecha."','".$minutos."','".$concepto."', '".$encabezado."'),";
                        }
			
			
		}
		if(($mixtodiurna!="00:00")&&($mixtodiurna!=""))
		{
			$min=explode(":",$mixtodiurna);
			$minutos=($min[0]*60)+$min[1];
                        $concepto="1.5";
			if(($minutos>=1)&&($minutos!=''))
				$insert .="('".$ficha."','".$fecha."','".$minutos."','".$concepto."', '".$encabezado."'),";
			//$guardar = query($insert,$conexion);	
		}
		if(($mixtoextdiurna!="00:00")&&($mixtoextdiurna!=""))
		{
			$min=explode(":",$mixtoextdiurna);
			$minutos=($min[0]*60)+$min[1];
                        if(($minutos>=1)&&($minutos!=''))
                        {
                            $concepto="1.50x1.75";
//                            if($dia==6)
//                            {
//                                $concepto="mixtoextdiurnasab";
//                            }
                                
                            $insert .="('".$ficha."','".$fecha."','".$minutos."','".$concepto."', '".$encabezado."'),";
                        }
			
			
		}
		if(($mixtonoc!="00:00")&&($mixtonoc!=""))
		{
			$min=explode(":",$mixtonoc);
			$minutos=($min[0]*60)+$min[1];
                        $concepto="1.75";
			if(($minutos>=1)&&($minutos!=''))
				$insert .="('".$ficha."','".$fecha."','".$minutos."','".$concepto."', '".$encabezado."'),";
			//$guardar = query($insert,$conexion);	
		}
		if(($mixtoextnoc!="00:00")&&($mixtoextnoc!=""))
		{
			$min=explode(":",$mixtoextnoc);
			$minutos=($min[0]*60)+$min[1];
                        if(($minutos>=1)&&($minutos!=''))
                        {
                            $concepto="1.75x1.75";
//                            if($dia==6)
//                            {
//                                $concepto="mixtoextnocsab";
//                            }
                                
                            $insert .="('".$ficha."','".$fecha."','".$minutos."','".$concepto."', '".$encabezado."'),";
                        }
			
		}
		if(($dialibre!="00:00")&&($dialibre!=""))
		{
			$min=explode(":",$dialibre);
			$minutos=($min[0]*60)+$min[1];
                        $concepto="1.5";
			if(($minutos>=1)&&($minutos!=''))
				$insert .="('".$ficha."','".$fecha."','".$minutos."','".$concepto."', '".$encabezado."'),";
			//$guardar = query($insert,$conexion);	
		}
		if(($emergencia!="00:00")&&($emergencia!=""))
		{
			$min=explode(":",$emergencia);
			$minutos=($min[0]*60)+$min[1];
			if(($minutos>=1)&&($minutos!=''))
				$insert .="('".$ficha."','".$fecha."','".$minutos."','emergencia', '".$encabezado."'),";
			//$guardar = query($insert,$conexion);	
		}
		if(($descansoincompleto!="00:00")&&($descansoincompleto!=""))
		{
			$min=explode(":",$descansoincompleto);
			$minutos=($min[0]*60)+$min[1];
			if(($minutos>=1)&&($minutos!=''))
				$insert .="('".$ficha."','".$fecha."','".$minutos."','descansoincompleto', '".$encabezado."'),";
			//$guardar = query($insert,$conexion);	
		}
                
                if(($tarea!="00:00")&&($tarea!=""))
		{
			$min=explode(":",$tarea);
			$minutos=($min[0]*60)+$min[1];
			if(($minutos>=1)&&($minutos!=''))
				$insert .="('".$ficha."','".$fecha."','".$minutos."','tarea', '".$encabezado."'),";
			//$guardar = query($insert,$conexion);	
		}
                
                if(($lluvia!="00:00")&&($lluvia!=""))
		{
			$min=explode(":",$lluvia);
			$minutos=($min[0]*60)+$min[1];
			if(($minutos>=1)&&($minutos!=''))
				$insert .="('".$ficha."','".$fecha."','".$minutos."','lluvia', '".$encabezado."'),";
			//$guardar = query($insert,$conexion);	
		}
                
                if(($paralizacion_lluvia!="00:00")&&($paralizacion_lluvia!=""))
		{
			$min=explode(":",$paralizacion_lluvia);
			$minutos=($min[0]*60)+$min[1];
			if(($minutos>=1)&&($minutos!=''))
				$insert .="('".$ficha."','".$fecha."','".$minutos."','paralizacionlluvia', '".$encabezado."'),";
			//$guardar = query($insert,$conexion);	
		}
                
                if(($altura_menor!="00:00")&&($altura_menor!=""))
		{
			$min=explode(":",$altura_menor);
			$minutos=($min[0]*60)+$min[1];
			if(($minutos>=1)&&($minutos!=''))
				$insert .="('".$ficha."','".$fecha."','".$minutos."','alturamenor', '".$encabezado."'),";
			//$guardar = query($insert,$conexion);	
		}
                
                if(($altura_mayor!="00:00")&&($altura_mayor!=""))
		{
			$min=explode(":",$altura_mayor);
			$minutos=($min[0]*60)+$min[1];
			if(($minutos>=1)&&($minutos!=''))
				$insert .="('".$ficha."','".$fecha."','".$minutos."','alturamayor', '".$encabezado."'),";
			//$guardar = query($insert,$conexion);	
		}
                
                if(($profundidad!="00:00")&&($profundidad!=""))
		{
			$min=explode(":",$profundidad);
			$minutos=($min[0]*60)+$min[1];
			if(($minutos>=1)&&($minutos!=''))
				$insert .="('".$ficha."','".$fecha."','".$minutos."','profundidad', '".$encabezado."'),";
			//$guardar = query($insert,$conexion);	
		}
                
                if(($tunel!="00:00")&&($tunel!=""))
		{
			$min=explode(":",$tunel);
			$minutos=($min[0]*60)+$min[1];
			if(($minutos>=1)&&($minutos!=''))
				$insert .="('".$ficha."','".$fecha."','".$minutos."','tunel', '".$encabezado."'),";
			//$guardar = query($insert,$conexion);	
		}
                
                if(($martillo!="00:00")&&($martillo!=""))
		{
			$min=explode(":",$martillo);
			$minutos=($min[0]*60)+$min[1];
			if(($minutos>=1)&&($minutos!=''))
				$insert .="('".$ficha."','".$fecha."','".$minutos."','martillo', '".$encabezado."'),";
			//$guardar = query($insert,$conexion);	
		}
                
                if(($rastrilleo!="00:00")&&($rastrilleo!=""))
		{
			$min=explode(":",$rastrilleo);
			$minutos=($min[0]*60)+$min[1];
			if(($minutos>=1)&&($minutos!=''))
				$insert .="('".$ficha."','".$fecha."','".$minutos."','rastrilleo', '".$encabezado."'),";
			//$guardar = query($insert,$conexion);	
		}
                
                if(($otras!="00:00")&&($otras!=""))
		{
			$min=explode(":",$otras);
			$minutos=($min[0]*60)+$min[1];
			if(($minutos>=1)&&($minutos!=''))
				$insert .="('".$ficha."','".$fecha."','".$minutos."','otras', '".$encabezado."'),";
			//$guardar = query($insert,$conexion);	
		}
                
                if(($descanso_contrato!="00:00")&&($descanso_contrato!=""))
		{
			$min=explode(":",$descanso_contrato);
			$minutos=($min[0]*60)+$min[1];
			if(($minutos>=1)&&($minutos!=''))
				$insert .="('".$ficha."','".$fecha."','".$minutos."','descansocontrato', '".$encabezado."'),";
			//$guardar = query($insert,$conexion);	
		}
	}
	$i++;

	$ordinaria="";
 	$extra="";
 	$extraext="";
 	$extranoc="";
 	$extraextnoc="";
 	$dom="";
 	$fecha = "";
 	$tardanza = "";
 	$nacional = "";
 	$extranac = "";
 	$extranocnac = "";
 	$descextra1 = "";
}

$insert .= '****';
$insert = str_replace(',****', ';', $insert);

if($conv == 1) 
	$guardar = query($insert,$conexion);


$datos="SELECT turno_id FROM nomturnos WHERE tipo=6;";
$rsLibre = query($datos,$conexion);
$filaLibre = fetch_array($rsLibre);
$turnoLibre = $filaLibre[turno_id];
//if((num_rows($rs)%2)==0)	

$datos="SELECT
 reloj_detalle.ficha,
 reloj_encabezado.cod_enca,
 reloj_encabezado.fecha_reg,
 reloj_encabezado.fecha_ini,
 reloj_encabezado.fecha_fin,
 reloj_detalle.id_encabezado,
 reloj_detalle.fecha
FROM
 nomturnos INNER JOIN nompersonal ON nomturnos.turno_id = nompersonal.turno_id
 INNER JOIN reloj_detalle ON nompersonal.ficha = reloj_detalle.ficha
 INNER JOIN reloj_encabezado ON reloj_detalle.id_encabezado = reloj_encabezado.cod_enca
where reloj_encabezado.cod_enca='$reg' group by reloj_detalle.ficha order by ficha, fecha";
$rs = query($datos,$conexion);
//if((num_rows($rs)%2)==0)
//{
$i = 1;
$fichaaux = ''; 
$insert="insert into reloj_procesar (ficha, fecha, minutos, concepto, id_encabezado) values ";
while($fila=fetch_array($rs))
{

	//$conv1 = 1 ;
	$fecha_ini = $fila[fecha_ini];
 	$fecha_fin = $fila[fecha_fin];
 	$fecha = $fila[fecha];
        $ficha = $fila[ficha];
        $encabezado = $fila[id_encabezado];

	$consulta ="select ifnull(count(fecha),0) as cantidad from nomcalendarios_personal where turno_id<>'$turnoLibre' and fecha not in ( SELECT reloj_detalle.fecha FROM reloj_detalle INNER JOIN reloj_encabezado ON reloj_detalle.id_encabezado = reloj_encabezado.cod_enca  WHERE reloj_encabezado.cod_enca='$reg' AND reloj_detalle.ficha='".$ficha."' ) and fecha between '$fecha_ini' and '$fecha_fin' AND ficha='".$ficha."'";
	//exit;
 	$resultadox = query($consulta,$conexion);
 	$fetch = fetch_array($resultadox);
 	$cantidad = $fetch[cantidad];
 	if($cantidad>=1)
 	{
 		$insert .=" ('".$ficha."','".$fecha_fin."','".$cantidad."','inasistencia', '".$encabezado."'),";
		//$guardar = query($insert,$conexion);
 	}
}
$insert  .= '****';
$insert  = str_replace(',****', ';', $insert);

if($conv == 1) 
    $guardar  = query($insert,$conexion);


if($color==0)
{
    $encabezado = "UPDATE reloj_encabezado "
            . "SET fecha_aprobacion = NOW(), "
            . "usuario_aprobacion = '".$_SESSION['usuario']."',"
            . " status = 'Aprobado' "
            . "WHERE cod_enca='$reg'";
    $result   = query($encabezado,$conexion);
    
    $detalle = "UPDATE reloj_detalle SET status = 3 where id_encabezado='$reg'";
    $result   = query($encabezado,$conexion);

    
    $asunto="El Encabezado de Control de Asistencias Código <strong>".$reg."</strong><br> "
            . "de Fecha: <strong>".$fecha_reg."</strong> del: <strong>".$fecha_ini."</strong> al: <strong>".$fecha_fin."</strong><br> "
            . "Ha sido Aparobado Satisfactoriamente por el usuario: <strong>".$_SESSION['usuario']."</strong><br>"
            . "En la fecha: <strong>".date('Y-m-d H:i:s')."</strong>";

    $mail->Body = $asunto;
    $mail->IsHTML(true);

    if (!$mail->send()) {
        $msg = "Mailer Error: " . $mail->ErrorInfo;
    } else {
        $msg = "Message sent!";
    }
    $mail->ClearAddresses();
    
    $sql_log  = "INSERT INTO log_transacciones 
                (cod_log, 
                descripcion, 
                fecha_hora, 
                modulo, 
                url, 
                accion, 
                valor, 
                usuario, 
                host) 
                VALUES 
                (NULL, 
                'Control de Asistencia - Aprobación Exitosa', 
                now(), 
                'Control Acceso', 
                'procesar_para_nomina2.php',
                'aprobar',
                '{$reg}',"
                . "'".$_SESSION['usuario'] ."', "
                . "'".$host_ip."')";
    $res_log  = query($sql_log,$conexion);
    
    header("Location:control_acceso2.php?listo=3");
}

elseif($color==1)
{
    $asunto="El Encabezado de Control de Asistencias Código <strong>".$reg."</strong><br> "
            . "de Fecha: <strong>".$fecha_reg."</strong> del: <strong>".$fecha_ini."</strong> al: <strong>".$fecha_fin."</strong><br> "
            . "Ha sido Intentado Aprobar por el usuario: <strong>".$_SESSION['usuario']."</strong><br>"
            . "En la fecha: <strong>".date('Y-m-d H:i:s')."</strong> de manera fallida.<br> "
            . "Debe Preaprobar o Revisar Registros";
    $mail->Body = $asunto;
    $mail->IsHTML(true);

    if (!$mail->send()) {
        $msg = "Mailer Error: " . $mail->ErrorInfo;
    } else {
        $msg = "Message sent!";
    }
    $mail->ClearAddresses();
    
    $sql_log  = "INSERT INTO log_transacciones 
        (cod_log, 
        descripcion, 
        fecha_hora, 
        modulo, 
        url, 
        accion, 
        valor, 
        usuario, 
        host) 
    VALUES 
    (NULL, 
    'Control de Asistencia - Aprobación Fallida', 
    now(), 
    'Control Acceso',
    'procesar_para_nomina2.php', 
    'aprobar',
    '{$reg}',"
    . "'".$_SESSION['usuario'] ."', "
    . "'".$host_ip."')";
    $res_log  = query($sql_log,$conexion);
    header("Location:control_acceso2.php?listo=2");
}
?>
