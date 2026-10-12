<?php
include "conexion.php"; // Ajusta si tu conexión está en otra ruta

if(!isset($_POST["uid"]) || !isset($_POST["usuario"])){
    die("Faltan datos");
}

$uid = $_POST["uid"];
$id_usuario = $_POST["usuario"];

// Obtener moto del usuario si existe
$sqlMoto = $conexion->query("SELECT id_moto FROM motocicletas WHERE id_usuario=$id_usuario LIMIT 1");
$id_moto = ($sqlMoto->num_rows > 0) ? $sqlMoto->fetch_assoc()["id_moto"] : "NULL";

// Insertar o actualizar si ya existe UID
$sql = "
INSERT INTO rfid_tags(uid, id_usuario, id_moto, activo)
VALUES('$uid', $id_usuario, $id_moto, 1)
ON DUPLICATE KEY UPDATE id_usuario=$id_usuario, id_moto=$id_moto, activo=1
";

if($conexion->query($sql)){
    // *** IMPORTANTE *** limpiar para que no siga autocompletando
    file_put_contents("ultima_uid.txt", "");

    echo "<script>alert('Tarjeta asignada exitosamente'); window.location='../views/asignar_rfid.php';</script>";
} else {
    echo "<script>alert('Error al guardar: ".$conexion->error."')</script>";
}
?>
