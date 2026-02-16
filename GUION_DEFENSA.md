# 🎤 Guion de Defensa - WoW Test Manager
## Proyecto Final IAW - Alejandro García Ripalda

**Duración Total**: 10 minutos  
**Estructura**: 12 slides + Demo en vivo

---

## 📌 CONSEJOS GENERALES ANTES DE EMPEZAR

### Preparación Mental
- ✅ **Respira profundo** antes de comenzar
- ✅ **Habla despacio y claro** - Tienes 10 minutos, no hay prisa
- ✅ **Mira al tribunal** - No leas las slides
- ✅ **Muestra confianza** - Conoces tu proyecto mejor que nadie

### Checklist Técnico Pre-Defensa
- [ ] XAMPP iniciado (Apache corriendo)
- [ ] Navegador abierto en `http://localhost/my_web/auth/wow_login.php`
- [ ] Presentación HTML abierta en otra pestaña
- [ ] Base de datos con datos de ejemplo cargados
- [ ] Credenciales a mano: `admin` / `admin123`

---

## 🎬 SLIDE 1: PORTADA (30 segundos)

### Qué Mostrar
- Logo de WoW
- Título del proyecto
- Tu nombre y datos

### Qué Decir

> "Buenos días/tardes. Mi nombre es Alejandro García Ripalda y voy a presentar mi proyecto final de Implantación de Aplicaciones Web: **WoW Test Manager**, un sistema completo de gestión de calidad QA con temática de World of Warcraft."

> "El proyecto está desarrollado íntegramente en **PHP puro**, sin frameworks, utilizando **SQLite** como base de datos, y cumple con todos los requisitos establecidos en la asignatura."

### Puntos Clave a Recordar
- ✅ Proyecto completo y funcional
- ✅ PHP vanilla (sin Laravel/Symfony)
- ✅ Temática WoW coherente en todo el sistema

### Transición
> "Empecemos viendo el problema que resuelve esta aplicación..."

**⏱️ Tiempo acumulado: 0:30**

---

## 🎬 SLIDE 2: PROBLEMA Y SOLUCIÓN (1 minuto)

### Qué Mostrar
- Tarjeta "Situación Actual" vs "Nuestra Solución"

### Qué Decir

> "El problema que identifiqué es común en equipos de testing: **datos dispersos en hojas de cálculo**, sin control de acceso, reportes manuales que consumen mucho tiempo, y errores constantes en la transcripción de datos."

> "Mi solución es **WoW Test Manager**: una aplicación web que centraliza todos los datos en una base de datos SQLite, implementa un sistema de roles y permisos granulares, y permite exportar reportes automáticamente en CSV y PDF. Además, se integra con la API oficial de Blizzard para sincronizar datos de personajes reales."

### Puntos Técnicos Clave
- **Problema real**: Gestión manual ineficiente
- **Solución técnica**: Base de datos centralizada + API externa
- **Valor añadido**: Automatización y reducción de errores

### Posibles Preguntas
**P: ¿Por qué elegiste este tema?**  
R: "Soy jugador de WoW y conozco de primera mano cómo los equipos de raid llevan sus registros en Excel. Vi una oportunidad de aplicar mis conocimientos de desarrollo web a un problema real que me apasiona."

**⏱️ Tiempo acumulado: 1:30**

---

## 🎬 SLIDE 3: STACK TECNOLÓGICO (1 minuto 30 segundos)

### Qué Mostrar
- Badges de tecnologías: PHP, SQLite, HTML5, CSS3, JavaScript ES6, Chart.js, TCPDF, Blizzard API

### Qué Decir

> "El stack tecnológico está completamente basado en **tecnologías nativas y robustas**, sin frameworks pesados."

> "En el **backend** utilizo **PHP 8.2.12** puro, sin Laravel ni Symfony, para demostrar dominio del lenguaje base. La base de datos es **SQLite 3**, gestionada mediante PDO con sentencias preparadas para máxima seguridad."

> "En el **frontend**, uso **HTML5 semántico**, **CSS3 nativo** con Grid y Flexbox para el diseño responsive, y **JavaScript ES6** para la interactividad. Para los gráficos estadísticos integré **Chart.js**, y para la generación de PDFs profesionales uso **TCPDF** instalado vía Composer."

> "Finalmente, me integro con la **API oficial de Blizzard** usando cURL nativo de PHP para sincronizar datos reales de personajes de World of Warcraft."

