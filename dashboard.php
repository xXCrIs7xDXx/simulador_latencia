<?php
/**
 * ============================================================
 * DASHBOARD PRINCIPAL
 * ============================================================
 * Nombre: dashboard.php
 * Propósito: Panel principal con proyectos y estadísticas
 * Autor: Arquitecto Senior PHP/MySQL
 * Fecha: Mayo 2026
 * ============================================================
 */

session_start();
require_once __DIR__ . '/config/conexion.php';
require_once __DIR__ . '/includes/auth_guard.php';
require_once __DIR__ . '/includes/funciones.php';

// Verificar sesión activa (todos los roles acceden)
verificar_sesion();

$usuario_id = $_SESSION['usuario_id'];
$usuario_nombre = $_SESSION['usuario_nombre'];
$rol = $_SESSION['rol'];
$username = $_SESSION['username'];

// Obtener estadísticas
$stats = obtener_estadisticas_dashboard();

// Obtener proyectos (todos)
$stmt = $pdo->prepare("
    SELECT p.*, u.nombre as creador_nombre
    FROM proyectos p
    JOIN usuarios u ON p.creado_por = u.id
    ORDER BY p.fecha_creacion DESC
");
$stmt->execute();
$proyectos = $stmt->fetchAll();

// Obtener mensaje flash si existe
$mensaje_flash = $_SESSION['flash_msg'] ?? null;
$tipo_flash = $_SESSION['flash_type'] ?? 'info';
if (isset($_SESSION['flash_msg'])) {
    unset($_SESSION['flash_msg']);
    unset($_SESSION['flash_type']);
}

// Determinar color del rol
$colores_rol = [
    'DEV' => '#6b7280',
    'CIO' => '#3b82f6',
    'CTO' => '#8b5cf6',
    'CISO' => '#f59e0b'
];
$color_rol = $colores_rol[$rol] ?? '#6b7280';

// Iniciales del usuario
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
    <title>Dashboard - Simulador de Latencia</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="/simulador_latencia/assets/css/estilo.php" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
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
            <a href="/simulador_latencia/dashboard.php" class="active">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
            <?php if ($rol === 'DEV'): ?>
                <a href="/simulador_latencia/crear_proyecto.php">
                    <i class="bi bi-plus-circle"></i> Crear Proyecto
                </a>
            <?php endif; ?>
            <?php if ($rol === 'CISO'): ?>
                <a href="/simulador_latencia/ver_auditoria.php">
                    <i class="bi bi-shield-check"></i> Auditoría
                </a>
            <?php endif; ?>
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
        <!-- Encabezado -->
        <div class="page-header">
            <h1>Dashboard</h1>
            <p>Bienvenido, <?php echo htmlspecialchars($usuario_nombre, ENT_QUOTES, 'UTF-8'); ?>. Vista: <strong><?php echo obtener_nombre_rol($rol); ?></strong></p>
        </div>

        <!-- Mensaje Flash -->
        <?php if ($mensaje_flash): ?>
            <div class="alert alert-<?php echo htmlspecialchars($tipo_flash, ENT_QUOTES, 'UTF-8'); ?> alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($mensaje_flash, ENT_QUOTES, 'UTF-8'); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Tarjetas de Estadísticas -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-label">Total Proyectos Activos</div>
                <div class="stat-value"><?php echo htmlspecialchars($stats['total_activos'], ENT_QUOTES, 'UTF-8'); ?></div>
                <div class="stat-subtext">En proceso de aprobación</div>
            </div>

            <div class="stat-card">
                <div class="stat-label">Flujo Tradicional</div>
                <div class="stat-value"><?php echo htmlspecialchars($stats['proyectos_tradicionales'], ENT_QUOTES, 'UTF-8'); ?></div>
                <div class="stat-subtext">Tiempo promedio: <?php echo htmlspecialchars(round($stats['tiempo_promedio_trad_minutos'] / 60, 1), ENT_QUOTES, 'UTF-8'); ?> horas</div>
            </div>

            <div class="stat-card">
                <div class="stat-label">Flujo Startup</div>
                <div class="stat-value"><?php echo htmlspecialchars($stats['proyectos_startup'], ENT_QUOTES, 'UTF-8'); ?></div>
                <div class="stat-subtext">Tiempo promedio: <?php echo htmlspecialchars(round($stats['tiempo_promedio_startup_minutos'] / 60, 1), ENT_QUOTES, 'UTF-8'); ?> horas</div>
            </div>

            <div class="stat-card">
                <div class="stat-label">Aprobados Esta Semana</div>
                <div class="stat-value"><?php echo htmlspecialchars($stats['aprobados_semana'], ENT_QUOTES, 'UTF-8'); ?></div>
                <div class="stat-subtext">Últimos 7 días</div>
            </div>
        </div>

        <!-- Gráfico Comparativo -->
        <div class="projects-section">
            <div style="background: var(--card-bg); border: 1px solid var(--border-color); border-radius: 0.75rem; padding: 1.5rem; margin-bottom: 2rem;">
                <h5 style="margin-bottom: 1rem; color: var(--text-primary);">Comparativa Tradicional vs Startup (Tiempo en minutos)</h5>
                <canvas id="chartComparativo" height="80"></canvas>
            </div>
        </div>

        <!-- Proyectos -->
        <div class="projects-section">
            <h2>Proyectos</h2>

            <?php if (empty($proyectos)): ?>
                <div class="alert alert-info">
                    No hay proyectos registrados. 
                    <?php if ($rol === 'DEV'): ?>
                        <a href="/simulador_latencia/crear_proyecto.php">Crear el primer proyecto</a>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="projects-grid">
                    <?php foreach ($proyectos as $proyecto): ?>
                        <?php
                        $progreso = obtener_progreso_pipeline($proyecto['modelo_organizacional'], $proyecto['estado_actual']);
                        $color_modelo = $proyecto['modelo_organizacional'] === 'Tradicional' ? 'tradicional' : 'startup';
                        $puede_ejecutar = puede_ejecutar_transicion($proyecto['modelo_organizacional'], $proyecto['estado_actual'], $rol);
                        $siguiente_rol = obtener_rol_siguiente($proyecto['modelo_organizacional'], $proyecto['estado_actual']);
                        $tiempo_transcurrido = calcular_tiempo_transcurrido($proyecto['fecha_creacion']);
                        ?>
                        <div class="project-card <?php echo htmlspecialchars($color_modelo, ENT_QUOTES, 'UTF-8'); ?>">
                            <div class="project-header">
                                <div class="project-title">
                                    <h3><?php echo htmlspecialchars($proyecto['titulo'], ENT_QUOTES, 'UTF-8'); ?></h3>
                                    <span class="model-badge <?php echo htmlspecialchars($color_modelo, ENT_QUOTES, 'UTF-8'); ?>">
                                        <?php echo htmlspecialchars($proyecto['modelo_organizacional'], ENT_QUOTES, 'UTF-8'); ?>
                                    </span>
                                </div>
                                <span class="state-badge <?php echo htmlspecialchars(obtener_color_estado($proyecto['estado_actual']), ENT_QUOTES, 'UTF-8'); ?>">
                                    <?php echo htmlspecialchars(obtener_descripcion_estado($proyecto['estado_actual']), ENT_QUOTES, 'UTF-8'); ?>
                                </span>
                            </div>

                            <!-- Pipeline Visual -->
                            <div class="pipeline-progress">
                                <div class="pipeline-steps">
                                    <?php if ($proyecto['modelo_organizacional'] === 'Tradicional'): ?>
                                        <div class="pipeline-step <?php echo $progreso['paso_actual'] >= 1 ? 'active' : ''; ?>">
                                            <div class="step-circle">DEV</div>
                                            <div class="step-label">Creación</div>
                                        </div>
                                        <div class="pipeline-step <?php echo $progreso['paso_actual'] >= 2 ? 'active' : ''; ?>">
                                            <div class="step-circle">CIO</div>
                                            <div class="step-label">Aprobación CIO</div>
                                        </div>
                                        <div class="pipeline-step <?php echo $progreso['paso_actual'] >= 3 ? 'active' : ''; ?>">
                                            <div class="step-circle">CISO</div>
                                            <div class="step-label">Aprobación CISO</div>
                                        </div>
                                    <?php else: ?>
                                        <div class="pipeline-step <?php echo $progreso['paso_actual'] >= 1 ? 'active' : ''; ?>">
                                            <div class="step-circle">DEV</div>
                                            <div class="step-label">Creación</div>
                                        </div>
                                        <div class="pipeline-step <?php echo $progreso['paso_actual'] >= 2 ? 'active' : ''; ?>">
                                            <div class="step-circle">CTO</div>
                                            <div class="step-label">Squad Review</div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="project-content">
                                <div class="project-description">
                                    <?php echo htmlspecialchars($proyecto['descripcion'] ?? 'Sin descripción', ENT_QUOTES, 'UTF-8'); ?>
                                </div>
                            </div>

                            <div class="project-footer">
                                <div class="project-meta">
                                    <small><i class="bi bi-person"></i> <?php echo htmlspecialchars($proyecto['creador_nombre'], ENT_QUOTES, 'UTF-8'); ?></small>
                                    <small><i class="bi bi-clock"></i> <?php echo htmlspecialchars($tiempo_transcurrido, ENT_QUOTES, 'UTF-8'); ?></small>
                                </div>
                                <div class="action-buttons">
                                    <?php if ($puede_ejecutar): ?>
                                        <form method="POST" action="/simulador_latencia/procesar_accion.php" style="display: inline;">
                                            <input type="hidden" name="proyecto_id" value="<?php echo htmlspecialchars($proyecto['id'], ENT_QUOTES, 'UTF-8'); ?>">
                                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                                            <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('¿Estás seguro de esta aprobación?')">
                                                <i class="bi bi-check2"></i> Aprobar
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <small style="color: #94a3b8;">
                                            Esperando: <strong><?php echo htmlspecialchars($siguiente_rol, ENT_QUOTES, 'UTF-8'); ?></strong>
                                        </small>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Gráfico comparativo Tradicional vs Startup
        const tiempoTrad = <?php echo json_encode($stats['tiempo_promedio_trad_minutos']); ?>;
        const tiempoStartup = <?php echo json_encode($stats['tiempo_promedio_startup_minutos']); ?>;

        const ctx = document.getElementById('chartComparativo').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Tiempo Promedio'],
                datasets: [
                    {
                        label: 'Flujo Tradicional (minutos)',
                        data: [tiempoTrad],
                        backgroundColor: 'rgba(239, 68, 68, 0.8)',
                        borderColor: 'rgba(239, 68, 68, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Flujo Startup (minutos)',
                        data: [tiempoStartup],
                        backgroundColor: 'rgba(16, 185, 129, 0.8)',
                        borderColor: 'rgba(16, 185, 129, 1)',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        labels: {
                            color: '#f1f5f9',
                            font: { size: 12 }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            color: '#cbd5e1'
                        },
                        grid: {
                            color: '#334155'
                        }
                    },
                    x: {
                        ticks: {
                            color: '#cbd5e1'
                        },
                        grid: {
                            color: '#334155'
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>
