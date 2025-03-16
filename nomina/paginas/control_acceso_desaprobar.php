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
$tit='Control de Asistencias - Desaprobacion';

$mail->Subject = $tit;
$mail->AddAddress ($email1);

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


    $encabezado = "UPDATE reloj_encabezado "
            . "SET fecha_desaprobacion = NOW(), "
            . "usuario_desaprobacion = '".$_SESSION['usuario']."',"
            . " status = 'Preaprobado' "
            . "WHERE cod_enca='$reg'";
    $result   = query($encabezado,$conexion);
    
    $detalle = "UPDATE reloj_detalle SET status = 2 where id_encabezado='$reg'";
    $result   = query($encabezado,$conexion);
    
    $vaciar="TRUNCATE TABLE reloj_procesar";
    $query=query($vaciar,$conexion);
    
    $asunto="El Encabezado de Control de Asistencias Código <strong>".$reg."</strong><br> "
            . "de Fecha: <strong>".$fecha_reg."</strong> del: <strong>".$fecha_ini."</strong> al: <strong>".$fecha_fin."</strong><br> "
            . "Ha sido Desaprobado Satisfactoriamente por el usuario: <strong>".$_SESSION['usuario']."</strong><br>"
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
                'Control de Asistencia - Desaprobación Exitosa', 
                now(), 
                'Control Acceso', 
                'control_acceso_desaprobar.php',
                'desaprobar',
                '{$reg}',"
                . "'".$_SESSION['usuario'] ."', "
                . "'".$host_ip."')";
    $res_log  = query($sql_log,$conexion);
    
    header("Location:control_acceso2.php?listo=5");

?>