### Puntos Técnicos Clave
- ✅ **PHP Vanilla**: Sin frameworks, dominio del lenguaje
- ✅ **SQLite + PDO**: Portabilidad y seguridad
- ✅ **HTML5/CSS3/JS ES6**: Tecnologías estándar modernas
- ✅ **Chart.js**: Visualización de datos
- ✅ **TCPDF**: Generación de documentos profesionales
- ✅ **Blizzard API**: Integración con API externa real

### Posibles Preguntas
**P: ¿Por qué no usaste Laravel?**  
R: "El objetivo académico era demostrar dominio de PHP puro. Laravel abstrae mucha complejidad, pero quería mostrar que entiendo cómo funciona PHP a bajo nivel: sesiones, PDO, validaciones nativas, etc."

**P: ¿Por qué SQLite y no MySQL?**  
R: "SQLite es perfecto para este proyecto por su portabilidad y simplicidad de despliegue. No requiere servidor de base de datos separado, y para una aplicación de este tamaño es más que suficiente. Además, PDO me permite cambiar a MySQL en el futuro con cambios mínimos."

**⏱️ Tiempo acumulado: 3:00**

---

## 🎬 SLIDE 4: FUNCIONALIDADES CORE (1 minuto 30 segundos)

### Qué Mostrar
- 4 tarjetas: Autenticación, Dashboard, CRUD Completo, Reportes

### Qué Decir

> "Las funcionalidades principales del sistema son cuatro:"

> "**1. Autenticación segura**: Sistema de login con protección CSRF, roles jerárquicos (viewer, tester, admin), y protección anti-fuerza bruta con límite de 5 intentos y bloqueo de 15 minutos."

> "**2. Dashboard interactivo**: Muestra KPIs en tiempo real como número de testers activos, contenidos, sesiones y score global. Incluye gráficos dinámicos con Chart.js que visualizan el rendimiento por tipo de contenido y distribución de dificultad."

> "**3. CRUD completo**: Gestión total de todas las entidades: Testers, Contenido, Sesiones de prueba y Usuarios. Cada módulo incluye paginación, búsqueda y validación de datos."

> "**4. Sistema de reportes**: Exportación automática a CSV para análisis en Excel, y generación de PDFs profesionales con diseño corporativo usando TCPDF."

### Puntos Técnicos Clave
- **Autenticación**: `password_verify()`, sesiones PHP, tokens CSRF
- **Dashboard**: Consultas SQL optimizadas con JOINs, Chart.js
- **CRUD**: Sentencias preparadas PDO, validaciones `filter_var()`
- **Reportes**: `fputcsv()` para CSV, TCPDF para PDF

### Demostración Rápida (si hay tiempo)
> "Les puedo mostrar rápidamente el dashboard..." [Cambiar a navegador, mostrar 10 segundos]

**⏱️ Tiempo acumulado: 4:30**

---

## 🎬 SLIDE 5: FUNCIONALIDADES AVANZADAS (1 minuto 30 segundos)

### Qué Mostrar
- Lista de 3 funcionalidades avanzadas: Blizzard API, PDFs, Auditoría

### Qué Decir

> "Además de las funcionalidades básicas, implementé tres características avanzadas que van más allá de los requisitos mínimos:"

> "**1. Integración con Blizzard API**: Implementé desde cero el flujo de autenticación OAuth 2.0 con la API oficial de Battle.net. Esto me permite sincronizar automáticamente datos reales de personajes: nivel, clase, item level y facción. Incluye un sistema de caché inteligente con TTL configurable para minimizar peticiones a la API."

> "**2. Generación de PDFs profesionales**: No es una simple impresión de pantalla. Usando TCPDF, construyo documentos vectoriales con cabeceras personalizadas, tablas maquetadas y diseño corporativo que mantiene la estética de World of Warcraft."

> "**3. Sistema de auditoría completo**: Registro automático de todas las acciones administrativas en una tabla inmutable. Cada registro guarda quién hizo qué, cuándo y desde qué IP, proporcionando trazabilidad completa del sistema."

### Puntos Técnicos Clave
- **OAuth 2.0**: Implementación manual con cURL, gestión de tokens, manejo de errores HTTP
- **TCPDF**: Extensión de clase base, personalización de headers/footers
- **Auditoría**: Tabla `audit_log` con índices, registro automático en cada POST

