<?php
// /admin/wow_contenido.php
require_once __DIR__ . '/../includes/wow_auth.php';
verificarLogin();
verificarRol('tester');

// Determinar cursor según rol
$rolUsuario = $_SESSION['user']['role'] ?? 'viewer';
$claseCursor = ($rolUsuario === 'tester') ? 'cursor-sword' : 'cursor-gauntlet';

$db = new PDO("sqlite:" . __DIR__ . "/../database/wow.sqlite");
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$TIPOS = ['Dungeon', 'Raid', 'Quest', 'Scenario', 'Other'];
$mensaje = '';
$error = '';
$pagina = (int) ($_GET['page'] ?? 1);
$porPagina = (int) ($APP_CONFIG['items_per_page'] ?? 10);

$editMode = false;
$item = ['id' => 0, 'name' => '', 'type' => 'Dungeon', 'patch' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  validarCSRF($_POST['csrf_token'] ?? '');

  if (isset($_POST['delete'])) {
    $stmt = $db->prepare("DELETE FROM content WHERE id = ?");
    $stmt->execute([(int) $_POST['delete']]);
    $mensaje = "Contenido eliminado.";
  } else {
    $id = (int) $_POST['id'];
    $name = trim($_POST['name']);
    $type = $_POST['type'];
    $patch = trim($_POST['patch']);

    if ($id > 0) {
      $stmt = $db->prepare("UPDATE content SET name=?, type=?, patch=? WHERE id=?");
      $stmt->execute([$name, $type, $patch, $id]);
      $mensaje = "Contenido actualizado.";
    } else {
      $stmt = $db->prepare("INSERT INTO content (name, type, patch) VALUES (?, ?, ?)");
      $stmt->execute([$name, $type, $patch]);
      $mensaje = "Contenido creado.";
    }
  }
}

if (isset($_GET['edit'])) {
  $stmt = $db->prepare("SELECT * FROM content WHERE id = ?");
  $stmt->execute([(int) $_GET['edit']]);
  $found = $stmt->fetch(PDO::FETCH_ASSOC);
  if ($found) {
    $item = $found;
    $editMode = true;
  }
}

// Búsqueda
$search = trim($_GET['search'] ?? '');
$searchSql = "";
$params = [];
if ($search) {
  $searchSql = " WHERE name LIKE ? OR type LIKE ? OR patch LIKE ?";
  $params = ["%$search%", "%$search%", "%$search%"];
}

// Paginación
$stmtCount = $db->prepare("SELECT COUNT(*) FROM content $searchSql");
$stmtCount->execute($params);
$totalRegistros = $stmtCount->fetchColumn();
$totalPaginas = ceil($totalRegistros / $porPagina);
if ($totalPaginas < 1)
  $totalPaginas = 1;
if ($pagina > $totalPaginas)
  $pagina = $totalPaginas;
$offset = ($pagina - 1) * $porPagina;

$stmt = $db->prepare("SELECT * FROM content $searchSql ORDER BY id DESC LIMIT $porPagina OFFSET $offset");
$stmt->execute($params);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <title>Contenido - WoW Test Manager</title>
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
      <a href="wow_contenido.php" class="active"><i class="fa-solid fa-book-journal-whills"></i> Contenido</a>
      <a href="wow_testers.php"><i class="fa-solid fa-helmet-safety"></i> Testers</a>
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
    <h1>Códice de Mazmorras</h1>
    <?php if ($mensaje): ?>
      <div class="status success"><?= $mensaje ?></div><?php endif; ?>
    <div class="page-grid">
      <div class="grid-col-form">
        <section class="panel">
          <h2><?= $editMode ? 'Editar' : 'Añadir Contenido' ?></h2>
          <form method="POST">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
            <input type="hidden" name="id" value="<?= $item['id'] ?>">
            <label>Nombre</label>
            <input type="text" name="name" value="<?= htmlspecialchars($item['name']) ?>" required
              style="margin-bottom:15px;">
            <label>Tipo</label>
            <select name="type" style="margin-bottom:15px;">
              <?php foreach ($TIPOS as $t): ?>
                <option value="<?= $t ?>" <?= $item['type'] == $t ? 'selected' : '' ?>><?= $t ?></option>
              <?php endforeach; ?>
            </select>
            <label>Parche/Expansión</label>
            <input type="text" name="patch" value="<?= htmlspecialchars($item['patch']) ?>" style="margin-bottom:15px;">
            <div class="form-button-group">
              <button type="submit" class="btn-wow primary"><?= $editMode ? 'Guardar' : 'Añadir' ?></button>
              <?php if ($editMode): ?>
                <a href="wow_contenido.php" class="btn-wow secondary">Cancelar</a>
              <?php endif; ?>
            </div>
          </form>
        </section>
      </div>
      <div class="grid-col-table">
        <section class="panel">
          <div style="display:flex; justify-content:space-between; margin-bottom:15px;">
            <h2>Listado</h2>
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
                <th>Tipo</th>
                <th>Parche</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($rows as $r): ?>
                <tr>
                  <td><?= $r['id'] ?></td>
                  <td><?= htmlspecialchars($r['name']) ?></td>
                  <td><?= htmlspecialchars($r['type']) ?></td>
                  <td><?= htmlspecialchars($r['patch']) ?></td>
                  <td style="display:flex; gap:5px; justify-content:center;">
                    <a href="?edit=<?= $r['id'] ?>" class="btn-wow secondary"><i class="fa-solid fa-pen"></i></a>
                    <form method="POST" onsubmit="return confirm('¿Borrar?');" style="margin:0;">
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