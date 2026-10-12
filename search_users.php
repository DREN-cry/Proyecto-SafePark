<?php
include_once "conexion.php";

$search = isset($_GET['search']) ? $conexion->real_escape_string($_GET['search']) : '';

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

    m.id_moto,
    m.matricula AS matricula_moto,
    m.marca,
    m.modelo,
    m.color,
    m.cilindraje,
    m.accesorios
FROM usuarios u
LEFT JOIN estudiantes e ON u.id_usuario = e.id_usuario
LEFT JOIN motocicletas m ON u.id_usuario = m.id_usuario
WHERE 
    u.nombre LIKE '%$search%' OR
    u.apellido_paterno LIKE '%$search%' OR
    u.apellido_materno LIKE '%$search%' OR
    CONCAT(u.nombre,' ',u.apellido_paterno,' ',u.apellido_materno) LIKE '%$search%' OR
    u.email LIKE '%$search%' OR
    e.matricula LIKE '%$search%' OR
    m.matricula LIKE '%$search%'
ORDER BY u.id_usuario DESC
";

$res = $conexion->query($sql);
$usuarios = [];

while($row = $res->fetch_assoc()){
    $row['nombre_completo'] = $row['nombre']." ".$row['apellido_paterno']." ".$row['apellido_materno'];
    $usuarios[] = $row;
}

echo json_encode(["status"=>"success","usuarios"=>$usuarios], JSON_UNESCAPED_UNICODE);
exit;
?>
