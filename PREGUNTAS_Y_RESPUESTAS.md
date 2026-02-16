# 🎯 Batería de Preguntas y Respuestas - Defensa del Proyecto

## 📚 CATEGORÍA 1: DECISIONES TÉCNICAS

### P1: ¿Por qué elegiste PHP vanilla en lugar de un framework como Laravel?
**R:** "El objetivo académico era demostrar dominio del lenguaje base. Los frameworks abstraen mucha complejidad, pero quería mostrar que entiendo cómo funcionan las sesiones, PDO, validaciones y seguridad a bajo nivel. Esto me da un conocimiento más profundo que facilitará aprender cualquier framework en el futuro."

### P2: ¿Por qué SQLite y no MySQL?
**R:** "SQLite es perfecto para este proyecto por tres razones: 1) Portabilidad - es un solo archivo fácil de mover, 2) Simplicidad - no requiere servidor de BD separado, ideal para desarrollo rápido, y 3) Suficiencia - para el volumen de datos es más que capaz. Además, uso PDO, así que migrar a MySQL sería cambiar solo la cadena de conexión."

### P3: ¿Por qué World of Warcraft como temática?
**R:** "Soy jugador de WoW y conozco de primera mano cómo los equipos llevan sus registros en Excel. Vi una oportunidad de aplicar mis conocimientos a un problema real que me apasiona. Además, la temática me permitió crear un diseño coherente y profesional."

---

## 🔒 CATEGORÍA 2: SEGURIDAD

### P4: ¿Cómo proteges contra SQL Injection?
**R:** "Uso exclusivamente sentencias preparadas con PDO. Nunca concateno variables en las consultas. Por ejemplo, en lugar de `SELECT * FROM usuarios WHERE id = $id`, uso `$stmt = $db->prepare('SELECT * FROM usuarios WHERE id = ?')` y luego `$stmt->execute([$id])`. PDO escapa y valida automáticamente."

**Código de ejemplo:**
```php
// MAL (vulnerable)
$query = "SELECT * FROM usuarios WHERE username = '$username'";

// BIEN (seguro)
$stmt = $db->prepare("SELECT * FROM usuarios WHERE username = ?");
$stmt->execute([$username]);
```

### P5: ¿Qué es CSRF y cómo lo previenes?
**R:** "CSRF (Cross-Site Request Forgery) es cuando un atacante engaña al navegador del usuario para hacer peticiones no autorizadas. Lo prevengo generando un token único por sesión con `bin2hex(random_bytes(32))`. Este token se incluye en todos los formularios y se valida en el servidor antes de procesar cualquier POST."

