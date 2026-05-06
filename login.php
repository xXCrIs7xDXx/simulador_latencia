<?php
/**
 * ============================================================
 * PANTALLA DE LOGIN
 * ============================================================
 * Nombre: login.php
 * Propósito: Autenticación de usuarios con Bootstrap
 * Autor: Arquitecto Senior PHP/MySQL
 * Fecha: Mayo 2026
 * ============================================================
 */

// Headers de seguridad obligatorios
header('X-Frame-Options: DENY');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Content-Type: text/html; charset=utf-8');

session_start();

// Si ya tiene sesión, redirigir al dashboard
if (isset($_SESSION['usuario_id'])) {
    header('Location: /simulador_latencia/dashboard.php');
    exit;
}

require_once __DIR__ . '/config/conexion.php';

$error = '';

// Procesar envío del formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = filter_var($_POST['username'] ?? '', FILTER_SANITIZE_STRING);
    $password = $_POST['password'] ?? '';

    // Validación básica
    if (empty($username) || empty($password)) {
        $error = 'Usuario y contraseña son requeridos.';
    } else {
        // Buscar usuario en BD
        try {
            $stmt = $pdo->prepare("SELECT id, nombre, username, password, rol FROM usuarios WHERE username = ?");
            $stmt->execute([$username]);
            $usuario = $stmt->fetch();

            // Verificar usuario y contraseña con bcrypt
            if ($usuario && password_verify($password, $usuario['password'])) {
                // Crear sesión segura
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['usuario_nombre'] = $usuario['nombre'];
                $_SESSION['username'] = $usuario['username'];
                $_SESSION['rol'] = $usuario['rol'];
                $_SESSION['login_time'] = time();

                // Log de auditoría de login
                $ip = $_SERVER['REMOTE_ADDR'] ?? 'desconocida';
                $action = "Login del usuario {$usuario['username']} ({$usuario['rol']})";
                $stmt_audit = $pdo->prepare("
                    INSERT INTO auditoria_flujo (proyecto_id, usuario_id, accion_realizada, ip_origen)
                    VALUES (NULL, ?, ?, ?)
                ");
                $stmt_audit->execute([$usuario['id'], $action, $ip]);

                header('Location: /simulador_latencia/dashboard.php');
                exit;
            } else {
                $error = 'Usuario o contraseña incorrectos.';
            }
        } catch (PDOException $e) {
            error_log('Error en login: ' . $e->getMessage());
            $error = 'Error en la autenticación. Intente nuevamente.';
        }
    }
}

// Verificar si viene de error de sesión expirada
$error_type = $_GET['error'] ?? '';
if ($error_type === 'session_expired') {
    $error = 'Su sesión ha expirado. Por favor, inicie sesión nuevamente.';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Simulador de Latencia</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/simulador_latencia/assets/css/estilo.php" rel="stylesheet">
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <div class="login-header">
                <div class="logo-circle">⚡</div>
                <h1>Simulador de Latencia</h1>
                <p>Sistema de gestión organizacional</p>
            </div>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger" role="alert">
                    <?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="/simulador_latencia/login.php">
                <div class="mb-3">
                    <label for="username" class="form-label">Usuario</label>
                    <input
                        type="text"
                        class="form-control"
                        id="username"
                        name="username"
                        placeholder="Ingrese su usuario"
                        required
                    >
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Contraseña</label>
                    <input
                        type="password"
                        class="form-control"
                        id="password"
                        name="password"
                        placeholder="Ingrese su contraseña"
                        required
                    >
                </div>

                <button type="submit" class="btn btn-primary w-100 mb-3">
                    <i class="bi bi-box-arrow-in-right"></i> Iniciar Sesión
                </button>
            </form>

            <div class="alert alert-info mt-3">
                <strong>Usuarios de prueba:</strong>
                <ul class="mb-0 mt-2 small">
                    <li><strong>sergio_bautista</strong> / dev123 (DEV)</li>
                    <li><strong>rashell_fernandez</strong> / dev123 (DEV)</li>
                    <li><strong>tania_pinto</strong> / cio123 (CIO)</li>
                    <li><strong>cristian_velasco</strong> / cio123 (CIO)</li>
                    <li><strong>jhoseline_marca</strong> / cto123 (CTO)</li>
                    <li><strong>javier_murguia</strong> / ciso123 (CISO)</li>
                </ul>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
