<?php
session_start();
header("Content-Type: application/json; charset=utf-8");

if(!isset($_SESSION["usuario"]))
    exit(json_encode(["error" => "No autorizado"]));

$idUsuario = $_SESSION["usuario"]["id"];
include __DIR__ . "/conexion.php";

// Último movimiento general
$stmt = $conexion->prepare("
    SELECT tipo, fecha
    FROM historial_entradas
    WHERE id_usuario = ?
    ORDER BY fecha DESC
    LIMIT 1
");
$stmt->bind_param("i", $idUsuario);
$stmt->execute();
$ultimo = $stmt->get_result()->fetch_assoc();

// Movimientos de hoy (últimos 5)
$stmt2 = $conexion->prepare("
    SELECT tipo, fecha
    FROM historial_entradas
    WHERE id_usuario = ?
      AND DATE(fecha) = CURDATE()
    ORDER BY fecha DESC
    LIMIT 5
");
$stmt2->bind_param("i", $idUsuario);
$stmt2->execute();
$hoy = [];
$res = $stmt2->get_result();
while($fila = $res->fetch_assoc())
    $hoy[] = $fila;

echo json_encode([
    "ultimo"  => $ultimo,
    "hoy"     => $hoy
]);
?>