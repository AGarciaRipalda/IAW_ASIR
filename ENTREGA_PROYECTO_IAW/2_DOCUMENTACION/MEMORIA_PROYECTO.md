# MEMORIA DEL PROYECTO
## Desarrollo e Implementación de Aplicaciones Web (IAW)

---

**Título del Proyecto**: WoW Test Manager  
**Alumno**: Alejandro  
**Ciclo Formativo**: ASIR 2º  
**Fecha de Entrega**: Febrero 2026

---

## ÍNDICE DE CONTENIDOS

1.  **Introducción**
    *   1.1. Descripción del Proyecto
    *   1.2. Objetivos
2.  **Tecnologías Utilizadas**
    *   2.1. Entorno de Servidor
    *   2.2. Backend y Base de Datos
    *   2.3. Frontend
3.  **Arquitectura del Sistema**
    *   3.1. Estructura de Directorios
    *   3.2. Diseño de la Base de Datos
4.  **Desarrollo e Implementación**
    *   4.1. Módulo de Autenticación
    *   4.2. Gestión de Entidades (CRUD)
    *   4.3. Dashboard y Estadísticas
5.  **Seguridad**
    *   5.1. Control de Acceso y Roles
    *   5.2. Protección de Datos
6.  **Funcionalidades Avanzadas**
    *   6.1. Integración con Blizzard API
    *   6.2. Generación de Reportes PDF
    *   6.3. Sistema de Auditoría
7.  **Manual de Usuario**
    *   7.1. Acceso y Perfiles
    *   7.2. Operativa Diaria
8.  **Dificultades Encontradas**
9.  **Conclusiones**

---

## 1. INTRODUCCIÓN

### 1.1. Descripción del Proyecto
**WoW Test Manager** es una aplicación web diseñada para la gestión integral de procesos de aseguramiento de calidad (QA) en entornos de videojuegos, específicamente inspirada en el MMORPG "World of Warcraft". La herramienta permite a los coordinadores (Administradores) y probadores (Testers) registrar, analizar y reportar el desempeño en diferentes contenidos del juego, como Bandas (Raids) y Mazmorras.

### 1.2. Objetivos
El objetivo principal es digitalizar el cuaderno de campo de un equipo de pruebas, sustituyendo hojas de cálculo dispersas por una aplicación centralizada, segura y accesible vía web.
*   **Centralización**: Unificar los datos de pruebas en una base de datos relacional.
*   **Automatización**: Facilitar la obtención de datos oficiales de personajes mediante APIs.
*   **Seguridad**: Garantizar que solo personal autorizado pueda acceder o modificar información sensible.
*   **Profesionalización**: Generar reportes estandarizados en formatos universales (PDF/CSV).

## 2. TECNOLOGÍAS UTILIZADAS

Para el desarrollo de este proyecto se ha optado por un stack tecnológico robusto y estándar en la industria, priorizando el uso de funciones nativas sobre frameworks pesados para demostrar dominio del lenguaje.

### 2.1. Entorno de Servidor
*   **Servidor Web**: Apache 2.4.58 (XAMPP).
*   **Intérprete**: PHP 8.2.12.

### 2.2. Backend y Base de Datos
*   **Lenguaje**: PHP Puro (Vanilla). No se han utilizado frameworks (Laravel/Symfony) para cumplir con los requisitos académicos de comprensión del lenguaje base.
*   **Base de Datos**: SQLite 3. Elegido por su portabilidad y eficiencia en aplicaciones de escala media, manejado mediante la extensión PDO (PHP Data Objects) para mayor seguridad y abstracción.
*   **Integración API**: cURL nativo de PHP para comunicación HTTP.
*   **Librerías Externas**: TCPDF (instalada vía Composer) para la generación de documentos.

### 2.3. Frontend
*   **Estructura**: HTML5 Semántico.
*   **Estilos**: CSS3 nativo (Grid y Flexbox) con diseño responsivo.
*   **Interactividad**: JavaScript (ES6) para manipulaciones DOM y lógica de cliente.
*   **Gráficos**: Chart.js para visualización de datos estadísticos.

## 3. ARQUITECTURA DEL SISTEMA

### 3.1. Estructura de Directorios
El proyecto sigue una arquitectura MVC (Modelo-Vista-Controlador) simplificada, separando la lógica de negocio de la presentación visual y los activos estáticos.

*   `/admin`: Contiene los controladores y vistas de la zona privada (Dashboard, Gestión de Testers, etc.).
*   `/includes`: Librerías compartidas, configuración de base de datos y sistema de autenticación (`wow_auth.php`).
*   `/auth`: Scripts de inicio y cierre de sesión.
*   `/database`: Archivo `wow.sqlite` y scripts de conexión.
*   `/assets`: Imágenes, hojas de estilo CSS y archivos JavaScript.
*   `/setup`: Scripts de inicialización y migración de base de datos.

### 3.2. Diseño de la Base de Datos
El modelo de datos es relacional y consta de las siguientes tablas principales:
*   **usuarios**: Almacena credenciales (hash) y roles de acceso.
*   **tester**: Información de los probadores, incluyendo datos sincronizados con Blizzard (Nivel, Item Level).
*   **content**: Catálogo de pruebas disponibles (Raids, Dungeons).
*   **test_session**: Tabla central que registra las actividades, vinculando Testers y Contenido con métricas de desempeño.
*   **audit_log**: Registro de seguridad para trazabilidad de acciones.

