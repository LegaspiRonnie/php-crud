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
function isAdmin(): void {
    if (strtolower(trim((string)($_SESSION['role'] ?? ''))) !== 'admin') {
        getStatus('error', "You don't have permission");
        header('Location: landing_page.php');
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

