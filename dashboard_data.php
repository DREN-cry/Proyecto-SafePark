<?php
header("Content-Type: application/json; charset=utf-8");
include "conexion.php";

/* MÉTRICAS */

// Usuarios registrados
$qUsuarios = $conexion->query("SELECT COUNT(*) as total FROM usuarios");
$totalUsuarios = $qUsuarios->fetch_assoc()['total'] ?? 0;

// Ingresos de hoy
$qIngresosHoy = $conexion->query("
    SELECT COUNT(*) as total 
    FROM historial_entradas 
    WHERE tipo='entrada' 
    AND DATE(fecha)=CURDATE()
");
$ingresosHoy = $qIngresosHoy->fetch_assoc()['total'] ?? 0;

// Motos dentro
$qMotosDentro = $conexion->query("
    SELECT 
        (SELECT COUNT(*) FROM historial_entradas WHERE tipo='entrada') -
        (SELECT COUNT(*) FROM historial_entradas WHERE tipo='salida')
        AS total
");
$motosDentro = $qMotosDentro->fetch_assoc()['total'] ?? 0;
if($motosDentro < 0) $motosDentro = 0;

$capacidadTotal = 100;
$porcentaje = ($motosDentro / $capacidadTotal) * 100;
$disponibles = $capacidadTotal - $motosDentro;

// Actividad reciente
$qActividad = $conexion->query("
    SELECT h.*, 
           u.nombre, u.apellido_paterno,
           m.matricula
    FROM historial_entradas h
    INNER JOIN usuarios u ON h.id_usuario = u.id_usuario
    LEFT JOIN motocicletas m ON h.id_moto = m.id_moto
    ORDER BY h.fecha DESC
    limit 5
");

$actividad = [];
while($fila = $qActividad->fetch_assoc()){
    $actividad[] = $fila;
}

echo json_encode([
    "usuarios" => $totalUsuarios,
    "ingresosHoy" => $ingresosHoy,
    "motosDentro" => $motosDentro,
    "capacidad" => $capacidadTotal,
    "porcentaje" => round($porcentaje),
    "disponibles" => $disponibles,
    "actividad" => $actividad
]);