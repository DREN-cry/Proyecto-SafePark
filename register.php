<?php

header("Content-Type: application/json");
include "conexion.php";

$data = $_POST;
$tipoUsuario = $data["tipo_usuario"];
$nombre = $data["firstName"];
$apellidoP = $data["lastNameP"];
$apellidoM = $data["lastNameM"];
$email = strtolower(trim($data["email"] ?? ""));
$telefono = $data["phone"];
$password = password_hash($data["password"], PASSWORD_BCRYPT);

// --------------------------------------
// 1. Limpiar y validar formato
// --------------------------------------

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(["status" => "error", "message" => "El formato del correo no es válido."]);
    exit;
}



// --------------------------------------
// 2. Verificar dominio institucional (solo administrador)
// --------------------------------------
$tiposInstitucionales = ["administrador", "docente", "seguridad", "coordinador"];

if (in_array($tipoUsuario, $tiposInstitucionales) && !str_ends_with($email, "@utvm.edu.mx")) {
    echo json_encode(["status" => "error", "message" => "Solo se permiten correos institucionales (@utvm.edu.mx)."]);
    exit;
}

// --------------------------------------
// 3. Verificar correo repetido en BD
// --------------------------------------
$q = $conexion->prepare("SELECT id_usuario FROM usuarios WHERE email = ?");
$q->bind_param("s", $email);
$q->execute();
$q->store_result();

if ($q->num_rows > 0) {
    echo json_encode(["status" => "error", "message" => "Ese correo ya está registrado."]);
    exit;
}
$q->close();

// --------------------------------------
// 4. Insertar en usuarios
// --------------------------------------
$query = $conexion->prepare("
    INSERT INTO usuarios 
    (tipo_usuario, nombre, apellido_paterno, apellido_materno, email, telefono, password)
    VALUES (?, ?, ?, ?, ?, ?, ?)
");
$query->bind_param("sssssss",
    $tipoUsuario, $nombre, $apellidoP, $apellidoM, $email, $telefono, $password
);

if (!$query->execute()) {
    echo json_encode(["status" => "error", "message" => "Error al registrar usuario."]);
    exit;
}

$idUsuario = $query->insert_id;
$query->close();

// --------------------------------------
// 5A. Insertar ADMINISTRADOR
// --------------------------------------
if ($tipoUsuario === "administrador") {
    $empleadoId = $data["employeeId"];
    $rol        = $data["role"];

    $q2 = $conexion->prepare("
        INSERT INTO administradores (id_usuario, empleado_id, rol)
        VALUES (?, ?, ?)
    ");
    $q2->bind_param("iss", $idUsuario, $empleadoId, $rol);
    $q2->execute();
    $q2->close();
}

// --------------------------------------
// 5B. Insertar ESTUDIANTE + MOTOCICLETA
// --------------------------------------
if ($tipoUsuario === "estudiante") {
    $matricula = $data["studentId"];
    $programa  = $data["educationalProgram"];

    $qEst = $conexion->prepare("
        INSERT INTO estudiantes (id_usuario, matricula, programa)
        VALUES (?, ?, ?)
    ");
    $qEst->bind_param("iss", $idUsuario, $matricula, $programa);
    $qEst->execute();
    $qEst->close();

    $plate = $data["motorcyclePlate"];
    $brand = $data["motorcycleBrand"];
    $model = $data["motorcycleModel"];
    $color = $data["motorcycleColor"];
    $disp  = $data["motorcycleDisplacement"];
    $acc   = $data["motorcycleAccessories"];

    $qMoto = $conexion->prepare("
        INSERT INTO motocicletas 
        (id_usuario, matricula, marca, modelo, color, cilindraje, accesorios)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    $qMoto->bind_param("issssss",
        $idUsuario, $plate, $brand, $model, $color, $disp, $acc
    );
    $qMoto->execute();
    $qMoto->close();
}



echo json_encode(["status" => "success", "message" => "Usuario registrado correctamente."]);
?>


