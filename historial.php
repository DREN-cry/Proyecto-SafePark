<?php
session_start();
if (!isset($_SESSION["usuario"])) {
    header("Location: ../index.html");
    exit;
}

$usuario = $_SESSION["usuario"];

// Filtro de días (por defecto 7)
$dias = isset($_GET["dias"]) ? (int)$_GET["dias"] : 7;


?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Historial de Registros</title>
  <link rel="stylesheet" href="../styles/historial.css">
  <link rel="stylesheet" type="text/css" href="../styles/fontello.css">
  <style>
    .filtros {
      display: flex;
      gap: 8px;
      margin-bottom: 20px;
    }
    .filtro-btn {
      padding: 6px 16px;
      border-radius: 20px;
      border: 1px solid #e0e0e0;
      background: transparent;
      cursor: pointer;
      font-size: 13px;
      transition: all .2s;
      text-decoration: none;
      color: inherit;
    }
    .filtro-btn.active {
      background: var(--accent, #0044ff);
      color: #fff;
      border-color: transparent;
    }
  </style>
</head>
<body>

<div class="page">
  <!-- SIDEBAR -->
  <aside class="sidebar" id="sidebar">
    <div class="sidebar-top">
      <div class="brand">
        <div class="brand-icon"></div>
        <div class="brand-text">
          <h3>SafePark</h3>
          <small>Sistema de Control</small>
        </div>
      </div>
      <nav class="nav">
        <a href="/views/dashboard-user.php" class="nav-item">
          <span class="nav-icon"></span>
          <span class="nav-label">Dashboard</span>
        </a>
        <a href="/views/historial.php" class="nav-item active">
          <span class="nav-icon"></span>
          <span class="nav-label">Mi Historial</span>
        </a>
      </nav>
    </div>
    <div class="sidebar-bottom">
      <div class="profile-wrapper">
        <div id="profileToggle" class="profile-row" aria-haspopup="true" aria-expanded="false">
          <div class="profile-avatar"><?php echo strtoupper($usuario['nombre'][0]); ?></div>
          <div class="profile-info">
            <div class="profile-name"><?php echo $usuario['nombre'].' '.$usuario['apellidos']; ?></div>
            <div class="profile-email"><?php echo $usuario['email']; ?></div>
          </div>
          <div class="profile-arrow icon-arrow"></div>
        </div>
        <div id="profileMenu" class="profile-menu" role="menu" aria-hidden="true">
          <a href="/views/configuracion.php" class="pm-item">Configuración</a>
          <a href="../api/logout.php" class="pm-item danger">Cerrar Sesión</a>
        </div>
      </div>
    </div>
  </aside>

  <main class="main-area">
    <header class="main-header">
      <div class="header-left">
        <h1>Bienvenido, <?php echo $usuario['nombre']; ?></h1>
        <p class="subtitle">Historial de registros en tiempo real</p>
      </div>
    </header>

    <div class="container">

      <!-- Filtros -->
      <div class="filtros">
        <a href="?dias=1"   class="filtro-btn <?= $dias==1   ? 'active' : '' ?>">Hoy</a>
        <a href="?dias=7"   class="filtro-btn <?= $dias==7   ? 'active' : '' ?>">7 días</a>
        <a href="?dias=30"  class="filtro-btn <?= $dias==30  ? 'active' : '' ?>">30 días</a>
        <a href="?dias=365" class="filtro-btn <?= $dias==365 ? 'active' : '' ?>">Todo</a>
      </div>

      <div id="historialContainer">
        <p style="color:#888">Cargando...</p>
      </div>
    </div>
  </main>
</div>

<script>
const diasSemana = ["Domingo","Lunes","Martes","Miércoles","Jueves","Viernes","Sábado"];
const meses = ["Enero","Febrero","Marzo","Abril","Mayo","Junio",
               "Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre"];

function formatearFecha(fechaStr) {
    // Forzar interpretación local agregando T00:00:00
    const d = new Date(fechaStr + "T00:00:00");
    return `${diasSemana[d.getDay()]}, ${d.getDate()} ${meses[d.getMonth()]} ${d.getFullYear()}`;
}
function formatearHora(fechaStr) {
    const d = new Date(fechaStr);
    return d.toLocaleTimeString('es-MX', { hour: '2-digit', minute: '2-digit' });
}

function cargarHistorial() {
    const dias = new URLSearchParams(window.location.search).get("dias") || 7;

    fetch(`../api/historial_usuario.php?dias=${dias}`)
        .then(r => r.json())
        .then(data => {
            const container = document.getElementById("historialContainer");

            if(!Array.isArray(data) || data.length === 0){
                container.innerHTML = `
                    <div class="card">
                        <div class="card-content"><p>No hay registros en este período.</p></div>
                    </div>`;
                return;
            }

            // Agrupar por día
            const porDia = {};
            data.forEach(reg => {
                const dia = reg.fecha.substring(0, 10);
                if(!porDia[dia]) porDia[dia] = [];
                porDia[dia].push(reg);
            });

            container.innerHTML = Object.entries(porDia).map(([fecha, registros]) => `
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">${formatearFecha(fecha)}</div>
                    </div>
                    <div class="card-content">
                        ${registros.map(reg => `
                            <div class="entry-row">
                                <div class="entry-icon ${reg.tipo === 'entrada' ? 'entry-bg icon-right' : 'exit-bg icon-left'}"></div>
                                <div class="entry-info">
                                    <p class="entry-type">${reg.tipo.charAt(0).toUpperCase() + reg.tipo.slice(1)}</p>
                                </div>
                                <div class="entry-time">${formatearHora(reg.fecha)}</div>
                            </div>
                        `).join("")}
                    </div>
                </div>
            `).join("");
        })
        .catch(() => {
            document.getElementById("historialContainer").innerHTML =
                `<div class="card"><div class="card-content"><p>Error al cargar registros.</p></div></div>`;
        });
}

cargarHistorial();
setInterval(cargarHistorial, 900); 
</script>
</body>
</html>