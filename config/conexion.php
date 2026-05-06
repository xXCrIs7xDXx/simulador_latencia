<?php
/**
 * ============================================================
 * CONFIGURACIÓN Y CONEXIÓN A BASE DE DATOS
 * ============================================================
 * Nombre: config/conexion.php
 * Propósito: PDO Singleton para gestionar la conexión a MySQL
 * Patrón: Singleton Pattern
 * Autor: Arquitecto Senior PHP/MySQL
 * Fecha: Mayo 2026
 * ============================================================
 */

// Credenciales de conexión (ajustar según tu entorno XAMPP)
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'simulador_latencia');

/**
 * Clase Database - Singleton Pattern
 * Garantiza una única instancia de conexión PDO en toda la aplicación
 */
class Database {
    private static ?PDO $instance = null;

    /**
     * Obtener instancia única de la conexión PDO
     * @return PDO
     * @throws PDOException Si falla la conexión
     */
    public static function getInstance(): PDO {
        if (self::$instance === null) {
            try {
                $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
                self::$instance = new PDO(
                    $dsn,
                    DB_USER,
                    DB_PASS,
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES => false,
                        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
                    ]
                );
            } catch (PDOException $e) {
                // Loguear errores internamente, mostrar mensaje genérico al usuario
                error_log('Error de conexión BD: ' . $e->getMessage());
                die('Error de conexión a la base de datos. Por favor, contacte al administrador.');
            }
        }
        return self::$instance;
    }

    /**
     * Evitar clonación del singleton
     */
    private function __clone() {}

    /**
     * Evitar deserialización del singleton
     */
    public function __wakeup() {
        throw new Exception('No se puede deserializar un Singleton');
    }
}

// Obtener instancia global de PDO
$pdo = Database::getInstance();

?>
