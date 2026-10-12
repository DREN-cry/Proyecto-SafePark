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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuarios Registrados</title>
    <link rel="stylesheet" href="../styles/user-view.css">
    <link rel="stylesheet" type="text/css" href="../styles/fontello.css">
</head>

<body>
<div class="layout">

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
            <a href="/views/dashboard-admin.php" class="nav-item">
                <span class="nav-icon"></span>
                <span class="nav-label">Dashboard</span>
            </a>

            <a href="/views/users-view.php" class="nav-item active">
                <span class="nav-icon"></span>
                <span class="nav-label">Usuarios Registrados</span>
            </a>

            <a href="/views/admin-historial.php" class="nav-item">
                <span class="nav-icon"></span>
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
            <div id="profileToggle" class="profile-row">
                <div class="profile-avatar"><?php echo strtoupper($usuario['nombre'][0]); ?></div>

                <div class="profile-info">
                    <div class="profile-name">
                        <?= $usuario['nombre'] . " " . $usuario['apellidos'] ?>
                    </div>

                    <div class="profile-email">
                        <?= $usuario['email'] ?>
                    </div>
                </div>

                <div class="profile-arrow icon-arrow"></div>
            </div>

            <div id="profileMenu" class="profile-menu">
                <a href="#" class="pm-item">Configuración</a>
                <a href="../api/logout.php" class="pm-item danger">Cerrar Sesión</a>
            </div>
        </div>
    </div>

</aside>

<!-- CONTENIDO PRINCIPAL -->
<main class="main-area">

    <header class="main-header">
        <div class="header-left">
            <h1>Sistema de Usuarios</h1>
            <p class="subtitle">Monitorea el estacionamiento en tiempo real</p>
        </div>
    </header>

    <section class="container">

        <div class="page-title">
            <div>
                <h1>Usuarios Registrados</h1>
                <p><?= count($users) ?> personas registradas en el sistema</p>
            </div>
        </div>

        <!-- BUSCADOR -->
        <!-- BUSCADOR -->
<div class="search-box">
    <input type="text" id="searchInput" placeholder="Buscar por nombre, matrícula o email...">
</div>

<!-- GRID DE USUARIOS -->
<p class="resultados" id="countUsers"></p>
<div class="users-grid" id="usersContainer"></div>


        <?php if (!empty($users)): ?>
            <?php foreach ($users as $u): ?>

    <div class="card user-card">

        <div class="card-content">

            <!-- Usuario -->
            <div class="user-info">
                <div class="avatar">
                    <?= strtoupper(substr($u['nombre'],0,1)) . strtoupper(substr($u['apellido_paterno'],0,1)) ?>
                </div>

                <div class="info">
                    <h3><?= $u['nombre_completo'] ?></h3>

                    <div class="tags">
                        <span class="badge"><?= ucfirst($u['tipo_usuario']) ?></span>

                        <?php if ($u['matricula_est']): ?>
                            <span class="matricula">Matricula: <?= $u['matricula_est'] ?></span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Contacto -->
            <div class="contact">
                <div class="item">
                    <span class="icon">🎓</span>
                    <p>P.E.: <?= $u['programa_est'] ?: "Sin programa" ?></p>
                </div>

                <div class="item">
                    <span class="icon">📧</span>
                    <p class="datos">Correo: <?= $u['email'] ?></p>
                </div>

                <div class="item">
                    <span class="icon">📞</span>
                    <p>Telefono: <?= $u['telefono'] ?></p>
                </div>
            </div>

            <!-- Moto -->
            <?php if ($u['id_moto']): ?>
            <div class="moto">
                <h4>🏍 Motocicleta</h4>
                <div class="moto-grid">
                    <div><span>ID Motocicleta:</span> <?= $u['id_moto'] ?></div>
                    <div><span>Matrícula:</span> <?= $u['matricula_moto'] ?></div>
                    <div><span>Marca:</span> <?= $u['marca'] ?></div>
                    <div><span>Modelo:</span> <?= $u['modelo'] ?></div>
                    <div><span>Color:</span> <?= $u['color'] ?></div>
                    <div><span>Cilindraje:</span> <?= $u['cilindraje'] ?></div>
                    <div class="full"><span>Accesorios:</span> <?= $u['accesorios'] ?></div>
                </div>
            </div>
            <?php endif; ?>

            
                
            <div class="acciones-usuario ">

    <!-- ✏ BOTÓN EDITAR -->
    <a href="edit-user-view.php?id=<?= $u['id_usuario'] ?>" 
       class="btn-edit">
       ✏ Editar Usuario
    </a>

    <!-- 🗑 BOTÓN ELIMINAR -->
    <form action="../api/delete-user.php" method="POST"
          onsubmit="return confirm('¿Seguro que deseas eliminar este usuario?');">

        <input type="hidden" name="id_usuario" value="<?= $u['id_usuario'] ?>">

        <button type="submit" class="btn-delete">
            🗑 Eliminar Usuario
        </button>
    </form>

</div>
        </div>
    </div>

<?php endforeach; ?>
        <?php else: ?>
            <p>No hay usuarios registrados.</p>
        <?php endif; ?>

        </div>

    </section>
</main>

</div>
<script src="../javascript/dashboard-admin.js"></script>
<script src="../javascript/search_users.js"></script>

</body>
</html>