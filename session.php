<?php
session_start();

function getStatus(string $status, string $message) {
    $_SESSION['status'] = $status;
    $_SESSION['message'] = $message;
}