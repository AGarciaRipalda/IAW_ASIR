<?php
// auth/logout.php

// Iniciar sesión para poder destruirla
session_start();

// Vaciar todas las variables de sesión
$_SESSION = array();

// Borrar la cookie de sesión si existe
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destruir la sesión completamente
session_destroy();

// Redirigir al usuario al Login CON MENSAJE
header("Location: wow_login.php?msg=logged_out");
exit;
?>
