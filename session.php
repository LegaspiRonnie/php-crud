<?php
session_start();

function getStatus(string $status, string $message): void {
    $_SESSION['status'] = $status;
    $_SESSION['message'] = $message;
}

function checkAuth(): void {
    if (empty($_SESSION['username']) || empty($_SESSION['email'])) {
        header('Location: login.php');
        exit;
    }
}

function redirectIfAuthenticated(): void {
    if (!empty($_SESSION['username']) && !empty($_SESSION['email'])) {
        header('Location: index.php');
        exit;
    }
}

function logout(): void {
    unset($_SESSION['username'], $_SESSION['email']);
    session_regenerate_id(true);
    getStatus('success', 'Logged out successfully');
    header('Location: login.php');
    exit;
}

