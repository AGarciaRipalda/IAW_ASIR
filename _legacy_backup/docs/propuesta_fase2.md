# Propuesta Fase 2 - WoW Test Manager
## Sistema de Gestión de Pruebas QA con Integración Blizzard API

---

## 📋 Contexto del Proyecto

**WoW Test Manager** es un sistema de gestión de pruebas de calidad (QA) desarrollado completamente en **PHP puro** con base de datos **SQLite**. El proyecto utiliza tecnologías nativas de PHP para implementar un panel de administración robusto y seguro con temática de World of Warcraft.

### Tecnologías Actuales Implementadas

- **Backend**: PHP 7.4+ con PDO (PHP Data Objects)
- **Base de Datos**: SQLite 3 con sentencias preparadas
- **Seguridad**: Sistema de autenticación manual con CSRF tokens y control de acceso basado en roles
- **Frontend**: HTML5, CSS3 vanilla, JavaScript nativo
- **Visualización**: Chart.js para gráficos estadísticos

---

## 🎯 Objetivos de la Fase 2

La Fase 2 se centra en tres pilares fundamentales que elevarán el proyecto a un nivel profesional:

### 1. Integración con Blizzard API (cURL)
### 2. Perfeccionamiento del Sistema de Seguridad
### 3. Sistema de Reportes Profesionales (PDF)

---

## 🔌 1. Integración con Blizzard API usando cURL

### Objetivo
Conectar el sistema con la **Blizzard Battle.net API** para obtener datos reales de personajes de World of Warcraft y vincularlos con los testers del sistema.

### Implementación Técnica

#### Autenticación OAuth2 con cURL

La Blizzard API requiere autenticación OAuth2. Implementaremos el flujo de credenciales de cliente usando **cURL nativo de PHP**:

```php
function obtenerTokenBlizzard($clientId, $clientSecret) {
    $url = "https://oauth.battle.net/token";
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_USERPWD, "$clientId:$clientSecret");
    curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials");
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode === 200) {
        $data = json_decode($response, true);
        return $data['access_token'];
    }
    return false;
}
```

#### Consulta de Datos de Personajes

Una vez autenticados, consultaremos el perfil de personajes usando endpoints de la API:

```php
function obtenerPerfilPersonaje($realm, $nombre, $token) {
    $url = "https://eu.api.blizzard.com/profile/wow/character/" 
         . urlencode($realm) . "/" . urlencode(strtolower($nombre))
         . "?namespace=profile-eu&locale=es_ES";
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer $token"
    ]);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode === 200) {
        return json_decode($response, true);
    }
    return null;
}
```

#### Sistema de Caché

Para evitar llamadas excesivas a la API (límite de rate limiting), implementaremos un **sistema de caché simple con archivos**:

```php
function obtenerDatosConCache($clave, $callable, $ttl = 3600) {
    $cacheDir = __DIR__ . '/../cache/';
    $cacheFile = $cacheDir . md5($clave) . '.cache';
    
    // Verificar si existe caché válida
    if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) < $ttl) {
        return json_decode(file_get_contents($cacheFile), true);
    }
    
    // Obtener datos frescos
    $datos = $callable();
    
    // Guardar en caché
    if ($datos !== null) {
        file_put_contents($cacheFile, json_encode($datos));
    }
    
    return $datos;
}
```

### Funcionalidades a Implementar

1. **Módulo de Sincronización** (`admin/wow_blizzard_sync.php`):
   - Formulario para vincular tester con personaje WoW
   - Campos: Realm, Nombre de personaje
   - Botón "Sincronizar Datos"
   - Visualización de última sincronización

2. **Datos a Mostrar**:
   - Nivel del personaje
   - Clase (con icono correspondiente)
   - Raza
   - Item Level (ilvl)
   - Facción (Alianza/Horda)

3. **Integración en Testers**:
   - Añadir columnas en `admin/wow_testers.php`
   - Mostrar avatar de clase
   - Enlace al perfil en Blizzard Armory

