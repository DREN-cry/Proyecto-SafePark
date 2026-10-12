<?php
session_start();
header("Content-Type: application/json");
include "conexion.php";

if (!isset($_SESSION["usuario"])) {
    echo json_encode(["status" => "error", "message" => "Sesión no iniciada"]);
    exit;
}

$id_usuario = $_SESSION["usuario"]["id"];

$query = $conexion->prepare("
    SELECT 
        u.nombre, u.apellido_paterno, u.apellido_materno,
        u.email, u.telefono, u.tipo_usuario,
        e.matricula, e.programa,
        m.id_moto, m.matricula AS moto_matricula, 
        m.marca, m.modelo, m.color, m.cilindraje, m.accesorios
    FROM usuarios u
    LEFT JOIN estudiantes e ON u.id_usuario = e.id_usuario
    LEFT JOIN motocicletas m ON u.id_usuario = m.id_usuario
    WHERE u.id_usuario = ?
");
$query->bind_param("i", $id_usuario);
$query->execute();
$result = $query->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["status" => "error", "message" => "Usuario no encontrado"]);
    exit;
}

echo json_encode(["status" => "success", "data" => $result->fetch_assoc()]);
?>
