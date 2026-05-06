<?php
/**
 * ============================================================
 * LOGOUT - DESTRUIR SESIÓN
 * ============================================================
 * Nombre: logout.php
 * Propósito: Cerrar sesión y redirigir a login
 * Autor: Arquitecto Senior PHP/MySQL
 * Fecha: Mayo 2026
 * ============================================================
 */

session_start();

// Log de auditoría para logout
if (isset($_SESSION['usuario_id'])) {
    require_once __DIR__ . '/config/conexion.php';
    $usuario_id = $_SESSION['usuario_id'];
    $username = $_SESSION['username'] ?? 'desconocido';
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'desconocida';
    $action = "Logout del usuario {$username}";
    
    try {
        $stmt = $pdo->prepare("
            INSERT INTO auditoria_flujo (proyecto_id, usuario_id, accion_realizada, ip_origen)
            VALUES (NULL, ?, ?, ?)
        ");
        $stmt->execute([$usuario_id, $action, $ip]);
    } catch (PDOException $e) {
        error_log('Error al registrar logout: ' . $e->getMessage());
    }
}

// Destruir sesión
$_SESSION = array();
session_destroy();

// Redirigir a login
header('Location: /simulador_latencia/login.php');
exit;
?>
