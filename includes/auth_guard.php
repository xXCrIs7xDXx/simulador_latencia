<?php
/**
 * ============================================================
 * MIDDLEWARE DE AUTENTICACIÓN Y AUTORIZACIÓN
 * ============================================================
 * Nombre: includes/auth_guard.php
 * Propósito: Verificar sesión activa y autorización por roles
 * Patrón: Middleware Guard
 * Autor: Arquitecto Senior PHP/MySQL
 * Fecha: Mayo 2026
 * ============================================================
 */

/**
 * Verificar sesión activa y permisos de rol
 * 
 * @param array $roles_permitidos Array de roles autorizados (ej: ['CIO', 'CISO'])
 * @return void
 * @throws Redirige si no hay sesión o rol no autorizado
 */
function verificar_sesion(array $roles_permitidos = []): void {
    // Iniciar sesión si no está activa
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Verificar que existe usuario_id en sesión
    if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['rol'])) {
        header('Location: /simulador_latencia/login.php?error=session_expired');
        exit;
    }

    // Verificar que el rol está en la lista permitida (si se especifica)
    if (!empty($roles_permitidos) && !in_array($_SESSION['rol'], $roles_permitidos, true)) {
        header('Location: /simulador_latencia/dashboard.php?error=unauthorized');
        exit;
    }
}

/**
 * Obtener color de insignia para el rol
 * @param string $rol Rol del usuario
 * @return string Código hexadecimal de color
 */
function obtener_color_rol(string $rol): string {
    $colores = [
        'DEV' => '#6b7280',    // Gris
        'CIO' => '#3b82f6',    // Azul
        'CTO' => '#8b5cf6',    // Violeta
        'CISO' => '#f59e0b'    // Ámbar
    ];
    return $colores[$rol] ?? '#6b7280';
}

/**
 * Obtener nombre de rol en formato legible
 * @param string $rol Rol del usuario
 * @return string Nombre formateado
 */
function obtener_nombre_rol(string $rol): string {
    $nombres = [
        'DEV' => 'Desarrollador',
        'CIO' => 'CIO (Jefe de IT)',
        'CTO' => 'CTO (Jefe Tecnología)',
        'CISO' => 'CISO (Seguridad)'
    ];
    return $nombres[$rol] ?? $rol;
}

/**
 * Obtener iniciales del nombre para avatar
 * @param string $nombre Nombre completo
 * @return string Dos iniciales mayúsculas
 */
function obtener_iniciales(string $nombre): string {
    $palabras = explode(' ', trim($nombre));
    $inicial1 = substr($palabras[0] ?? '', 0, 1);
    $inicial2 = substr($palabras[1] ?? '', 0, 1);
    return strtoupper($inicial1 . $inicial2);
}

?>
