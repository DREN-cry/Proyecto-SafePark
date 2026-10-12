<?php
session_start();
header("Content-Type: application/json");
include "conexion.php";

// Verificar datos enviados
if (!isset($_POST["identifier"]) || !isset($_POST["password"])) {
    echo json_encode(["status" => "error", "message" => "Datos incompletos."]);
    exit;
}

$identifier = trim($_POST["identifier"]);
$password = $_POST["password"];

// -------------------------------------------------------
// Buscar usuario por email o matrícula
// -------------------------------------------------------
$query = $conexion->prepare("
    SELECT 
        u.id_usuario, u.tipo_usuario, u.nombre, u.apellido_paterno, u.apellido_materno,
        u.email, u.telefono, u.password,
        e.matricula, e.programa,
        a.empleado_id, a.rol,
        m.id_moto, m.matricula AS moto_matricula, m.marca, m.modelo, m.color, m.cilindraje, m.accesorios
    FROM usuarios u
    LEFT JOIN estudiantes e ON u.id_usuario = e.id_usuario
    LEFT JOIN administradores a ON u.id_usuario = a.id_usuario
    LEFT JOIN motocicletas m ON u.id_usuario = m.id_usuario
    WHERE u.email = ? OR e.matricula = ?
");
$query->bind_param("ss", $identifier, $identifier);
$query->execute();
$result = $query->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["status" => "error", "message" => "Usuario no encontrado."]);
    exit;
}

$user = $result->fetch_assoc();

// -------------------------------------------------------
// Verificar contraseña
// -------------------------------------------------------
if (!password_verify($password, $user["password"])) {
    echo json_encode(["status" => "error", "message" => "Contraseña incorrecta."]);
    exit;
}

// -------------------------------------------------------
// Guardar datos en sesión
// -------------------------------------------------------
$_SESSION["usuario"] = [
    "id"        => $user["id_usuario"],
    "tipo"      => $user["tipo_usuario"],
    "nombre"    => $user["nombre"],
    "apellidos" => $user["apellido_paterno"] . " " . $user["apellido_materno"],
    "email"     => $user["email"],
    "telefono"  => $user["telefono"],
    "matricula" => $user["matricula"] ?? null,
    "programa"  => $user["programa"] ?? null,
    "empleado_id" => $user["empleado_id"] ?? null,
    "rol"       => $user["rol"] ?? null,
    "moto"      => [
        "id_moto"      => $user["id_moto"] ?? null,
        "matricula"    => $user["moto_matricula"] ?? null,
        "marca"        => $user["marca"] ?? null,
        "modelo"       => $user["modelo"] ?? null,
        "color"        => $user["color"] ?? null,
        "cilindraje"   => $user["cilindraje"] ?? null,
        "accesorios"   => $user["accesorios"] ?? null
    ]
];

// -------------------------------------------------------
// Responder JSON
// -------------------------------------------------------
echo json_encode([
    "status" => "success",
    "message" => "Inicio de sesión correcto.",
    "tipo_usuario" => $user["tipo_usuario"]
]);
?>
