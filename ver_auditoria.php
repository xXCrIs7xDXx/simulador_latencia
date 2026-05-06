<?php
/**
 * ============================================================
 * PÁGINA DE AUDITORÍA - EXCLUSIVA CISO
 * ============================================================
 * Nombre: ver_auditoria.php
 * Propósito: Log de trazabilidad con filtros y exportación CSV
 * Autor: Arquitecto Senior PHP/MySQL
 * Fecha: Mayo 2026
 * ============================================================
 */

session_start();
require_once __DIR__ . '/config/conexion.php';
require_once __DIR__ . '/includes/auth_guard.php';
require_once __DIR__ . '/includes/funciones.php';

// Solo CISO puede acceder
verificar_sesion(['CISO']);

$usuario_id = $_SESSION['usuario_id'];
$usuario_nombre = $_SESSION['usuario_nombre'];
$username = $_SESSION['username'];
$rol = $_SESSION['rol'];

// Procesar exportación a CSV
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    // Aplicar filtros
    $query = "
        SELECT a.*, p.titulo as proyecto_titulo, u.nombre as usuario_nombre, u.rol as usuario_rol
        FROM auditoria_flujo a
        LEFT JOIN proyectos p ON a.proyecto_id = p.id
        LEFT JOIN usuarios u ON a.usuario_id = u.id
        WHERE 1=1
    ";
    $params = [];

    if (!empty($_GET['proyecto'])) {
        $query .= " AND a.proyecto_id = ?";
        $params[] = filter_var($_GET['proyecto'], FILTER_VALIDATE_INT);
    }

    if (!empty($_GET['rol'])) {
        $query .= " AND u.rol = ?";
        $params[] = filter_var($_GET['rol'], FILTER_SANITIZE_STRING);
    }

    if (!empty($_GET['fecha_desde'])) {
        $query .= " AND DATE(a.timestamp) >= ?";
        $params[] = filter_var($_GET['fecha_desde'], FILTER_SANITIZE_STRING);
    }

    if (!empty($_GET['fecha_hasta'])) {
        $query .= " AND DATE(a.timestamp) <= ?";
        $params[] = filter_var($_GET['fecha_hasta'], FILTER_SANITIZE_STRING);
    }

    $query .= " ORDER BY a.timestamp DESC";

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $registros = $stmt->fetchAll();

    // Generar CSV
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="auditoria_' . date('Y-m-d_His') . '.csv"');

    $output = fopen('php://output', 'w');
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM UTF-8

    // Encabezados
    fputcsv($output, [
        'ID',
        'Proyecto',
        'Usuario',
        'Rol',
        'Acción',
        'Estado Anterior',
        'Estado Nuevo',
        'IP Origen',
        'Timestamp'
    ]);

    // Datos
    foreach ($registros as $reg) {
        fputcsv($output, [
            $reg['id'],
            $reg['proyecto_titulo'] ?? 'N/A',
            $reg['usuario_nombre'],
            $reg['usuario_rol'],
            $reg['accion_realizada'],
            $reg['estado_anterior'] ?? 'N/A',
            $reg['estado_nuevo'] ?? 'N/A',
            $reg['ip_origen'],
            $reg['timestamp']
        ]);
    }

    fclose($output);
    exit;
}

// Obtener proyectos para filtro
$stmt = $pdo->prepare("SELECT id, titulo FROM proyectos ORDER BY titulo");
$stmt->execute();
$proyectos_lista = $stmt->fetchAll();

// Obtener registros de auditoría con filtros
$query = "
    SELECT a.*, p.titulo as proyecto_titulo, u.nombre as usuario_nombre, u.rol as usuario_rol
    FROM auditoria_flujo a
    LEFT JOIN proyectos p ON a.proyecto_id = p.id
    LEFT JOIN usuarios u ON a.usuario_id = u.id
    WHERE 1=1
";
$params = [];

// Filtro por proyecto
if (!empty($_GET['proyecto'])) {
    $query .= " AND a.proyecto_id = ?";
    $params[] = filter_var($_GET['proyecto'], FILTER_VALIDATE_INT);
}

