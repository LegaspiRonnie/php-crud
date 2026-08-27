<?php
require_once('./db.php');

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    

    //make query for getting all the data of all products
    $sql = "DELETE FROM php_crud_products WHERE id = ?";
    //prepare the query
    $stmt = $conn->prepare($sql);
    //bind parameters
    $stmt->bind_param('i', $id);
    if($stmt->execute()) {
        echo "product deleted successfully!";
    } else {
        echo "can't delete product";
        exit;
    }
    exit;
    

}