<?php
include("conexion_bd.php");
require("../../../nomina/procesos/PHPMailer_5.2.4/class.phpmailer.php");
include("../../../nomina/procesos/PHPMailer_5.2.4/class.smtp.php"); 

$enlace     = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
$codigos    = explode("?",$enlace);
$url        = base64_decode($codigos[1]);
$parametros = explode("?",$url);
$accion     = explode ("=",$parametros[0]);
switch ($accion[1]) {
	case 'reset_password':
	{
		$valores  = explode ("|",$parametros[1]);
		$conexion = new bd(SELECTRA_CONF_PYME);
		$query1   = "SELECT coduser, login_usuario FROM ".SELECTRA_CONF_PYME.".nomusuarios  WHERE coduser ='{$valores[0]}' AND correo ='{$valores[1]}'";
		$ress     = $conexion->query($query1);
		$row      = $ress -> fetch_assoc();
		$email = $valores[1];
		if($row[coduser] != "" AND !is_null($row[coduser])){
			$clave =  hash("sha256","12345");
			$UPDATE  = "UPDATE ".SELECTRA_CONF_PYME.".nomusuarios SET  clave = '{$clave}' WHERE coduser ='{$valores[0]}' AND correo ='{$valores[1]}'";
			$res = $conexion->query($UPDATE);
			/* Envío de correos electrónicos*/
	        if($res)
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
            $tit = "Se ha restablecido su contraseña | AMAXONIA PLANILLA";
            #
            
            $mail->Subject = $tit; 
            $cuerpo        = "Su nueva clave es 12345 \t";
            $mail->Body    = $cuerpo;
            $mail->IsHTML(true);
    
            // Se asigna la dirección de correo a donde se enviará el mensaje.
            //$mail->AddAddress ($correo, 'AMX');   
            $mail->AddAddress ($email, $nombre_completo);   
                //$mail->addCC('contacto@amaxoniaerp.com');
    
            // $mail->AddAddress('marianna.pessolano@gmail.com', 'Marianna Pessolano');
    
            // Si hay archivos adjuntos se mandan así.
            $mail->AddAttachment($filename);
            
            // Comprobamos que el correo se ha enviado.
            if( !$mail->Send() ) 
            {
				$error          = 0;
				$tipo           = 'warning';
				$mensaje_correo = "Error al enviar el correo";
            } 
            else 
            {
				$error          = 1;
				$tipo           = 'success';
				$mensaje_correo = "Se ha restablecido su contraseña";
            }
            
            $mail->ClearAddresses();
            $mail->ClearAttachments();
	        }
      		header("location:index.php?error=".$mensaje_correo."&tipo=".$tipo."");

		}

		break;
	}
	
	default:
		# code...
		break;
}