### P6: ¿Cómo manejas XSS?
**R:** "Uso `htmlspecialchars()` con flags ENT_QUOTES y UTF-8 en todas las salidas de datos a HTML. Esto convierte caracteres especiales como `<`, `>`, `"` en entidades HTML, evitando que se ejecute código JavaScript malicioso."

### P7: ¿Cómo funciona tu sistema anti-fuerza bruta?
**R:** "Registro cada intento de login fallido en la sesión. Después de 5 intentos fallidos, bloqueo el acceso durante 15 minutos guardando un timestamp. Antes de permitir otro intento, verifico si han pasado los 15 minutos."

---

## 🏗️ CATEGORÍA 3: ARQUITECTURA Y CÓDIGO

### P8: ¿Cómo funciona el sistema de roles?
**R:** "Implementé un sistema jerárquico con tres niveles: viewer (1), tester (2), y admin (3). Cada página verifica que el rol del usuario sea suficiente antes de ejecutar lógica. Uso un array asociativo para mapear roles a valores numéricos y comparo."

**Código:**
```php
function verificarRol($rolRequerido) {
    $roles = ['viewer' => 1, 'tester' => 2, 'admin' => 3];
    $miRol = $_SESSION['user']['role'] ?? 'viewer';
    if (($roles[$miRol] ?? 0) < ($roles[$rolRequerido] ?? 0)) {
        die("<h1>Acceso Denegado</h1>");
    }
}
```

### P9: ¿Cómo organizaste el código?
**R:** "Sigo un patrón MVC simplificado: `/admin` tiene controladores y vistas, `/includes` la lógica reutilizable como autenticación, `/database` el modelo de datos, y `/assets` los recursos estáticos. Cada archivo tiene una responsabilidad clara."

### P10: ¿Qué validaciones implementas en formularios?
**R:** "Uso funciones nativas `filter_var()`: `FILTER_VALIDATE_EMAIL` para emails, `FILTER_VALIDATE_INT` con rangos para números, `FILTER_VALIDATE_URL` para URLs. Para sanitización uso `htmlspecialchars()`. Siempre valido en servidor aunque haya validación en cliente."

---

## 🗄️ CATEGORÍA 4: BASE DE DATOS

### P11: ¿Cuántas tablas tiene tu BD y cuáles son?
**R:** "Tengo 5 tablas: `usuarios` (credenciales y roles), `tester` (probadores con datos Blizzard), `content` (catálogo de pruebas), `test_session` (tabla central de actividad QA), y `audit_log` (trazabilidad de acciones)."

### P12: ¿Qué relaciones hay entre las tablas?
**R:** "La tabla `test_session` tiene dos Foreign Keys: una hacia `tester` y otra hacia `content`. Esto asegura integridad referencial - no puedo crear una sesión con un tester o contenido que no existe."

### P13: ¿Cómo manejas las consultas complejas?
**R:** "Uso JOINs para combinar datos de múltiples tablas. Por ejemplo, en el dashboard hago JOIN entre `test_session`, `tester` y `content` para mostrar sesiones recientes con nombres legibles en lugar de IDs."

---

## 🌐 CATEGORÍA 5: INTEGRACIÓN BLIZZARD API

### P14: ¿Cómo funciona OAuth 2.0 con Blizzard?
**R:** "Uso el flujo 'Client Credentials'. Envío mis credenciales (Client ID y Secret) al endpoint OAuth de Blizzard. Recibo un access token que expira en 24 horas. Guardo ese token y lo uso en peticiones con el header 'Authorization: Bearer'. Implementé caché para no pedir el token en cada petición."

### P15: ¿Qué pasa si la API de Blizzard está caída?
**R:** "Implementé manejo robusto de errores. Si la API no responde, muestro un mensaje claro al usuario y registro el error. Tengo caché de 1 hora, así que si la API falla temporalmente, los datos cacheados siguen disponibles. El sistema nunca se rompe."

### P16: ¿Qué datos sincronizas de la API?
**R:** "Sincronizo nivel del personaje, clase (con nombre localizado), item level (ilvl) y facción. Estos datos se guardan en la tabla `tester` y se actualizan cuando el admin pulsa 'Sincronizar'."

---

## 📄 CATEGORÍA 6: GENERACIÓN DE REPORTES

### P17: ¿Cómo generas los PDFs?
**R:** "Uso TCPDF instalada vía Composer. Extiendo la clase base para personalizar headers y footers con el logo de WoW. Construyo el contenido usando `Cell()` para crear tablas con bordes y colores. El resultado es un documento vectorial profesional."

### P18: ¿Cómo funciona la exportación CSV?
**R:** "Uso `fputcsv()` nativo de PHP. Abro un stream de salida con `php://output`, establezco headers HTTP para descarga, escribo la fila de cabeceras, y luego itero sobre los datos escribiendo cada fila. Es eficiente incluso con muchos datos."

---

## 💻 CATEGORÍA 7: FRONTEND Y UX

### P19: ¿Por qué no usaste Bootstrap o Tailwind?
**R:** "Quería demostrar dominio de CSS puro. Usé CSS Grid y Flexbox nativos para el layout responsive. Esto me dio control total sobre el diseño y me permitió crear una estética única coherente con WoW."

### P20: ¿Cómo hiciste los gráficos?
**R:** "Usé Chart.js, una librería JavaScript ligera. Consulto los datos desde PHP, los paso a JavaScript en formato JSON, y Chart.js los renderiza como gráficos interactivos de barras y dona."

---

## 🐛 CATEGORÍA 8: DESAFÍOS Y SOLUCIONES

### P21: ¿Cuál fue el mayor desafío técnico?
**R:** "Implementar OAuth 2.0 desde cero sin librerías. Tuve que entender el flujo completo: autenticación, gestión del ciclo de vida del token, renovación automática, y manejo de errores HTTP. Lo resolví creando una clase dedicada `BlizzardAPI` que encapsula toda la lógica."

### P22: ¿Cómo manejas los tiempos en SQLite?
**R:** "SQLite no tiene tipo de dato robusto para intervalos. Diseñé un sistema de conversión: el formulario envía horas y minutos separados, PHP los convierte a formato texto '2h 30m' para guardar, y al editar uso regex para extraer los valores y rellenar el formulario."

---

## 🔧 CATEGORÍA 9: MANTENIMIENTO Y ESCALABILIDAD

