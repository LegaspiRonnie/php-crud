<?php
// 1. Include Composer's autoloader
require_once __DIR__ . '/vendor/autoload.php';

// 2. Load the environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();


$db_host     = $_ENV['DB_HOST'];
$db_user     = $_ENV['DB_USER'];
$db_password = $_ENV['DB_PASSWORD'];
$db_name     = $_ENV['DB_NAME'];

$conn = new mysqli($db_host,
                   $db_user, 
                   $db_password, 
                   $db_name
                   );

if ($conn->connect_error) {
    echo "cant connect to the database";
    exit;
}
echo "connected";