### Manejo de Errores

```php
// Validación de respuestas HTTP
switch ($httpCode) {
    case 200:
        // Éxito
        break;
    case 401:
        error_log("Token de Blizzard expirado o inválido");
        break;
    case 404:
        error_log("Personaje no encontrado: $realm/$nombre");
        break;
    case 429:
        error_log("Rate limit excedido en Blizzard API");
        break;
    default:
        error_log("Error desconocido en Blizzard API: $httpCode");
}
```

---

## 🛡️ 2. Perfeccionamiento del Sistema de Seguridad

### Situación Actual

El proyecto ya cuenta con un **sistema de seguridad robusto** implementado en `includes/wow_auth.php`:

- ✅ Protección CSRF manual con tokens de sesión
- ✅ Sistema de roles jerárquicos: `viewer < tester < admin`
- ✅ Funciones de control de acceso: `verificarLogin()` y `verificarRol()`
- ✅ Protección anti-fuerza bruta (5 intentos, bloqueo de 15 minutos)
- ✅ PDO con sentencias preparadas (prevención de SQL Injection)
- ✅ Sesiones blindadas con cookies `HttpOnly`

### Mejoras Propuestas

#### A. Blindaje de Acciones de Escritura (POST)

Crear una función específica para proteger todas las operaciones de escritura:

```php
function verificarPermisoEscritura($moduloRequerido = 'admin') {
    // Verificar que sea una petición POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return;
    }
    
    // Verificar rol mínimo
    verificarRol($moduloRequerido);
    
    // Validar token CSRF
    if (!isset($_POST['csrf_token'])) {
        die("Error de seguridad: Token CSRF ausente");
    }
    validarCSRF($_POST['csrf_token']);
    
    // Registrar acción en log de auditoría
    registrarAccionAuditoria($_SESSION['user']['id'], $moduloRequerido, 'WRITE');
}
```

Uso en formularios:

```php
// En wow_usuarios.php, wow_sesiones.php, etc.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verificarPermisoEscritura('admin');
    // ... resto del código de inserción/actualización
}
```

#### B. Sistema de Logs de Auditoría

Crear tabla para registrar acciones administrativas:

```sql
CREATE TABLE audit_log (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    usuario_id INTEGER,
    modulo TEXT,
    accion TEXT,
    detalles TEXT,
    ip_address TEXT,
    timestamp INTEGER,
    FOREIGN KEY(usuario_id) REFERENCES usuarios(id)
);
```

Función de registro:

```php
function registrarAccionAuditoria($usuarioId, $modulo, $accion, $detalles = '') {
    global $db;
    
    $stmt = $db->prepare(
        "INSERT INTO audit_log (usuario_id, modulo, accion, detalles, ip_address, timestamp) 
         VALUES (?, ?, ?, ?, ?, ?)"
    );
    
    $stmt->execute([
        $usuarioId,
        $modulo,
        $accion,
        $detalles,
        $_SERVER['REMOTE_ADDR'],
        time()
    ]);
}
```

#### C. Validaciones con Funciones Nativas de PHP

Reemplazar validaciones manuales por funciones nativas de PHP:

```php
// Validación de email
function validarEmail($email) {
    $emailLimpio = filter_var($email, FILTER_SANITIZE_EMAIL);
    return filter_var($emailLimpio, FILTER_VALIDATE_EMAIL) !== false;
}

// Validación de enteros
function validarEntero($valor, $min = null, $max = null) {
    $opciones = ['options' => []];
    if ($min !== null) $opciones['options']['min_range'] = $min;
    if ($max !== null) $opciones['options']['max_range'] = $max;
    
    return filter_var($valor, FILTER_VALIDATE_INT, $opciones) !== false;
}

// Sanitización de texto
function sanitizarTexto($texto) {
    $limpio = trim($texto);
    return htmlspecialchars($limpio, ENT_QUOTES, 'UTF-8');
}

// Validación de URL
function validarURL($url) {
    return filter_var($url, FILTER_VALIDATE_URL) !== false;
}
```

