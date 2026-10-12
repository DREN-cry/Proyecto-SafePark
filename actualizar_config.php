<?php
session_start();
error_reporting(E_ALL);
ini_set("display_errors", 1);

header("Content-Type: application/json");
include "conexion.php";

// Verificar sesión
if (!isset($_SESSION["usuario"])) {
    echo json_encode(["status" => "error", "message" => "Sesión no iniciada"]);
    exit;
}

$id_usuario = $_SESSION["usuario"]["id"];

// Leer JSON recibido
$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    echo json_encode(["status" => "error", "message" => "Datos no válidos"]);
    exit;
}

// Sanitizar datos
$nombre = trim($data["nombre"]);
$apellido_paterno = trim($data["apellido_paterno"]);
$apellido_materno = trim($data["apellido_materno"]);
$telefono = trim($data["telefono"]);
$email = trim($data["email"]);
$programa = trim($data["programa"]);


$moto_matricula = trim($data["moto_matricula"]);
$marca = trim($data["marca"]);
$modelo = trim($data["modelo"]);
$color = trim($data["color"]);
$cilindraje = trim($data["cilindraje"]);
$accesorios = trim($data["accesorios"]);

$conexion->begin_transaction();

try {

    // Actualizar tabla usuarios
    $q1 = $conexion->prepare("
        UPDATE usuarios SET 
            nombre = ?, 
            apellido_paterno = ?, 
            apellido_materno = ?, 
            telefono = ?, 
            email = ?
        WHERE id_usuario = ?
    ");
    $q1->bind_param("sssssi", $nombre, $apellido_paterno, $apellido_materno, $telefono, $email, $id_usuario);
    $q1->execute();

    // Actualizar estudiantes
    $q2 = $conexion->prepare("
        UPDATE estudiantes SET programa = ?
        WHERE id_usuario = ?
    ");
    $q2->bind_param("si", $programa, $id_usuario);
    $q2->execute();

    // Revisar si ya hay moto registrada
    $checkMoto = $conexion->prepare("SELECT id_moto FROM motocicletas WHERE id_usuario = ?");
    $checkMoto->bind_param("i", $id_usuario);
    $checkMoto->execute();
    $result = $checkMoto->get_result();

    if ($result->num_rows > 0) {
        // ACTUALIZAR moto
        $row = $result->fetch_assoc();
        $id_moto = $row['id_moto'];

        $q3 = $conexion->prepare("
            UPDATE motocicletas SET 
                matricula = ?, marca = ?, modelo = ?, color = ?, cilindraje = ?, accesorios = ?
            WHERE id_moto = ?
        ");
        $q3->bind_param("ssssssi", $moto_matricula, $marca, $modelo, $color, $cilindraje, $accesorios, $id_moto);
        $q3->execute();

    } else {
        // INSERTAR moto nueva
        $q3 = $conexion->prepare("
            INSERT INTO motocicletas (id_usuario, matricula, marca, modelo, color, cilindraje, accesorios)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $q3->bind_param("issssss", $id_usuario, $moto_matricula, $marca, $modelo, $color, $cilindraje, $accesorios);
        $q3->execute();

        $id_moto = $conexion->insert_id;
    }

    // Confirmar cambios
    $conexion->commit();

    // 🔵 Actualizar sesión correctamente
    $_SESSION["usuario"]["nombre"] = $nombre;
    $_SESSION["usuario"]["apellidos"] = $apellido_paterno . " " . $apellido_materno;
    $_SESSION["usuario"]["email"] = $email;
    $_SESSION["usuario"]["telefono"] = $telefono;
    $_SESSION["usuario"]["programa"] = $programa;
    $_SESSION["usuario"]["matricula"] = $_SESSION["usuario"]["matricula"];


    // Actualizar moto en sesión
    $_SESSION["usuario"]["moto"] = [
        "id_moto"     => $id_moto,
        "matricula"   => $moto_matricula,
        "marca"       => $marca,
        "modelo"      => $modelo,
        "color"       => $color,
        "cilindraje"  => $cilindraje,
        "accesorios"  => $accesorios
    ];

    echo json_encode([
        "status" => "success",
        "message" => "Datos actualizados correctamente"
    ]);

} catch (Exception $e) {

    $conexion->rollback();

    echo json_encode([
        "status" => "error",
        "message" => "Error al actualizar: " . $e->getMessage()
    ]);
}
?>

