# ⚔️ WoW Test Manager (Azeroth Edition)

> Un sistema de gestión de pruebas de calidad (QA) diseñado con la estética inmersiva de World of Warcraft. Gestiona sesiones, testers, contenido y reportes como si estuvieras liderando una Raid.

---

## 📜 Requisitos del Sistema (Loot Table)

Para desplegar este portal necesitas:

* **Servidor Web:** Apache (Recomendado) o Nginx.
* **PHP:** Versión 7.4 o superior.
* **Base de Datos:** SQLite 3 (Nativa en PHP, no requiere instalación externa).
* **Extensiones PHP:** `pdo_sqlite`, `session`, `openssl`.

---

## 🛠️ Instalación (Ritual de Invocación)

### 1. Despliegue de Archivos
Copia todas las carpetas (`admin`, `assets`, `auth`, `database`, `includes`, `public`) al directorio raíz de tu servidor web (por ejemplo, `htdocs` o `www`).

### 2. Permisos de Escritura (Crítico)
Para que la base de datos funcione y el sistema de seguridad registre intentos fallidos, el servidor web necesita **permisos de escritura** en:
* La carpeta `/database/`
* El archivo `/database/wow.sqlite`

*En Linux:* `chmod -R 775 database/` y `chown -R www-data:www-data database/`

### 3. Base de Datos
El sistema utiliza **SQLite**.
* Si el archivo `wow.sqlite` ya existe, el sistema lo usará.
* Si no existe (o la tabla de seguridad `login_attempts` falta), el sistema intentará **automáticamente** repararla al acceder al Login.

---

## 🔑 Acceso Inicial (Default Credentials)

Si es una instalación limpia, deberás tener un usuario en la base de datos.
*(Si estás migrando tu base de datos actual, usa tus credenciales existentes).*

**Usuario por defecto (Ejemplo):**
* **User:** `admin`
* **Pass:** `admin123` (¡Cámbiala inmediatamente en "Mi Perfil"!)

---

## 🛡️ Características de Seguridad

El sistema incluye protecciones activas de nivel 60:

1.  **Protección Anti-Fuerza Bruta:** Bloquea el acceso tras 5 intentos fallidos durante 15 minutos (basado en IP).
2.  **Protección CSRF Manual:** Tokens de sesión generados con `random_bytes()` para proteger todos los formularios contra falsificación de peticiones.
3.  **Sesiones Blindadas:** Cookies `HttpOnly` y regeneración de ID de sesión tras login exitoso.
4.  **Prevención de Inyección SQL:** Todo el sistema usa PDO con sentencias preparadas (`prepare()` + `execute()`).
5.  **Sistema de Control de Acceso:** Funciones PHP `verificarLogin()` y `verificarRol()` implementan roles jerárquicos (viewer < tester < admin).
6.  **Validaciones Nativas:** Uso de `filter_var()` y funciones nativas de PHP para validar y sanitizar datos de entrada.
7.  **Detección HTTPS:** Fuerza SSL si no estás en `localhost`.

---

## 🚀 Nuevas Funcionalidades - Fase 2

### Integración con Blizzard API

Conecta testers con personajes reales de World of Warcraft:

* **Vinculación de Personajes**: Asocia cada tester con su personaje de WoW (realm + nombre)
* **Sincronización Automática**: Obtén datos en tiempo real desde la Blizzard Battle.net API
* **Datos Disponibles**: Nivel, clase, raza, facción, item level, puntos de logro
* **Sistema de Caché**: Reduce llamadas a la API con caché inteligente (TTL configurable)
* **Autenticación OAuth2**: Implementado con cURL nativo de PHP

**Configuración**: Ver `INSTALACION_FASE2.md` para obtener credenciales en https://develop.battle.net/

### Sistema de Reportes Profesionales

Exporta reportes en múltiples formatos:

* **CSV**: Exportación rápida de datos tabulares (ya existente, mejorado)
* **PDF Profesional**: Reportes con diseño temático de WoW usando TCPDF
  * Portada con logo y fecha
  * Resumen ejecutivo con KPIs
  * Tablas de top testers y contenido difícil
  * Sesiones recientes con código de colores
  * Paginación automática

### Mejoras de Seguridad

Sistema de seguridad reforzado:

* **Logs de Auditoría**: Registro automático de todas las acciones administrativas
* **Validaciones Nativas**: Uso de `filter_var()` para emails, URLs, enteros
* **Sanitización**: Función `sanitizarTexto()` previene XSS
* **Control de Escritura**: `verificarPermisoEscritura()` protege operaciones POST
* **Trazabilidad**: Tabla `audit_log` con usuario, módulo, acción, IP y timestamp

---

## 🎨 Personalización (Transfiguración)

Todos los activos visuales residen en la carpeta `/assets/`.

* **Fuentes:** `Friz Quadrata UI` (Interfaz) y `Morpheus RPG` (Títulos).
* **Música:** Reemplaza `tavern.mp3` para cambiar la música del Dashboard.
* **Cursores:**
    * `guantelete.png`: Cursor para Admins y Login.
    * `espadita.png`: Cursor exclusivo para rol `Tester`.
* **Logos:** Reemplaza `wow_logo.png` o `test_manager_title.png`.

---

## 📂 Estructura del Proyecto

```text
/
├── admin/              # Panel de Control (Protegido)
│   ├── wow_dashboard.php   # Cuadro de mando + Gráficos
│   ├── wow_sesiones.php    # Gestión de sesiones
│   ├── ...                 # Otros módulos
├── assets/             # Recursos (CSS, JS, Imágenes, Fuentes, Audio)
│   ├── wow_style.css       # Hoja de estilos Maestra
│   ├── tavern.mp3          # Música
│   └── ...
├── auth/               # Autenticación
│   ├── wow_login.php       # Login con seguridad Brute-Force
│   └── logout.php          # Cierre de sesión seguro
├── database/           # Almacenamiento de datos
│   └── wow.sqlite          # Base de datos única
├── includes/           # Lógica central
│   └── wow_auth.php        # Configuración de seguridad y sesión
└── public/             # Acceso público
    └── index.php           # Portada de bienvenida
