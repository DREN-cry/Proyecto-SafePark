<?php
date_default_timezone_set('America/Mexico_City');
header("Content-Type: application/json; charset=utf-8");

$conexion = new mysqli("localhost","root","","safe_park");

if($conexion->connect_error)
    exit(json_encode(["error"=>"Conexión fallida"]));

if(!isset($_POST["uid"]))
    exit(json_encode(["error"=>"No llegó UID"]));

$uid = $conexion->real_escape_string($_POST["uid"]);

//Guardar última UID
$conexion->query("
    INSERT INTO ultimo_uid(id, uid, fecha)
    VALUES(1,'$uid',NOW())
    ON DUPLICATE KEY UPDATE uid='$uid', fecha=NOW()
");

// Buscar UID registrada
$sql = $conexion->query("
    SELECT r.id_usuario, r.id_moto,
           u.nombre, u.apellido_paterno, u.apellido_materno,
           u.email, u.telefono, m.matricula
    FROM rfid_tags r
    INNER JOIN usuarios u ON r.id_usuario = u.id_usuario
    LEFT JOIN motocicletas m ON r.id_moto = m.id_moto
    WHERE r.uid = '$uid' AND r.activo = 1
    LIMIT 1
");

if($sql->num_rows > 0){
    $datos = $sql->fetch_assoc();

    // Verificar último movimiento
    $ultimo = $conexion->query("
        SELECT tipo FROM historial_entradas
        WHERE id_usuario = {$datos['id_usuario']}
        ORDER BY fecha DESC LIMIT 1
    ");

    $tipoMovimiento = "entrada";
    if($ultimo->num_rows > 0){
        $filaUltimo = $ultimo->fetch_assoc();
        $tipoMovimiento = ($filaUltimo["tipo"] == "entrada") ? "salida" : "entrada";
    }

    // 4️ Insertar en historial
    $conexion->query("
        INSERT INTO historial_entradas(uid, id_usuario, id_moto, tipo)
        VALUES(
            '$uid',
            {$datos['id_usuario']},
            ".($datos['id_moto'] ? $datos['id_moto'] : "NULL").",
            '$tipoMovimiento'
        )
    ");

    //  Lanzar WhatsApp + correo en background
    $notifData = http_build_query([
        "email"    => $datos['email'],
        "tipo"     => $tipoMovimiento,
        "nombre"   => $datos['nombre']." ".$datos['apellido_paterno'],
        "telefono" => $datos['telefono'],
        "matricula"=> $datos['matricula'],
        "hora"     => date("H:i")
    ]);

    $ctx = stream_context_create([
        "http" => [
            "method"  => "POST",
            "header"  => "Content-Type: application/x-www-form-urlencoded\r\n",
            "content" => $notifData,
            "timeout" => 1
        ]
    ]);

@file_get_contents("http://192.168.100.8/prueba_SP/api/notificaciones.php", false, $ctx);

    //  Respuesta inmediata al ESP 
    echo json_encode([
        "status"           => "ok",
        "uid"              => $uid,
        "nombre"           => $datos["nombre"],
        "apellido_paterno" => $datos["apellido_paterno"],
        "apellido_materno" => $datos["apellido_materno"],
        "matricula"        => $datos["matricula"]
    ]);

} else {
    echo json_encode([
        "status" => "no_registrado",
        "uid"    => $uid
    ]);
}
?>