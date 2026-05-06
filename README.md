# Simulador de Latencia Operativa Organizacional

## Descripción del Proyecto

El **Simulador de Latencia Operativa Organizacional** es una Prueba de Concepto (PoC) interactiva y visual que demuestra cómo las estructuras organizacionales impactan el Time-to-Market de proyectos tecnológicos. El simulador compara dos modelos contrastantes:

1. **Modelo Tradicional (Silos Departamentales)**: Estructura jerárquica con múltiples puntos de aprobación, representando la burocracia organizacional típica de empresas de mediano/gran tamaño.
2. **Modelo Startup (Squads Ágiles)**: Estructura matricial con decisiones rápidas en equipo multidisciplinario, representando la agilidad de startups y organizaciones transformadas digitalmente.

El sistema visualiza cómo interactúan los roles C-Level (**CIO**, **CTO**, **CISO**) y cómo cada estructura organizacional genera diferentes tiempos de espera, cuellos de botella y latencias operacionales en la cadena de aprobación de proyectos de TI.

## Objetivos de Aprendizaje

- Comprender el impacto de la estructura organizacional en la velocidad de innovación
- Visualizar la latencia operativa en procesos de aprobación
- Comparar modelos tradicionales vs. ágiles de forma empírica
- Destacar el rol crítico de la trazabilidad (CISO) en ambos modelos

---

## Requisitos de Sistema

- **Servidor Web**: Apache/Nginx con soporte para PHP
- **PHP**: Versión 7.4 o superior (recomendado 8.0+)
- **MySQL**: Versión 5.7 o superior
- **Navegador**: Moderno (Chrome, Firefox, Edge, Safari)
- **Entorno Recomendado**: XAMPP 8.x en Windows/Linux/macOS

### Requisitos Adicionales
- PDO PHP (generalmente incluido en XAMPP)
- Soporte para sesiones PHP (session_start)
- Bootstrap 5.3 (se carga vía CDN)

---

## Instrucciones de Instalación

### Paso 1: Preparar el Directorio

```bash
# Copiar el proyecto a la carpeta htdocs de XAMPP
cp -r simulador_latencia/ C:\xampp\htdocs\simulador_latencia\
# O en Linux/Mac:
cp -r simulador_latencia/ /Applications/XAMPP/htdocs/simulador_latencia/
```

### Paso 2: Crear la Base de Datos

1. Abrir **phpMyAdmin**: http://localhost/phpmyadmin/
2. Hacer clic en el botón **"Importar"** (Import)
3. Seleccionar el archivo `database.sql` del proyecto
4. Hacer clic en **"Continuar"** (Go)

### Paso 3: Verificar Credenciales de Conexión

Editar el archivo `config/conexion.php` y verificar las credenciales:

```php
define('DB_HOST', 'localhost');     // Host de MySQL
define('DB_USER', 'root');          // Usuario de MySQL (por defecto en XAMPP)
define('DB_PASS', '');              // Contraseña (vacía en XAMPP por defecto)
define('DB_NAME', 'simulador_latencia');
```

### Paso 4: Acceder al Sistema

1. Asegurar que Apache y MySQL estén ejecutándose en XAMPP
2. Abrir: **http://localhost/simulador_latencia/login.php**
3. Usar cualquiera de las credenciales de prueba (ver tabla abajo)

---

## Usuarios de Prueba

| Usuario | Contraseña | Rol | Descripción |
|---------|-----------|-----|-------------|
| `sergio_bautista` | `dev123` | DEV | Desarrollador - Puede crear proyectos |
| `rashell_fernandez` | `dev123` | DEV | Desarrollador (segundo) - Puede crear proyectos |
| `tania_pinto` | `cio123` | CIO | Chief IT Officer - Aprueba en flujo Tradicional |
| `cristian_velasco` | `cio123` | CIO | Chief IT Officer (segundo) - Aprueba en flujo Tradicional |
| `jhoseline_marca` | `cto123` | CTO | Chief Technology Officer - Aprueba en flujo Startup |
| `javier_murguia` | `ciso123` | CISO | Chief Information Security Officer - Acceso a auditoría |

---

## Flujos de Negocio

