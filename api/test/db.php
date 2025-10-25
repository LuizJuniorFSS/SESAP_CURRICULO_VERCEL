<?php
// Simple DB connection test endpoint
header('Content-Type: application/json; charset=utf-8');

try {
    $configPhp = __DIR__ . '/../../includes/config.php';
    $configExample = __DIR__ . '/../../includes/config.example.php';

    if (file_exists($configPhp)) {
        require_once $configPhp;
        $configSource = 'config.php';
    } elseif (file_exists($configExample)) {
        require_once $configExample;
        $configSource = 'config.example.php';
    } else {
        throw new Exception('Arquivo de configuração não encontrado.');
    }

    $pdo = null;
    $driver = 'pdo_mysql';

    if (function_exists('getConnection')) {
        $pdo = getConnection();
    } elseif (function_exists('getPDOConnection')) {
        $pdo = getPDOConnection();
    }

    if (!$pdo) {
        // Fallback manual usando env vars
        $host = getenv('DB_HOST') ?: 'localhost';
        $db   = getenv('DB_NAME') ?: 'sesap_curriculo';
        $user = getenv('DB_USER') ?: 'root';
        $pass = getenv('DB_PASS') ?: '';
        $charset = getenv('DB_CHARSET') ?: 'utf8mb4';
        $dsn = "mysql:host={$host};dbname={$db};charset={$charset}";
        $pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    }

    // Pequeno teste
    $version = $pdo->query('SELECT VERSION()')->fetchColumn();
    // timezone pode depender da configuração do provedor
    $tz = null;
    try {
        $tz = $pdo->query('SELECT @@time_zone')->fetchColumn();
    } catch (Throwable $e) {
        $tz = 'unknown';
    }

    echo json_encode([
        'ok' => true,
        'config_source' => $configSource,
        'driver' => $driver,
        'db' => [
            'host' => getenv('DB_HOST') ?: 'localhost',
            'name' => getenv('DB_NAME') ?: 'sesap_curriculo',
            'charset' => getenv('DB_CHARSET') ?: 'utf8mb4',
        ],
        'mysql_version' => $version,
        'time_zone' => $tz,
        'env_present' => [
            'DB_HOST' => (bool) getenv('DB_HOST'),
            'DB_NAME' => (bool) getenv('DB_NAME'),
            'DB_USER' => (bool) getenv('DB_USER'),
            'DB_PASS' => (bool) getenv('DB_PASS'),
        ],
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'ok' => false,
        'error' => $e->getMessage(),
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
}