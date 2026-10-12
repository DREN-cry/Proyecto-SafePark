<?php
date_default_timezone_set('America/Mexico_City');

$email          = $_POST['email']     ?? null;
$tipoMovimiento = $_POST['tipo']      ?? null;
$nombreCompleto = $_POST['nombre']    ?? null;
$telefono       = $_POST['telefono']  ?? null;
$matricula      = $_POST['matricula'] ?? null;
$hora           = $_POST['hora']      ?? date("H:i");

if(!$email || !$tipoMovimiento || !$nombreCompleto || !$telefono || !$matricula)
    exit;

require __DIR__."/mail_config.php";
require __DIR__."/whatsapp.php";

if($tipoMovimiento == "entrada"){
    $asunto  = "SafePark - Entrada registrada";
    $mensaje = "Hola $nombreCompleto, tu motocicleta con matrícula $matricula ingresó al estacionamiento a las $hora.";
} else {
    $asunto  = "SafePark - Salida registrada";
    $mensaje = "Hola $nombreCompleto, tu motocicleta con matrícula $matricula salió del estacionamiento a las $hora.";
}

enviarCorreo($email, $asunto, $mensaje);

$telefonoWhats = "52".preg_replace("/[^0-9]/", "", $telefono);
$mensajeWhats  = "SafePark\n\nHola $nombreCompleto\nTu motocicleta $matricula registró una $tipoMovimiento a las $hora.";
enviarWhatsApp($telefonoWhats, $mensajeWhats);
?>