### Flujo Tradicional (Modelo en Silos)

```
DEV crea proyecto
        ↓
    [Pendiente_CIO]
        ↓
   CIO aprueba/rechaza
        ↓
    [Pendiente_CISO]
        ↓
   CISO aprueba/rechaza
        ↓
[Aprobado_Producción]
```

**Características:**
- 3 pasos de aprobación secuencial
- Requiere autorización de CIO y CISO
- Mayor latencia pero mayor control de seguridad
- Representa empresas grandes con silos departamentales

### Flujo Startup (Modelo Ágil)

```
DEV crea proyecto
        ↓
  [Revision_Squad]
        ↓
CTO (Squad Lead) aprueba
        ↓
[Aprobado_Producción]
```

**Características:**
- 2 pasos únicamente
- Decisión centralizada en CTO
- Menor latencia, decisiones ágiles
- Representa startups con equipos multidisciplinarios

---

## Funcionalidades Principales

### 1. Dashboard Administrativo
- **Estadísticas en tiempo real**: Total de proyectos, tiempo promedio por flujo
- **Visualización de pipeline**: Indicadores de progreso con animación de pulso
- **Gráfico comparativo**: Tiempos entre Tradicional vs Startup (Chart.js)
- **Proyectos en cards**: Información consolidada con estado, modelo, timestamps

### 2. Gestión de Proyectos (DEV)
- Crear proyectos con título, descripción y modelo organizacional
- Selección de flujo (Tradicional o Startup)
- Estado inicial automático según modelo
- Seguimiento completo desde creación hasta aprobación

### 3. Aprobaciones por Rol
- **CIO**: Aprueba transición `Pendiente_CIO → Pendiente_CISO`
- **CTO**: Aprueba transición `Revision_Squad → Aprobado_Producción`
- **CISO**: Aprueba transición `Pendiente_CISO → Aprobado_Producción`
- Validación de permisos con base en rol y estado actual

### 4. Auditoría Completa (CISO)
- Tabla detallada de todos los eventos del sistema
- Columnas: ID, Proyecto, Actor, Rol, Acción, Estados, IP, Timestamp
- Filtros por Proyecto, Rol, Rango de Fechas
- **Exportación a CSV** para análisis externo
- Color de fila por rol del actor para visualización rápida

### 5. Seguridad Implementada
- **Autenticación**: Password hashing con bcrypt (password_hash)
- **Autorización**: RBAC (Role-Based Access Control) por roles
- **CSRF Protection**: Token único por sesión
- **SQL Injection**: PDO con prepared statements (nunca concatenación)
- **XSS Prevention**: htmlspecialchars en toda salida HTML
- **Auditoría inmutable**: Tabla auditoria_flujo con registro de IP y timestamp
- **Headers de seguridad**: X-Frame-Options, X-Content-Type-Options, etc.

---

## Estructura de Directorios

```
simulador_latencia/
├── config/
│   └── conexion.php              # PDO Singleton
├── includes/
│   ├── auth_guard.php            # Middleware de autenticación
│   └── funciones.php             # Funciones helper reutilizables
├── assets/
│   └── css/
│       └── estilo.php            # Estilos CSS personalizados
├── login.php                      # Pantalla de login
├── logout.php                     # Cierre de sesión
├── dashboard.php                  # Panel principal
├── crear_proyecto.php             # Formulario de creación
├── procesar_accion.php            # Motor de transiciones de estado
├── ver_auditoria.php              # Log de trazabilidad (CISO)
├── database.sql                   # Script SQL completo
└── README.md                      # Este archivo
```

---

## Patrón de Diseño MVC-Light

La arquitectura implementa un patrón MVC ligero sin framework:

- **Models**: Funciones en `includes/funciones.php` para acceso a datos
- **Views**: Archivos `.php` en raíz (login, dashboard, etc.) con HTML/Bootstrap
- **Controllers**: Lógica de negocio en cada archivo (procesar_accion.php, etc.)
- **Config**: Singleton pattern en `config/conexion.php` para BD

---

## Notas de Seguridad

### Contraseñas de Prueba
⚠️ **IMPORTANTE**: Las contraseñas de prueba (`password123`) deben ser reemplazadas en producción:

