<?php
require_once __DIR__ . '/session.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    logout();
}

header('Location: index.php');
exit;
