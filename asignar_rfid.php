<?php 
session_start();
if (!isset($_SESSION["usuario"])) {
  header("Location: ../index.html");
  exit;
}
$usuario = $_SESSION["usuario"];

// Obtener usuarios desde API usando include
$data = include __DIR__ . "/../api/get_users.php";

// Verificar si hay error
if ($data["status"] === "error") {
    echo "<p style='color:red;'>Error en consulta SQL: ".$data["message"]."</p>";
    $users = [];
} else {
    $users = $data["usuarios"];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Asignar RFID</title>
<link rel="stylesheet" href="../styles/asignar_rfid.css" />
</head>
<body>
<div class="app-layout">

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
          <a href="/views/dashboard-admin.php" class="nav-item ">
            <span class="nav-icon"><!-- icon: grid --></span>
            <span class="nav-label">Dashboard</span>
          </a>

          <a href="/views/users-view.php" class="nav-item">
            <span class="nav-icon"><!-- icon: history --></span>
            <span class="nav-label">Usuarios Registrados</span>
          </a>

          <a href="/views/admin-historial.php" class="nav-item">
            <span class="nav-icon"><!-- icon: history --></span>
            <span class="nav-label">Historial de entradas</span>
          </a>

           <a href="/views/asignar_rfid.php" class="nav-item active">
            <span class="nav-icon"><!-- icon: history --></span>
            <span class="nav-label">Asignar tarjeta</span>
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
            <div class="profile-arrow icon-arrow"><!-- icon: chevron --></div>
          </div>

          <!-- DROPDOWN -->
          <div id="profileMenu" class="profile-menu" role="menu" aria-hidden="true">
            <a href="#" class="pm-item">Configuración</a>
            <a href="../api/logout.php" class="pm-item danger">Cerrar Sesión</a>
          </div>
        </div>
      </div>
    </aside>

<div class="main-area">
     <header class="main-header">
        <div class="header-left">
          <h1>Panel de Administración</h1>
          <p class="subtitle">Monitorea el estacionamiento en tiempo real</p>
        </div>
      </header>

<main class="page-content">
 <div class="card">
    <h2>Asignar Tarjeta RFID</h2>

    <form method="POST" action="../api/guardar_rfid.php">


        <label>Seleccionar Usuario:</label>
        <select name="usuario" required>
    <option value="">Selecciona uno...</option>
    <?php foreach($users as $u){ ?>
        <?php if($u['tipo_usuario'] === 'estudiante'){ ?>  <!-- 👈 Filtro -->
            <option value="<?=$u['id_usuario']?>">
                <?=$u['matricula_est']?> <?=$u['nombre']?> <?=$u['apellido_paterno']?> <?=$u['apellido_materno']?>
            </option>
        <?php } ?>
    <?php } ?>
</select>



        <label>UID de Tarjeta RFID:</label>
        <input type="text" name="uid" id="uid" placeholder="Escanea tarjeta..." required>

        <p class="msg">📌 Cuando acerques la tarjeta al lector, el UID aparecerá aquí automáticamente.</p>

        <button name="asignar">Asignar Tarjeta</button>
    </form>
    </main>
   </div>
 </div>
</div>

<!-- JS opcional para auto-focus -->
<script>
document.getElementById("uid").focus();
</script>
 <script src="../javascript/dashboard-admin.js"></script>
 <script src="../javascript/leer_uid.js"></script>


</body>
</html>