### Posibles Preguntas
**P: ¿Cómo funciona OAuth 2.0 con Blizzard?**  
R: "Uso el flujo 'Client Credentials'. Primero envío mis credenciales (Client ID y Secret) al endpoint de OAuth de Blizzard. Recibo un access token que expira en 24 horas. Guardo ese token y lo uso en las peticiones a la API con el header 'Authorization: Bearer'. Implementé caché para no pedir el token en cada petición."

**⏱️ Tiempo acumulado: 6:00**

---

## 🎬 SLIDE 6: SEGURIDAD (1 minuto)

### Qué Mostrar
- Grid de 6 medidas de seguridad: SQL Injection, XSS, CSRF, Roles, Fuerza Bruta, Auditoría

### Qué Decir

> "La seguridad fue un pilar fundamental del desarrollo. Implementé un enfoque de 'Defense in Depth' con múltiples capas:"

> "**SQL Injection**: Mitigada al 100% usando exclusivamente sentencias preparadas con PDO. Nunca concateno variables en las consultas."

> "**XSS**: Todas las salidas de datos a HTML se escapan con `htmlspecialchars()` con flags ENT_QUOTES y UTF-8."

> "**CSRF**: Genero tokens criptográficos únicos por sesión que deben acompañar a cada petición POST."

> "**Control de roles**: Sistema jerárquico que verifica permisos en cada página antes de ejecutar cualquier lógica."

> "**Anti-fuerza bruta**: Límite de 5 intentos fallidos con bloqueo temporal de 15 minutos."

> "**Auditoría**: Logs inmutables de todas las acciones administrativas."

> "Todo esto usando **validaciones nativas de PHP** con `filter_var()`, sin dependencias externas."

### Puntos Técnicos Clave
- **PDO Prepared Statements**: `$stmt->execute([$param])`
- **htmlspecialchars()**: `ENT_QUOTES`, `UTF-8`
- **CSRF tokens**: `bin2hex(random_bytes(32))`
- **filter_var()**: `FILTER_VALIDATE_EMAIL`, `FILTER_VALIDATE_INT`, etc.

**⏱️ Tiempo acumulado: 7:00**

---

## 🎬 SLIDE 7: ARQUITECTURA (45 segundos)

### Qué Mostrar
- 2 tarjetas: Patrón MVC, Base de Datos Relacional

### Qué Decir

> "La arquitectura sigue un **patrón MVC simplificado**:"

> "La carpeta `/admin` contiene los controladores y vistas, `/includes` tiene la lógica de negocio como el sistema de autenticación, `/database` almacena el modelo de datos, y `/assets` los recursos estáticos."

> "La base de datos relacional consta de **5 tablas principales**: `usuarios` para credenciales y roles, `tester` para los probadores con datos de Blizzard, `content` para el catálogo de pruebas, `test_session` que es la tabla central de actividad QA, y `audit_log` para trazabilidad."

### Puntos Técnicos Clave
- **MVC**: Separación de responsabilidades
- **5 tablas**: Relaciones con Foreign Keys
- **Normalización**: Sin redundancia de datos

**⏱️ Tiempo acumulado: 7:45**

---

## 🎬 SLIDE 8: MÉTRICAS DEL PROYECTO (30 segundos)

### Qué Mostrar
- Estadísticas: 20+ archivos PHP, ~5,000 líneas, 5 tablas BD, 10+ funciones seguridad
- Checklist de funcionalidades completadas

### Qué Decir

> "En números, el proyecto incluye **más de 20 archivos PHP**, aproximadamente **5,000 líneas de código**, **5 tablas** en la base de datos, y **más de 10 funciones** dedicadas exclusivamente a seguridad."

> "Todas las funcionalidades planificadas están completadas: autenticación, CRUD completo, dashboard con KPIs, exportación CSV y PDF, integración con Blizzard API, sistema de auditoría, validaciones nativas, diseño responsive y música ambiente opcional."

**⏱️ Tiempo acumulado: 8:15**

---

## 🎬 SLIDE 9: DESAFÍOS TÉCNICOS (45 segundos)

### Qué Mostrar
- 4 tarjetas: OAuth 2.0, Manejo de Tiempos, Maquetación PDF, Seguridad Multicapa

### Qué Decir

> "Durante el desarrollo superé varios desafíos técnicos importantes:"

> "**OAuth 2.0**: Implementar desde cero el flujo de autenticación con Blizzard sin librerías de terceros fue complejo. Tuve que gestionar manualmente el ciclo de vida del token y el manejo de errores HTTP."

> "**Manejo de tiempos**: SQLite no tiene tipos de fecha robustos. Diseñé un sistema de conversión en PHP que transforma horas y minutos del formulario a formato estandarizado en texto."

