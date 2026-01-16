<?php
// public/index.php
require_once __DIR__ . '/../includes/wow_auth.php'; 

// Verificamos si hay sesión activa
$isLoggedIn = isset($_SESSION['user']);
$userName = $_SESSION['user']['username'] ?? 'Viajero';
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($APP_CONFIG['language'] ?? 'es') ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido - WoW Test Manager</title>
    <link rel="icon" href="../assets/favicon.png" type="image/png">
    
    <link rel="stylesheet" href="../assets/wow_style.css">

    <style>
        /* === ESTILOS ESPECÍFICOS PARA PORTADA (INDEX) === */
        
        body.login-body {
            margin: 0 !important; padding: 0 !important;
            height: 100vh !important; overflow: hidden !important;
            background: #000 !important;
            position: relative;
        }

        /* Video de fondo */
        #bg-video { 
            position: fixed; top: 50%; left: 50%;
            min-width: 100%; min-height: 100%;
            transform: translate(-50%, -50%); z-index: -1;
            filter: brightness(1.15) saturate(1.25);
        }
        
        /* Capa contenedora principal */
        .login-overlay { 
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0, 0, 0, 0.2);
            z-index: 1;
        }

        .wow-logo-corner {
            position: absolute; top: 30px; left: 30px;
            max-width: 280px; z-index: 10;
            filter: drop-shadow(0 0 10px rgba(0,0,0,0.8));
        }

        /* === POSICIÓN DEL CONTENIDO CENTRAL === */
        .welcome-content { 
            position: absolute;
            left: 50%;
            top: 60%; /* Ajustado para que el título y botón queden en el hueco del portal */
            transform: translate(-50%, -50%);
            
            text-align: center; color: #fff; padding: 20px; 
            background: none !important; 
            display: flex; flex-direction: column;
            align-items: center; justify-content: center;
            width: 100%;
        }

        /* Título chulo (Imagen) */
        .title-image {
            max-width: 90%; width: 550px; height: auto;
            margin: 0; 
            filter: drop-shadow(0 5px 10px rgba(0,0,0,0.8));
        }
        
        .welcome-text { 
            font-family: 'Friz Quadrata UI', serif; color: #fcdba8; font-size: 1.3rem; 
            margin-bottom: 5px; min-height: 0;
            line-height: 1.5; text-shadow: 1px 1px 2px #000;
        }

        /* Botón */
        .btn-welcome {
            background-image: url('../assets/btn_normal.png');
            background-size: 100% 100%; background-color: transparent; border: none;
            font-family: 'Friz Quadrata UI', serif; color: #FFD100 !important;
            text-shadow: 1px 1px 0 #000; text-decoration: none; text-transform: uppercase;
            width: 150px; height: 38px; font-size: 0.9rem;
            display: inline-flex; justify-content: center; align-items: center;
            transition: all 0.2s; filter: drop-shadow(0 4px 4px rgba(0,0,0,0.5));
            margin-top: 0px; 
        }
        .btn-welcome:hover {
            color: #fff !important;
            filter: brightness(1.2) drop-shadow(0 0 8px rgba(255, 209, 0, 0.6));
            transform: translateY(-2px);
        }

        /* === FOOTER PEGADO AL FONDO === */
        .login-footer {
            position: absolute; 
            bottom: 5px; /* LO HEMOS BAJADO AL MÁXIMO (antes 20px) */
            width: 100%; 
            text-align: center;
            z-index: 10;
        }
        
        .login-footer p {
             font-family: 'Friz Quadrata UI', serif; font-size: 0.9rem;
             text-transform: uppercase; font-weight: bold; letter-spacing: 1.5px;
             margin: 0; padding-bottom: 5px; /* Ajuste fino */
             
             /* Estilo Oro Metálico */
             background: linear-gradient(to bottom, #ffffff 0%, #fff0a5 25%, #ffd700 50%, #b8860b 100%);
             -webkit-background-clip: text; -webkit-text-fill-color: transparent; color: #FFD100;
             filter: drop-shadow(0 2px 0px rgba(0,0,0,1)) drop-shadow(0 0 5px rgba(255, 215, 0, 0.3)); opacity: 0.8;
        }

        /* === BOTÓN MÚSICA (Fuente Original) === */
        .music-control {
            position: fixed; top: 30px; right: 30px;
            background: rgba(0, 0, 0, 0.6); 
            border: 2px solid #6b5a3d; 
            color: #f8b700; 
            padding: 8px 15px; 
            border-radius: 4px; 
            cursor: pointer;
            
            /* AQUI LA FUENTE ORIGINAL */
            font-family: 'Morpheus RPG', serif !important;
            font-size: 1rem;
            letter-spacing: 1px;
            text-shadow: 1px 1px 0 #000;
            
            z-index: 1000; transition: all 0.3s;
            display: flex; align-items: center; gap: 8px;
        }
        .music-control:hover { 
            background: rgba(0, 0, 0, 0.9); 
            border-color: #ffd700; 
            transform: scale(1.05); 
            color: #fff;
        }
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
        
        <img src="../assets/wow_logo.png" alt="WoW Logo" class="wow-logo-corner">
            
        <div class="welcome-content">
            
            <img src="../assets/test_manager_title.png" alt="Test Manager" class="title-image">
            
            <div class="welcome-text">
                <?php if ($isLoggedIn): ?>
                    <p>Saludos, <strong><?= htmlspecialchars($userName) ?></strong>.</p>
                    <p>Los reinos están listos para tu inspección.</p>
                <?php endif; ?>
            </div>

            <div class="login-actions" style="display: flex; justify-content: center; gap: 20px; width: 100%;">
                <?php if ($isLoggedIn): ?>
                    <a href="../admin/wow_dashboard.php" class="btn-welcome">Ir al Dashboard</a>
                <?php else: ?>
                    <a href="../auth/wow_login.php" class="btn-welcome">Iniciar Sesión</a>
                <?php endif; ?>
            </div>
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
                }).catch(error => { console.log("Audio bloqueado: " + error); alert("El navegador ha bloqueado el audio. Interactúa con la página primero."); });
            }
        }
    </script>
</body>
</html>
