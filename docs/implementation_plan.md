# Plan de Implementación - Fase 2: WoW Test Manager (Enfoque PHP Nativo)

Adaptación del proyecto WoW Test Manager para alinearlo con las tecnologías y conceptos nativos de PHP, eliminando referencias incorrectas a Node.js/Express y añadiendo funcionalidades profesionales solicitadas por el profesor.

## User Review Required

> [!IMPORTANT]
> **Cambios Significativos en la Documentación**
> Se reescribirá completamente la propuesta de Fase 2 para reflejar la realidad del proyecto en PHP puro, eliminando toda referencia a middlewares de Express, express-validator, y estructuras asíncronas propias de Node.js.

> [!WARNING]
> **Integración con API Externa**
> La integración con la Blizzard API requerirá credenciales OAuth2. Necesitarás registrar una aplicación en el [Blizzard Developer Portal](https://develop.battle.net/) para obtener Client ID y Client Secret.

---

## Proposed Changes

### Documentación del Proyecto

#### [NEW] [propuesta_fase2.md](file:///d:/ASIR/2º/IAW/my_web/propuesta_fase2.md)

Documento nuevo que reemplazará cualquier propuesta anterior. Incluirá:

- **Contexto del Proyecto**: Descripción del sistema actual en PHP puro con SQLite
- **Tecnologías Utilizadas**: PHP 7.4+, PDO, SQLite, cURL para APIs externas
- **Objetivos de Fase 2**:
  1. **Integración Blizzard API**: Usando cURL nativo de PHP para consultar datos de personajes
  2. **Mejoras de Seguridad**: Perfeccionamiento del sistema de control de acceso en `wow_auth.php`
  3. **Reportes Profesionales**: Exportación a PDF usando TCPDF además del CSV existente
- **Justificación Técnica**: Por qué cada tecnología es apropiada para PHP

#### [MODIFY] [README.md](file:///d:/ASIR/2º/IAW/my_web/README.md)

Actualizar la sección de características de seguridad para ser más específica sobre la implementación PHP:

- Cambiar "Tokens CSRF" por "Protección CSRF manual con tokens de sesión"
- Añadir sección sobre el sistema de control de acceso basado en funciones PHP
- Documentar el uso de `filter_var()` para validaciones
- Explicar la estructura de roles jerárquicos implementada

---

### Sistema de Integración con Blizzard API

#### [NEW] [includes/blizzard_api.php](file:///d:/ASIR/2º/IAW/my_web/includes/blizzard_api.php)

Módulo completo para interactuar con la Blizzard API usando cURL:

```php
class BlizzardAPI {
    private $clientId;
    private $clientSecret;
    private $accessToken;
    private $region = 'eu';
    
    // Autenticación OAuth2
    public function authenticate() { /* cURL para obtener token */ }
    
    // Consultar perfil de personaje
    public function getCharacterProfile($realm, $name) { /* cURL GET */ }
    
    // Obtener equipamiento
    public function getCharacterEquipment($realm, $name) { /* cURL GET */ }
    
    // Sistema de caché simple con archivos
    private function getCachedData($key) { /* leer cache */ }
    private function setCachedData($key, $data, $ttl = 3600) { /* guardar cache */ }
}
```

Características:
- Autenticación OAuth2 con cURL
- Manejo de errores HTTP (401, 404, 500)
- Sistema de caché en archivos para reducir llamadas API
- Timeouts configurables
- Logs de errores

#### [NEW] [admin/wow_blizzard_sync.php](file:///d:/ASIR/2º/IAW/my_web/admin/wow_blizzard_sync.php)

Interfaz administrativa para sincronizar datos de testers con la API de Blizzard:

- Formulario para vincular tester con personaje de WoW (realm + nombre)
- Botón para sincronizar datos manualmente
- Visualización de última sincronización
- Mostrar nivel, clase, ilvl del personaje

#### [MODIFY] [admin/wow_testers.php](file:///d:/ASIR/2º/IAW/my_web/admin/wow_testers.php)

Añadir columnas opcionales para datos de Blizzard:
- Icono de clase de WoW
- Nivel de personaje
- Item level
- Enlace a perfil en Blizzard

---

### Mejoras del Sistema de Seguridad

#### [MODIFY] [includes/wow_auth.php](file:///d:/ASIR/2º/IAW/my_web/includes/wow_auth.php)

Perfeccionar el sistema de control de acceso existente:

1. **Nueva función `verificarPermisoEscritura()`**:
   ```php
   function verificarPermisoEscritura($modulo) {
       if ($_SERVER['REQUEST_METHOD'] === 'POST') {
           verificarRol('admin'); // Solo admins pueden escribir
           registrarAccion($modulo, 'WRITE');
       }
   }
   ```

2. **Sistema de logs de acciones**:
   ```php
   function registrarAccion($modulo, $tipo) {
       // Guardar en tabla audit_log
   }
   ```

3. **Validaciones mejoradas**:
   ```php
   function validarEmail($email) {
       return filter_var($email, FILTER_VALIDATE_EMAIL);
   }
   
   function sanitizarTexto($texto) {
       return htmlspecialchars(trim($texto), ENT_QUOTES, 'UTF-8');
   }
   ```

#### [NEW] [setup/crear_tabla_audit.php](file:///d:/ASIR/2º/IAW/my_web/setup/crear_tabla_audit.php)

Script para crear tabla de auditoría:
```sql
CREATE TABLE audit_log (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    usuario_id INTEGER,
    modulo TEXT,
    accion TEXT,
    ip_address TEXT,
    timestamp INTEGER
);
```

---

### Sistema de Exportación a PDF

#### [NEW] [admin/wow_reportes_pdf.php](file:///d:/ASIR/2º/IAW/my_web/admin/wow_reportes_pdf.php)

Generador de reportes en PDF usando TCPDF:

- **Portada**: Logo WoW, título del reporte, fecha
- **Resumen Ejecutivo**: KPIs principales con iconos
- **Tablas de Datos**: 
  - Mejores testers por score
  - Contenido más difícil
  - Sesiones recientes
- **Gráficos**: Exportar gráficos de Chart.js como imágenes y embeber en PDF
- **Footer**: Paginación y marca de agua

Estructura del código:
```php
require_once('../vendor/tcpdf/tcpdf.php');

class WoWReportPDF extends TCPDF {
    public function Header() { /* Logo y título */ }
    public function Footer() { /* Paginación */ }
}

// Generar PDF
$pdf = new WoWReportPDF();
$pdf->AddPage();
$pdf->generarPortada();
$pdf->generarResumen($stats);
$pdf->generarTablas($data);
$pdf->Output('reporte_wow.pdf', 'D');
```

#### [MODIFY] [admin/wow_reportes.php](file:///d:/ASIR/2º/IAW/my_web/admin/wow_reportes.php)

Añadir botón de exportación a PDF junto al CSV existente:

```php
<div style="margin-bottom:30px; display: flex; gap: 10px;">
    <a href="?export=csv" class="btn-wow primary">
        <i class="fa-solid fa-file-csv"></i> Descargar CSV
    </a>
    <a href="wow_reportes_pdf.php" class="btn-wow secondary">
        <i class="fa-solid fa-file-pdf"></i> Descargar PDF Profesional
    </a>
</div>
```

---

### Instalación de Dependencias

#### [NEW] [composer.json](file:///d:/ASIR/2º/IAW/my_web/composer.json)

Archivo de configuración para instalar TCPDF vía Composer:

```json
{
    "require": {
        "tecnickcom/tcpdf": "^6.6"
    }
}
```

Comando a ejecutar: `composer install`

---

## Verification Plan

### Automated Tests

No existen tests automatizados en el proyecto actual. La verificación será manual y funcional.

### Manual Verification

#### 1. Verificar Documentación Actualizada

**Pasos**:
1. Abrir `propuesta_fase2.md` y verificar que:
   - No menciona "middlewares de Express"
   - No menciona "express-validator"
   - No menciona "estructura asíncrona"
   - Describe correctamente el uso de cURL para APIs
   - Menciona `filter_var()` para validaciones
   - Explica el sistema de control de acceso con `verificarRol()`

2. Revisar `README.md` actualizado para confirmar terminología PHP correcta

#### 2. Probar Integración Blizzard API

**Prerequisitos**:
- Obtener credenciales de Blizzard Developer Portal
- Configurar Client ID y Secret en archivo de configuración

**Pasos**:
1. Acceder a `admin/wow_blizzard_sync.php`
2. Vincular un tester con un personaje real de WoW (ej: "Thrall" en "Ragnaros-EU")
3. Hacer clic en "Sincronizar Datos"
4. Verificar que se muestran:
   - Nivel del personaje
   - Clase con icono correcto
   - Item level
   - Última sincronización

**Resultado Esperado**: Datos del personaje se muestran correctamente sin errores de cURL

#### 3. Validar Mejoras de Seguridad

**Pasos**:
1. Iniciar sesión como usuario con rol `tester`
2. Intentar acceder directamente a `wow_usuarios.php` (solo admin)
3. Verificar mensaje: "Acceso Denegado - No tienes nivel suficiente"
4. Intentar hacer POST a `wow_sesiones.php` sin token CSRF
5. Verificar mensaje de error CSRF
6. Iniciar sesión como `admin`
7. Crear un nuevo usuario
8. Verificar que se registró la acción en `audit_log`

**Resultado Esperado**: Sistema de permisos funciona correctamente y se registran acciones

#### 4. Probar Exportación PDF

**Pasos**:
1. Acceder a `admin/wow_reportes.php` como admin
2. Hacer clic en "Descargar PDF Profesional"
3. Verificar que se descarga un archivo PDF
4. Abrir el PDF y confirmar:
   - Portada con logo WoW
   - Resumen con KPIs
   - Tabla de mejores testers
   - Tabla de contenido difícil
   - Gráficos (si es posible)
   - Footer con paginación

**Resultado Esperado**: PDF se genera correctamente con diseño profesional temático de WoW

#### 5. Comparar CSV vs PDF

**Pasos**:
1. Descargar reporte en CSV
2. Descargar reporte en PDF
3. Verificar que ambos contienen los mismos datos
4. Confirmar que PDF tiene mejor presentación visual

**Resultado Esperado**: Ambos formatos disponibles, PDF más profesional

---

## Notas Adicionales

- **Composer**: Si no está instalado, se puede descargar de [getcomposer.org](https://getcomposer.org/)
- **Credenciales Blizzard**: Registrarse en [develop.battle.net](https://develop.battle.net/)
- **TCPDF**: Librería PHP nativa, no requiere extensiones adicionales
- **Caché API**: Se guardará en carpeta `cache/` (crear con permisos de escritura)
