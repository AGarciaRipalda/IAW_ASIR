# 📦 ENTREGA PROYECTO IAW - WoW Test Manager

**Alumno**: Alejandro García Ripalda  
**Asignatura**: Implantación de Aplicaciones Web (IAW)  
**Fecha**: Febrero 2026

---

## 📋 Contenido de la Entrega

Este paquete contiene todo lo necesario para desplegar y evaluar el proyecto **WoW Test Manager**.

### 📁 Estructura de Carpetas

```
ENTREGA_PROYECTO_IAW/
│
├── 1_PROYECTO_WEB/          ← CÓDIGO FUENTE COMPLETO
│   ├── admin/               # Panel de administración (10 módulos)
│   ├── auth/                # Sistema de autenticación
│   ├── includes/            # Configuración y API Blizzard
│   ├── assets/              # CSS, JS, imágenes
│   ├── setup/               # Scripts de instalación BD
│   ├── composer.json        # Dependencias PHP
│   └── README.md            # Documentación del proyecto
│
├── 2_DOCUMENTACION/         ← MEMORIA Y DOCUMENTACIÓN
│   ├── MEMORIA_PROYECTO.md  # Memoria oficial del proyecto
│   ├── DOCUMENTACION_TECNICA_COMPLETA.md  # Documentación técnica
│   ├── presentacion.html    # Presentación del proyecto
│   └── screenshots/         # Capturas de pantalla
│
├── 3_INSTALACION/           ← GUÍA DE INSTALACIÓN
│   └── GUIA_INSTALACION.md  # Instrucciones paso a paso
│
└── LEEME.md                 ← ESTE ARCHIVO
```

---

## 🚀 Inicio Rápido (5 minutos)

### Opción 1: Instalación Rápida con PHP Built-in Server

```bash
# 1. Ir a la carpeta del proyecto
cd 1_PROYECTO_WEB

# 2. Instalar dependencias
composer install

# 3. Crear base de datos
php setup/crear_bd_wow.php
php setup/insertar_datos_wow.php
php setup/crear_tabla_audit.php

# 4. Iniciar servidor
php -S localhost:8000

# 5. Abrir navegador
# http://localhost:8000/auth/wow_login.php
```

### Opción 2: Instalación en XAMPP/WAMP

```bash
# 1. Copiar carpeta a htdocs
xcopy "1_PROYECTO_WEB" "C:\xampp\htdocs\wow_test_manager" /E /I

# 2. Abrir terminal en la carpeta
cd C:\xampp\htdocs\wow_test_manager

# 3. Instalar dependencias y crear BD
composer install
php setup/crear_bd_wow.php
php setup/insertar_datos_wow.php
php setup/crear_tabla_audit.php

# 4. Iniciar Apache desde XAMPP
# Acceder a: http://localhost/wow_test_manager/auth/wow_login.php
```

---

## 🔐 Credenciales de Acceso

| Usuario | Contraseña | Rol | Permisos |
|---------|------------|-----|----------|
| `admin` | `admin123` | Administrador | Acceso total |
| `manager` | `manager123` | Manager | Gestión de testers/reportes |
| `tester` | `tester123` | Tester | Acceso limitado |

---

## ✅ Requisitos del Sistema

- **PHP**: 7.4+ (recomendado 8.0+)
- **Servidor Web**: Apache 2.4+ o PHP Built-in Server
- **Base de Datos**: SQLite 3 (incluida con PHP)
- **Extensiones PHP**: `pdo_sqlite`, `curl`, `json`, `mbstring`

### Verificar Requisitos

```bash
php -v                    # Versión de PHP
php -m | grep pdo_sqlite  # Extensión SQLite
php -m | grep curl        # Extensión cURL
```

---

## 📖 Documentación Completa

Para instrucciones detalladas de instalación, consulte:

**📄 `3_INSTALACION/GUIA_INSTALACION.md`**

Esta guía incluye:
- ✅ Instalación paso a paso (local y producción)
- ✅ Solución de problemas comunes
- ✅ Configuración de Virtual Host
- ✅ Integración con Blizzard API
- ✅ Checklist de verificación

---

## 🎯 Características Principales del Proyecto

### Funcionalidades Implementadas

- ✅ **Sistema de Autenticación**: Login seguro con roles jerárquicos
- ✅ **Dashboard Interactivo**: KPIs en tiempo real con Chart.js
- ✅ **Gestión Completa (CRUD)**: Testers, Sesiones, Contenido, Reportes
- ✅ **Exportación Profesional**: CSV y PDF con TCPDF
- ✅ **Integración API Externa**: Blizzard Battle.net API
- ✅ **Sistema de Auditoría**: Logs automáticos de todas las acciones
- ✅ **Diseño Temático**: Interfaz inspirada en World of Warcraft

