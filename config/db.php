<?php
// ============================================================
//  Rentings.lol — Configuración de Base de Datos
//  Alojado en NAS
// ============================================================

define('DB_HOST', 'localhost');
define('DB_USER', 'user');   // Cambiar según tu phpMyAdmin
define('DB_PASS', 'passwordcontra');      // Cambiar según tu contraseña
define('DB_NAME', 'rentings_db');
define('SITE_URL', 'https://rentingslol.com');
define('SITE_NAME', 'Rentings.lol');

// Conexión PDO
function getDB() {
    static $pdo = null;
    if ($pdo === null) {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            die('<div style="text-align:center;padding:50px;font-family:sans-serif;background:#1a1a1a;color:#f97316;min-height:100vh;">
                <h2>⚠️ Error de Conexión</h2>
                <p>No se pudo conectar a la base de datos.</p>
                <small>' . htmlspecialchars($e->getMessage()) . '</small>
            </div>');
        }
    }
    return $pdo;
}

// Funciones de sesión
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function getCurrentUser() {
    if (!isLoggedIn()) return null;
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM usuarios WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch();
}

function redirect($url) {
    header("Location: $url");
    exit;
}

function sanitize($str) {
    return htmlspecialchars(strip_tags(trim($str)));
}
?>
