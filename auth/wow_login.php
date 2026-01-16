<?php
// auth/wow_login.php
require_once __DIR__ . '/../includes/wow_auth.php'; 

if (isset($_SESSION['user'])) {
    header("Location: ../admin/wow_dashboard.php");
    exit;
}

$error = '';
$success = '';

// Mensaje de logout
if (isset($_GET['msg']) && $_GET['msg'] === 'logged_out') {
    $success = "Te has desconectado con éxito.";
}

// --- CONEXIÓN BD PARA LOGIN Y SEGURIDAD ---
try {
    $db = new PDO("sqlite:" . __DIR__ . "/../database/wow.sqlite");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // === FIX AUTOMÁTICO: CREAR TABLA SI NO EXISTE ===
    $db->exec("CREATE TABLE IF NOT EXISTS login_attempts (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        ip_address TEXT,
        attempt_time INTEGER
    )");
    // ===============================================

} catch (Exception $e) {
    die("Error crítico del sistema de base de datos.");
}

// --- LÓGICA DEL POST ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validarCSRF($_POST['csrf_token'] ?? '');
    
    $input = trim($_POST['username'] ?? ''); 
    $password = trim($_POST['password'] ?? '');
    $ip = $_SERVER['REMOTE_ADDR']; 

    // 1. VERIFICAR INTENTOS FALLIDOS (Rate Limiting)
    $stmtCheck = $db->prepare("SELECT COUNT(*) FROM login_attempts WHERE ip_address = ? AND attempt_time > ?");
    $stmtCheck->execute([$ip, time() - (15 * 60)]); 
    $intentos = $stmtCheck->fetchColumn();

    if ($intentos >= 5) {
        $error = "Demasiados intentos fallidos. Espera 15 minutos.";
    } elseif ($input === '' || $password === '') {
        $error = "Por favor, ingresa usuario/email y contraseña.";
    } else {
        // 2. INTENTAR LOGIN
        $stmt = $db->prepare("SELECT * FROM usuarios WHERE username = :input OR email = :input LIMIT 1");
        $stmt->execute([':input' => $input]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            // Limpiar intentos fallidos
            $stmtClean = $db->prepare("DELETE FROM login_attempts WHERE ip_address = ?");
            $stmtClean->execute([$ip]);

            session_regenerate_id(true);
            $_SESSION['user'] = [
                'id' => $user['id'],
                'username' => $user['username'],
                'role' => $user['role']
            ];
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            header("Location: ../admin/wow_dashboard.php");
            exit;
        } else {
            $error = "Credenciales incorrectas.";
            // Registrar el fallo
            $stmtLog = $db->prepare("INSERT INTO login_attempts (ip_address, attempt_time) VALUES (?, ?)");
            $stmtLog->execute([$ip, time()]);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($APP_CONFIG['language'] ?? 'es') ?>">
<head>
  <meta charset="UTF-8">
  <title>Iniciar Sesión - WoW Test Manager</title>
  <link rel="icon" href="../assets/favicon.png" type="image/png">
  <link rel="stylesheet" href="../assets/wow_style.css">
  
  <style>
    /* === DEFINICIÓN DE FUENTES (Respaldo) === */
    @font-face { font-family: 'Friz Quadrata UI'; src: url('../assets/frizqt___cyr.ttf') format('truetype'); font-weight: normal; font-style: normal; }
    @font-face { font-family: 'Morpheus RPG'; src: url('../assets/morpheus_cyr.ttf') format('truetype'); font-weight: normal; font-style: normal; }

    /* === FIX LAYOUT === */
    body.login-body {
        margin: 0 !important; padding: 0 !important;
        height: 100vh !important; overflow: hidden !important;
        background: #000 !important;
        position: relative;
    }

    #bg-video {
        position: fixed; top: 50%; left: 50%;
        min-width: 100%; min-height: 100%;
        width: auto; height: auto;
        transform: translate(-50%, -50%);
        z-index: -1;
        filter: brightness(1.15) saturate(1.25);
    }

    .login-overlay {
        position: fixed; top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0, 0, 0, 0.2); 
        z-index: 1;
    }

    /* === CAJA CENTRAL === */
    .wow-login-box {
        position: absolute;
        left: 50%;
        top: 62%; 
        transform: translate(-50%, -50%);
        width: 100%; max-width: 240px; 
        text-align: center;
        z-index: 10;
        background: none !important; border: none !important; box-shadow: none !important;
    }

    .wow-logo-corner {
        position: absolute; top: 30px; left: 30px;
        max-width: 280px; z-index: 10;
        filter: drop-shadow(0 0 10px rgba(0,0,0,0.8));
    }

    .wow-login-box .input-group { margin-bottom: 15px; text-align: center; }
    
    /* === ETIQUETAS: TU ESTILO FAVORITO (Borde Gris Fino) === */
    .wow-login-box label {
        font-family: 'Friz Quadrata UI', serif; 
        font-weight: bold !important;
        font-size: 1.1rem !important;
        letter-spacing: 1px;
        color: #FFD100 !important; 
        
        /* BORDE GRIS FINO */
        -webkit-text-stroke: 0.6px #444 !important; 
        text-shadow: 1px 1px 2px rgba(0,0,0,0.8);
        
        margin-bottom: 5px !important; 
        display: block; text-align: center;
    }

    /* INPUTS */
    .wow-login-box input {
        width: 90% !important; height: 30px !important; margin: 0 auto !important; display: block;
        padding: 4px 8px !important;
        font-family: 'Friz Quadrata UI', sans-serif !important; font-size: 0.95rem !important;
        background: rgba(0, 0, 0, 0.6) !important; border: 1px solid #555 !important;
        box-shadow: inset 0 0 5px #000 !important; color: #fcdba8 !important; 
    }
    .wow-login-box input:focus {
        border-color: #f8b700 !important; background: rgba(0, 0, 0, 0.8) !important;
    }

    /* === BOTÓN ENTRAR (Texto Normal) === */
    .btn-login-hero {
        font-family: 'Friz Quadrata UI', serif !important;
        background: url('../assets/btn_normal.png') no-repeat center center;
        background-size: 100% 100%;
        border: none !important; color: #FFD100 !important;
        text-transform: none !important; /* Respetar Mayúsculas/Minúsculas */
        text-shadow: 1px 1px 0 #000; 
        font-size: 1rem !important; 
        font-weight: bold;
        margin: 25px auto 0 auto !important;
        display: flex !important; justify-content: center; align-items: center;
        width: 140px !important; height: 38px !important; cursor: pointer;
        padding-top: 3px;
    }
    .btn-login-hero:hover { 
        color: #fff !important; filter: brightness(1.2); 
    }

    /* === FOOTER PEGADO AL FONDO === */
    .login-footer {
        position: absolute; bottom: 10px; width: 100%; text-align: center; z-index: 10;
    }
    .login-footer p {
        font-family: 'Friz Quadrata UI', serif; font-size: 0.9rem; 
        text-transform: uppercase; font-weight: bold; letter-spacing: 1.5px;
        margin: 0; padding-bottom: 5px;
        background: linear-gradient(to bottom, #ffffff 0%, #fff0a5 25%, #ffd700 50%, #b8860b 100%);
        -webkit-background-clip: text; -webkit-text-fill-color: transparent; color: #FFD100;
        filter: drop-shadow(0 2px 0px rgba(0,0,0,1)) drop-shadow(0 0 5px rgba(255, 215, 0, 0.3)); 
        opacity: 0.8;
    }

    /* === BOTÓN MÚSICA (Fuente Original) === */
    .music-control {
        position: fixed; top: 30px; right: 30px;
        background: rgba(0, 0, 0, 0.6); border: 2px solid #6b5a3d; color: #f8b700;
        padding: 8px 15px; border-radius: 4px; cursor: pointer;
        font-family: 'Morpheus RPG', serif !important; 
        font-size: 1rem; letter-spacing: 1px; text-shadow: 1px 1px 0 #000;
        z-index: 1000; transition: all 0.3s;
        display: flex; align-items: center; gap: 8px;
    }
    .music-control:hover { background: rgba(0, 0, 0, 0.9); border-color: #ffd700; transform: scale(1.05); color: #fff; }
  </style>
</head>
<body class="login-body cursor-gauntlet">

  <video autoplay muted loop id="bg-video">
      <source src="../assets/login_video.mp4" type="video/mp4">
  </video>

  <audio id="wowAudio" loop>
      <source src="../assets/wow_theme.mp3" type="audio/mpeg">
  </audio>

  <div class="music-control" onclick="toggleMusic()">
      <span id="musicIcon">🔇</span> Activar Audio
  </div>

  <div class="login-overlay">
    <img src="../assets/wow_logo.png" alt="Logo WoW" class="wow-logo-corner">

    <div class="wow-login-box">
      
      <?php if ($success): ?>
        <div class="status success" style="
            margin-bottom: 15px; font-size: 0.8rem; padding: 6px; 
            background: rgba(0, 80, 0, 0.6); border: 1px solid #0a0; 
            color: #afa; font-family: 'Friz Quadrata UI'; text-shadow: 1px 1px 1px #000;">
            <?= htmlspecialchars($success) ?>
        </div>
      <?php endif; ?>

      <?php if ($error): ?>
        <div class="status err" style="
            margin-bottom: 15px; font-size: 0.8rem; padding: 6px; 
            background: rgba(100,0,0,0.6); border: 1px solid #a00; 
            color: #faa; font-family: 'Friz Quadrata UI'; text-shadow: 1px 1px 1px #000;">
            <?= htmlspecialchars($error) ?>
        </div>
      <?php endif; ?>
      
      <form method="POST" autocomplete="off">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
        <div class="input-group login-field">
          <label>Usuario</label>
          <input type="text" name="username" required>
        </div>
        <div class="input-group login-field">
          <label>Contraseña</label>
          <input type="password" name="password" required>
        </div>
        <div class="login-actions">
          <button type="submit" class="btn-login-hero">Entrar</button>
        </div>
      </form>
    </div>
    
    <div class="login-footer">
        <p>WoW Test Manager &copy; <?= date("Y") ?></p>
    </div>
  </div>
  
  <script>
      const audio = document.getElementById('wowAudio');
      const icon = document.getElementById('musicIcon');
      const btnText = document.querySelector('.music-control');
      let isPlaying = false;
      function toggleMusic() {
          if (isPlaying) {
              audio.pause(); icon.innerText = "🔇"; btnText.innerHTML = '<span id="musicIcon">🔇</span> Activar Audio'; isPlaying = false;
          } else {
              audio.volume = 0.4; 
              audio.play().then(() => { icon.innerText = "🔊"; btnText.innerHTML = '<span id="musicIcon">🔊</span> Silenciar'; isPlaying = true;
              }).catch(error => { console.log("Audio bloqueado: " + error); });
          }
      }
  </script>
</body>
</html>
