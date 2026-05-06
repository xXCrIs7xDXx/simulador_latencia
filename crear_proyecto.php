<?php
/**
 * ============================================================
 * CREAR PROYECTO
 * ============================================================
 * Nombre: crear_proyecto.php
 * Propósito: Formulario para crear nuevos proyectos (solo DEV)
 * Autor: Arquitecto Senior PHP/MySQL
 * Fecha: Mayo 2026
 * ============================================================
 */

session_start();
require_once __DIR__ . '/config/conexion.php';
require_once __DIR__ . '/includes/auth_guard.php';
require_once __DIR__ . '/includes/funciones.php';

// Solo DEV puede crear proyectos
verificar_sesion(['DEV']);

$usuario_id = $_SESSION['usuario_id'];
$usuario_nombre = $_SESSION['usuario_nombre'];
$username = $_SESSION['username'];
$rol = $_SESSION['rol'];

// Generar CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$mensaje = '';
$tipo_mensaje = 'info';

// Procesar creación de proyecto
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validar CSRF token
    if (!hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'] ?? '')) {
        $mensaje = 'Token de seguridad inválido.';
        $tipo_mensaje = 'danger';
    } else {
        $titulo = filter_var($_POST['titulo'] ?? '', FILTER_SANITIZE_STRING);
        $descripcion = filter_var($_POST['descripcion'] ?? '', FILTER_SANITIZE_STRING);
        $modelo = filter_var($_POST['modelo'] ?? '', FILTER_SANITIZE_STRING);

        // Validar campos
        if (empty($titulo) || empty($modelo)) {
            $mensaje = 'Título y modelo organizacional son requeridos.';
            $tipo_mensaje = 'warning';
        } elseif (!in_array($modelo, ['Tradicional', 'Startup'], true)) {
            $mensaje = 'Modelo organizacional inválido.';
            $tipo_mensaje = 'danger';
        } else {
            try {
                $pdo->beginTransaction();

                // Determinar estado inicial según modelo
                $estado_inicial = ($modelo === 'Tradicional') ? 'Pendiente_CIO' : 'Revision_Squad';

                // Insertar proyecto
                $stmt = $pdo->prepare("
                    INSERT INTO proyectos (titulo, descripcion, modelo_organizacional, estado_actual, creado_por)
                    VALUES (?, ?, ?, ?, ?)
                ");
                $stmt->execute([$titulo, $descripcion, $modelo, $estado_inicial, $usuario_id]);
                $proyecto_id = $pdo->lastInsertId();

                // Registrar en auditoría
                $ip = $_SERVER['REMOTE_ADDR'] ?? 'desconocida';
                $stmt_audit = $pdo->prepare("
                    INSERT INTO auditoria_flujo (proyecto_id, usuario_id, accion_realizada, estado_anterior, estado_nuevo, ip_origen)
                    VALUES (?, ?, ?, ?, ?, ?)
                ");
                $stmt_audit->execute([
                    $proyecto_id,
                    $usuario_id,
                    "Creación de proyecto: {$titulo}",
                    NULL,
                    $estado_inicial,
                    $ip
                ]);

                $pdo->commit();

                // Guardar mensaje y redirigir
                $_SESSION['flash_msg'] = 'Proyecto creado exitosamente: ' . htmlspecialchars($titulo, ENT_QUOTES, 'UTF-8');
                $_SESSION['flash_type'] = 'success';

                header('Location: /simulador_latencia/dashboard.php');
                exit;

            } catch (PDOException $e) {
                $pdo->rollBack();
                error_log('Error al crear proyecto: ' . $e->getMessage());
                $mensaje = 'Error al crear el proyecto. Intente nuevamente.';
                $tipo_mensaje = 'danger';
            }
        }
    }
}

// Determinar color del rol
$colores_rol = [
    'DEV' => '#6b7280',
    'CIO' => '#3b82f6',
    'CTO' => '#8b5cf6',
    'CISO' => '#f59e0b'
];
$color_rol = $colores_rol[$rol] ?? '#6b7280';

// Iniciales
$palabras = explode(' ', trim($usuario_nombre));
$inicial1 = substr($palabras[0] ?? '', 0, 1);
$inicial2 = substr($palabras[1] ?? '', 0, 1);
$iniciales = strtoupper($inicial1 . $inicial2);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Proyecto - Simulador de Latencia</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="/simulador_latencia/assets/css/estilo.php" rel="stylesheet">
</head>
<body>
    <!-- SIDEBAR -->
    <aside class="sidebar">
        <div class="sidebar-header">
            <div class="avatar-circle" style="background-color: <?php echo htmlspecialchars($color_rol, ENT_QUOTES, 'UTF-8'); ?>">
                <?php echo htmlspecialchars($iniciales, ENT_QUOTES, 'UTF-8'); ?>
            </div>
            <div class="user-info">
                <h5><?php echo htmlspecialchars($usuario_nombre, ENT_QUOTES, 'UTF-8'); ?></h5>
                <p><?php echo htmlspecialchars($username, ENT_QUOTES, 'UTF-8'); ?></p>
                <span class="role-badge" style="background-color: <?php echo htmlspecialchars($color_rol, ENT_QUOTES, 'UTF-8'); ?>">
                    <?php echo htmlspecialchars($rol, ENT_QUOTES, 'UTF-8'); ?>
                </span>
            </div>
        </div>

        <nav class="sidebar-nav">
            <a href="/simulador_latencia/dashboard.php">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
            <a href="/simulador_latencia/crear_proyecto.php" class="active">
                <i class="bi bi-plus-circle"></i> Crear Proyecto
            </a>
            <a href="/simulador_latencia/logout.php">
                <i class="bi bi-box-arrow-right"></i> Cerrar Sesión
            </a>
        </nav>

        <div class="sidebar-footer">
            <small style="color: #94a3b8;">Simulador v1.0</small>
        </div>
    </aside>

    <!-- CONTENIDO PRINCIPAL -->
    <div class="main-content">
        <div class="page-header">
            <h1>Crear Nuevo Proyecto</h1>
            <p>Ingresa los detalles del proyecto. El flujo de aprobación se determinará automáticamente según el modelo organizacional.</p>
        </div>

        <?php if (!empty($mensaje)): ?>
            <div class="alert alert-<?php echo htmlspecialchars($tipo_mensaje, ENT_QUOTES, 'UTF-8'); ?> alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8'); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div style="max-width: 700px;">
            <div style="background: var(--card-bg); border: 1px solid var(--border-color); border-radius: 0.75rem; padding: 2rem;">
                <form method="POST" action="/simulador_latencia/crear_proyecto.php">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">

                    <div class="mb-3">
                        <label for="titulo" class="form-label">Título del Proyecto *</label>
                        <input
                            type="text"
                            class="form-control"
                            id="titulo"
                            name="titulo"
                            placeholder="Ej: Portal de Autogestión Empresarial"
                            required
                        >
                        <small class="text-muted">Nombre descriptivo del proyecto</small>
                    </div>

                    <div class="mb-3">
                        <label for="descripcion" class="form-label">Descripción</label>
                        <textarea
                            class="form-control"
                            id="descripcion"
                            name="descripcion"
                            rows="4"
                            placeholder="Describe brevemente el proyecto, objetivos y alcance..."
                        ></textarea>
                        <small class="text-muted">Máximo 500 caracteres (opcional)</small>
                    </div>

                    <div class="mb-3">
                        <label for="modelo" class="form-label">Modelo Organizacional *</label>
                        <select class="form-control" id="modelo" name="modelo" required>
                            <option value="">-- Selecciona un modelo --</option>
                            <option value="Tradicional">
                                🏛️ Tradicional (3 pasos: DEV → CIO → CISO → Producción)
                            </option>
                            <option value="Startup">
                                ⚡ Startup (2 pasos: DEV → CTO → Producción)
                            </option>
                        </select>
                        <small class="text-muted d-block mt-2">
                            <strong>Tradicional:</strong> Flujo en silos con múltiples aprobaciones (lento pero seguro)<br>
                            <strong>Startup:</strong> Flujo ágil con Squad (rápido y flexible)
                        </small>
                    </div>

                    <div style="background: #334155; border-left: 4px solid var(--color-primary); padding: 1rem; border-radius: 0.5rem; margin-bottom: 1.5rem;">
                        <strong>📋 Información:</strong>
                        <ul class="mb-0 mt-2 small">
                            <li>El estado inicial se asignará automáticamente según el modelo seleccionado.</li>
                            <li>Los roles de aprobación varían según el modelo organizacional.</li>
                            <li>Cada transición se registrará en la auditoría del sistema.</li>
                        </ul>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check2"></i> Crear Proyecto
                        </button>
                        <a href="/simulador_latencia/dashboard.php" class="btn btn-secondary">
                            <i class="bi bi-x-lg"></i> Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <div style="max-width: 700px; margin-top: 2rem;">
            <div style="background: rgba(16, 185, 129, 0.1); border-left: 4px solid var(--color-startup); padding: 1.5rem; border-radius: 0.75rem;">
                <h5 style="color: var(--color-startup); margin-bottom: 1rem;">
                    <i class="bi bi-lightbulb"></i> Diferencia entre Modelos
                </h5>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                    <div>
                        <h6 style="color: var(--color-tradicional); margin-bottom: 0.75rem;">🏛️ Tradicional</h6>
                        <p style="font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 0.5rem;">
                            Estructura jerárquica en silos. Cada decisión requiere aprobación de múltiples C-Level:
                        </p>
                        <ul style="font-size: 0.85rem; margin-bottom: 0; padding-left: 1.2rem; color: var(--text-secondary);">
                            <li>CIO: Alineación IT</li>
                            <li>CISO: Seguridad</li>
                        </ul>
                    </div>
                    <div>
                        <h6 style="color: var(--color-startup); margin-bottom: 0.75rem;">⚡ Startup</h6>
                        <p style="font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 0.5rem;">
                            Estructura ágil con Squads. Decisión rápida del CTO elimina intermediarios:
                        </p>
                        <ul style="font-size: 0.85rem; margin-bottom: 0; padding-left: 1.2rem; color: var(--text-secondary);">
                            <li>CTO: Revisión técnica</li>
                            <li>Sin burocracia</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
