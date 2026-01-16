<?php
// /admin/wow_configuracion.php
require_once __DIR__ . '/../includes/wow_auth.php'; 
verificarLogin();
verificarRol('admin');

// Determinar cursor según rol
$rolUsuario = $_SESSION['user']['role'] ?? 'viewer';
$claseCursor = ($rolUsuario === 'tester') ? 'cursor-sword' : 'cursor-gauntlet';

$db = new PDO("sqlite:" . __DIR__ . "/../database/wow.sqlite");
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validarCSRF($_POST['csrf_token'] ?? '');
    
    $items = (int)$_POST['items_per_page'];
    if($items < 1) $items = 10;
    
    // Guardar en tabla settings
    $stmt = $db->prepare("INSERT OR REPLACE INTO settings (key, value) VALUES ('items_per_page', ?)");
    $stmt->execute([$items]);
    
    // Actualizar config en memoria (si wow_auth lo permite) o recargar
    $mensaje = "Configuración guardada.";
}

// Leer config actual
$currentItems = $db->query("SELECT value FROM settings WHERE key='items_per_page'")->fetchColumn();
if(!$currentItems) $currentItems = 10;
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Configuración - WoW Test Manager</title>
  <link rel="icon" href="../assets/favicon.png" type="image/png">
  <link rel="stylesheet" href="../assets/wow_style.css">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body class="<?= $claseCursor ?>">
  <aside class="sidebar">
    <div class="sidebar-logo-top"><img src="../assets/wow_logo.png" alt="WoW"></div>
    <nav class="sidebar-nav">
      <a href="wow_dashboard.php"><i class="fa-solid fa-gauge-high"></i> Dashboard</a>
      <a href="wow_sesiones.php"><i class="fa-solid fa-scroll"></i> Sesiones</a>
      <a href="wow_contenido.php"><i class="fa-solid fa-book-journal-whills"></i> Contenido</a>
      <a href="wow_testers.php"><i class="fa-solid fa-helmet-safety"></i> Testers</a>
      <a href="wow_usuarios.php"><i class="fa-solid fa-users"></i> Usuarios</a>
      <a href="wow_reportes.php"><i class="fa-solid fa-chart-line"></i> Reportes</a>
      <a href="wow_configuracion.php" class="active"><i class="fa-solid fa-gears"></i> Configuración</a>
      <a href="wow_perfil.php"><i class="fa-solid fa-user-shield"></i> Mi Perfil</a>
    </nav>
    <div class="sidebar-footer">
      <a href="../auth/logout.php" class="btn-wow danger" style="width:100%;">Cerrar Sesión</a>
    </div>
  </aside>
  <main class="content">
    <h1>Ajustes del Reino</h1>
    <?php if($mensaje): ?><div class="status success"><?= $mensaje ?></div><?php endif; ?>
    <div class="page-grid">
      <div class="grid-col-form" style="max-width:500px;">
        <section class="panel">
          <h2>Preferencias Globales</h2>
          <form method="POST">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
            <div style="margin-bottom:15px;">
                <label>Elementos por página (Paginación)</label>
                <input type="number" name="items_per_page" value="<?= $currentItems ?>" min="1" max="100">
            </div>
            <button type="submit" class="btn-wow primary">Guardar Cambios</button>
          </form>
        </section>
      </div>
    </div>
  </main>
</body>
</html>