Uso en formularios:

```php
// En wow_usuarios.php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verificarPermisoEscritura('admin');
    
    $username = sanitizarTexto($_POST['username'] ?? '');
    $email = $_POST['email'] ?? '';
    
    if (!validarEmail($email)) {
        $error = "Email inválido";
    } elseif (strlen($username) < 3) {
        $error = "El nombre de usuario debe tener al menos 3 caracteres";
    } else {
        // Procesar inserción...
    }
}
```

#### D. Panel de Auditoría (Solo Admin)

Crear `admin/wow_auditoria.php` para visualizar el log de acciones:

- Tabla con: Usuario, Módulo, Acción, IP, Fecha/Hora
- Filtros por usuario, módulo, rango de fechas
- Paginación
- Exportación a CSV

---

## 📊 3. Sistema de Reportes Profesionales (PDF)

### Situación Actual

El sistema ya cuenta con **exportación a CSV** en `admin/wow_reportes.php`:

```php
if (isset($_GET['export'])) {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=reporte_wow.csv');
    $output = fopen('php://output', 'w');
    fputcsv($output, ['ID', 'Tester', 'Contenido', 'Dificultad', 'Score', 'Horas', 'Comentarios']);
    // ... exportar datos
}
```

### Mejora: Exportación a PDF con TCPDF

#### Instalación de TCPDF

Usar **Composer** para instalar la librería TCPDF (100% PHP, sin dependencias externas):

```bash
composer require tecnickcom/tcpdf
```

Archivo `composer.json`:

```json
{
    "require": {
        "tecnickcom/tcpdf": "^6.6"
    }
}
```

#### Implementación del Generador PDF

Crear `admin/wow_reportes_pdf.php`:

```php
<?php
require_once __DIR__ . '/../includes/wow_auth.php';
verificarLogin();
verificarRol('admin');

require_once __DIR__ . '/../vendor/autoload.php';

// Clase personalizada para el PDF
class WoWReportPDF extends TCPDF {
    public function Header() {
        // Logo WoW
        $this->Image(__DIR__ . '/../assets/wow_logo.png', 15, 10, 30);
        
        // Título
        $this->SetFont('helvetica', 'B', 16);
        $this->SetTextColor(255, 209, 0); // Dorado WoW
        $this->Cell(0, 15, 'WoW Test Manager - Reporte de Sesiones', 0, false, 'C', 0, '', 0, false, 'M', 'M');
        $this->Ln(10);
    }
    
    public function Footer() {
        $this->SetY(-15);
        $this->SetFont('helvetica', 'I', 8);
        $this->SetTextColor(128, 128, 128);
        $this->Cell(0, 10, 'Página ' . $this->getAliasNumPage() . ' de ' . $this->getAliasNbPages(), 0, false, 'C');
    }
}

// Obtener datos
$db = new PDO("sqlite:" . __DIR__ . "/../database/wow.sqlite");
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$totalSesiones = $db->query("SELECT COUNT(*) FROM test_session")->fetchColumn();
$promedioScore = $db->query("SELECT AVG(score) FROM test_session")->fetchColumn();
$topTesters = $db->query("SELECT t.name, AVG(s.score) as prom FROM test_session s JOIN tester t ON s.tester=t.id GROUP BY t.name ORDER BY prom DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);

// Crear PDF
$pdf = new WoWReportPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

$pdf->SetCreator('WoW Test Manager');
$pdf->SetAuthor('Admin');
$pdf->SetTitle('Reporte de Sesiones QA');
$pdf->SetSubject('Estadísticas de Testing');

$pdf->setPrintHeader(true);
$pdf->setPrintFooter(true);
$pdf->SetMargins(15, 30, 15);
$pdf->SetAutoPageBreak(TRUE, 25);

$pdf->AddPage();

// Portada
$pdf->SetFont('helvetica', 'B', 24);
$pdf->SetTextColor(255, 209, 0);
$pdf->Cell(0, 20, 'Reporte Ejecutivo', 0, 1, 'C');
$pdf->SetFont('helvetica', '', 12);
$pdf->SetTextColor(0, 0, 0);
$pdf->Cell(0, 10, 'Fecha: ' . date('d/m/Y H:i'), 0, 1, 'C');
$pdf->Ln(20);

// Resumen KPIs
$pdf->SetFont('helvetica', 'B', 14);
$pdf->Cell(0, 10, 'Resumen Ejecutivo', 0, 1, 'L');
$pdf->SetFont('helvetica', '', 11);
$pdf->Cell(0, 8, 'Total de Sesiones: ' . $totalSesiones, 0, 1);
$pdf->Cell(0, 8, 'Score Promedio Global: ' . round($promedioScore, 2), 0, 1);
$pdf->Ln(10);

// Tabla de Top Testers
$pdf->SetFont('helvetica', 'B', 14);
$pdf->Cell(0, 10, 'Top 5 Testers por Calidad', 0, 1, 'L');

$pdf->SetFillColor(42, 42, 42);
$pdf->SetTextColor(255, 209, 0);
$pdf->SetFont('helvetica', 'B', 10);
$pdf->Cell(100, 7, 'Tester', 1, 0, 'L', true);
$pdf->Cell(80, 7, 'Score Promedio', 1, 1, 'C', true);

$pdf->SetTextColor(0, 0, 0);
$pdf->SetFont('helvetica', '', 10);
foreach ($topTesters as $tester) {
    $pdf->Cell(100, 6, $tester['name'], 1, 0, 'L');
    $pdf->Cell(80, 6, round($tester['prom'], 2), 1, 1, 'C');
}

// Salida del PDF
$pdf->Output('reporte_wow_' . date('Ymd') . '.pdf', 'D');
?>
```