> "**Maquetación PDF**: Trasladar el diseño visual de la web a PDF con TCPDF requirió trabajo minucioso de posicionamiento de celdas y ajuste de fuentes."

> "**Seguridad multicapa**: Implementar todas las medidas de seguridad sin frameworks, usando solo funciones nativas de PHP, fue un reto que me obligó a entender profundamente cada vulnerabilidad."

**⏱️ Tiempo acumulado: 9:00**

---

## 🎬 SLIDE 10: DEMO VISUAL (30 segundos)

### Qué Mostrar
- Screenshot del dashboard completo

### Qué Decir

> "Aquí pueden ver la interfaz de usuario con el diseño temático coherente con World of Warcraft. Paleta de colores dorados y rojos, tipografías personalizadas, y un diseño que mantiene la estética del juego en todas las páginas."

### Demostración en Vivo (OPCIONAL - solo si hay tiempo)
> "Si quieren, puedo hacer una demostración rápida en vivo..." 

**Si dicen que sí:**
1. Cambiar a navegador
2. Login con admin/admin123 (5 seg)
3. Mostrar dashboard con gráficos (5 seg)
4. Ir a Testers, mostrar tabla (3 seg)
5. Volver a presentación

**⏱️ Tiempo acumulado: 9:30**

---

## 🎬 SLIDE 11: CONCLUSIONES (20 segundos)

### Qué Mostrar
- Lista de logros principales

### Qué Decir

> "En conclusión, **WoW Test Manager** es un proyecto completo y funcional que cumple todos los requisitos académicos. Demuestra dominio de PHP nativo sin frameworks, integración exitosa con una API externa real, implementación de buenas prácticas de seguridad, y un diseño profesional coherente con la temática."

> "Como dice el lema de la Horda: **'Lok'tar Ogar! - Victoria o Muerte'**"

**⏱️ Tiempo acumulado: 9:50**

---

## 🎬 SLIDE 12: GRACIAS (10 segundos)

### Qué Mostrar
- Logo WoW, "¡Gracias!", "¿Preguntas?"
- GitHub y email

### Qué Decir

> "Muchas gracias por su atención. Estoy listo para responder cualquier pregunta que tengan."

**⏱️ Tiempo acumulado: 10:00**

---

## 🔥 SECCIÓN DE PREGUNTAS - RESPUESTAS PREPARADAS

### Preguntas Técnicas Probables

#### 1. "¿Por qué PHP vanilla y no un framework?"
**Respuesta:**
> "El objetivo académico era demostrar dominio del lenguaje base. Los frameworks como Laravel abstraen mucha complejidad, pero yo quería mostrar que entiendo cómo funcionan las sesiones, PDO, validaciones, y seguridad a bajo nivel. Además, esto me ha dado un conocimiento más profundo que me facilitará aprender cualquier framework en el futuro."

#### 2. "¿Cómo garantizas la seguridad contra SQL Injection?"
**Respuesta:**
> "Uso exclusivamente sentencias preparadas con PDO. Nunca concateno variables directamente en las consultas SQL. Por ejemplo, en lugar de escribir `SELECT * FROM usuarios WHERE id = $id`, uso `$stmt = $db->prepare('SELECT * FROM usuarios WHERE id = ?')` y luego `$stmt->execute([$id])`. PDO se encarga de escapar y validar los parámetros automáticamente."

**Código de ejemplo para mostrar:**
```php
// MAL (vulnerable)
$query = "SELECT * FROM usuarios WHERE username = '$username'";

// BIEN (seguro)
$stmt = $db->prepare("SELECT * FROM usuarios WHERE username = ?");
$stmt->execute([$username]);
```

#### 3. "¿Cómo funciona el sistema de roles?"
**Respuesta:**
> "Implementé un sistema jerárquico con tres niveles: viewer (solo lectura), tester (puede registrar sesiones), y admin (control total). Cada nivel tiene un valor numérico, y en cada página verifico que el rol del usuario sea suficiente para acceder. Por ejemplo, para acceder a la gestión de usuarios, verifico que el rol sea 'admin' antes de ejecutar cualquier lógica."

**Código de ejemplo:**
```php
function verificarRol($rolRequerido) {
    $roles = ['viewer' => 1, 'tester' => 2, 'admin' => 3];
    $miRol = $_SESSION['user']['role'] ?? 'viewer';
    
    if (($roles[$miRol] ?? 0) < ($roles[$rolRequerido] ?? 0)) {
        die("<h1>Acceso Denegado</h1>");
    }
}
```

