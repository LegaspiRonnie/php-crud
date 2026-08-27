<?php
require_once('./db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $image = $_POST['image'];
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $quantity = $_POST['quantity'];
    $id = $_GET['id'];

    $sql = "UPDATE php_crud_products SET image=?, name=?, description=?, price=?, quantity=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssdii", $image, $name, $description, $price, $quantity, $id );
    $stmt->execute();
    if($stmt) {
        echo "product updated successfully";
    } else {
        echo 'cant update product';
        exit;
    }
    exit;
}
