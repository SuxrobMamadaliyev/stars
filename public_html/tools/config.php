<?php
ob_start();

// Error reporting based on environment
if (getenv('RENDER')) {
    // Production settings for Render
    error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT & ~E_NOTICE);
    ini_set('display_errors', '0');
    ini_set('log_errors', '1');
    ini_set('error_log', __DIR__ . '/../storage/logs/error.log');
} else {
    // Development settings
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
}

date_default_timezone_set('Asia/Tashkent');

// Ensure logs directory exists
if (!file_exists(__DIR__ . '/../storage/logs')) {
    mkdir(__DIR__ . '/../storage/logs', 0755, true);
}

// Get environment variables with defaults
define("API_KEY", getenv('TELEGRAM_BOT_TOKEN') ?: '7878472286:AAErmF7ZPnQmFMYpXTXXbpst0_scZWV0HlA');
$admin = getenv('ADMIN_CHAT_ID') ?: "5735723011";
$bot = @bot('getme', ['bot'])->result->username ?: 'starsbot';
$soat = date('H:i');
$sana = date("d.m.Y");

// Database connection function
function getDbConnection() {
    static $connection = null;
    
    if ($connection !== null) {
        return $connection;
    }

    try {
        if (getenv('RENDER')) {
            // For Render PostgreSQL
            $dbUrl = getenv('DATABASE_URL');
            $dbParts = parse_url($dbUrl);
            
            $dsn = "pgsql:host={$dbParts['host']};port={$dbParts['port']};dbname=" . substr($dbParts['path'], 1);
            $username = $dbParts['user'];
            $password = $dbParts['pass'];
            
            $connection = new PDO($dsn, $username, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } else {
            // For local MySQL development
            $connection = new PDO(
                "mysql:host=" . getenv('DB_HOST') . ";dbname=" . getenv('DB_NAME'),
                getenv('DB_USERNAME'),
                getenv('DB_PASSWORD'),
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4",
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]
            );
        }
        
        return $connection;
    } catch (PDOException $e) {
        error_log('Database connection failed: ' . $e->getMessage());
        if (getenv('RENDER')) {
            die('Database connection error. Please check your configuration.');
        } else {
            die('Local database connection failed. Please check your .env file.');
        }
    }
}

// Initialize database connection
$connect = getDbConnection();





function bot($method, $datas = [])
{
    $url = "https://api.telegram.org/bot" . API_KEY . "/" . $method;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $datas);
    return json_decode(curl_exec($ch));
}