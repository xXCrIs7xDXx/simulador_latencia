<?php
/**
 * ============================================================
 * FUNCIONES HELPER Y UTILIDADES
 * ============================================================
 * Nombre: includes/funciones.php
 * Propósito: Funciones reutilizables para todo el sistema
 * Autor: Arquitecto Senior PHP/MySQL
 * Fecha: Mayo 2026
 * ============================================================
 */

require_once __DIR__ . '/../config/conexion.php';

/**
 * Calcular tiempo transcurrido en formato legible
 * @param string $fecha_inicio Fecha en formato YYYY-MM-DD HH:MM:SS
 * @return string Tiempo formateado (ej: "2 días, 3 horas")
 */
function calcular_tiempo_transcurrido(string $fecha_inicio): string {
    $inicio = new DateTime($fecha_inicio);
    $ahora = new DateTime();
    $diff = $ahora->diff($inicio);

    if ($diff->d > 0) {
        return $diff->d . ' día' . ($diff->d > 1 ? 's' : '') . ', ' . $diff->h . ' hora' . ($diff->h !== 1 ? 's' : '');
    } elseif ($diff->h > 0) {
        return $diff->h . ' hora' . ($diff->h !== 1 ? 's' : '') . ', ' . $diff->i . ' min';
    } else {
        return $diff->i . ' minuto' . ($diff->i !== 1 ? 's' : '');
    }
}

/**
 * Obtener descripción amigable del estado
 * @param string $estado Estado actual del proyecto
 * @return string Descripción legible
 */
function obtener_descripcion_estado(string $estado): string {
    $descripciones = [
        'Pendiente_CIO' => 'Esperando aprobación del CIO',
        'Pendiente_CISO' => 'Esperando aprobación del CISO',
        'Revision_Squad' => 'En revisión del Squad (CTO)',
        'Aprobado_Produccion' => 'Aprobado para Producción',
        'En_Produccion' => 'En Producción'
    ];
    return $descripciones[$estado] ?? $estado;
}

/**
 * Obtener color para insignia de estado
 * @param string $estado Estado del proyecto
 * @return string Clase Bootstrap de color
 */
function obtener_color_estado(string $estado): string {
    $colores = [
        'Pendiente_CIO' => 'warning',
        'Pendiente_CISO' => 'info',
        'Revision_Squad' => 'primary',
        'Aprobado_Produccion' => 'success',
        'En_Produccion' => 'success'
    ];
    return $colores[$estado] ?? 'secondary';
}

/**
 * Obtener paso actual en el pipeline de aprobación
 * @param string $modelo_organizacional Modelo: 'Tradicional' o 'Startup'
 * @param string $estado_actual Estado actual del proyecto
 * @return array Array con ['paso_actual' => int, 'total_pasos' => int]
 */
function obtener_progreso_pipeline(string $modelo_organizacional, string $estado_actual): array {
    if ($modelo_organizacional === 'Tradicional') {
        $pasos = [
            'Pendiente_CIO' => 1,
            'Pendiente_CISO' => 2,
            'Aprobado_Produccion' => 3
        ];
        return [
            'paso_actual' => $pasos[$estado_actual] ?? 0,
            'total_pasos' => 3
        ];
    } else { // Startup
        $pasos = [
            'Revision_Squad' => 1,
            'Aprobado_Produccion' => 2
        ];
        return [
            'paso_actual' => $pasos[$estado_actual] ?? 0,
            'total_pasos' => 2
        ];
    }
}

/**
 * Determinar qué rol puede ejecutar la siguiente acción
 * @param string $modelo_organizacional Modelo del proyecto
 * @param string $estado_actual Estado actual
 * @return string Rol que puede ejecutar la acción siguiente
 */
function obtener_rol_siguiente(string $modelo_organizacional, string $estado_actual): string {
    if ($modelo_organizacional === 'Tradicional') {
        if ($estado_actual === 'Pendiente_CIO') return 'CIO';
        if ($estado_actual === 'Pendiente_CISO') return 'CISO';
    } else { // Startup
        if ($estado_actual === 'Revision_Squad') return 'CTO';
    }
    return 'NINGUNO';
}

/**
 * Obtener estadísticas del dashboard
 * @return array Array con métricas clave
 */
