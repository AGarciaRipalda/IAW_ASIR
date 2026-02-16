<?php
// /admin/wow_usuarios.php
require_once __DIR__ . '/../includes/wow_auth.php';
verificarLogin();
verificarRol('admin'); // Solo admins

// Determinar cursor según rol
$rolUsuario = $_SESSION['user']['role'] ?? 'viewer';
$claseCursor = ($rolUsuario === 'tester') ? 'cursor-sword' : 'cursor-gauntlet';

$db = new PDO("sqlite:" . __DIR__ . "/../database/wow.sqlite");
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$ROLES = ['admin', 'tester', 'viewer'];
$mensaje = '';
$error = '';
$pagina = (int) ($_GET['page'] ?? 1);
$porPagina = (int) ($APP_CONFIG['items_per_page'] ?? 10);

$editMode = false;
$item = ['id' => 0, 'username' => '', 'email' => '', 'role' => 'viewer'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  validarCSRF($_POST['csrf_token'] ?? '');

  if (isset($_POST['delete'])) {
    // Evitar borrarse a uno mismo
    if ((int) $_POST['delete'] === $_SESSION['user']['id']) {
      $error = "No puedes eliminar tu propia cuenta.";
    } else {
      $stmt = $db->prepare("DELETE FROM usuarios WHERE id = ?");
      $stmt->execute([(int) $_POST['delete']]);
      $mensaje = "Usuario eliminado.";
    }
  } else {
    $id = (int) $_POST['id'];
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $role = $_POST['role'];
    $pass = $_POST['password'];

    if ($id > 0) {
      // Actualizar
      $sql = "UPDATE usuarios SET username=?, email=?, role=? WHERE id=?";
      $params = [$username, $email, $role, $id];

      // Si pusieron password, actualizamos hash
      if (!empty($pass)) {
        $sql = "UPDATE usuarios SET username=?, email=?, role=?, password=? WHERE id=?";
        $params = [$username, $email, $role, password_hash($pass, PASSWORD_DEFAULT), $id];
      }
      $stmt = $db->prepare($sql);
      $stmt->execute($params);
      $mensaje = "Usuario actualizado.";
    } else {
      // Crear
      if (empty($pass)) {
        $error = "La contraseña es obligatoria para nuevos usuarios.";
      } else {
        $stmt = $db->prepare("INSERT INTO usuarios (username, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->execute([$username, $email, password_hash($pass, PASSWORD_DEFAULT), $role]);
        $mensaje = "Usuario creado.";
      }
    }
  }
}

if (isset($_GET['edit'])) {
  $stmt = $db->prepare("SELECT * FROM usuarios WHERE id = ?");
  $stmt->execute([(int) $_GET['edit']]);
  $found = $stmt->fetch(PDO::FETCH_ASSOC);
  if ($found) {
    $item = $found;
    $editMode = true;
  }
}

// Listado
$stmtCount = $db->query("SELECT COUNT(*) FROM usuarios");
$totalRegistros = $stmtCount->fetchColumn();
$totalPaginas = ceil($totalRegistros / $porPagina);
if ($pagina > $totalPaginas)
  $pagina = $totalPaginas;
$offset = ($pagina - 1) * $porPagina;

$rows = $db->query("SELECT * FROM usuarios ORDER BY username ASC LIMIT $porPagina OFFSET $offset")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <title>Usuarios - WoW Test Manager</title>
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
      <a href="wow_blizzard_sync.php"><i class="fa-solid fa-dragon"></i> Blizzard API</a>
      <a href="wow_usuarios.php" class="active"><i class="fa-solid fa-users"></i> Usuarios</a>
      <a href="wow_reportes.php"><i class="fa-solid fa-chart-line"></i> Reportes</a>
      <a href="wow_configuracion.php"><i class="fa-solid fa-gears"></i> Configuración</a>

      <a href="wow_perfil.php"><i class="fa-solid fa-user-shield"></i> Mi Perfil</a>
    </nav>
    <div class="sidebar-footer">
      <a href="../auth/logout.php" class="btn-wow danger" style="width:100%;">Cerrar Sesión</a>
    </div>
  </aside>

  <main class="content">
    <h1>Gestión de Cuentas</h1>
    <?php if ($mensaje): ?>
      <div class="status success"><?= $mensaje ?></div><?php endif; ?>
    <?php if ($error): ?>
      <div class="status err"><?= $error ?></div><?php endif; ?>

    <div class="page-grid">
      <div class="grid-col-form">
        <section class="panel">
          <h2><?= $editMode ? 'Editar Usuario' : 'Nuevo Usuario' ?></h2>
          <form method="POST">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
            <input type="hidden" name="id" value="<?= $item['id'] ?>">

            <label>Usuario</label>
            <input type="text" name="username" value="<?= htmlspecialchars($item['username']) ?>" required
              style="margin-bottom:15px;">

            <label>Email</label>
            <input type="email" name="email" value="<?= htmlspecialchars($item['email']) ?>" required
              style="margin-bottom:15px;">

            <label>Contraseña <?= $editMode ? '(Dejar en blanco para no cambiar)' : '' ?></label>
            <input type="password" name="password" style="margin-bottom:15px;">

            <label>Rol</label>
            <select name="role" style="margin-bottom:15px;">
              <?php foreach ($ROLES as $r): ?>
                <option value="<?= $r ?>" <?= $item['role'] == $r ? 'selected' : '' ?>><?= ucfirst($r) ?></option>
              <?php endforeach; ?>
            </select>

            <div class="form-button-group">
              <button type="submit" class="btn-wow primary"><?= $editMode ? 'Guardar' : 'Crear' ?></button>
              <?php if ($editMode): ?>
                <a href="wow_usuarios.php" class="btn-wow secondary">Cancelar</a>
              <?php endif; ?>
            </div>
          </form>
        </section>
      </div>

      <div class="grid-col-table">
        <section class="panel">
          <h2>Usuarios del Sistema</h2>
          <table>
            <thead>
              <tr>
                <th>ID</th>
                <th>Usuario</th>
                <th>Email</th>
                <th>Rol</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($rows as $r): ?>
                <tr>
                  <td><?= $r['id'] ?></td>
                  <td style="color:#fff; font-weight:bold;"><?= htmlspecialchars($r['username']) ?></td>
                  <td><?= htmlspecialchars($r['email']) ?></td>
                  <td><?= htmlspecialchars($r['role']) ?></td>
                  <td style="display:flex; gap:5px; justify-content:center;">
                    <a href="?edit=<?= $r['id'] ?>" class="btn-wow secondary"><i class="fa-solid fa-pen"></i></a>
                    <?php if ($r['id'] !== $_SESSION['user']['id']): ?>
                      <form method="POST" onsubmit="return confirm('¿Borrar usuario?');" style="margin:0;">
                        <input type="hidden" name="delete" value="<?= $r['id'] ?>">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                        <button type="submit" class="btn-wow danger"><i class="fa-solid fa-trash"></i></button>
                      </form>
                    <?php endif; ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>

          <div class="pagination">
            <?php if ($pagina > 1): ?><a href="?page=<?= $pagina - 1 ?>" class="btn-wow secondary">Ant</a><?php endif; ?>
            <span style="padding:5px 10px;">Pág <?= $pagina ?></span>
            <?php if ($pagina < $totalPaginas): ?><a href="?page=<?= $pagina + 1 ?>"
                class="btn-wow secondary">Sig</a><?php endif; ?>
          </div>
        </section>
      </div>
    </div>
  </main>
</body>

</html>