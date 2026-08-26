<?php


class Database
{
    <?php
// 1. Include the Composer autoloader
require_file_exists(__DIR__ . '/vendor/autoload.php') ? require __DIR__ . '/vendor/autoload.php' : die('Run composer install');

// 2. Load the .env file from the current directory
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// 3. Access your variables anywhere in your code
$db_host = $_ENV['DB_HOST'];
$db_user = $_ENV['DB_USER'];
$db_pass = $_ENV['DB_PASS'];

echo "Connecting to database at: " . $db_host;
?>
}