#### 4. "¿Qué pasa si la API de Blizzard está caída?"
**Respuesta:**
> "Implementé manejo robusto de errores. Si la API no responde o devuelve un error, el sistema muestra un mensaje claro al usuario y registra el error en logs. Además, tengo un sistema de caché que guarda las respuestas exitosas durante 1 hora, así que si la API falla temporalmente, los datos cacheados siguen disponibles. El sistema nunca se rompe, simplemente no puede sincronizar datos nuevos hasta que la API vuelva."

#### 5. "¿Cómo manejas las sesiones de usuario?"
**Respuesta:**
> "Uso sesiones nativas de PHP con configuración segura. Las cookies de sesión tienen flags HttpOnly para prevenir acceso desde JavaScript (protección XSS), y SameSite para prevenir CSRF. Además, regenero el ID de sesión después del login para prevenir session fixation. La sesión almacena el ID del usuario, su rol, y un token CSRF único."

#### 6. "¿Por qué elegiste SQLite sobre MySQL?"
**Respuesta:**
> "SQLite es perfecto para este proyecto por tres razones: 1) Portabilidad - es un solo archivo que puedo mover fácilmente, 2) Simplicidad - no requiere servidor de base de datos separado, ideal para desarrollo y despliegue rápido, y 3) Suficiencia - para el volumen de datos de este proyecto, SQLite es más que capaz. Además, uso PDO, así que migrar a MySQL en el futuro sería cambiar solo la cadena de conexión."

#### 7. "¿Cómo generas los PDFs?"
**Respuesta:**
> "Uso la librería TCPDF instalada vía Composer. Extiendo la clase base para personalizar las cabeceras y pies de página con el logo de WoW y colores corporativos. Luego construyo el contenido usando el método Cell() para crear tablas con bordes y colores de fondo. El resultado es un documento vectorial profesional, no una simple captura de pantalla."

#### 8. "¿Qué validaciones implementas en los formularios?"
**Respuesta:**
> "Uso las funciones nativas de PHP `filter_var()` para validaciones. Por ejemplo, `FILTER_VALIDATE_EMAIL` para emails, `FILTER_VALIDATE_INT` con rangos para números, `FILTER_VALIDATE_URL` para URLs. Para sanitización uso `htmlspecialchars()` con ENT_QUOTES y UTF-8. También valido en el lado del servidor aunque tenga validación en cliente, porque nunca se debe confiar en el navegador."

#### 9. "¿Cómo organizaste el código?"
**Respuesta:**
> "Sigo un patrón MVC simplificado. Los controladores y vistas están en `/admin`, la lógica de negocio reutilizable en `/includes` (como el sistema de autenticación), el modelo de datos en `/database`, y los recursos estáticos en `/assets`. Cada archivo tiene una responsabilidad clara, lo que hace el código mantenible y fácil de entender."

#### 10. "¿Qué harías diferente si empezaras de nuevo?"
**Respuesta:**
> "Probablemente implementaría un sistema de routing más robusto desde el principio, en lugar de tener archivos PHP individuales para cada página. También añadiría tests unitarios para las funciones críticas de seguridad y validación. Y quizás usaría un sistema de plantillas simple para evitar repetir código HTML en cada vista."

---

### Preguntas sobre Funcionalidades

#### "¿Puedes mostrar cómo funciona el sistema en vivo?"
**Respuesta:**
> "Por supuesto. Déjame hacer login..." [Proceder con demo]

**Demo en Vivo - Guion (2-3 minutos máximo):**

1. **Login** (15 seg)
   - Ir a `http://localhost/my_web/auth/wow_login.php`
   - Escribir: `admin` / `admin123`
   - "Aquí vemos el sistema de login con protección CSRF"
   - Click en "Entrar"

2. **Dashboard** (30 seg)
   - "Este es el dashboard con KPIs en tiempo real"
   - Señalar: "Testers activos, contenidos, sesiones, score promedio"
   - Scroll down: "Gráficos interactivos con Chart.js"
   - "Tabla de sesiones recientes con código de colores"

3. **Gestión de Testers** (30 seg)
   - Click en "Testers" en menú
   - "Aquí gestiono los probadores"
   - Mostrar tabla: "CRUD completo con paginación"
   - "Puedo crear, editar y eliminar testers"

