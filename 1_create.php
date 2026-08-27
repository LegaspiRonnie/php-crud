<?php
require_once('./db.php');
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $image       = $_POST['image'] ?? '';
    $name        = $_POST['name'] ?? '';
    $description = $_POST['description'] ?? '';
    $price       = $_POST['price'] ?? '';
    $quantity    = (int)$_POST['quantity'] ?? '';

    if($name == '') {
        $_SESSION['status'] = 'error';
        $_SESSION['message'] = 'Name is required';
        exit;
    }
    if($price == '') {
        $_SESSION['status'] = 'error';
        $_SESSION['message'] = 'Price is required';
        exit;
    }
    if(!is_numeric($price)) {
        $_SESSION['status'] = 'error';
        $_SESSION['message'] = 'Price must be a valid number';
        exit;
    }
    if($quantity == '') {
        $_SESSION['status'] = 'error';
        $_SESSION['message'] = 'Quntity is required';
        exit;
    }
    if(!is_numeric($quantity)) {
        $_SESSION['status'] = 'error';
        $_SESSION['message'] = 'Quantity must be a valid number';
        exit;
    }
    
    try {

    } catch (Exception $e) {
        
    }
    $sql = "INSERT INTO php_crud_products (image, name, description, price, quantity)
            VALUES (?,?,?,?,?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssii", $image, $name, $description, $price, $quantity);
    if($stmt->execute()){
        echo "Added Successfully!";
    } else {
        echo "can't add product";
        exit;
    }
    exit;
    
}