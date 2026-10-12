<?php
include('conect.php');

$id = $_POST['id_tag'];

$sql = "SELECT uid FROM rfid_tags WHERE id = ?'";
$result = $conexion->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo json_encode([
         "success" => true,
        "UID_tarjeta" => $row['UID_tarjeta']

    ]);
} else {
    echo json_encode(["success" => false, "message" => "Usuario no encontrado"]);
}

$conexion->close();
?>