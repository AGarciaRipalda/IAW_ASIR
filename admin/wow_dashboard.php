<?php
// /admin/wow_dashboard.php
require_once __DIR__ . '/../includes/wow_auth.php'; 
verificarLogin();

// Determinar cursor según rol
$rolUsuario = $_SESSION['user']['role'] ?? 'viewer';
$claseCursor = ($rolUsuario === 'tester') ? 'cursor-sword' : 'cursor-gauntlet';

$db = new PDO("sqlite:" . __DIR__ . "/../database/wow.sqlite");
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Estadísticas
$totalTesters     = (int)$db->query("SELECT COUNT(*) FROM tester")->fetchColumn();
$totalContenido   = (int)$db->query("SELECT COUNT(*) FROM content")->fetchColumn();
$totalSesiones    = (int)$db->query("SELECT COUNT(*) FROM test_session")->fetchColumn();
$promedioScore    = $db->query("SELECT AVG(score) FROM test_session")->fetchColumn();
$promedioScore    = $promedioScore !== null ? round($promedioScore, 2) : 0;

// Últimas sesiones
$sql = "
  SELECT s.id, tester.name AS tester, content.name AS contenido,
         s.difficulty, s.score
  FROM test_session s
  JOIN tester ON s.tester = tester.id
  JOIN content ON s.content = content.id
  ORDER BY s.id DESC LIMIT 5
";
$sesiones = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);

// Datos Gráficos
$stmt1 = $db->query("SELECT content.type AS tipo, AVG(s.score) AS promedio FROM test_session s JOIN content ON s.content = content.id GROUP BY content.type");
$data1 = $stmt1->fetchAll(PDO::FETCH_ASSOC);

