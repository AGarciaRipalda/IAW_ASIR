<?php
// /admin/wow_perfil.php
require_once __DIR__ . '/../includes/wow_auth.php';
verificarLogin();

// Determinar cursor según rol
$rolUsuario = $_SESSION['user']['role'] ?? 'viewer';
$claseCursor = ($rolUsuario === 'tester') ? 'cursor-sword' : 'cursor-gauntlet';

$db = new PDO("sqlite:" . __DIR__ . "/../database/wow.sqlite");
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$userId = $_SESSION['user']['id'];
$mensaje = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  validarCSRF($_POST['csrf_token'] ?? '');

  // Cambiar Email
  if (isset($_POST['email'])) {
    $email = trim($_POST['email']);
    $stmt = $db->prepare("UPDATE usuarios SET email = ? WHERE id = ?");
    $stmt->execute([$email, $userId]);
    $mensaje = "Email actualizado.";
  }

  // Cambiar Password
  if (isset($_POST['new_pass']) && !empty($_POST['new_pass'])) {
    $newPass = $_POST['new_pass'];
    $stmt = $db->prepare("UPDATE usuarios SET password = ? WHERE id = ?");
    $stmt->execute([password_hash($newPass, PASSWORD_DEFAULT), $userId]);
    $mensaje = "Contraseña actualizada.";
  }
}

// Obtener datos actuales
$stmt = $db->prepare("SELECT * FROM usuarios WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <title>Perfil - WoW Test Manager</title>
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
      <?php if ($_SESSION['user']['role'] === 'admin'): ?>
        <a href="wow_blizzard_sync.php"><i class="fa-solid fa-dragon"></i> Blizzard API</a>
        <a href="wow_usuarios.php"><i class="fa-solid fa-users"></i> Usuarios</a>
        <a href="wow_reportes.php"><i class="fa-solid fa-chart-line"></i> Reportes</a>
        <a href="wow_configuracion.php"><i class="fa-solid fa-gears"></i> Configuración</a>
      <?php endif; ?>
      <a href="wow_perfil.php" class="active"><i class="fa-solid fa-user-shield"></i> Mi Perfil</a>
    </nav>
    <div class="sidebar-footer">
      <a href="../auth/logout.php" class="btn-wow danger" style="width:100%;">Cerrar Sesión</a>
    </div>
  </aside>

  <main class="content">
    <h1>Ficha de Personaje</h1>
    <?php if ($mensaje): ?>
      <div class="status success"><?= $mensaje ?></div><?php endif; ?>

    <div class="page-grid">
      <div class="grid-col-form">
        <section class="panel">
          <h2>Datos de Cuenta</h2>
          <form method="POST">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">

            <label>Usuario (No editable)</label>
            <input type="text" value="<?= htmlspecialchars($user['username']) ?>" disabled
              style="margin-bottom:15px; opacity:0.7;">

            <label>Rol</label>
            <input type="text" value="<?= ucfirst($user['role']) ?>" disabled
              style="margin-bottom:15px; opacity:0.7; color:#ffd100;">

            <label>Email</label>
            <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required
              style="margin-bottom:15px;">

            <button type="submit" class="btn-wow primary">Actualizar Datos</button>
          </form>
        </section>

        <section class="panel" style="margin-top:20px;">
          <h2>Seguridad</h2>
          <form method="POST">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">

            <label>Nueva Contraseña</label>
            <input type="password" name="new_pass" required style="margin-bottom:15px;">

            <button type="submit" class="btn-wow secondary">Cambiar Contraseña</button>
          </form>
        </section>
      </div>
    </div>
  </main>
</body>

</html>