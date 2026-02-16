<?php
/**
 * Sincronización con Blizzard API
 * 
 * Interfaz administrativa para vincular testers con personajes de WoW
 * y sincronizar sus datos desde la Blizzard Battle.net API
 */

require_once __DIR__ . '/../includes/wow_auth.php';
verificarLogin();
verificarRol('admin');

require_once __DIR__ . '/../includes/blizzard_config.php';
require_once __DIR__ . '/../includes/blizzard_api.php';

// Determinar cursor según rol
$rolUsuario = $_SESSION['user']['role'] ?? 'viewer';
$claseCursor = ($rolUsuario === 'tester') ? 'cursor-sword' : 'cursor-gauntlet';

$db = new PDO("sqlite:" . __DIR__ . "/../database/wow.sqlite");
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$mensaje = '';
$error = '';

// Verificar si las credenciales están configuradas
$credencialesConfiguradas = (
    defined('BLIZZARD_CLIENT_ID') &&
    BLIZZARD_CLIENT_ID !== 'tu_client_id_aqui' &&
    defined('BLIZZARD_CLIENT_SECRET') &&
    BLIZZARD_CLIENT_SECRET !== 'tu_client_secret_aqui'
);

// Añadir columnas a la tabla tester si no existen
try {
    $db->exec("ALTER TABLE tester ADD COLUMN wow_realm TEXT");
} catch (Exception $e) {
    // Columna ya existe
}
try {
    $db->exec("ALTER TABLE tester ADD COLUMN wow_character TEXT");
} catch (Exception $e) {
    // Columna ya existe
}
try {
    $db->exec("ALTER TABLE tester ADD COLUMN wow_level INTEGER DEFAULT 0");
} catch (Exception $e) {
    // Columna ya existe
}
try {
    $db->exec("ALTER TABLE tester ADD COLUMN wow_class TEXT");
} catch (Exception $e) {
    // Columna ya existe
}
try {
    $db->exec("ALTER TABLE tester ADD COLUMN wow_ilvl INTEGER DEFAULT 0");
} catch (Exception $e) {
    // Columna ya existe
}
try {
    $db->exec("ALTER TABLE tester ADD COLUMN wow_last_sync INTEGER DEFAULT 0");
} catch (Exception $e) {
    // Columna ya existe
}

// Procesar formulario de vinculación
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    verificarPermisoEscritura('admin', 'blizzard_sync');

    if ($_POST['action'] === 'vincular') {
        $testerId = (int) $_POST['tester_id'];
        $realm = sanitizarTexto($_POST['realm']);
        $character = sanitizarTexto($_POST['character']);

        if ($testerId > 0 && !empty($realm) && !empty($character)) {
            $stmt = $db->prepare("UPDATE tester SET wow_realm = ?, wow_character = ? WHERE id = ?");
            if ($stmt->execute([$realm, $character, $testerId])) {
                $mensaje = "Personaje vinculado correctamente. Ahora puedes sincronizar los datos.";
            } else {
                $error = "Error al vincular el personaje.";
            }
        } else {
            $error = "Datos incompletos.";
        }
    }

    if ($_POST['action'] === 'sincronizar' && $credencialesConfiguradas) {
        $testerId = (int) $_POST['tester_id'];

        $stmt = $db->prepare("SELECT wow_realm, wow_character FROM tester WHERE id = ?");
        $stmt->execute([$testerId]);
        $tester = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($tester && !empty($tester['wow_realm']) && !empty($tester['wow_character'])) {
            $api = new BlizzardAPI(BLIZZARD_CLIENT_ID, BLIZZARD_CLIENT_SECRET);

            $profile = $api->getCharacterProfile($tester['wow_realm'], $tester['wow_character']);

            if ($profile) {
                $data = BlizzardAPI::extractProfileData($profile);

                $stmt = $db->prepare("
                    UPDATE tester 
                    SET wow_level = ?, wow_class = ?, wow_ilvl = ?, wow_last_sync = ?
                    WHERE id = ?
                ");

                $stmt->execute([
                    $data['level'],
                    $data['class'],
                    $data['equipped_item_level'],
                    time(),
                    $testerId
                ]);

                $mensaje = "Datos sincronizados correctamente desde Blizzard API.";
            } else {
                $error = "No se pudo obtener datos del personaje. Verifica el realm y nombre.";
            }
        } else {
            $error = "Primero debes vincular un personaje a este tester.";
        }
    }
}

