<?php
session_start();
if (!isset($_SESSION["usuario"])) {
  header("Location: ../index.html");
  exit;
}

require_once(__DIR__ . "/../api/conexion.php");

if(!isset($_GET['id'])){
    header("Location: users-view.php");
    exit();
}

$id = $_GET['id'];

$sql = "SELECT * FROM usuarios WHERE id_usuario = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$resultado = $stmt->get_result();
$u = $resultado->fetch_assoc();

$usuario = $_SESSION["usuario"];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Usuario</title>
    <link rel="stylesheet" href="../styles/user-view.css">
</head>

<body>
<div class="layout">

<!-- SIDEBAR -->
<aside class="sidebar">

    <div class="sidebar-top">
        <div class="brand">
            <h3>SafePark</h3>
            <small>Sistema de Control</small>
        </div>

        <nav class="nav">
            <a href="dashboard-admin.php" class="nav-item">Dashboard</a>
            <a href="users-view.php" class="nav-item active">Usuarios</a>
        </nav>
    </div>

    <div class="sidebar-bottom">
        <div class="profile-info">
            <?= $usuario['nombre'] ?>
        </div>
    </div>

</aside>

<!-- CONTENIDO -->
<main class="main-area">

    <header class="main-header">
        <h1>Editar Usuario</h1>
        <p class="subtitle">Modifica la información del usuario</p>
    </header>

    <section class="container">

        <div class="card edit-card">

            <form action="../api/update-user.php" method="POST" class="edit-form">

                <input type="hidden" name="id_usuario" value="<?= $u['id_usuario'] ?>">

                <div class="form-group">
                    <label>Nombre</label>
                    <input type="text" name="nombre" value="<?= $u['nombre'] ?>" required>
                </div>

                <div class="form-group">
                    <label>Apellido Paterno</label>
                    <input type="text" name="apellido_paterno" value="<?= $u['apellido_paterno'] ?>" required>
                </div>

                  <div class="form-group">
                    <label>Apellido Materno</label>
                    <input type="text" name="apellido_materno" value="<?= $u['apellido_materno'] ?>" required>
                </div>

                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" value="<?= $u['email'] ?>" required>
                </div>

                <div class="form-group">
                    <label>Teléfono</label>
                    <input type="text" name="telefono" value="<?= $u['telefono'] ?>">
                </div>

                <div class="form-actions">
                    <a href="users-view.php" class="btn-cancel">Cancelar</a>
                    <button type="submit" class="btn-save">
                        💾 Guardar Cambios
                    </button>
                </div>

            </form>

        </div>

    </section>

</main>
</div>

</body>
</html>