#### Integración en la Interfaz

Modificar `admin/wow_reportes.php` para añadir el botón de PDF:

```php
<div style="margin-bottom:30px; display: flex; gap: 10px;">
    <a href="?export=1" class="btn-wow primary">
        <i class="fa-solid fa-file-csv"></i> Descargar CSV Completo
    </a>
    <a href="wow_reportes_pdf.php" class="btn-wow secondary">
        <i class="fa-solid fa-file-pdf"></i> Descargar PDF Profesional
    </a>
</div>
```

### Características del PDF

1. **Portada Profesional**:
   - Logo de WoW
   - Título del reporte
   - Fecha y hora de generación

2. **Resumen Ejecutivo**:
   - KPIs principales (Total sesiones, Score promedio, etc.)
   - Formato visualmente atractivo

3. **Tablas de Datos**:
   - Top 5 Testers por calidad
   - Contenido más difícil
   - Sesiones recientes
   - Formato con colores temáticos de WoW

4. **Footer Profesional**:
   - Numeración de páginas
   - Marca de agua opcional

---

## 📦 Estructura de Archivos Nuevos

```
my_web/
├── includes/
│   ├── wow_auth.php (MODIFICADO - mejoras de seguridad)
│   └── blizzard_api.php (NUEVO - integración API)
├── admin/
│   ├── wow_blizzard_sync.php (NUEVO - sincronización)
│   ├── wow_reportes_pdf.php (NUEVO - generador PDF)
│   ├── wow_auditoria.php (NUEVO - logs de auditoría)
│   ├── wow_reportes.php (MODIFICADO - botón PDF)
│   └── wow_testers.php (MODIFICADO - datos Blizzard)
├── setup/
│   └── crear_tabla_audit.php (NUEVO - tabla auditoría)
├── cache/ (NUEVO - caché de API)
├── vendor/ (Composer - TCPDF)
├── composer.json (NUEVO)
├── propuesta_fase2.md (ESTE DOCUMENTO)
└── README.md (MODIFICADO - documentación actualizada)
```

