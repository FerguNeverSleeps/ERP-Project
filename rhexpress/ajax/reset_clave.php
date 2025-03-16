<?php
session_start();
$empresa_seleccionada = $_POST['empresa_seleccionada'];
//----------------------------------------------------------------------------
require_once "../config/rhexpress_config.php";
require("../../nomina/procesos/PHPMailer_5.2.4/class.phpmailer.php");
include("../../nomina/procesos/PHPMailer_5.2.4/class.smtp.php");
//---------------------------------------------------------------------------
$var_sql="SELECT * FROM $empresa_seleccionada.nomempresa";
$resul_nomempresa=$conexion->query($var_sql);
$res=mysqli_fetch_assoc($resul_nomempresa);
$correo_sistemas2=$res['correo_sistemas2'];
$correo_sistemas2_password=$res['correo_sistemas2_password'];
$correo_sistemas2_remitente=$res['correo_sistemas2_remitente'];
$correo_sistemas2_host=$res['correo_sistemas2_host'];
$correo_sistemas2_puerto=$res['correo_sistemas2_puerto'];
$correo_sistemas2_modo=$res['correo_sistemas2_modo'];
//----------------------------------------------------------------------------
$query1 = "SELECT * FROM $empresa_seleccionada.nompersonal  WHERE usuario_workflow ='{$_POST['usuario_forget']}' ";
$ress = $conexion->query($query1);
$row = $ress -> fetch_assoc();
    if($row['cedula'] == ""){
      header("location:../rhexpress_login.php?error=El Usuario no existe&tipo=danger");
    }
    else{
      $correo_colaborador=$row['email'];      
      $query2 = "UPDATE nompersonal SET usr_password = md5('123456') WHERE usuario_workflow ='{$_POST['usuario_forget']}'";
      $conexion->query($query2);
      
      $mail = new PHPMailer();
      $mail->SMTPAuth = true;
      $mail->isSMTP();
      $mail->SMTPDebug = 0;
      $mail->Host = $correo_sistemas2_host;                        
      $mail->Port = $correo_sistemas2_puerto;
      $mail->SMTPSecure = $correo_sistemas2_modo;			
      $mail->Username = $correo_sistemas2;
      $mail->Password = $correo_sistemas2_password;
      $mail->From = $correo_sistemas2_remitente;
      $mail->FromName = "Rhexpress, Portal de colaborador";
      $tit='Recuperación de contraseña';			
      $mail->Subject = utf8_decode($tit);
      $asunto="Su clave fue restaurada con éxito su clave es 123456, puede ingresar al portal y cambiarla";
      $mail->Body = $asunto;
      //$mail->IsHTML(true);      
      $mail->AddAddress ($correo_colaborador);      
      //$mail->AddAttachment($ruta);
      if(!$mail->Send()) {
          echo $email." Mailer Error: " . $mail->ErrorInfo;
          $enviado="mensaje no enviado";
        }else{
          $enviado="mensaje enviado";
        }
      $mail->ClearAddresses();
      $mail->ClearAttachments();                  
      
      //print_r($mail);exit;
      header("location:../rhexpress_login.php?error=Se ha restaurado su clave con éxito.&tipo=success&correo=".$enviado);
    }    
?>
