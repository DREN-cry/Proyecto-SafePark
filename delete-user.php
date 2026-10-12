<?php
require_once 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if(isset($_POST['id_usuario'])){

        $id = intval($_POST['id_usuario']);

        $stmt = $conexion->prepare("DELETE FROM usuarios WHERE id_usuario = ?");
        $stmt->bind_param("i", $id);

        if($stmt->execute()){
            header("Location: ../views/users-view.php");
            exit();
        } else {
            echo "Error al eliminar usuario";
        }

        $stmt->close();
        $conexion->close();
    }
}
?>