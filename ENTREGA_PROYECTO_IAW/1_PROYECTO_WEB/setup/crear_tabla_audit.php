<?php
/**
 * Script de Creación de Tabla de Auditoría
 * 
 * Crea la tabla audit_log para registrar acciones administrativas
 * y operaciones de escritura en el sistema.
 */

// Activar errores para depuración
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

try {
    // Conectar a la base de datos
    $db = new PDO("sqlite:" . __DIR__ . "/../database/wow.sqlite");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Crear tabla de auditoría
    $db->exec("
        CREATE TABLE IF NOT EXISTS audit_log (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            usuario_id INTEGER,
            modulo TEXT NOT NULL,
            accion TEXT NOT NULL,
            detalles TEXT,
            ip_address TEXT,
            timestamp INTEGER NOT NULL,
            FOREIGN KEY(usuario_id) REFERENCES usuarios(id)
        )
    ");

    // Crear índices para mejorar rendimiento de consultas
    $db->exec("CREATE INDEX IF NOT EXISTS idx_audit_usuario ON audit_log(usuario_id)");
    $db->exec("CREATE INDEX IF NOT EXISTS idx_audit_timestamp ON audit_log(timestamp)");
    $db->exec("CREATE INDEX IF NOT EXISTS idx_audit_modulo ON audit_log(modulo)");

    echo "✅ Tabla de auditoría creada correctamente.\n";
    echo "📊 Índices creados para optimizar consultas.\n";
    echo "\n";
    echo "La tabla audit_log está lista para registrar:\n";
    echo "  - Acciones de usuarios\n";
    echo "  - Operaciones de escritura\n";
    echo "  - Cambios administrativos\n";
    echo "  - Direcciones IP de origen\n";

} catch (PDOException $e) {
    die("❌ Error al crear tabla de auditoría: " . $e->getMessage());
}
?>