<?php
session_start();
header("Content-Type: application/json; charset=utf-8");

if(!isset($_SESSION["usuario"]))
    exit(json_encode(["error" => "No autorizado"]));

$idUsuario = $_SESSION["usuario"]["id"];
$dias      = isset($_GET["dias"]) ? (int)$_GET["dias"] : 7;

include __DIR__ . "/conexion.php";

$stmt = $conexion->prepare("
    SELECT h.tipo, h.fecha, m.matricula
    FROM historial_entradas h
    LEFT JOIN motocicletas m ON h.id_moto = m.id_moto
    WHERE h.id_usuario = ?
      AND h.fecha >= NOW() - INTERVAL ? DAY
    ORDER BY h.fecha DESC
");
$stmt->bind_param("ii", $idUsuario, $dias);
$stmt->execute();
$resultado = $stmt->get_result();

$registros = [];
while($fila = $resultado->fetch_assoc())
    $registros[] = $fila;

echo json_encode($registros);
?>