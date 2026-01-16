<?php
// --- AÑADIDO PARA VER ERRORES ---
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// --- FIN DE AÑADIDO ---

// La ruta a la BBDD ahora es SUBIENDO un nivel
$db = new PDO("sqlite:" . __DIR__ . "/../database/wow.sqlite");
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$db->exec("
CREATE TABLE IF NOT EXISTS usuarios (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  username TEXT NOT NULL UNIQUE,
  email TEXT NOT NULL UNIQUE,
  password TEXT NOT NULL,
  role TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS tester (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  name TEXT NOT NULL,
  role TEXT,
  faction TEXT
);

CREATE TABLE IF NOT EXISTS content (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  type TEXT,
  name TEXT,
  patch TEXT
);

CREATE TABLE IF NOT EXISTS test_session (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  tester INTEGER,
  content INTEGER,
  time_played TEXT,
  score INTEGER,
  difficulty TEXT,
  comments TEXT,
  FOREIGN KEY(tester) REFERENCES tester(id),
  FOREIGN KEY(content) REFERENCES content(id)
);

CREATE TABLE IF NOT EXISTS settings (
  key TEXT PRIMARY KEY,
  value TEXT
);
");

echo '✅ Base de datos WoW creada correctamente (Esquema v2 - Corregido).';
?>
