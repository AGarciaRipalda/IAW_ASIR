<?php
// /admin/wow_reportes.php
require_once __DIR__ . '/../includes/wow_auth.php';
verificarLogin();
verificarRol('admin');

// Determinar cursor según rol
$rolUsuario = $_SESSION['user']['role'] ?? 'viewer';
$claseCursor = ($rolUsuario === 'tester') ? 'cursor-sword' : 'cursor-gauntlet';

$db = new PDO("sqlite:" . __DIR__ . "/../database/wow.sqlite");
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Lógica de exportación CSV
if (isset($_GET['export'])) {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=reporte_wow.csv');
    $output = fopen('php://output', 'w');
    fputcsv($output, ['ID', 'Tester', 'Contenido', 'Dificultad', 'Score', 'Horas', 'Comentarios']);

    $rows = $db->query("SELECT s.id, t.name, c.name as content, s.difficulty, s.score, s.time_hours, s.comments 
                        FROM test_session s JOIN tester t ON s.tester=t.id JOIN content c ON s.content=c.id");
    while ($row = $rows->fetch(PDO::FETCH_ASSOC))
        fputcsv($output, $row);
    fclose($output);
    exit;
}

// Estadísticas Avanzadas
// 1. Mejores Testers (Por Score Promedio)
$topScore = $db->query("SELECT t.name, AVG(s.score) as prom, COUNT(*) as total FROM test_session s JOIN tester t ON s.tester=t.id GROUP BY t.name ORDER BY prom DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);

// 2. Contenido más difícil (Menor Score Promedio)
$hardestContent = $db->query("SELECT c.name, AVG(s.score) as prom FROM test_session s JOIN content c ON s.content=c.id GROUP BY c.name ORDER BY prom ASC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Reportes - WoW Test Manager</title>
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
            <a href="wow_reportes.php" class="active"><i class="fa-solid fa-chart-line"></i> Reportes</a>
            <a href="wow_configuracion.php"><i class="fa-solid fa-gears"></i> Configuración</a>
            <a href="wow_perfil.php"><i class="fa-solid fa-user-shield"></i> Mi Perfil</a>
        </nav>
        <div class="sidebar-footer">
            <a href="../auth/logout.php" class="btn-wow danger" style="width:100%;">Cerrar Sesión</a>
        </div>
    </aside>

    <main class="content">
        <h1>Archivos de la Biblioteca</h1>
        <div style="margin-bottom:30px; display: flex; gap: 10px;">
            <a href="?export=1" class="btn-wow primary">
                <i class="fa-solid fa-file-csv"></i> Descargar CSV Completo
            </a>
            <a href="wow_reportes_pdf.php" class="btn-wow secondary">
                <i class="fa-solid fa-file-pdf"></i> Descargar PDF Profesional
            </a>
        </div>
        <div class="page-grid">
            <div class="grid-col-table">
                <section class="panel">
                    <h2>Mejores Testers (Calidad)</h2>
                    <table>
                        <thead>
                            <tr>
                                <th>Tester</th>
                                <th>Score Promedio</th>
                                <th>Sesiones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($topScore as $t): ?>
                                <tr>
                                    <td style="color:#fff; font-weight:bold;"><?= htmlspecialchars($t['name']) ?></td>
                                    <td class="score-high"><?= round($t['prom'], 1) ?></td>
                                    <td><?= $t['total'] ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </section>
            </div>
            <div class="grid-col-table">
                <section class="panel">
                    <h2>Contenido Más Difícil</h2>
                    <table>
                        <thead>
                            <tr>
                                <th>Contenido</th>
                                <th>Score Promedio</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($hardestContent as $c): ?>
                                <tr>
                                    <td><?= htmlspecialchars($c['name']) ?></td>
                                    <td class="score-low"><?= round($c['prom'], 1) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </section>
            </div>
        </div>
    </main>
</body>

</html>