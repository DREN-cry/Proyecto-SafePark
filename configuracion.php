<?php
session_start();
if (!isset($_SESSION["usuario"])) {
    header("Location: ../index.html");
    exit;
}

$usuario = $_SESSION["usuario"];
$moto = $usuario["moto"];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuración</title>
    <link rel="stylesheet" href="../styles/dashboard-user.css">
    <link rel="stylesheet" href="../styles/Styles_Configuracion.css">
</head>
<body>

    <!-- SIDEBAR -->
    <aside class="sidebar" id="sidebar">
      <div class="sidebar-top">
        <div class="brand">
          <div class="brand-icon"><!-- icon: bike --></div>
          <div class="brand-text">
            <h3>SafePark</h3>
            <small>Sistema de Control</small>
          </div>
        </div>

        <nav class="nav">
          <a href="/views/dashboard-user.php" class="nav-item active">
            <span class="nav-icon"><!-- icon: grid --></span>
            <span class="nav-label">Dashboard</span>
          </a>

          <a href="/views/historial.php" class="nav-item">
            <span class="nav-icon"><!-- icon: history --></span>
            <span class="nav-label">Mi Historial</span>
          </a>
        </nav>
      </div>

      <div class="sidebar-bottom">
        <div class="profile-wrapper">
          <div id="profileToggle" class="profile-row" aria-haspopup="true" aria-expanded="false">
            <div class="profile-avatar"><?php echo strtoupper($usuario['nombre'][0]); ?></div>
            <div class="profile-info">
              <div class="profile-name"><?php echo $usuario['nombre'] . ' ' . $usuario['apellidos']; ?></div>
              <div class="profile-email"><?php echo $usuario['email']; ?></div>
            </div>
            <div class="profile-arrow"><!-- icon: chevron --></div>
          </div>

          <!-- DROPDOWN -->
          <div id="profileMenu" class="profile-menu" role="menu" aria-hidden="true">
            <a href="#" class="pm-item">Configuración</a>
            <a href="../api/logout.php" class="pm-item danger">Cerrar Sesión</a>
          </div>
        </div>
      </div>
    </aside>

    <!-- HEADER -->
    <header class="config-header">
        <div>
            <h1>Panel de Administración</h1>
          <p class="subtitle">Monitorea el estacionamiento en tiempo real</p>
        </div>
    </header>

    <!-- CONTENEDOR PRINCIPAL -->
    <div class="page-container">

        <!-- 🔵 INFORMACIÓN PERSONAL -->
        <div class="card">
            <h2>Información Personal</h2>

            <div class="form-grid-2">
                <div>
                    <label>Nombre</label>
                    <input type="text" id="nombre">
                </div>

                <div>
                    <label>Apellido</label>
                    <input type="text" id="apellido">
                </div>
            </div>

            <label>Matrícula de Alumno</label>
            <input type="text" id="matricula" disabled class="input-disabled">
            <small class="note">La matrícula no puede ser modificada</small>

            <label>Teléfono</label>
            <input type="text" id="telefono">

            <label>Email Institucional</label>
            <input type="email" id="email">

            <label>Programa Educativo</label>
            <input type="text" id="programa">
        </div>

        <!-- 🔵 INFORMACIÓN DE MOTOCICLETA -->
        <div class="card">
            <h2>Información de Motocicleta</h2>

            <label>Matrícula de Moto</label>
            <input type="text" id="moto_matricula">

            <div class="form-grid-2">
                <div>
                    <label>Marca</label>
                    <input type="text" id="marca">
                </div>

                <div>
                    <label>Modelo</label>
                    <input type="text" id="modelo">
                </div>
            </div>

            <label>Color Principal</label>
            <input type="text" id="color">

            <label>Cilindraje (cc)</label>
            <input type="text" id="cilindraje">

            <label>Accesorios Característicos</label>
            <textarea id="accesorios"></textarea>
        </div>

        <button class="save-btn" onclick="guardarCambios()">Guardar cambios</button>
    </div>

<script src="../javascript/config.js"></script>
<script src="../javascript/dashboard-user.js"></script>
</body>
</html>