### P23: ¿Qué harías diferente si empezaras de nuevo?
**R:** "Implementaría un sistema de routing más robusto desde el principio, añadiría tests unitarios para funciones críticas de seguridad, y usaría un sistema de plantillas simple para evitar repetir HTML."

### P24: ¿Cómo escalarías este proyecto?
**R:** "Migraría a MySQL para mejor rendimiento con muchos usuarios concurrentes, implementaría un sistema de caché con Redis, separaría el frontend en una SPA con React, y añadiría una API REST para permitir integraciones externas."

### P25: ¿Cómo manejas los errores?
**R:** "Uso try-catch en operaciones críticas como conexión a BD y llamadas a API. Los errores se registran con `error_log()` y se muestran mensajes amigables al usuario. En producción desactivaría `display_errors` de PHP."

---

## 📊 CATEGORÍA 10: FUNCIONALIDADES ESPECÍFICAS

### P26: ¿Cómo funciona el sistema de auditoría?
**R:** "Cada vez que un admin hace una operación POST, registro automáticamente en `audit_log`: ID del usuario, módulo afectado, acción realizada, detalles, IP y timestamp. Esto proporciona trazabilidad completa e inmutable."

### P27: ¿Cómo implementaste la paginación?
**R:** "Uso LIMIT y OFFSET en las consultas SQL. Calculo el offset multiplicando la página actual por el tamaño de página. Muestro enlaces de navegación calculando el total de páginas dividiendo el total de registros entre el tamaño de página."

### P28: ¿Cómo funciona la búsqueda?
**R:** "Uso LIKE en SQL con wildcards. Sanitizo el input del usuario y construyo la consulta con sentencias preparadas: `WHERE name LIKE ?` y paso `'%' . $busqueda . '%'` como parámetro."

---

## 🎨 CATEGORÍA 11: DISEÑO

### P29: ¿Cómo elegiste la paleta de colores?
**R:** "Usé los colores oficiales de WoW: dorado (#ffd100) para elementos importantes, rojo épico (#a31414) para acciones críticas, azul raro (#0070dd) para highlights, y fondos oscuros (#1a1a1a) para el tema general."

### P30: ¿Es responsive el diseño?
**R:** "Sí, uso media queries en CSS y diseño mobile-first con Flexbox y Grid. Los elementos se reorganizan automáticamente en pantallas pequeñas. Las tablas tienen scroll horizontal en móvil."

---

## 🚀 CATEGORÍA 12: DESPLIEGUE Y CONFIGURACIÓN

### P31: ¿Cómo se instala tu proyecto?
**R:** "Copiar a htdocs, ejecutar los scripts de setup para crear la BD (`crear_bd_wow.php`, `insertar_datos_wow.php`), instalar dependencias con `composer install`, configurar credenciales de Blizzard API si se quiere esa funcionalidad, e iniciar Apache."

### P32: ¿Qué requisitos tiene?
**R:** "PHP 7.4+, Apache 2.4+, extensiones PHP: pdo_sqlite, curl, json. Composer para TCPDF. Todo incluido en XAMPP estándar."

---

## 💡 PREGUNTAS TRAMPA

### P33: ¿Tu código tiene bugs?
**R:** "He probado exhaustivamente todas las funcionalidades y no he encontrado bugs críticos. Como cualquier software, podría tener edge cases no contemplados, pero las funcionalidades principales funcionan correctamente y están validadas."

### P34: ¿Copiaste código de internet?
**R:** "Consulté documentación oficial de PHP, ejemplos de PDO y Chart.js, pero todo el código está escrito por mí y adaptado a las necesidades específicas del proyecto. Entiendo cada línea y puedo explicar cualquier parte."

### P35: ¿Por qué no hay tests unitarios?
**R:** "Por limitaciones de tiempo me enfoqué en implementar todas las funcionalidades requeridas. En un entorno profesional, implementaría PHPUnit para testear funciones críticas de validación y seguridad."

---

## 📝 DATOS CLAVE PARA MEMORIZAR

- **20+** archivos PHP
- **~5,000** líneas de código
- **5** tablas de BD
- **10+** funciones de seguridad
- **7** medidas de seguridad implementadas
- **10** páginas de administración
- **PHP 8.2.12** + **SQLite 3**

---

## ✅ CONSEJOS PARA RESPONDER

1. **Sé honesto** - Si no sabes algo, admítelo
2. **Sé específico** - Da ejemplos de código cuando sea posible
3. **Sé conciso** - Responde directo, no divagues
4. **Muestra confianza** - Conoces tu proyecto
5. **Relaciona con la teoría** - Conecta con conceptos de clase

**¡Buena suerte!** 🚀