// Obtener lista de testers
$testers = $db->query("SELECT * FROM tester ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Sincronización Blizzard - WoW Test Manager</title>
    <link rel="icon" href="../assets/favicon.png" type="image/png">
    <link rel="stylesheet" href="../assets/wow_style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        .sync-card {
            background: linear-gradient(to bottom, #2a2a2a, #1a1a1a);
            border: 1px solid #444;
            border-radius: 4px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .sync-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }

        .sync-info-item {
            background: rgba(0, 0, 0, 0.3);
            padding: 10px;
            border-radius: 3px;
            border-left: 3px solid #ffd100;
        }

        .sync-info-item label {
            display: block;
            font-size: 0.8rem;
            color: #888;
            margin-bottom: 5px;
        }

        .sync-info-item .value {
            font-size: 1.1rem;
            color: #ffd100;
            font-weight: bold;
        }

        .warning-box {
            background: rgba(255, 140, 0, 0.1);
            border: 1px solid #ff8c00;
            border-radius: 4px;
            padding: 15px;
            margin-bottom: 20px;
        }
    </style>
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
            <a href="wow_blizzard_sync.php" class="active"><i class="fa-brands fa-battle-net"></i> Blizzard API</a>
            <a href="wow_configuracion.php"><i class="fa-solid fa-gears"></i> Configuración</a>
            <a href="wow_perfil.php"><i class="fa-solid fa-user-shield"></i> Mi Perfil</a>
        </nav>
        <div class="sidebar-footer">
            <a href="../auth/logout.php" class="btn-wow danger" style="width:100%;">Cerrar Sesión</a>
        </div>
    </aside>

    <main class="content">
        <h1><i class="fa-brands fa-battle-net"></i> Sincronización con Blizzard API</h1>
        <p style="color: #888;">Vincula testers con personajes de WoW y sincroniza sus datos automáticamente.</p>

        <?php if (!$credencialesConfiguradas): ?>
            <div class="warning-box">
                <h3 style="color: #ff8c00; margin-top: 0;"><i class="fa-solid fa-triangle-exclamation"></i> Credenciales No
                    Configuradas</h3>
                <p>Para usar la integración con Blizzard API, debes configurar tus credenciales:</p>
                <ol>
                    <li>Regístrate en <a href="https://develop.battle.net/" target="_blank" style="color: #ffd100;">Blizzard
                            Developer Portal</a></li>
                    <li>Crea una nueva aplicación</li>
                    <li>Copia el Client ID y Client Secret</li>
                    <li>Edita el archivo <code>includes/blizzard_config.php</code> y pega tus credenciales</li>
                </ol>
            </div>
        <?php endif; ?>

        <?php if ($mensaje): ?>
            <div class="status success" style="margin-bottom: 20px;">
                <i class="fa-solid fa-check-circle"></i>
                <?= htmlspecialchars($mensaje) ?>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="status err" style="margin-bottom: 20px;">
                <i class="fa-solid fa-exclamation-triangle"></i>
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <?php foreach ($testers as $tester): ?>
            <div class="sync-card">
                <h2 style="margin-top: 0; color: #ffd100;">
                    <i class="fa-solid fa-user"></i>
                    <?= htmlspecialchars($tester['name']) ?>
                </h2>

                <form method="POST" style="display: flex; gap: 10px; align-items: end; margin-bottom: 15px;">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                    <input type="hidden" name="action" value="vincular">
                    <input type="hidden" name="tester_id" value="<?= $tester['id'] ?>">

                    <div style="flex: 1;">
                        <label>Realm (ej: Ragnaros)</label>
                        <input type="text" name="realm" value="<?= htmlspecialchars($tester['wow_realm'] ?? '') ?>"
                            placeholder="Nombre del reino" required>
                    </div>

                    <div style="flex: 1;">
                        <label>Personaje</label>
                        <input type="text" name="character" value="<?= htmlspecialchars($tester['wow_character'] ?? '') ?>"
                            placeholder="Nombre del personaje" required>
                    </div>

                    <button type="submit" class="btn-wow primary">
                        <i class="fa-solid fa-link"></i> Vincular
                    </button>
                </form>

                <?php if (!empty($tester['wow_realm']) && !empty($tester['wow_character'])): ?>
                    <div class="sync-info">
                        <div class="sync-info-item">
                            <label>Personaje Vinculado</label>
                            <div class="value">
                                <?= htmlspecialchars($tester['wow_character']) ?>
                            </div>
                        </div>
                        <div class="sync-info-item">
                            <label>Realm</label>
                            <div class="value">
                                <?= htmlspecialchars($tester['wow_realm']) ?>
                            </div>
                        </div>
                        <div class="sync-info-item">
                            <label>Nivel</label>
                            <div class="value">
                                <?= $tester['wow_level'] ?: 'No sincronizado' ?>
                            </div>
                        </div>
                        <div class="sync-info-item">
                            <label>Clase</label>
                            <div class="value">
                                <?= htmlspecialchars($tester['wow_class'] ?: 'No sincronizado') ?>
                            </div>
                        </div>
                        <div class="sync-info-item">
                            <label>Item Level</label>
                            <div class="value">
                                <?= $tester['wow_ilvl'] ?: 'No sincronizado' ?>
                            </div>
                        </div>
                        <div class="sync-info-item">
                            <label>Última Sincronización</label>
                            <div class="value" style="font-size: 0.9rem;">
                                <?= $tester['wow_last_sync'] ? date('d/m/Y H:i', $tester['wow_last_sync']) : 'Nunca' ?>
                            </div>
                        </div>
                    </div>

                    <form method="POST" style="margin-top: 15px;">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                        <input type="hidden" name="action" value="sincronizar">
                        <input type="hidden" name="tester_id" value="<?= $tester['id'] ?>">
                        <button type="submit" class="btn-wow secondary" <?= !$credencialesConfiguradas ? 'disabled' : '' ?>>
                            <i class="fa-solid fa-sync"></i> Sincronizar Datos desde Blizzard
                        </button>
                    </form>
                <?php else: ?>
                    <p style="color: #888; font-style: italic;">
                        <i class="fa-solid fa-info-circle"></i> Vincula un personaje para poder sincronizar datos.
                    </p>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>

    </main>
</body>

</html>