### Medidas de Seguridad

- 🔒 Protección CSRF en todos los formularios
- 🔒 Sentencias preparadas PDO (anti SQL Injection)
- 🔒 Sanitización XSS con `htmlspecialchars()`
- 🔒 Sistema de roles y permisos
- 🔒 Logs de auditoría automáticos
- 🔒 Protección anti-fuerza bruta

---

## 🧪 Pruebas Sugeridas para Evaluación

### 1. Autenticación y Seguridad
- [ ] Login con diferentes roles (admin, manager, tester)
- [ ] Verificar restricciones de permisos por rol
- [ ] Intentar acceso directo a URLs protegidas sin login

### 2. Funcionalidad CRUD
- [ ] Crear, editar y eliminar un tester
- [ ] Crear una nueva sesión de prueba
- [ ] Gestionar contenido y reportes

### 3. Dashboard y Reportes
- [ ] Verificar que los KPIs se calculan correctamente
- [ ] Generar reporte en formato CSV
- [ ] Generar reporte en formato PDF

### 4. Integración API (Opcional)
- [ ] Configurar credenciales de Blizzard API
- [ ] Sincronizar personajes de WoW

### 5. Auditoría
- [ ] Verificar que las acciones se registran en `audit_log`
- [ ] Revisar logs de creación/edición/eliminación

---

## 📊 Tecnologías Utilizadas

| Categoría | Tecnología |
|-----------|------------|
| **Backend** | PHP 8.2.12 con PDO |
| **Base de Datos** | SQLite 3 |
| **Frontend** | HTML5, CSS3, JavaScript |
| **Visualización** | Chart.js |
| **Exportación** | TCPDF |
| **API Externa** | Blizzard Battle.net API con cURL |
| **Dependencias** | Composer |

---

## 🔧 Solución Rápida de Problemas

### ❌ Error: "Base de datos no encontrada"
```bash
php setup/crear_bd_wow.php
php setup/insertar_datos_wow.php
```

### ❌ Error: "Class 'TCPDF' not found"
```bash
composer install
```

### ❌ Error: "Permission denied" en database/
```bash
# Linux/Mac
chmod -R 777 database/

# Windows: Dar permisos de escritura a la carpeta database/
```

### ❌ La página muestra código PHP
- Asegúrese de acceder vía `http://localhost` (no `file://`)
- Verifique que Apache tenga el módulo PHP cargado

---

## 📚 Documentos Incluidos

### Memoria y Documentación

1. **MEMORIA_PROYECTO.md** (2_DOCUMENTACION/)
   - Descripción del proyecto
   - Objetivos y alcance
   - Conclusiones

2. **DOCUMENTACION_TECNICA_COMPLETA.md** (2_DOCUMENTACION/)
   - Arquitectura del sistema
   - Estructura de base de datos
   - Código de ejemplo
   - Medidas de seguridad

3. **presentacion.html** (2_DOCUMENTACION/)
   - Presentación visual del proyecto
   - Capturas de pantalla
   - Demostración de funcionalidades

### Capturas de Pantalla

Ubicación: `2_DOCUMENTACION/screenshots/`

- Dashboard completo
- Gestión de testers
- Gestión de sesiones
- Generación de reportes
- Integración con Blizzard API

---

## 🎓 Notas para el Evaluador

### Puntos Destacados

1. **Código Limpio y Documentado**: 
   - Comentarios en español
   - Estructura modular
   - Separación de responsabilidades

2. **Seguridad Robusta**:
   - Múltiples capas de protección
   - Validación en cliente y servidor
   - Sistema de auditoría completo

3. **Experiencia de Usuario**:
   - Interfaz intuitiva y temática
   - Feedback visual en todas las acciones
   - Diseño responsive

4. **Integración con API Externa**:
   - Implementación completa de OAuth2
   - Manejo de errores robusto
   - Caché de tokens

### Criterios de Evaluación Cubiertos

- ✅ Instalación y configuración
- ✅ Funcionalidad completa del sistema
- ✅ Operaciones CRUD
- ✅ Generación de reportes
- ✅ Integración con API externa
- ✅ Medidas de seguridad
- ✅ Calidad del código
- ✅ Documentación completa
- ✅ Diseño y UX

---

## 📞 Contacto

**Alumno**: Alejandro García Ripalda  
**Asignatura**: IAW - ASIR 2º  
**Fecha de Entrega**: Febrero 2026

---

## 🎮 ¡Gracias por Evaluar!

Este proyecto representa el trabajo realizado durante el módulo de Implantación de Aplicaciones Web. Espero que la instalación sea sencilla y la evaluación satisfactoria.

**For the Horde!** 🔥

---

**Última actualización**: 16 de febrero de 2026
