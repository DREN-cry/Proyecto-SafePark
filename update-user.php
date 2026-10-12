<?php
require_once(__DIR__ . "/conexion.php");

if(isset($_POST['id_usuario'])){

    $id = $_POST['id_usuario'];
    $nombre = $_POST['nombre'];
    $apellido_paterno = $_POST['apellido_paterno'];
    $apellido_materno = $_POST['apellido_materno'];
    $email = $_POST['email'];
    $telefono = $_POST['telefono'];

    $sql = "UPDATE usuarios 
            SET nombre=?, apellido_paterno=?,apellido_materno=?, email=?, telefono=? 
            WHERE id_usuario=?";

    $stmt = $conexion->prepare($sql);
    $stmt->bind_param('ssssss', $nombre, $apellido_paterno,$apellido_materno, $email, $telefono, $id);

    if($stmt->execute()){
        header("Location: ../views/users-view.php?mensaje=actualizado");
        exit();
    } else {
        echo "Error al actualizar";
    }
}
?>