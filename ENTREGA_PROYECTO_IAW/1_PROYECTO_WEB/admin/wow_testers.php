<?php
// /admin/wow_testers.php
require_once __DIR__ . '/../includes/wow_auth.php';
verificarLogin();
verificarRol('tester');

// Determinar cursor según rol
$rolUsuario = $_SESSION['user']['role'] ?? 'viewer';
$claseCursor = ($rolUsuario === 'tester') ? 'cursor-sword' : 'cursor-gauntlet';

$db = new PDO("sqlite:" . __DIR__ . "/../database/wow.sqlite");
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$ROLES = ['Tank', 'Healer', 'DPS'];
$FACCIONES = ['Alliance', 'Horde', 'Neutral'];
$mensaje = '';
$error = '';
$pagina = (int) ($_GET['page'] ?? 1);
$porPagina = (int) ($APP_CONFIG['items_per_page'] ?? 10);

$editMode = false;
$item = ['id' => 0, 'name' => '', 'role' => 'DPS', 'faction' => 'Neutral'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  validarCSRF($_POST['csrf_token'] ?? '');

  if (isset($_POST['delete'])) {
    $stmt = $db->prepare("DELETE FROM tester WHERE id = ?");
    $stmt->execute([(int) $_POST['delete']]);
    $mensaje = "Tester eliminado.";
  } else {
    $id = (int) $_POST['id'];
    $name = trim($_POST['name']);
    $role = $_POST['role'];
    $faction = $_POST['faction'];

    if ($id > 0) {
      $stmt = $db->prepare("UPDATE tester SET name=?, role=?, faction=? WHERE id=?");
      $stmt->execute([$name, $role, $faction, $id]);
      $mensaje = "Tester actualizado.";
    } else {
      $stmt = $db->prepare("INSERT INTO tester (name, role, faction) VALUES (?, ?, ?)");
      $stmt->execute([$name, $role, $faction]);
      $mensaje = "Tester reclutado.";
    }
  }
}

if (isset($_GET['edit'])) {
  $stmt = $db->prepare("SELECT * FROM tester WHERE id = ?");
  $stmt->execute([(int) $_GET['edit']]);
  $found = $stmt->fetch(PDO::FETCH_ASSOC);
  if ($found) {
    $item = $found;
    $editMode = true;
  }
}

$search = trim($_GET['search'] ?? '');
$searchSql = "";
$params = [];
if ($search) {
  $searchSql = " WHERE name LIKE ? OR role LIKE ? OR faction LIKE ?";
  $params = ["%$search%", "%$search%", "%$search%"];
}

$stmtCount = $db->prepare("SELECT COUNT(*) FROM tester $searchSql");
$stmtCount->execute($params);
$totalRegistros = $stmtCount->fetchColumn();
$totalPaginas = ceil($totalRegistros / $porPagina);
if ($pagina > $totalPaginas)
  $pagina = $totalPaginas;
if ($totalPaginas < 1)
  $totalPaginas = 1;
$offset = ($pagina - 1) * $porPagina;

$stmt = $db->prepare("SELECT * FROM tester $searchSql ORDER BY name ASC LIMIT $porPagina OFFSET $offset");
$stmt->execute($params);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <title>Testers - WoW Test Manager</title>
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
      <a href="wow_testers.php" class="active"><i class="fa-solid fa-helmet-safety"></i> Testers</a>
      <?php if ($_SESSION['user']['role'] === 'admin'): ?>
        <a href="wow_blizzard_sync.php"><i class="fa-solid fa-dragon"></i> Blizzard API</a>
        <a href="wow_usuarios.php"><i class="fa-solid fa-users"></i> Usuarios</a>
        <a href="wow_reportes.php"><i class="fa-solid fa-chart-line"></i> Reportes</a>
        <a href="wow_configuracion.php"><i class="fa-solid fa-gears"></i> Configuración</a>
      <?php endif; ?>
      <a href="wow_perfil.php"><i class="fa-solid fa-user-shield"></i> Mi Perfil</a>
    </nav>
    <div class="sidebar-footer">
      <a href="../auth/logout.php" class="btn-wow danger" style="width:100%;">Cerrar Sesión</a>
    </div>
  </aside>

  <main class="content">
    <h1>Barracones (Testers)</h1>
    <?php if ($mensaje): ?>
      <div class="status success"><?= $mensaje ?></div><?php endif; ?>

    <div class="page-grid">
      <div class="grid-col-form">
        <section class="panel">
          <h2><?= $editMode ? 'Editar' : 'Nuevo Recluta' ?></h2>
          <form method="POST">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
            <input type="hidden" name="id" value="<?= $item['id'] ?>">

            <label>Nombre</label>
            <input type="text" name="name" value="<?= htmlspecialchars($item['name']) ?>" required
              style="margin-bottom:15px;">

            <label>Rol</label>
            <select name="role" style="margin-bottom:15px;">
              <?php foreach ($ROLES as $r): ?>
                <option value="<?= $r ?>" <?= $item['role'] == $r ? 'selected' : '' ?>><?= $r ?></option>
              <?php endforeach; ?>
            </select>

            <label>Facción</label>
            <select name="faction" style="margin-bottom:15px;">
              <?php foreach ($FACCIONES as $f): ?>
                <option value="<?= $f ?>" <?= $item['faction'] == $f ? 'selected' : '' ?>><?= $f ?></option>
              <?php endforeach; ?>
            </select>

            <div class="form-button-group">
              <button type="submit" class="btn-wow primary"><?= $editMode ? 'Guardar' : 'Añadir' ?></button>
              <?php if ($editMode): ?>
                <a href="wow_testers.php" class="btn-wow secondary">Cancelar</a>
              <?php endif; ?>
            </div>
          </form>
        </section>
      </div>

      <div class="grid-col-table">
        <section class="panel">
          <div style="display:flex; justify-content:space-between; margin-bottom:15px;">
            <h2>Roster</h2>
            <form method="GET" style="display:flex; gap:10px;">
              <input type="text" name="search" placeholder="Buscar..." value="<?= htmlspecialchars($search) ?>">
              <button type="submit" class="btn-wow secondary"><i class="fa-solid fa-magnifying-glass"></i></button>
            </form>
          </div>

          <table>
            <thead>
              <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Rol</th>
                <th>Facción</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($rows as $r): ?>
                <tr>
                  <td><?= $r['id'] ?></td>
                  <td style="font-weight:bold; color:#fff;"><?= htmlspecialchars($r['name']) ?></td>
                  <td><?= htmlspecialchars($r['role']) ?></td>
                  <td><?= htmlspecialchars($r['faction']) ?></td>
                  <td style="display:flex; gap:5px; justify-content:center;">
                    <a href="?edit=<?= $r['id'] ?>" class="btn-wow secondary"><i class="fa-solid fa-pen"></i></a>
                    <form method="POST" onsubmit="return confirm('¿Expulsar tester?');" style="margin:0;">
                      <input type="hidden" name="delete" value="<?= $r['id'] ?>">
                      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                      <button type="submit" class="btn-wow danger"><i class="fa-solid fa-trash"></i></button>
                    </form>
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