<?php
// /admin/wow_sesiones.php
require_once __DIR__ . '/../includes/wow_auth.php'; 
verificarLogin();
verificarRol('tester');

// Determinar cursor según rol
$rolUsuario = $_SESSION['user']['role'] ?? 'viewer';
$claseCursor = ($rolUsuario === 'tester') ? 'cursor-sword' : 'cursor-gauntlet';

$db = new PDO("sqlite:" . __DIR__ . "/../database/wow.sqlite");
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$DIFICULTADES = ['Normal','Heroic','Mythic'];
$mensaje = '';
$error = '';
$pagina = (int)($_GET['page'] ?? 1);
$porPagina = (int)($APP_CONFIG['items_per_page'] ?? 10);

$editMode = false;
$item = [ 
    'id' => 0, 
    'tester' => 0, 
    'content' => 0, 
    'time_hours' => 0, 
    'time_minutes' => 0, 
    'score' => 50, 
    'difficulty' => 'Normal', 
    'comments' => '' 
];

// --- LOGICA POST (Crear/Editar/Eliminar) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validarCSRF($_POST['csrf_token'] ?? '');

    if (isset($_POST['delete'])) {
        // Eliminar
        $stmt = $db->prepare("DELETE FROM test_session WHERE id = ?");
        $stmt->execute([(int)$_POST['delete']]);
        $mensaje = "Sesión eliminada correctamente.";
    } else {
        // Guardar / Editar
        $id = (int)($_POST['id'] ?? 0);
        $tester = (int)$_POST['tester'];
        $content = (int)$_POST['content'];
        $diff = $_POST['difficulty'];
        $score = (int)$_POST['score'];
        $hours = (int)$_POST['time_hours'];
        $minutes = (int)$_POST['time_minutes'];
        $comments = trim($_POST['comments']);

        if ($id > 0) {
            $stmt = $db->prepare("UPDATE test_session SET tester=?, content=?, difficulty=?, score=?, time_hours=?, time_minutes=?, comments=? WHERE id=?");
            $stmt->execute([$tester, $content, $diff, $score, $hours, $minutes, $comments, $id]);
            $mensaje = "Sesión actualizada.";
        } else {
            $stmt = $db->prepare("INSERT INTO test_session (tester, content, difficulty, score, time_hours, time_minutes, comments) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$tester, $content, $diff, $score, $hours, $minutes, $comments]);
            $mensaje = "Nueva sesión registrada.";
        }
    }
}

// --- MODO EDICIÓN (Cargar datos si se pide) ---
if (isset($_GET['edit'])) {
    $stmt = $db->prepare("SELECT * FROM test_session WHERE id = ?");
    $stmt->execute([(int)$_GET['edit']]);
    $found = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($found) {
        $item = $found;
        $editMode = true;
    }
}

// --- FILTROS Y BÚSQUEDA ---
$search = trim($_GET['search'] ?? '');
$params = [];
$searchSqlWhere = "";
if (!empty($search)) {
    $searchSqlWhere = " WHERE (t.name LIKE ? OR c.name LIKE ? OR s.difficulty LIKE ? OR s.comments LIKE ?)";
    $params = ["%$search%", "%$search%", "%$search%", "%$search%"];
}

// --- PAGINACIÓN ---
$sqlCount = "SELECT COUNT(*) FROM test_session s JOIN tester t ON s.tester=t.id JOIN content c ON s.content=c.id $searchSqlWhere";
$stmtCount = $db->prepare($sqlCount);
$stmtCount->execute($params);
$totalRegistros = $stmtCount->fetchColumn();
$totalPaginas = ceil($totalRegistros / $porPagina);
if ($totalPaginas < 1) $totalPaginas = 1;
if ($pagina > $totalPaginas) $pagina = $totalPaginas;
$offset = ($pagina - 1) * $porPagina;

// --- CONSULTA PRINCIPAL ---
$sql = "SELECT s.*, t.name as tester_name, c.name as content_name 
        FROM test_session s 
        JOIN tester t ON s.tester=t.id 
        JOIN content c ON s.content=c.id 
        $searchSqlWhere 
        ORDER BY s.id DESC LIMIT $porPagina OFFSET $offset";
