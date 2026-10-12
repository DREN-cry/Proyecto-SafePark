<?php
$conexion = new mysqli("localhost","root","","safe_park");

if($conexion->connect_error){
    exit;
}

$resultado = $conexion->query("SELECT uid FROM ultimo_uid WHERE id=1 LIMIT 1");

if($resultado && $resultado->num_rows > 0){
    $fila = $resultado->fetch_assoc();
    $uid = $fila["uid"];

    if(!empty($uid)){
        echo $uid;

        // Limpiar después de leer
        $conexion->query("UPDATE ultimo_uid SET uid='' WHERE id=1");
    }
}
?>