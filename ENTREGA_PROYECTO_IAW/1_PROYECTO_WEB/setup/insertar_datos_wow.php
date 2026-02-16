<?php
// --- AÑADIDO PARA VER ERRORES ---
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// --- FIN DE AÑADIDO ---

// La ruta a la BBDD ahora es SUBIENDO un nivel
$db = new PDO("sqlite:" . __DIR__ . "/../database/wow.sqlite");
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Crear usuario admin
$hash = password_hash("admin123", PASSWORD_DEFAULT);
$db->prepare("INSERT OR IGNORE INTO usuarios (username, email, password, role) VALUES (?, ?, ?, ?)")
   ->execute(["admin", "admin@correo.com", $hash, "admin"]);

// Insertar testers
$testers = [
  ["Thrall", "Tank", "Horde"],
  ["Jaina", "DPS", "Alliance"],
  ["Anduin", "Healer", "Alliance"],
  ["Sylvanas", "DPS", "Horde"]
];
// Usar INSERT OR IGNORE para evitar duplicados si se ejecuta de nuevo
$stmtTester = $db->prepare("INSERT OR IGNORE INTO tester (name, role, faction) VALUES (?, ?, ?)");
foreach ($testers as $t) {
  $stmtTester->execute($t);
}

// Insertar contenido
$contenidos = [
  ["Raid", "Castle Nathria", "Shadowlands 9.0"],
  ["Dungeon", "Mists of Tirna Scithe", "Shadowlands 9.0"],
  ["Class", "Mage Frost", "Dragonflight 10.2"],
  ["Talent", "Warrior Arms", "Dragonflight 10.2"]
];
$stmtContent = $db->prepare("INSERT OR IGNORE INTO content (type, name, patch) VALUES (?, ?, ?)");
foreach ($contenidos as $c) {
  $stmtContent->execute($c);
}

// Insertar sesiones de prueba
$sesiones = [
  [1, 1, "2h", 85, "Normal", "Buen balance de daño"],
  [2, 2, "1h30m", 70, "Heroic", "Mecánicas confusas"],
  [3, 3, "3h", 90, "Mythic", "Rotación fluida"],
  [4, 4, "45m", 60, "Normal", "Talentos poco útiles"]
];
$stmtSession = $db->prepare("INSERT OR IGNORE INTO test_session (tester, content, time_played, score, difficulty, comments)
                             VALUES (?, ?, ?, ?, ?, ?)");
foreach ($sesiones as $s) {
  $stmtSession->execute($s);
}

// Insertar settings por defecto
$db->exec("
  INSERT OR IGNORE INTO settings (key, value) VALUES 
  ('theme', 'dark'), 
  ('primary', '#ffd700'), 
  ('language', 'es')
");


echo "✅ Datos de ejemplo insertados correctamente en WoW Test Manager.";
?>
