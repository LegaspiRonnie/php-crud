<?php
require_once('./db.php');
session_start();
if($_SERVER['REQUEST_METHOD'] === 'GET') {
    
    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT) ?? null;
    if($id === null || $id === false || $id <= 0) {
        http_response_code(400);
        $_SESSION['status'] = 'error';
        $_SESSION['message'] = 'Id must be a valid number';
    } else {
        //make query for getting all the data of all products
        $sql = "SELECT * FROM php_crud_products WHERE id = ?";
        //prepare the query
        $stmt = $conn->prepare($sql);
        //bind parameters
        $stmt->bind_param('i', $id);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            http_response_code(404);
            $_SESSION['status'] = 'error';
            $_SESSION['message'] = 'Product ID does not exist';
        } else {
            while ($row = $result->fetch_assoc()) {
                // put table datas
                echo "ID: " . $row['id'] . " - Name: " . $row['name'] . " Description: " .$row['description'] . " Price: " . $row['price'] . " Quantity: " . $row['quantity'] . "<br>";
            }
        }
    }

}
?>
<p><?php require_once('./alert.php'); ?></p>