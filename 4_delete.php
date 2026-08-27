<?php
require_once('./db.php');
session_start();
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? '';
    if($id === '') {
        $_SESSION['status'] = 'error';
        $_SESSION['message'] = 'ID is required';
        header('Location: index.php');
        exit;
    }
    if(!is_numeric($id) || (int)$id < 0) {
        $_SESSION['status'] = 'error';
        $_SESSION['message'] = 'ID must be a valid positive number';
        header('Location: index.php');
        exit;
    }
    //make query for getting all the data of all products
    $sql = "DELETE FROM php_crud_products WHERE id = ?";
    //prepare the query
    $stmt = $conn->prepare($sql);
    //bind parameters
    $stmt->bind_param('i', $id);
    if($stmt->execute()) {
        $_SESSION['status'] = 'success';
        $_SESSION['message'] = 'product deleted successfully!';
        header('Location: index.php');
        exit;
    } else {
        $_SESSION['status'] = 'error';
        $_SESSION['message'] = 'Cant delete product!';
        header('Location: index.php');
        exit;
    }
}