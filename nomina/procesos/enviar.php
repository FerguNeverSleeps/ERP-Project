<?php
// Import PHPMailer classes into the global namespace
// These must be at the top of your script, not inside a function
require("PHPMailer_5.2.4/class.phpmailer.php");
include("PHPMailer_5.2.4/class.smtp.php");
$mail = new PHPMailer();
$mail->IsSMTP();
$email='lorogon@gmail.com';
$mail = new PHPMailer();
       $mail->SMTPAuth = true;
       $mail->isSMTP();
       $mail->SMTPDebug = 2;
       $mail->Host = "smtp.gmail.com";
       $mail->Port = 465;
       $mail->SMTPSecure = 'ssl';
       $mail->Username = "planillaexpresspanama@gmail.com";
       $mail->Password = "S3l3ctr4";
       $mail->From = "planillaexpresspanama@gmail.com";
       $mail->FromName = "RRHH Solicitudes";
       $tit='SOLICITUD ';

       $mail->Subject = $tit;

       $asunto="Adjunto SOLICITUD Pajudo";
       $mail->Body = $asunto;
       $mail->IsHTML(true);
     
       $mail->AddAddress ($email);

       $mail->AddAttachment($ruta);
     
       if(!$mail->Send()) {
           echo $ruta." ".$email." Mailer Error: " . $mail->ErrorInfo;
       } else {
           echo $ruta." "."enviado a $email";
           //echo "Message enviado!";
       }
       $mail->ClearAddresses();
       $mail->ClearAttachments();

?>