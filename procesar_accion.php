<?php
/**
 * ============================================================
 * PROCESADOR DE ACCIONES - MOTOR DE TRANSICIONES DE ESTADO
 * ============================================================
 * Nombre: procesar_accion.php
 * Propósito: Motor de estados finitos con transacciones ACID y auditoría
 * Autor: Arquitecto Senior PHP/MySQL
 * Fecha: Mayo 2026
 * ============================================================
 */

session_start();
require_once __DIR__ . '/config/conexion.php';
require_once __DIR__ . '/includes/auth_guard.php';
require_once __DIR__ . '/includes/funciones.php';

// Verificar sesión activa (todos los roles que aprueban)
verificar_sesion();

$usuario_id = $_SESSION['usuario_id'];
$rol = $_SESSION['rol'];

// Solo acepta POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /simulador_latencia/dashboard.php');
    exit;
}

// Validar CSRF token
if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'])) {
    $_SESSION['flash_msg'] = 'Error de seguridad: Token CSRF inválido.';
    $_SESSION['flash_type'] = 'danger';
    header('Location: /simulador_latencia/dashboard.php');
    exit;
}

$proyecto_id = filter_var($_POST['proyecto_id'] ?? '', FILTER_VALIDATE_INT);

if (!$proyecto_id) {
    $_SESSION['flash_msg'] = 'ID de proyecto inválido.';
    $_SESSION['flash_type'] = 'danger';
    header('Location: /simulador_latencia/dashboard.php');
    exit;
}

try {
    // Obtener proyecto
    $stmt = $pdo->prepare("
        SELECT id, titulo, modelo_organizacional, estado_actual, creado_por, fecha_creacion
        FROM proyectos
        WHERE id = ?
    ");
    $stmt->execute([$proyecto_id]);
    $proyecto = $stmt->fetch();

    if (!$proyecto) {
        throw new Exception('Proyecto no encontrado.');
    }

    // Validar que el rol puede ejecutar la transición
    $puede_ejecutar = puede_ejecutar_transicion(
        $proyecto['modelo_organizacional'],
        $proyecto['estado_actual'],
        $rol
    );

    if (!$puede_ejecutar) {
        throw new Exception('No tienes permisos para aprobar este proyecto en este estado.');
    }

    // Obtener siguiente estado
    $estado_nuevo = obtener_siguiente_estado(
        $proyecto['modelo_organizacional'],
        $proyecto['estado_actual']
    );

    if ($estado_nuevo === $proyecto['estado_actual']) {
        throw new Exception('No hay transición válida disponible para este estado.');
    }

    // Iniciar transacción ACID
    $pdo->beginTransaction();

    // Actualizar estado del proyecto
    $fecha_aprobacion = null;
    if ($estado_nuevo === 'Aprobado_Produccion') {
        $fecha_aprobacion = date('Y-m-d H:i:s');
    }

    $stmt_update = $pdo->prepare("
        UPDATE proyectos
        SET estado_actual = ?,
            fecha_aprobacion = CASE WHEN ? IS NOT NULL THEN ? ELSE fecha_aprobacion END
        WHERE id = ?
    ");
    $stmt_update->execute([$estado_nuevo, $fecha_aprobacion, $fecha_aprobacion, $proyecto_id]);

    // Registrar en auditoría
    $ip_origen = $_SERVER['REMOTE_ADDR'] ?? 'desconocida';
    $accion_realizada = "Aprobación por {$rol}: {$proyecto['titulo']}";

    $stmt_audit = $pdo->prepare("
        INSERT INTO auditoria_flujo (proyecto_id, usuario_id, accion_realizada, estado_anterior, estado_nuevo, ip_origen)
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    $stmt_audit->execute([
        $proyecto_id,
        $usuario_id,
        $accion_realizada,
        $proyecto['estado_actual'],
        $estado_nuevo,
        $ip_origen
    ]);

    // Commit de transacción
    $pdo->commit();

    // Mensaje de éxito
    $descripcion_nuevo = obtener_descripcion_estado($estado_nuevo);
    $_SESSION['flash_msg'] = "Proyecto aprobado exitosamente. Nuevo estado: {$descripcion_nuevo}";
    $_SESSION['flash_type'] = 'success';

} catch (Exception $e) {
    // Rollback en caso de error
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    error_log("Error en procesar_accion.php: " . $e->getMessage());
    $_SESSION['flash_msg'] = 'Error al procesar la aprobación: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
    $_SESSION['flash_type'] = 'danger';

} catch (PDOException $e) {
    // Rollback en caso de error PDO
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    error_log('Error PDO en procesar_accion.php: ' . $e->getMessage());
    $_SESSION['flash_msg'] = 'Error de base de datos. Contacte al administrador.';
    $_SESSION['flash_type'] = 'danger';
}

// Redirigir al dashboard
header('Location: /simulador_latencia/dashboard.php');
exit;
?>