4. **Sesiones** (30 seg)
   - Click en "Sesiones"
   - "Registro de sesiones de prueba"
   - Mostrar formulario: "Selecciono tester, contenido, dificultad, puntuación"
   - "El tiempo se guarda en formato legible"

5. **Reportes** (30 seg)
   - Click en "Reportes"
   - "Estadísticas avanzadas: mejores testers, contenido difícil"
   - "Puedo exportar a CSV o PDF profesional"
   - Click en "Descargar CSV" (si hay tiempo)

6. **Volver a presentación** (15 seg)
   - "Como ven, todo funciona perfectamente"
   - Volver a la presentación

---

## 📚 DATOS TÉCNICOS PARA MEMORIZAR

### Números Clave
- **20+** archivos PHP
- **~5,000** líneas de código
- **5** tablas en base de datos
- **10+** funciones de seguridad
- **10** páginas de administración
- **12** slides en presentación
- **10** minutos de defensa

### Tecnologías (en orden de importancia)
1. **PHP 8.2.12** - Lenguaje principal
2. **SQLite 3** - Base de datos
3. **PDO** - Abstracción de base de datos
4. **HTML5** - Estructura
5. **CSS3** - Estilos (Grid, Flexbox)
6. **JavaScript ES6** - Interactividad
7. **Chart.js** - Gráficos
8. **TCPDF** - PDFs
9. **cURL** - Peticiones HTTP
10. **Blizzard API** - Integración externa

### Medidas de Seguridad (7)
1. **SQL Injection** → Sentencias preparadas PDO
2. **XSS** → htmlspecialchars()
3. **CSRF** → Tokens únicos por sesión
4. **Roles** → Control de acceso granular
5. **Fuerza Bruta** → Límite 5 intentos + bloqueo 15 min
6. **Sesiones** → HttpOnly, SameSite
7. **Auditoría** → Logs inmutables

### Tablas de Base de Datos (5)
1. **usuarios** - Credenciales y roles
2. **tester** - Probadores + datos Blizzard
3. **content** - Catálogo de pruebas
4. **test_session** - Actividad QA (tabla central)
5. **audit_log** - Trazabilidad

---

## ✅ CHECKLIST FINAL PRE-DEFENSA

### Día Anterior
- [ ] Repasar este guion completo 2-3 veces
- [ ] Practicar la presentación en voz alta con cronómetro
- [ ] Verificar que XAMPP funciona correctamente
- [ ] Asegurar que la base de datos tiene datos de ejemplo
- [ ] Probar la demo en vivo al menos 2 veces
- [ ] Dormir bien (mínimo 7 horas)

### 1 Hora Antes
- [ ] Revisar puntos clave de cada slide
- [ ] Repasar respuestas a preguntas probables
- [ ] Hacer ejercicios de respiración
- [ ] Beber agua

### 15 Minutos Antes
- [ ] Iniciar XAMPP
- [ ] Abrir navegador en login page
- [ ] Abrir presentación HTML
- [ ] Verificar que todo funciona
- [ ] Respirar profundo 3 veces

### Durante la Defensa
- [ ] Hablar despacio y claro
- [ ] Mirar al tribunal
- [ ] No leer las slides
- [ ] Mostrar confianza
- [ ] Gesticular moderadamente
- [ ] Sonreír ocasionalmente
- [ ] Controlar el tiempo

---

## 🎯 MENSAJES CLAVE PARA REPETIR

Estos son los 5 mensajes que DEBES transmitir durante la defensa:

1. **"PHP puro sin frameworks"** - Demuestra dominio del lenguaje base
2. **"Seguridad multicapa"** - Defense in Depth con 7 medidas
3. **"Integración con API externa real"** - OAuth 2.0 con Blizzard
4. **"Proyecto completo y funcional"** - Cumple todos los requisitos
5. **"Diseño profesional coherente"** - Temática WoW en todo el sistema

---

## 💪 FRASES DE CONFIANZA

Si te pones nervioso, recuerda:

- ✅ "He trabajado duro en este proyecto"
- ✅ "Conozco cada línea de código"
- ✅ "He superado desafíos técnicos complejos"
- ✅ "Mi proyecto funciona perfectamente"
- ✅ "Estoy preparado para cualquier pregunta"

---

## 🚀 ¡MUCHA SUERTE!

**Recuerda**: Conoces tu proyecto mejor que nadie. Has hecho un trabajo excelente. Respira, habla claro, y demuestra todo lo que has aprendido.

**"Lok'tar Ogar! - Victoria o Muerte"** ⚔️