```php
// Generar nueva contraseña hasheada (en línea de comandos PHP):
php -r "echo password_hash('nueva_contraseña', PASSWORD_BCRYPT);"
```

### Variables de Entorno
Para producción, usar variables de entorno en lugar de constantes hardcodeadas:

```php
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_USER', getenv('DB_USER') ?: 'root');
```

### SSL/HTTPS
Configurar redirección a HTTPS en producción:

```php
if (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] === 'off') {
    header('Location: https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
    exit;
}
```

---

## Resolución de Problemas

### Error: "Error de conexión a la base de datos"
- Verificar que MySQL está corriendo en XAMPP
- Confirmar credenciales en `config/conexion.php`
- Ejecutar `database.sql` nuevamente en phpMyAdmin

### Error: "No se puede deserializar un Singleton"
- Esto es normal - es una protección del patrón Singleton
- No afecta el funcionamiento del sistema

### Sesión expirada
- Las sesiones PHP tienen timeout (por defecto 24 minutos)
- Volver a iniciar sesión haciendo clic en "Cerrar Sesión"

### Permisos de archivo
En Linux/Mac, asegurar permisos correctos:
```bash
chmod -R 755 simulador_latencia/
chmod -R 777 simulador_latencia/assets/
```

---

## Sección Académica

### Párrafo 1 - Explicación Técnica

El patrón MVC-light implementado en PHP puro modela computacionalmente los flujos de aprobación mediante un motor de estados finitos en `procesar_accion.php`. La arquitectura utiliza el patrón **Singleton** en `config/conexion.php` para garantizar una única instancia de conexión PDO, eliminando overhead de conexiones redundantes. Las transacciones ACID en PDO (`beginTransaction`/`commit`/`rollback`) aseguran que cada transición de estado es atómica: la actualización del proyecto y su registro en auditoría ocurren conjuntamente o ninguno. El control de acceso implementa **RBAC** (Role-Based Access Control) mediante la función `verificar_sesion()` que valida tanto existencia de sesión como permisos de rol. Los **prepared statements** previenen inyección SQL al separar estructura de datos. Finalmente, la tabla `auditoria_flujo` implementa un **Event Log inmutable** que registra cada transición con IP, timestamp y usuario, garantizando trazabilidad completa y cumplimiento regulatorio en sistemas críticos.

### Párrafo 2 - Teoría O&M y Transformación Digital

Este simulador conecta directamente con la **Teoría de Silos de Beer** (Modelo de Sistema Viable), que postula que las estructuras rígidas en silos generan ineficiencia operacional al bloquear flujos de información. El concepto de **latencia operativa** (delay entre decisión y ejecución) es el enemigo del **Time-to-Market (TTM)**: empresas con flujos lentos pierden ventaja competitiva en mercados dinámicos. La metodología **Squad de Spotify** emerge como antítesis directa, eliminando silos departamentales en favor de equipos multidisciplinarios con autonomía para decidir. Según **Westerman (MIT)**, la **Transformación Digital** no es adopción de herramientas, sino **reorganización de procesos** apoyada en tecnología: cambiar de Tradicional a Startup requiere rediseño arquitectónico, no solo comprar software. El **CISO** juega rol dual crítico: en Tradicional, es guardián final de seguridad que ralentiza (necesariamente); en Startup, participa como parte del Squad manteniendo trazabilidad sin crear cuello de botella. Este simulador visualiza empíricamente que **agilidad organizacional genera ventaja competitiva**, pero requiere cambio cultural profundo más allá de procesos técnicos.

---

## Licencia y Autoría

- **Autor**: Arquitecto de Software Junior - Cristian J. Velasco Conde
- **Fecha**: Mayo 2026
- **Propósito**: Prueba de Concepto y comparacion
- **Licencia**: Código abierto para fines educativos

---

## Contacto y Soporte

Para preguntas o problemas específicos del simulador, revisar los logs de PHP:

```bash
# En XAMPP, logs típicamente en:
C:\xampp\php\logs\php_error_log
# O en Linux:
/var/log/apache2/error.log
```

---

**Última actualización**: Mayo 5, 2026