// Filtro por rol
if (!empty($_GET['rol'])) {
    $query .= " AND u.rol = ?";
    $params[] = filter_var($_GET['rol'], FILTER_SANITIZE_STRING);
}

// Filtro por fecha desde
if (!empty($_GET['fecha_desde'])) {
    $query .= " AND DATE(a.timestamp) >= ?";
    $params[] = filter_var($_GET['fecha_desde'], FILTER_SANITIZE_STRING);
}

// Filtro por fecha hasta
if (!empty($_GET['fecha_hasta'])) {
    $query .= " AND DATE(a.timestamp) <= ?";
    $params[] = filter_var($_GET['fecha_hasta'], FILTER_SANITIZE_STRING);
}

$query .= " ORDER BY a.timestamp DESC LIMIT 500";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$auditoria = $stmt->fetchAll();

// Colores de rol
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
    <title>Auditoría - Simulador de Latencia</title>
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
            <a href="/simulador_latencia/ver_auditoria.php" class="active">
                <i class="bi bi-shield-check"></i> Auditoría
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
            <h1><i class="bi bi-shield-check"></i> Auditoría del Sistema</h1>
            <p>Log de trazabilidad completo de todas las acciones y transiciones. Acceso exclusivo CISO.</p>
        </div>

        <!-- FILTROS -->
        <div style="background: var(--card-bg); border: 1px solid var(--border-color); border-radius: 0.75rem; padding: 1.5rem; margin-bottom: 2rem;">
            <h5 style="margin-bottom: 1.5rem; color: var(--text-primary);">Filtros</h5>
            <form method="GET" action="/simulador_latencia/ver_auditoria.php" class="row g-3">
                <div class="col-md-3">
                    <label for="proyecto" class="form-label">Proyecto</label>
                    <select class="form-control" id="proyecto" name="proyecto">
                        <option value="">-- Todos los proyectos --</option>
                        <?php foreach ($proyectos_lista as $proy): ?>
                            <option value="<?php echo htmlspecialchars($proy['id'], ENT_QUOTES, 'UTF-8'); ?>"
                                <?php echo (!empty($_GET['proyecto']) && $_GET['proyecto'] == $proy['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($proy['titulo'], ENT_QUOTES, 'UTF-8'); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-3">
                    <label for="rol" class="form-label">Rol del Usuario</label>
                    <select class="form-control" id="rol" name="rol">
                        <option value="">-- Todos los roles --</option>
                        <option value="DEV" <?php echo $_GET['rol'] === 'DEV' ? 'selected' : ''; ?>>DEV</option>
                        <option value="CIO" <?php echo $_GET['rol'] === 'CIO' ? 'selected' : ''; ?>>CIO</option>
                        <option value="CTO" <?php echo $_GET['rol'] === 'CTO' ? 'selected' : ''; ?>>CTO</option>
                        <option value="CISO" <?php echo $_GET['rol'] === 'CISO' ? 'selected' : ''; ?>>CISO</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label for="fecha_desde" class="form-label">Desde</label>
                    <input type="date" class="form-control" id="fecha_desde" name="fecha_desde" 
                        value="<?php echo !empty($_GET['fecha_desde']) ? htmlspecialchars($_GET['fecha_desde'], ENT_QUOTES, 'UTF-8') : ''; ?>">
                </div>

                <div class="col-md-2">
                    <label for="fecha_hasta" class="form-label">Hasta</label>
                    <input type="date" class="form-control" id="fecha_hasta" name="fecha_hasta"
                        value="<?php echo !empty($_GET['fecha_hasta']) ? htmlspecialchars($_GET['fecha_hasta'], ENT_QUOTES, 'UTF-8') : ''; ?>">
                </div>

                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search"></i> Filtrar
                    </button>
                </div>
            </form>

            <div class="mt-3">
                <a href="/simulador_latencia/ver_auditoria.php?export=csv<?php echo !empty($_GET['proyecto']) ? '&proyecto=' . htmlspecialchars($_GET['proyecto'], ENT_QUOTES, 'UTF-8') : ''; ?><?php echo !empty($_GET['rol']) ? '&rol=' . htmlspecialchars($_GET['rol'], ENT_QUOTES, 'UTF-8') : ''; ?><?php echo !empty($_GET['fecha_desde']) ? '&fecha_desde=' . htmlspecialchars($_GET['fecha_desde'], ENT_QUOTES, 'UTF-8') : ''; ?><?php echo !empty($_GET['fecha_hasta']) ? '&fecha_hasta=' . htmlspecialchars($_GET['fecha_hasta'], ENT_QUOTES, 'UTF-8') : ''; ?>" class="btn btn-success">
                    <i class="bi bi-download"></i> Exportar a CSV
                </a>
            </div>
        </div>

        <!-- TABLA DE AUDITORÍA -->
        <div style="background: var(--card-bg); border: 1px solid var(--border-color); border-radius: 0.75rem; overflow: hidden;">
            <div style="overflow-x: auto;">
                <table class="table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Proyecto</th>
                            <th>Actor</th>
                            <th>Rol</th>
                            <th>Acción</th>
                            <th>Estado Anterior</th>
                            <th>Estado Nuevo</th>
                            <th>IP Origen</th>
                            <th>Timestamp</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($auditoria)): ?>
                            <tr>
                                <td colspan="9" style="text-align: center; padding: 2rem; color: var(--text-secondary);">
                                    No hay registros de auditoría
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($auditoria as $registro): ?>
                                <?php 
                                    $fila_clase = 'table-row-' . strtolower($registro['usuario_rol'] ?? 'dev');
                                ?>
                                <tr class="<?php echo htmlspecialchars($fila_clase, ENT_QUOTES, 'UTF-8'); ?>">
                                    <td><?php echo htmlspecialchars($registro['id'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td>
                                        <?php if ($registro['proyecto_titulo']): ?>
                                            <small><?php echo htmlspecialchars($registro['proyecto_titulo'], ENT_QUOTES, 'UTF-8'); ?></small>
                                        <?php else: ?>
                                            <small style="color: #94a3b8;">Sistema</small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <small><?php echo htmlspecialchars($registro['usuario_nombre'], ENT_QUOTES, 'UTF-8'); ?></small>
                                    </td>
                                    <td>
                                        <span class="role-badge" style="background-color: <?php echo htmlspecialchars($colores_rol[$registro['usuario_rol']] ?? '#6b7280', ENT_QUOTES, 'UTF-8'); ?>">
                                            <?php echo htmlspecialchars($registro['usuario_rol'] ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <small><?php echo htmlspecialchars($registro['accion_realizada'], ENT_QUOTES, 'UTF-8'); ?></small>
                                    </td>
                                    <td>
                                        <small>
                                            <?php if ($registro['estado_anterior']): ?>
                                                <?php echo htmlspecialchars($registro['estado_anterior'], ENT_QUOTES, 'UTF-8'); ?>
                                            <?php else: ?>
                                                <span style="color: #94a3b8;">--</span>
                                            <?php endif; ?>
                                        </small>
                                    </td>
                                    <td>
                                        <small>
                                            <?php if ($registro['estado_nuevo']): ?>
                                                <?php echo htmlspecialchars($registro['estado_nuevo'], ENT_QUOTES, 'UTF-8'); ?>
                                            <?php else: ?>
                                                <span style="color: #94a3b8;">--</span>
                                            <?php endif; ?>
                                        </small>
                                    </td>
                                    <td>
                                        <small style="font-family: monospace; color: #94a3b8;">
                                            <?php echo htmlspecialchars($registro['ip_origen'], ENT_QUOTES, 'UTF-8'); ?>
                                        </small>
                                    </td>
                                    <td>
                                        <small><?php echo htmlspecialchars($registro['timestamp'], ENT_QUOTES, 'UTF-8'); ?></small>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div style="margin-top: 1.5rem; padding: 1rem; background: rgba(245, 158, 11, 0.1); border-left: 4px solid var(--color-ciso); border-radius: 0.5rem;">
            <small style="color: var(--color-ciso);">
                <strong>⚠️ Información de Seguridad:</strong> Esta página de auditoría es de acceso exclusivo para el CISO. 
                Todos los eventos del sistema se registran automáticamente incluyendo IP de origen y timestamp.
            </small>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
