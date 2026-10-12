<?php
include_once "conexion.php";

// Saber si el archivo fue llamado directamente por el navegador
$called_direct = (realpath($_SERVER['SCRIPT_FILENAME']) === __FILE__);

function fetch_all_users($conexion) {
    $usuarios = [];

    $sql = "
        SELECT 
            u.id_usuario,
            u.tipo_usuario,
            u.nombre,
            u.apellido_paterno,
            u.apellido_materno,
            u.email,
            u.telefono,

            e.matricula AS matricula_est,
            e.programa AS programa_est,

            a.empleado_id AS empleado_id,
            a.rol AS admin_rol,

            m.id_moto,
            m.matricula AS matricula_moto,
            m.marca,
            m.modelo,
            m.color,
            m.cilindraje,
            m.accesorios
        FROM usuarios u
        LEFT JOIN estudiantes e ON u.id_usuario = e.id_usuario
        LEFT JOIN administradores a ON u.id_usuario = a.id_usuario
        LEFT JOIN motocicletas m ON u.id_usuario = m.id_usuario
        ORDER BY u.id_usuario DESC
    ";

    $res = $conexion->query($sql);

    if ($res === false) {
        return [
            "status" => "error",
            "message" => "SQL ERROR: " . $conexion->error,
            "usuarios" => []
        ];
    }

    while ($row = $res->fetch_assoc()) {
        $row['nombre_completo'] = trim(
            $row['nombre'] . " " . 
            $row['apellido_paterno'] . " " . 
            $row['apellido_materno']
        );

        $usuarios[] = $row;
    }

    return [
        "status" => "success",
        "usuarios" => $usuarios
    ];
}

$result = fetch_all_users($conexion);

// Si fue llamado desde el navegador (no por include)
if ($called_direct) {
    header("Content-Type: application/json; charset=UTF-8");
    echo json_encode($result, JSON_UNESCAPED_UNICODE);
    exit;
}

// Si es include, devolver el array
return $result;

?>