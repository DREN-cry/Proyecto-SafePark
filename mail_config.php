<?php

require '../PHPMailer-master/src/PHPMailer.php';
require '../PHPMailer-master/src/SMTP.php';
require '../PHPMailer-master/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function enviarCorreo($destino,$asunto,$mensaje){

    $mail = new PHPMailer(true);

    try {

        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'omarolguin3529@gmail.com';
        $mail->Password = 'ezfefhxijlidntdx';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->CharSet = 'UTF-8';

        $mail->setFrom('omarolguin3529@gmail.com','SafePark');
        $mail->addAddress($destino);

        $mail->Subject = $asunto;
        $mail->Body = $mensaje;
        

        $mail->send();

    } catch (Exception $e) {
        error_log("Error correo: ".$mail->ErrorInfo);
    }
}