---

## 🔧 Requisitos Técnicos

### Extensiones PHP Necesarias

- ✅ `pdo_sqlite` (ya instalada)
- ✅ `curl` (verificar con `php -m | grep curl`)
- ✅ `json` (incluida por defecto en PHP 7.4+)
- ✅ `gd` o `imagick` (opcional, para manipulación de imágenes en PDF)

### Credenciales Blizzard

1. Registrarse en [Blizzard Developer Portal](https://develop.battle.net/)
2. Crear una aplicación
3. Obtener **Client ID** y **Client Secret**
4. Configurar en archivo `includes/blizzard_config.php`:

```php
<?php
define('BLIZZARD_CLIENT_ID', 'tu_client_id_aqui');
define('BLIZZARD_CLIENT_SECRET', 'tu_client_secret_aqui');
define('BLIZZARD_REGION', 'eu');
?>
```

### Instalación de Composer

Si no está instalado:

```bash
# Windows
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php composer-setup.php
php -r "unlink('composer-setup.php');"
```

Luego ejecutar:

```bash
php composer.phar install
```

---

## ✅ Ventajas de este Enfoque

### 1. Tecnologías Nativas de PHP

- **cURL**: Incluido en PHP, robusto y ampliamente documentado
- **filter_var()**: Funciones nativas de validación, más seguras que regex manuales
- **PDO**: Ya implementado, sin cambios necesarios
- **TCPDF**: Librería PHP pura, sin dependencias externas

### 2. Compatibilidad Total

- No requiere Node.js, npm, ni Express
- Funciona en cualquier servidor con PHP 7.4+
- SQLite portátil, sin configuración de MySQL/PostgreSQL

### 3. Seguridad Mejorada

- Control de acceso granular por módulo
- Logs de auditoría para trazabilidad
- Validaciones con funciones probadas de PHP
- Protección CSRF en todas las operaciones de escritura

### 4. Profesionalidad

- Reportes PDF de calidad empresarial
- Integración con API oficial de Blizzard
- Sistema de caché para optimizar rendimiento
- Documentación técnica completa

---

## 📅 Cronograma Estimado

| Tarea | Tiempo Estimado |
|-------|----------------|
| Configuración Blizzard API + cURL | 3-4 horas |
| Módulo de sincronización de personajes | 2-3 horas |
| Mejoras de seguridad (auditoría + validaciones) | 2-3 horas |
| Instalación TCPDF + generador PDF | 3-4 horas |
| Pruebas y ajustes | 2 horas |
| **TOTAL** | **12-16 horas** |

---

## 🎓 Justificación Académica

Este proyecto demuestra conocimientos avanzados de:

1. **Integración de APIs externas** usando cURL nativo de PHP
2. **Seguridad web** con múltiples capas de protección
3. **Generación de documentos** con librerías PHP profesionales
4. **Arquitectura limpia** con separación de responsabilidades
5. **Optimización** mediante sistemas de caché
6. **Validación de datos** con funciones nativas de PHP

Todo ello utilizando **exclusivamente tecnologías PHP**, sin mezclar conceptos de otros ecosistemas como Node.js/Express.

---

## 📚 Referencias Técnicas

- [Blizzard API Documentation](https://develop.battle.net/documentation)
- [PHP cURL Manual](https://www.php.net/manual/es/book.curl.php)
- [PHP filter_var() Documentation](https://www.php.net/manual/es/function.filter-var.php)
- [TCPDF Documentation](https://tcpdf.org/docs/)
- [PDO Security Best Practices](https://www.php.net/manual/es/pdo.prepared-statements.php)

---

**Fecha de Propuesta**: 14 de Enero de 2026  
**Autor**: Alejandro  
**Asignatura**: Desarrollo Web (IAW)  
**Fase**: 2 - Integración y Profesionalización