$stmt2 = $db->query("SELECT difficulty, COUNT(*) as cantidad FROM test_session GROUP BY difficulty");
$data2 = $stmt2->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Dashboard - WoW Test Manager</title>
  <link rel="icon" href="../assets/favicon.png" type="image/png">
  <link rel="stylesheet" href="../assets/wow_style.css">  
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet"> 
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> 
  <style>
    /* Estilos Dashboard específicos */
    .kpi-container {
        display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 25px; margin-bottom: 40px;
    }
    .kpi-card {
        background: linear-gradient(to bottom, #2a2a2a, #1a1a1a);
        border: 1px solid #444; border-radius: 4px; padding: 25px 20px;
        text-align: center; box-shadow: 0 4px 6px rgba(0,0,0,0.5);
        display: flex; flex-direction: column; justify-content: center;
    }
    .kpi-card h3 { 
        font-family: 'Friz Quadrata UI', serif; font-size: 0.85rem; color: #aaa; 
        margin: 0 0 10px 0; border: none; padding: 0; text-transform: uppercase; letter-spacing: 1px;
    }
    .kpi-card .value {
        font-family: 'Morpheus RPG', serif; font-size: 2.5rem; color: #ffd100; 
        text-shadow: 0 0 10px rgba(255, 209, 0, 0.4); line-height: 1;
    }
    .charts-grid {
        display: grid; grid-template-columns: repeat(auto-fit, minmax(450px, 1fr)); 
        gap: 30px; margin-bottom: 30px;
    }
    .chart-panel {
        position: relative; width: 100%; height: 350px; 
        padding: 15px; box-sizing: border-box; overflow: hidden; 
    }
    @media (max-width: 1200px) { .charts-grid { grid-template-columns: 1fr; } }
  </style>
</head>
<body class="<?= $claseCursor ?>">
  <audio id="dashAudio" loop>
      <source src="../assets/tavern.mp3" type="audio/mpeg">
  </audio>
  <div class="music-control" onclick="toggleDashMusic()">
      <span id="musicIcon">🔇</span> <span id="musicText">Ambiente Taberna</span>
  </div>
  <aside class="sidebar">
    <div class="sidebar-logo-top">
       <img src="../assets/wow_logo.png" alt="WoW">
    </div>
    <nav class="sidebar-nav">
      <a href="wow_dashboard.php" class="active">
          <i class="fa-solid fa-gauge-high"></i> Dashboard
      </a>
      <a href="wow_sesiones.php">
          <i class="fa-solid fa-scroll"></i> Sesiones
      </a>
      <a href="wow_contenido.php">
          <i class="fa-solid fa-book-journal-whills"></i> Contenido
      </a>
      <a href="wow_testers.php">
          <i class="fa-solid fa-helmet-safety"></i> Testers
      </a>
      
      <?php if($_SESSION['user']['role'] === 'admin'): ?>
        <a href="wow_usuarios.php">
            <i class="fa-solid fa-users"></i> Usuarios
        </a>
        <a href="wow_reportes.php">
            <i class="fa-solid fa-chart-line"></i> Reportes
        </a>
        <a href="wow_configuracion.php">
            <i class="fa-solid fa-gears"></i> Configuración
        </a>
      <?php endif; ?>
      
      <a href="wow_perfil.php">
          <i class="fa-solid fa-user-shield"></i> Mi Perfil
      </a>
    </nav>
    
    <div class="sidebar-footer">
      <a href="../auth/logout.php" class="btn-wow danger" style="width:100%;">Cerrar Sesión</a>
    </div>
  </aside>
  <main class="content">
    
    <h1>Command Center</h1>
    <p style="color: #888;">Bienvenido de nuevo, héroe <?= htmlspecialchars($_SESSION['user']['username']) ?>.</p>

    <div class="kpi-container">
        <div class="kpi-card">
            <h3>Testers Activos</h3>
            <div class="value"><?= $totalTesters ?></div>
        </div>
        <div class="kpi-card">
            <h3>Contenidos</h3>
            <div class="value"><?= $totalContenido ?></div>
        </div>
        <div class="kpi-card">
            <h3>Sesiones</h3>
            <div class="value"><?= $totalSesiones ?></div>
        </div>
        <div class="kpi-card">
            <h3>Score Global</h3>
            <div class="value"><?= $promedioScore ?></div>
        </div>
    </div>

    <div class="charts-grid">
        <section class="panel chart-panel">
            <h2>Rendimiento por Tipo</h2>
            <div style="position: relative; height: 260px; width: 100%;">
                <canvas id="chartType"></canvas>
            </div>
        </section>

        <section class="panel chart-panel">
            <h2>Dificultad de Sesiones</h2>
            <div style="position: relative; height: 260px; width: 100%;">
                <canvas id="chartDiff"></canvas>
            </div>
        </section>
    </div>
    <section class="panel">
        <h2>Últimos Reportes de Batalla</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tester</th>
                    <th>Objetivo</th>
                    <th>Dificultad</th>
                    <th>Puntuación</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($sesiones as $s): ?>
                <tr>
                    <td>#<?= $s['id'] ?></td>
                    <td style="color:#fff; font-weight:bold;"><?= htmlspecialchars($s['tester']) ?></td>
                    <td><?= htmlspecialchars($s['contenido']) ?></td>
                    <td><?= htmlspecialchars($s['difficulty']) ?></td>
                    <td>
                        <?php 
                          $color = '#9d9d9d';
                          if($s['score'] >= 80) $color = '#1eff00';
                          elseif($s['score'] >= 50) $color = '#ffd100';
                        ?>
                        <span style="color:<?= $color ?>; font-weight:bold;"><?= $s['score'] ?></span>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <div style="margin-top:15px; text-align:right;">
            <a href="wow_sesiones.php" class="btn-wow secondary">Ver todos los registros</a>
        </div>
    </section>
  </main>
  <script>
    // --- Lógica de la Música ---
    const audio = document.getElementById('dashAudio');
    const icon = document.getElementById('musicIcon');
    const label = document.getElementById('musicText');
    let isPlaying = false;

    function toggleDashMusic() {
        if (isPlaying) {
            audio.pause();
            icon.innerText = "🔇";
            label.innerText = "Ambiente Taberna";
            isPlaying = false;
        } else {
            audio.volume = 0.3;
            audio.play().then(() => {
                icon.innerText = "🍺";
                label.innerText = "Detener Música";
                isPlaying = true;
            }).catch(error => {
                console.log("Audio bloqueado: " + error);
                alert("Interactúa con la página primero para reproducir audio.");
            });
        }
    }

    // --- Lógica de Gráficos ---
    const wowGold = '#ffd100';
    const wowRed  = '#a31414';
    const wowBlue = '#0070dd';
    const wowText = '#fcdba8';

    Chart.defaults.color = wowText;
    Chart.defaults.borderColor = '#333';
    Chart.defaults.font.family = "'Friz Quadrata UI', serif";

    const labels1 = <?= json_encode(array_column($data1, 'tipo')) ?>;
    const values1 = <?= json_encode(array_column($data1, 'promedio')) ?>;

    const labels2 = <?= json_encode(array_column($data2, 'difficulty')) ?>;
    const values2 = <?= json_encode(array_column($data2, 'cantidad')) ?>;

    new Chart(document.getElementById('chartType'), {
      type: 'bar',
      data: {
        labels: labels1,
        datasets: [{
          label: 'Puntuación Promedio',
          data: values1,
          backgroundColor: 'rgba(255, 209, 0, 0.2)',
          borderColor: wowGold,
          borderWidth: 1
        }]
      },
      options: { 
          responsive: true, maintainAspectRatio: false,
          plugins: { legend: { display: false } }
      }
    });

    new Chart(document.getElementById('chartDiff'), {
      type: 'doughnut',
      data: {
        labels: labels2,
        datasets: [{
          data: values2,
          backgroundColor: [wowBlue, wowRed, wowGold, '#9d9d9d', '#ff8000'],
          borderColor: '#181d24', borderWidth: 2
        }]
      },
      options: { 
          responsive: true, maintainAspectRatio: false,
          layout: { padding: 10 },
          plugins: {
              legend: {
                  position: 'right',
                  labels: { boxWidth: 15, font: { size: 11 } }
              }
          }
      }
    });
  </script>
</body>
</html>
