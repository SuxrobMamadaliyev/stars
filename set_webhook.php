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

// Get environment variables with validation
$requiredEnvVars = ['TELEGRAM_BOT_TOKEN', 'ADMIN_CHAT_ID'];
$missingVars = [];

foreach ($requiredEnvVars as $var) {
    if (!getenv($var)) {
        $missingVars[] = $var;
    }
}

if (!empty($missingVars) && getenv('APP_ENV') !== 'production') {
    die('Error: The following required environment variables are missing: ' . implode(', ', $missingVars));
}

// Define constants
define("API_KEY", getenv('TELEGRAM_BOT_TOKEN'));
$admin = getenv('ADMIN_CHAT_ID');
$bot = 'starsbot'; // Will be updated after bot initialization
$soat = date('H:i');
$sana = date("d.m.Y");

// Function to log errors
function logError($message, $data = null) {
    $logMessage = '[' . date('Y-m-d H:i:s') . '] ' . $message . PHP_EOL;
    if ($data !== null) {
        $logMessage .= 'Data: ' . print_r($data, true) . PHP_EOL;
    }
    $logMessage .= '------------------------' . PHP_EOL;
    
    $logFile = __DIR__ . '/../storage/logs/error.log';
    file_put_contents($logFile, $logMessage, FILE_APPEND);
}

// Database connection function with improved error handling
function getDbConnection() {
    static $connection = null;
    
    if ($connection !== null) {
        return $connection;
    }

    try {
        if (getenv('RENDER')) {
            // For Render PostgreSQL
            $dbUrl = getenv('DATABASE_URL');
            if (!$dbUrl) {
                throw new Exception('DATABASE_URL environment variable is not set');
            }
            
            $dbParts = parse_url($dbUrl);
            if (!$dbParts) {
                throw new Exception('Failed to parse DATABASE_URL');
            }
            
            $dsn = sprintf(
                'pgsql:host=%s;port=%s;dbname=%s',
                $dbParts['host'],
                $dbParts['port'],
                ltrim($dbParts['path'], '/')
            );
            
            $username = $dbParts['user'] ?? '';
            $password = $dbParts['pass'] ?? '';
            
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_TIMEOUT => 5, // 5 second timeout
                PDO::ATTR_PERSISTENT => false
            ];
            
            $connection = new PDO($dsn, $username, $password, $options);
            
            // Test the connection
            $connection->query('SELECT 1')->fetch();
            
        } else {
            // For local MySQL development
            $requiredDbVars = ['DB_HOST', 'DB_NAME', 'DB_USERNAME', 'DB_PASSWORD'];
            $missingVars = [];
            
            foreach ($requiredDbVars as $var) {
                if (!getenv($var)) {
                    $missingVars[] = $var;
                }
            }
            
            if (!empty($missingVars)) {
                throw new Exception('Missing required database environment variables: ' . implode(', ', $missingVars));
            }
            
            $dsn = sprintf(
                'mysql:host=%s;dbname=%s;charset=utf8mb4',
                getenv('DB_HOST'),
                getenv('DB_NAME')
            );
            
            $options = [
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
