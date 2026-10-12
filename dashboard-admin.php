<?php 
session_start();
if (!isset($_SESSION["usuario"])) {
  header("Location: ../index.html");
  exit;
}

$usuario = $_SESSION["usuario"];
include __DIR__ . "/../api/conexion.php";

// Usuarios registrados
$qUsuarios = $conexion->query("SELECT COUNT(*) as total FROM usuarios");
$totalUsuarios = $qUsuarios->fetch_assoc()['total'] ?? 0;

// Ingresos de hoy
$qIngresosHoy = $conexion->query("
    SELECT COUNT(*) as total 
    FROM historial_entradas 
    WHERE tipo='entrada' 
    AND DATE(fecha)=CURDATE()
");
$ingresosHoy = $qIngresosHoy->fetch_assoc()['total'] ?? 0;

// Motos actualmente dentro (entradas - salidas)
$qMotosDentro = $conexion->query("
    SELECT 
        (SELECT COUNT(*) FROM historial_entradas WHERE tipo='entrada') -
        (SELECT COUNT(*) FROM historial_entradas WHERE tipo='salida')
        AS total
");
$motosDentro = $qMotosDentro->fetch_assoc()['total'] ?? 0;
if($motosDentro < 0) $motosDentro = 0;

// Capacidad
$capacidadTotal = 100;
$porcentaje = ($motosDentro / $capacidadTotal) * 100;
$disponibles = $capacidadTotal - $motosDentro;

// Actividad reciente
$qActividad = $conexion->query("
    SELECT h.*, 
           u.nombre, u.apellido_paterno, u.apellido_materno,
           m.matricula
    FROM historial_entradas h
    INNER JOIN usuarios u ON h.id_usuario = u.id_usuario
    LEFT JOIN motocicletas m ON h.id_moto = m.id_moto
    ORDER BY h.fecha DESC
    LIMIT 5
");
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Dashboard - Admin</title>
  <link rel="stylesheet" href="../styles/dashboard-admin.css" />
   <link rel="stylesheet" type="text/css" href="../styles/fontello.css">
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
          <a href="/views/dashboard-admin.php" class="nav-item active">
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

           <a href="/views/asignar_rfid.php" class="nav-item">
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
            <!--<a href="#" class="pm-item">Configuración</a>-->
            <a href="../api/logout.php" class="pm-item danger">Cerrar Sesión</a>
          </div>
        </div>
      </div>
    </aside>

    <!-- MAIN -->
    <div class="main-area">
      <header class="main-header">
        <div class="header-left">
          <h1>Panel de Administración</h1>
          <p class="subtitle">Monitorea el estacionamiento en tiempo real</p>
        </div>
      </header>

      <main class="page-content">

        <!-- Métricas -->
        <section class="row grid-3">
         <article class="card metric-card">
             <div class="icon-placeholder icon-motorcycle"></div>
             <h2 class="card-title">Motos en Estacionamiento</h2>
             <p class="metric-value" id="motosDentro"><?= $motosDentro ?></p>
             <small class="muted">de <?= $capacidadTotal ?> espacios</small>
         </article>

         <article class="card metric-card">
             <div class="icon-placeholder icon-ingreso"></div>
             <h2 class="card-title">Ingresos de hoy</h2>
             <p class="metric-value" id="ingresosHoy"><?= $ingresosHoy ?></p>
             <small class="muted">Registros del día actual</small>
         </article>

         <article class="card metric-card">
             <div class="icon-placeholder icon-users"></div>
             <h2 class="card-title">Usuarios Registrados</h2>
             <p class="metric-value" id="totalUsuarios"><?= $totalUsuarios ?></p>
             <small class="muted">Estudiantes y personal</small>
         </article>
        </section>

        <!-- Capacidad + Usuarios -->
       <section class="row grid-2-1">
  <article class="card capacity-card">
    <h2 class="card-title">Capacidad del Estacionamiento</h2>

    <div class="capacity-row">
      <span class="subtitle">Ocupación Actual</span>
      <span class="capacity-number" id="capacidadTexto">
    <?= $motosDentro ?>/<?= $capacidadTotal ?>
</span>
    </div>

    <div class="progress-bar">
      <div class="progress-fill" id="progressBar"
     style="width: <?= round($porcentaje) ?>%;"></div>
    </div>
 <!-- Barra de progreso -->
    <div class="capacity-footer">
      <span class="spaces-available" id="disponibles">
    <?= $disponibles ?> espacios disponibles
</span>
      <span class="percent-occupied" id="porcentaje">
    <?= round($porcentaje) ?>% ocupado
</span>
    </div>
  </article>

  <aside class="card users-card">
    <div class="icon-placeholder icon-user"></div>
    <h2 class="card-title usuarios">Ver Usuarios</h2>
    <small class="muted">Gestiona todos los usuarios registrados</small>
    <a href="/views/users-view.php" class="btn-link">Ir a Usuarios</a>
  </aside>
</section>

    
        </section>

        <!-- Actividad -->
        <!-- ACTIVIDAD RECIENTE -->
         <section class="activity-section">
  <div class="activity-header">
    <h2 class="card-title">Actividad Reciente</h2>
    <a href="/views/admin-historial.php" class="see-history">Ver historial completo →</a>
  </div>

  <ul class="activity-list" id="actividadList">
    <?php while($fila = $qActividad->fetch_assoc()): ?>
      <li class="activity-item">
        <div class="icon <?= $fila['tipo'] == 'entrada' ? 'icon-in' : 'icon-out' ?>">➜</div>

        <div class="activity-info">
          <span class="plate"><?= $fila["matricula"] ?? "Sin placa" ?></span>
          <span class="tag <?= $fila['tipo'] ?>">
            <?= ucfirst($fila["tipo"]) ?>
          </span>
          <span class="name">
            <?= $fila["nombre"] . " " . $fila["apellido_paterno"] ?>
          </span>
        </div>

        <div class="activity-time">
          <span><?= date("H:i", strtotime($fila["fecha"])) ?></span>
        </div>
      </li>
    <?php endwhile; ?>
  </ul>
</section>



      </main>
    </div>
  </div>

  <script src="../javascript/dashboard-admin.js"></script>
</body>
</html>