## 4. DESARROLLO E IMPLEMENTACIÓN

### 4.1. Módulo de Autenticación
Se ha implementado un sistema de login propio que verifica las credenciales contra la base de datos utilizando `password_verify()`. El sistema gestiona variables de sesión (`$_SESSION`) para persistir la identidad del usuario y su rol durante la navegación.

### 4.2. Gestión de Entidades (CRUD)
Cada entidad principal (Testers, Usuarios, Contenido) dispone de un módulo de gestión completo que permite:
*   **C**reate: Añadir nuevos registros mediante formularios validados.
*   **R**ead: Listar registros con paginación y filtros de búsqueda.
*   **U**pdate: Modificar información existente.
*   **D**elete: Eliminar registros (con confirmación de seguridad).

### 4.3. Dashboard y Estadísticas
La página de inicio del panel de administración ofrece una visión global del estado del sistema, calculando KPIs en tiempo real (puntuación media, actividad reciente) y mostrando gráficos de distribución de dificultad y tipos de contenido.

## 5. SEGURIDAD

La seguridad ha sido un pilar fundamental en el desarrollo, implementando múltiples capas de defensa ("Defense in Depth").

### 5.1. Control de Acceso y Roles
El sistema distingue tres niveles de acceso:
1.  **Viewer**: Solo lectura.
2.  **Tester**: Puede registrar sus propias sesiones.
3.  **Admin**: Control total, gestión de usuarios y configuración.
Cada script PHP verifica al inicio el rol del usuario mediante `verificarRol('nivel_requerido')`, deteniendo la ejecución si no se cumplen los permisos.

### 5.2. Protección de Datos
*   **SQL Injection**: Mitigada al 100% mediante el uso exclusivo de **Sentencias Preparadas** (Prepared Statements) en PDO.
*   **XSS (Cross-Site Scripting)**: Todas las salidas de datos a HTML se escapan utilizando `htmlspecialchars()`.
*   **CSRF (Cross-Site Request Forgery)**: Se generan tokens criptográficos únicos por sesión que deben acompañar a cada petición POST para ser válida.

## 6. FUNCIONALIDADES AVANZADAS

### 6.1. Integración con Blizzard API
Para enriquecer los datos de los testers, se conecta con la API oficial de Battle.net.
*   **Protocolo**: OAuth 2.0 (Client Credentials Flow).
*   **Implementación**: Clase `BlizzardAPI` propia que gestiona la obtención de tokens y realiza peticiones GET a los endpoints de perfil de personaje (/profile/wow/character).
*   **Beneficio**: Permite actualizar el Nivel, Clase e Item Level de los testers automáticamente, evitando errores manuales.

### 6.2. Generación de Reportes PDF
Se ha integrado la librería **TCPDF** para generar informes ejecutivos. A diferencia de una simple impresión web, este módulo construye un documento vectorial con cabeceras personalizadas, tablas maquetadas y diseño corporativo, listo para ser enviado a dirección.

### 6.3. Sistema de Auditoría
Un módulo transparente ("Logger") intercepta todas las acciones de escritura (POST) realizadas por administradores y las registra en una tabla inmutable (`audit_log`), guardando Quién, Qué, Cuándo y Desde dónde (IP) se realizó la acción.

## 7. MANUAL DE USUARIO

### 7.1. Acceso y Perfiles
El sistema es accesible vía navegador web en la ruta del servidor `/my_web`.
*   **Administrador**: Acceso total. Usuario: `admin` / Password: `admin123`.
*   **Tester**: Acceso restringido a registro de sesiones.

### 7.2. Operativa Diaria
1.  **Dar de alta un Tester**: En el menú "Testers", pulsar "Nuevo Recluta". Opcionalmente, usar "Vincular Blizzard" para sincronizar datos reales.
2.  **Registrar Sesión**: En "Sesiones", seleccionar el Tester y el Contenido. Introducir la duración (ej: 2h 30m) y la puntuación.
3.  **Exportar Datos**: Desde "Reportes", el administrador puede descargar el mes completo en CSV o generar un informe PDF para la reunión semanal.

## 8. DIFICULTADES ENCONTRADAS

1.  **Autenticación OAuth2**: La gestión del ciclo de vida del token de Blizzard (solicitud, uso, renovación) fue compleja de implementar desde cero sin librerías de terceros. Se solucionó encapsulando la lógica en una clase dedicada con manejo de errores HTTP.
2.  **Manejo de Tiempos en SQLite**: Al carecer de tipos de fecha nativos robustos, se tuvo que diseñar una lógica de conversión en PHP para transformar horas/minutos del formulario a un formato de texto estandarizado en base de datos.
3.  **Maquetación PDF**: Trasladar el diseño visual de la web a PDF con TCPDF requirió un trabajo minucioso de posicionamiento de celdas y ajuste de fuentes.

## 9. CONCLUSIONES

El proyecto **WoW Test Manager** cumple satisfactoriamente con todos los requisitos planteados en la asignatura. Se ha logrado construir una aplicación web completa, desde el backend hasta el frontend, demostrando la capacidad de integración de tecnologías (PHP, SQL, APIs REST) y aplicando buenas prácticas de seguridad e ingeniería de software. La aplicación es funcional, segura y estéticamente coherente con su temática.

---

*"Lok'tar Ogar! - Victoria o Muerte"*  
*(Fin de la Memoria)*
