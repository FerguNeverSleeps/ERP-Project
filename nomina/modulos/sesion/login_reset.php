<?php
session_start();
/* RECURSOS  */
include("conexion_bd.php");
require("../../../nomina/procesos/PHPMailer_5.2.4/class.phpmailer.php");
include("../../../nomina/procesos/PHPMailer_5.2.4/class.smtp.php"); 


/** Se valida si el usuario o el correo electrónico está en la base de datos*/
if($_POST['usuario_forget'] != "" AND $_POST['correo_forget'] != ""){
    $clave="123456";
    $clave =  hash("sha256",$clave);
    $correo = $_POST['correo_forget'];
    $conexion = new bd(SELECTRA_CONF_PYME);
    $query1 = "SELECT coduser,correo FROM ".SELECTRA_CONF_PYME.".nomusuarios  WHERE login_usuario ='{$_POST['usuario_forget']}' AND correo ='{$correo}' ";
    $ress = $conexion->query($query1);
    $row = $ress -> fetch_assoc();
   
    if($row['coduser'] == ""){
		header("location:index.php?error=Usuario o Correo electrónico no existe&tipo=danger");
    }
    else{
        
        /* Condicional para el envío de correos electrónicos*/
        if(!empty($correo))
        {
    
            $mail = new PHPMailer();
            $mail->IsSMTP();
            $mail->SMTPAuth = true;
            $mail->SMTPDebug = 0;
            $mail->Host = "smtp.gmail.com";
            $mail->Port = 587;
            $mail->SMTPSecure = 'tls';
            // Activa la condificacción utf-8
            $mail->CharSet = 'UTF-8';
    
            // El correo y contraseña de donde saldran los mensajes.
            $mail->Username = "planillaexpresspanama@gmail.com";
            $mail->Password = "S3l3ctr4";
    
            //Indicamos cual es nuestra dirección de correo y el nombre que 
            //queremos que vea el usuario que lee nuestro correo
    
            $mail->SetFrom("planillaexpresspanama@gmail.com", "Planillaexpress");
    
            // Asunto de mensaje
            $tit = "Restablecimiento de Contraseñas";
            #
            
            $mail->Subject = $tit; 
            $accion        = "action=reset_password?";
            $valor         = "".$row['coduser']."|".$correo;
            $acciones      = $accion.$valor;
            $enlace        = $_SESSION['LIVEURL']."/nomina/modulos/sesion/decode_url.php?".base64_encode($acciones);
            $cuerpo        = "Para restablecer su contraseña, presione <a href='".$enlace."'>aqu&iacute;</a> \t";
            $mail->Body    = $cuerpo;
            $mail->IsHTML(true);
    
            // Se asigna la dirección de correo a donde se enviará el mensaje.
            $mail->AddAddress ($correo, 'AMX');   
            $mail->AddAddress ($email, $nombre_completo);   
                //$mail->addCC('contacto@amaxoniaerp.com');
    
            // $mail->AddAddress('marianna.pessolano@gmail.com', 'Marianna Pessolano');
    
            // Si hay archivos adjuntos se mandan así.
            $mail->AddAttachment($filename);
            
            // Comprobamos que el correo se ha enviado.
            if( !$mail->Send() ) 
            {
               $error = 0;
               $mensaje_correo = "Error al enviar el correo";
            } 
            else 
            {
                $error = 1;
                $mensaje_correo = "Se ha enviado un correo de confirmación.";
            }
            
            $mail->ClearAddresses();
            $mail->ClearAttachments();
        }

      header("location:index.php?error=".$mensaje_correo."&tipo=success");
    }    

    
}else{
    header("location:index.php?error=Seleccione un usuario o correo electrónico&tipo=info");

}

?>
