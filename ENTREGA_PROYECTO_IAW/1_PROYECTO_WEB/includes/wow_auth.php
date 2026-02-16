<?php
// includes/wow_auth.php

// 1. MODO DEPURACIÓN (ACTIVADO TEMPORALMENTE)
// Esto nos permitirá ver errores reales si vuelven a ocurrir
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Desactivamos el log de archivo por si hay problemas de permisos de escritura
// ini_set('error_log', __DIR__ . '/../php_error.log'); 

// 2. CONFIGURACIÓN DE SESIÓN (MODO COMPATIBLE)
if (session_status() === PHP_SESSION_NONE) {
    // Detectamos si estamos usando HTTPS de verdad
    $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');

    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'domain' => '',
        'secure' => $isHttps, // Solo true si realmente hay HTTPS activado
        'httponly' => true,
        'samesite' => 'Lax'   // Relajamos a 'Lax' para evitar problemas en localhost
    ]);
    session_start();
}

// 3. GENERAR TOKEN CSRF (Si no existe)
if (empty($_SESSION['csrf_token'])) {
    // Si random_bytes falla (PHP muy antiguo), usamos un fallback
    if (function_exists('random_bytes')) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    } else {
        $_SESSION['csrf_token'] = bin2hex(openssl_random_pseudo_bytes(32));
    }
}

// 4. CONFIGURACIÓN GLOBAL
$APP_CONFIG = [
    'items_per_page' => 10,
    'language' => 'es'
];

try {
    // Ruta absoluta a la base de datos para evitar errores de ruta relativa
    $dbPath = __DIR__ . "/../database/wow.sqlite";
    $dbConfig = new PDO("sqlite:" . $dbPath);
    // Modo de error silencioso aquí para no romper el flujo si la tabla settings no existe
    $dbConfig->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);

    $stmtConfig = $dbConfig->query("SELECT * FROM settings");
    if ($stmtConfig) {
        while ($row = $stmtConfig->fetch(PDO::FETCH_ASSOC)) {
            $APP_CONFIG[$row['key']] = $row['value'];
        }
    }
} catch (Exception $e) {
    // Si falla la config, usamos los valores por defecto, no detenemos la ejecución
}

// --- FUNCIONES DE SEGURIDAD ---

function verificarLogin()
{
    if (!isset($_SESSION['user'])) {
        header("Location: ../auth/wow_login.php");
        exit;
    }
}

function verificarRol($rolRequerido)
{
    $roles = ['viewer' => 1, 'tester' => 2, 'admin' => 3];
    $miRol = $_SESSION['user']['role'] ?? 'viewer';

    if (($roles[$miRol] ?? 0) < ($roles[$rolRequerido] ?? 0)) {
        die("<h1>Acceso Denegado</h1><p>No tienes nivel suficiente.</p><a href='wow_dashboard.php'>Volver</a>");
    }
}

function validarCSRF($token)
{
    if (empty($token) || $token !== ($_SESSION['csrf_token'] ?? '')) {
        // En lugar de morir, si falla, regeneramos y avisamos
        die("Error de seguridad (CSRF). Por favor, recarga la página e intenta de nuevo.");
    }
}

// --- FUNCIONES DE VALIDACIÓN NATIVAS ---

/**
 * Validar email usando filter_var()
 */
function validarEmail($email)
{
    $emailLimpio = filter_var($email, FILTER_SANITIZE_EMAIL);
    return filter_var($emailLimpio, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Validar entero con rangos opcionales
 */
function validarEntero($valor, $min = null, $max = null)
{
    $opciones = ['options' => []];
    if ($min !== null)
        $opciones['options']['min_range'] = $min;
    if ($max !== null)
        $opciones['options']['max_range'] = $max;

    return filter_var($valor, FILTER_VALIDATE_INT, $opciones) !== false;
}

/**
 * Sanitizar texto para prevenir XSS
 */
function sanitizarTexto($texto)
{
    $limpio = trim($texto);
    return htmlspecialchars($limpio, ENT_QUOTES, 'UTF-8');
}

/**
 * Validar URL
 */
function validarURL($url)
{
    return filter_var($url, FILTER_VALIDATE_URL) !== false;
}

// --- SISTEMA DE AUDITORÍA ---

/**
 * Registrar acción en el log de auditoría
 */
function registrarAccionAuditoria($usuarioId, $modulo, $accion, $detalles = '')
{
    try {
        $dbPath = __DIR__ . "/../database/wow.sqlite";
        $db = new PDO("sqlite:" . $dbPath);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $db->prepare(
            "INSERT INTO audit_log (usuario_id, modulo, accion, detalles, ip_address, timestamp) 
             VALUES (?, ?, ?, ?, ?, ?)"
        );

        $stmt->execute([
            $usuarioId,
            $modulo,
            $accion,
            $detalles,
            $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            time()
        ]);

        return true;
    } catch (Exception $e) {
        error_log("Error en auditoría: " . $e->getMessage());
        return false;
    }
}

/**
 * Verificar permiso de escritura (POST) y registrar en auditoría
 */
function verificarPermisoEscritura($moduloRequerido = 'admin', $nombreModulo = '')
{
    // Solo verificar en peticiones POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return;
    }

    // Verificar rol mínimo
    verificarRol($moduloRequerido);

    // Validar token CSRF
    if (!isset($_POST['csrf_token'])) {
        die("Error de seguridad: Token CSRF ausente. Por favor, recarga la página.");
    }
    validarCSRF($_POST['csrf_token']);

    // Registrar acción en log de auditoría
    if (!empty($nombreModulo)) {
        registrarAccionAuditoria(
            $_SESSION['user']['id'] ?? 0,
            $nombreModulo,
            'WRITE',
            'Operación de escritura realizada'
        );
    }
}
?>