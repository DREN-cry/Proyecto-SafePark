<?php
session_start();
if (!isset($_SESSION["usuario"])) {
    header("Location: ../index.html");
    exit;
}

$usuario = $_SESSION["usuario"];
$dias = isset($_GET["dias"]) ? (int)$_GET["dias"] : 7;
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <title>Historial de entradas</title>
  <link rel="stylesheet" href="../styles/admin-historial.css" />
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
      <div class="brand-text">
        <h3>SafePark</h3>
        <small>Sistema de Control</small>
      </div>
    </div>

    <nav class="nav">
      <a href="/views/dashboard-admin.php" class="nav-item">
        <span class="nav-label">Dashboard</span>
      </a>

      <a href="/views/users-view.php" class="nav-item">
        <span class="nav-label">Usuarios Registrados</span>
      </a>

      <a href="/views/admin-historial.php" class="nav-item active">
        <span class="nav-label">Historial de entradas</span>
      </a>

      <a href="/views/asignar_rfid.php" class="nav-item">
        <span class="nav-label">Asignar tarjeta</span>
      </a>
    </nav>
  </div>

  <div class="sidebar-bottom">
    <div class="profile-wrapper">
      <div class="profile-row">
        <div class="profile-avatar">
          <?php echo strtoupper($usuario['nombre'][0]); ?>
        </div>
        <div class="profile-info">
          <div class="profile-name">
            <?php echo $usuario['nombre']; ?>
          </div>
          <div class="profile-email">
            <?php echo $usuario['email']; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</aside>

<!-- MAIN -->
<div class="main-area">
  <header class="header">    
    <div class="header-left">
      <h1>Bienvenido, <?php echo $usuario['nombre']; ?></h1>
      <p class="subtitle">Monitorea el estacionamiento en tiempo real</p>
    </div>
  </header>
   <div class="container">
    <div class="filtros">
        <a href="?dias=1"   class="filtro-btn <?= $dias==1   ? 'active' : '' ?>">Hoy</a>
        <a href="?dias=7"   class="filtro-btn <?= $dias==7   ? 'active' : '' ?>">7 días</a>
        <a href="?dias=30"  class="filtro-btn <?= $dias==30  ? 'active' : '' ?>">30 días</a>
        <a href="?dias=365" class="filtro-btn <?= $dias==365 ? 'active' : '' ?>">Todo</a>
    </div>

    <div class="card">
        <div class="card-header">
            <div class="card-title">Historial</div>
        </div>
        <div class="card-content" id="historialContainer"></div>
    </div>
</div>
</div>
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
               
function cargarHistorial() {
    const dias = new URLSearchParams(window.location.search).get("dias") || 1;

    fetch(`../api/obtener_historial.php?dias=${dias}`)
        .then(res => res.json())
        .then(data => {
            const container = document.getElementById("historialContainer");

            if(!data.length){
                container.innerHTML = `<p style="color:#888; padding:16px">Sin registros en este período.</p>`;
                return;
            }
            

            container.innerHTML = data.map(registro => `
              
                <div class="record-row">
                    <div class="record-info">
                        <p class="record-id">${registro.matricula ?? 'Sin placa'}</p>
                        <p class="record-student">
                            ${registro.nombre} 
                            ${registro.apellido_paterno} 
                            ${registro.apellido_materno}
                        </p>
                    </div>
                    <div class="record-right">
                        <span class="badge ${registro.tipo === 'entrada' ? 'badge-entry' : 'badge-exit'}">
                            ${registro.tipo}
                        </span>
                        <p class="record-time">
                            ${new Date(registro.fecha).toLocaleTimeString('es-MX', {hour:'2-digit', minute:'2-digit'})}
                        </p>
                    </div>
                </div>
            `).join("");
        })
        .catch(error => console.log(error));
}

cargarHistorial();
setInterval(cargarHistorial, 900);
</script>

</body>
</html>