$stmt = $db->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Datos para selectores
$testers = $db->query("SELECT * FROM tester ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
$contents = $db->query("SELECT * FROM content ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Sesiones - WoW Test Manager</title>
  <link rel="icon" href="../assets/favicon.png" type="image/png">
  <link rel="stylesheet" href="../assets/wow_style.css">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body class="<?= $claseCursor ?>">
  <aside class="sidebar">
    <div class="sidebar-logo-top"><img src="../assets/wow_logo.png" alt="WoW"></div>
    <nav class="sidebar-nav">
      <a href="wow_dashboard.php"><i class="fa-solid fa-gauge-high"></i> Dashboard</a>
      <a href="wow_sesiones.php" class="active"><i class="fa-solid fa-scroll"></i> Sesiones</a>
      <a href="wow_contenido.php"><i class="fa-solid fa-book-journal-whills"></i> Contenido</a>
      <a href="wow_testers.php"><i class="fa-solid fa-helmet-safety"></i> Testers</a>
      <?php if($_SESSION['user']['role'] === 'admin'): ?>
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
    <h1>Diario de Misiones (Sesiones)</h1>

    <?php if($mensaje): ?><div class="status success"><?= htmlspecialchars($mensaje) ?></div><?php endif; ?>
    <?php if($error): ?><div class="status err"><?= htmlspecialchars($error) ?></div><?php endif; ?>

    <div class="page-grid">
      <div class="grid-col-form">
        <section class="panel">
          <h2><?= $editMode ? 'Editar Registro' : 'Nueva Sesión' ?></h2>
          <form method="POST">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
            <input type="hidden" name="id" value="<?= $item['id'] ?>">
            
            <div style="margin-bottom:15px;">
              <label>Tester</label>
              <select name="tester" required>
                <?php foreach($testers as $t): ?>
                  <option value="<?= $t['id'] ?>" <?= $item['tester']==$t['id']?'selected':'' ?>>
                    <?= htmlspecialchars($t['name']) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>

            <div style="margin-bottom:15px;">
              <label>Contenido</label>
              <select name="content" required>
                <?php foreach($contents as $c): ?>
                  <option value="<?= $c['id'] ?>" <?= $item['content']==$c['id']?'selected':'' ?>>
                    <?= htmlspecialchars($c['name']) ?> (<?= $c['type'] ?>)
                  </option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="form-grid-sesiones" style="margin-bottom:15px;">
               <div>
                 <label>Dificultad</label>
                 <select name="difficulty">
                   <?php foreach($DIFICULTADES as $d): ?>
                     <option value="<?= $d ?>" <?= $item['difficulty']==$d?'selected':'' ?>><?= $d ?></option>
                   <?php endforeach; ?>
                 </select>
               </div>
               <div>
                 <label>Puntuación (0-100)</label>
                 <input type="number" name="score" min="0" max="100" value="<?= $item['score'] ?>" required>
               </div>
            </div>

            <div class="form-grid-sesiones" style="margin-bottom:15px;">
               <div>
                 <label>Horas</label>
                 <input type="number" name="time_hours" min="0" value="<?= $item['time_hours'] ?>">
               </div>
               <div>
                 <label>Minutos</label>
                 <input type="number" name="time_minutes" min="0" max="59" value="<?= $item['time_minutes'] ?>">
               </div>
            </div>

            <div style="margin-bottom:15px;">
                <label>Comentarios</label>
                <textarea name="comments" rows="3"><?= htmlspecialchars($item['comments']) ?></textarea>
            </div>

            <div class="form-button-group">
                <button type="submit" class="btn-wow primary"><?= $editMode ? 'Actualizar' : 'Registrar' ?></button>
                <?php if($editMode): ?>
                    <a href="wow_sesiones.php" class="btn-wow secondary">Cancelar</a>
                <?php endif; ?>
            </div>
          </form>
        </section>
      </div>

      <div class="grid-col-table">
        <section class="panel" style="overflow-x:auto;">
           <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:15px;">
              <h2>Registros</h2>
              <form method="GET" style="display:flex; gap:10px;">
                  <input type="text" name="search" placeholder="Buscar..." value="<?= htmlspecialchars($search) ?>" style="width:200px;">
                  <button type="submit" class="btn-wow secondary"><i class="fa-solid fa-magnifying-glass"></i></button>
              </form>
           </div>
           
           <table>
              <thead>
                  <tr>
                      <th>ID</th>
                      <th>Tester</th>
                      <th>Contenido</th>
                      <th>Dif.</th>
                      <th>Score</th>
                      <th>Acciones</th>
                  </tr>
              </thead>
              <tbody>
                  <?php foreach ($rows as $r): ?>
                  <tr>
                      <td><?= $r['id'] ?></td>
                      <td><?= htmlspecialchars($r['tester_name']) ?></td>
                      <td><?= htmlspecialchars($r['content_name']) ?></td>
                      <td><?= htmlspecialchars($r['difficulty']) ?></td>
                      <td class="<?= $r['score']>=80?'score-high':($r['score']>=50?'score-mid':'score-low') ?>">
                          <?= $r['score'] ?>
                      </td>
                      <td style="display:flex; gap:5px; justify-content:center;">
                          <a href="?edit=<?= $r['id'] ?>" class="btn-wow secondary" style="padding:4px 8px; font-size:0.8rem;">
                              <i class="fa-solid fa-pen"></i>
                          </a>
                          <form method="POST" onsubmit="return confirm('¿Borrar sesión?');" style="margin:0;">
                              <input type="hidden" name="delete" value="<?= $r['id'] ?>">
                              <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                              <button type="submit" class="btn-wow danger" style="padding:4px 8px; font-size:0.8rem;">
                                  <i class="fa-solid fa-trash"></i>
                              </button>
                          </form>
                      </td>
                  </tr>
                  <?php endforeach; ?>
              </tbody>
           </table>
           
           <div class="pagination">
              <?php if($pagina > 1): ?><a href="?page=<?= $pagina-1 ?>&search=<?= $search ?>" class="btn-wow secondary">Ant</a><?php endif; ?>
              <span style="padding:5px 10px; color:#aaa;">Pág <?= $pagina ?> de <?= $totalPaginas ?></span>
              <?php if($pagina < $totalPaginas): ?><a href="?page=<?= $pagina+1 ?>&search=<?= $search ?>" class="btn-wow secondary">Sig</a><?php endif; ?>
           </div>
        </section>
      </div>
    </div>
  </main>
</body>
</html>
