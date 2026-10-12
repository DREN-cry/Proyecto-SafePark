<?php
session_start();
include "conexion.php";

if (!isset($_SESSION["usuario"])) {
    echo json_encode(["status" => "error"]);
    exit;
}

$id = $_SESSION["usuario"]["id_usuario"];

// Estado actual (si tiene moto dentro)
$stmtEstado = $conexion->prepare("
    SELECT tipo, fecha 
    FROM historial_entradas 
    WHERE id_usuario = ?
    ORDER BY fecha DESC
    LIMIT 1
");
$stmtEstado->bind_param("i", $id);
$stmtEstado->execute();
$estadoResult = $stmtEstado->get_result()->fetch_assoc();

$estado = "Fuera";
if ($estadoResult && $estadoResult["tipo"] === "entrada") {
    $estado = "Dentro";
}

// Historial
$stmtHistorial = $conexion->prepare("
    SELECT tipo, fecha 
    FROM historial_entradas 
    WHERE id_usuario = ?
    ORDER BY fecha DESC
    LIMIT 10
");
$stmtHistorial->bind_param("i", $id);
$stmtHistorial->execute();
$historial = $stmtHistorial->get_result()->fetch_all(MYSQLI_ASSOC);

echo json_encode([
    "status" => "ok",
    "estado" => $estado,
    "historial" => $historial
]);