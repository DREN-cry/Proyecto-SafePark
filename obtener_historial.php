<?php
header("Content-Type: application/json; charset=utf-8");
include "conexion.php";

$dias = isset($_GET["dias"]) ? (int)$_GET["dias"] : 1;

$stmt = $conexion->prepare("
    SELECT h.*, 
           u.nombre, u.apellido_paterno, u.apellido_materno,
           m.matricula
    FROM historial_entradas h
    INNER JOIN usuarios u ON h.id_usuario = u.id_usuario
    LEFT JOIN motocicletas m ON h.id_moto = m.id_moto
    WHERE h.fecha >= NOW() - INTERVAL ? DAY
    ORDER BY h.fecha DESC
");
$stmt->bind_param("i", $dias);
$stmt->execute();

$resultado = $stmt->get_result();

$datos = [];
while($fila = $resultado->fetch_assoc())
    $datos[] = $fila;

echo json_encode($datos);
?>