<?php
require_once('./db.php');

if($_SERVER['REQUEST_METHOD'] === 'GET') {
    
    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

    if ($id === false || $id === null) {
        http_response_code(400);
        exit('A valid product id is required.');
    }

    //make query for getting all the data of all products
    $sql = "SELECT * FROM php_crud_products WHERE id = ?";
    //prepare the query
    $stmt = $conn->prepare($sql);
    //bind parameters
    $stmt->bind_param('i', $id);
    $stmt->execute();

    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        // put table datas
        echo "ID: " . $row['id'] . " - Name: " . $row['name'] . " Description: " .$row['description'] . " Price: " . $row['price'] . " Quantity: " . $row['quantity'] . "<br>";
    }

}