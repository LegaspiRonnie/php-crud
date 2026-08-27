<?php
require_once('./db.php');
require_once('./session.php');
checkAuth();
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? '';
    if($id === '') {
        getStatus('error', 'ID is required');
        header('Location: index.php');
        exit;
    }
    if(!is_numeric($id) || (int)$id < 0) {
        getStatus('error', 'ID must be a valid positive number');
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
        getStatus('success', 'product deleted successfully!');
        header('Location: index.php');
        exit;
    } else {
        getStatus('error', 'Cant delete product!');
        header('Location: index.php');
        exit;
    }
}