function obtener_estadisticas_dashboard(): array {
    global $pdo;
    
    // Total proyectos activos
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM proyectos WHERE estado_actual != 'Aprobado_Produccion'");
    $stmt->execute();
    $total_activos = $stmt->fetch()['total'] ?? 0;

    // Proyectos tradicionales en cola
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM proyectos WHERE modelo_organizacional = 'Tradicional' AND estado_actual != 'Aprobado_Produccion'");
    $stmt->execute();
    $proyectos_tradicionales = $stmt->fetch()['total'] ?? 0;

    // Proyectos startup
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM proyectos WHERE modelo_organizacional = 'Startup' AND estado_actual != 'Aprobado_Produccion'");
    $stmt->execute();
    $proyectos_startup = $stmt->fetch()['total'] ?? 0;

    // Proyectos aprobados esta semana
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM proyectos WHERE fecha_aprobacion >= DATE_SUB(NOW(), INTERVAL 7 DAY)");
    $stmt->execute();
    $aprobados_semana = $stmt->fetch()['total'] ?? 0;

    // Tiempo promedio en cola para Tradicional (en minutos)
    $stmt = $pdo->prepare("
        SELECT AVG(TIMESTAMPDIFF(MINUTE, fecha_creacion, COALESCE(fecha_aprobacion, NOW()))) as promedio
        FROM proyectos
        WHERE modelo_organizacional = 'Tradicional'
    ");
    $stmt->execute();
    $tiempo_promedio_trad = ceil($stmt->fetch()['promedio'] ?? 0);

    // Tiempo promedio para Startup
    $stmt = $pdo->prepare("
        SELECT AVG(TIMESTAMPDIFF(MINUTE, fecha_creacion, COALESCE(fecha_aprobacion, NOW()))) as promedio
        FROM proyectos
        WHERE modelo_organizacional = 'Startup'
    ");
    $stmt->execute();
    $tiempo_promedio_startup = ceil($stmt->fetch()['promedio'] ?? 0);

    return [
        'total_activos' => $total_activos,
        'proyectos_tradicionales' => $proyectos_tradicionales,
        'proyectos_startup' => $proyectos_startup,
        'aprobados_semana' => $aprobados_semana,
        'tiempo_promedio_trad_minutos' => $tiempo_promedio_trad,
        'tiempo_promedio_startup_minutos' => $tiempo_promedio_startup
    ];
}

/**
 * Obtener todos los proyectos del usuario actual
 * @param int $usuario_id ID del usuario
 * @return array Array de proyectos
 */
function obtener_proyectos_usuario(int $usuario_id): array {
    global $pdo;
    
    $stmt = $pdo->prepare("
        SELECT p.*, u.nombre as creador_nombre
        FROM proyectos p
        JOIN usuarios u ON p.creado_por = u.id
        WHERE p.creado_por = ?
        ORDER BY p.fecha_creacion DESC
    ");
    $stmt->execute([$usuario_id]);
    return $stmt->fetchAll();
}

/**
 * Obtener todos los proyectos accesibles
 * @param string $rol Rol del usuario actual
 * @return array Array de proyectos
 */
function obtener_todos_proyectos(string $rol): array {
    global $pdo;
    
    $stmt = $pdo->prepare("
        SELECT p.*, u.nombre as creador_nombre
        FROM proyectos p
        JOIN usuarios u ON p.creado_por = u.id
        ORDER BY p.fecha_creacion DESC
    ");
    $stmt->execute();
    return $stmt->fetchAll();
}

/**
 * Validar que una transición es permitida para el rol
 * @param string $modelo Modelo organizacional
 * @param string $estado_actual Estado actual
 * @param string $rol Rol del usuario
 * @return bool True si puede ejecutar transición
 */
function puede_ejecutar_transicion(string $modelo, string $estado_actual, string $rol): bool {
    if ($modelo === 'Tradicional') {
        if ($estado_actual === 'Pendiente_CIO' && $rol === 'CIO') return true;
        if ($estado_actual === 'Pendiente_CISO' && $rol === 'CISO') return true;
    } else { // Startup
        if ($estado_actual === 'Revision_Squad' && $rol === 'CTO') return true;
    }
    return false;
}

/**
 * Obtener el siguiente estado después de una aprobación
 * @param string $modelo Modelo organizacional
 * @param string $estado_actual Estado actual
 * @return string Nuevo estado
 */
function obtener_siguiente_estado(string $modelo, string $estado_actual): string {
    if ($modelo === 'Tradicional') {
        if ($estado_actual === 'Pendiente_CIO') return 'Pendiente_CISO';
        if ($estado_actual === 'Pendiente_CISO') return 'Aprobado_Produccion';
    } else { // Startup
        if ($estado_actual === 'Revision_Squad') return 'Aprobado_Produccion';
    }
    return $estado_actual;
}

?>
