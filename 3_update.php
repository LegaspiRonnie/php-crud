<?php
require_once('./db.php');
session_start();

/* 
   REPLACED: Binago ang pagkuha ng ID. 
   Tinitingnan muna kung ito ay galing sa $_GET (kapag unang loading ng page) 
   o galing sa $_POST (kapag isinubmit na ang form).
*/
$id = '';
if (isset($_GET['id'])) {
    $id = trim($_GET['id']);
} elseif (isset($_POST['id'])) {
    $id = trim($_POST['id']);
}

// Validation para sa ID (Tatakbo pareho sa GET at POST)
if($id === '') {
    $_SESSION['status'] = 'error';
    $_SESSION['message'] = 'ID is required';
    // REPLACED: Inalis ang auto-redirect dito upang maiwasan ang infinite loop kapag nag-error ang ID habang walang form
    echo "Error: ID is required.";
    exit;
}

if(!is_numeric($id) || (int)$id <= 0) { // REPLACED: Pinagsama ang numeric at positive validation
    $_SESSION['status'] = 'error';
    $_SESSION['message'] = 'Id must be a valid positive number';
    echo "Error: Invalid ID.";
    exit;
}

$id = (int)$id;
$result = null; // REPLACED: Ginawang default value para maiwasan ang undefined variable error sa HTML

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $sql = "SELECT * FROM php_crud_products WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows === 0) { // REPLACED: Inayos ang lohika mula `!$result->num_rows > 0` tungo sa `=== 0`
        $_SESSION['status'] = 'error';
        $_SESSION['message'] = 'Product could not be found';
        echo "Product not found.";
        exit;
    }

} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // REPLACED: Inayos ang pagkakasunod-sunod ng ?? at trim para hindi mag-error ang trim kung null ang $_POST key
    $image       = isset($_POST['image']) ? trim($_POST['image']) : '';
    $name        = isset($_POST['name']) ? trim($_POST['name']) : '';
    $description = isset($_POST['description']) ? trim($_POST['description']) : '';
    $price       = isset($_POST['price']) ? trim($_POST['price']) : '';
    $quantity    = isset($_POST['quantity']) ? trim($_POST['quantity']) : '';

    if($name === '') {
        $_SESSION['status'] = 'error';
        $_SESSION['message'] = 'Name is required';
        header("Location: " . $_SERVER['PHP_SELF'] . "?id=" . $id); // REPLACED: Idinagdag ang ?id=$id sa redirect
        exit;
    }
    if($price === '') {
        $_SESSION['status'] = 'error';
        $_SESSION['message'] = 'Price is required';
        header("Location: " . $_SERVER['PHP_SELF'] . "?id=" . $id);
        exit;
    }
    if(!is_numeric($price)) {
        $_SESSION['status'] = 'error';
        $_SESSION['message'] = 'Price must be a valid number';
        header("Location: " . $_SERVER['PHP_SELF'] . "?id=" . $id);
        exit;
    }
    if($quantity === '') {
        $_SESSION['status'] = 'error';
        $_SESSION['message'] = 'Quantity is required'; // REPLACED: Inayos ang maling text mula 'Id is required' tungo sa 'Quantity is required'
        header("Location: " . $_SERVER['PHP_SELF'] . "?id=" . $id);
        exit;
    }
    if(!is_numeric($quantity)) {
        $_SESSION['status'] = 'error';
        $_SESSION['message'] = 'Quantity must be a valid number';
        header("Location: " . $_SERVER['PHP_SELF'] . "?id=" . $id);
        exit;
    }

    $sql = "UPDATE php_crud_products 
            SET image=?, name=?, description=?, price=?, quantity=? 
            WHERE id=?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssdii", $image, $name, $description, $price, $quantity, $id);
    
    if($stmt->execute()) { // REPLACED: Dapat ang `$stmt->execute()` ang suriin, hindi ang `$stmt` object mismo
        $_SESSION['status'] = 'success';
        $_SESSION['message'] = "Product updated successfully";
        header("Location: index.php"); // REPLACED: Nag-redirect pagkatapos mag-update para ma-refresh ang form data
        exit;
    } else {
        echo 'Can\'t update product';
        exit;
    }
}
?>

<h2>EDIT PRODUCT</h2>

<!-- Notification Alert -->
<?php if (isset($_SESSION['message'])): ?>
    <div style="color: <?php echo $_SESSION['status'] === 'success' ? 'green' : 'red'; ?>;">
        <?php 
            echo $_SESSION['message']; 
            unset($_SESSION['message']);
            unset($_SESSION['status']);
        ?>
    </div>
    <br>
<?php endif; ?>

<!-- REPLACED: Kung galing sa POST redirect ang page, kailangan nating kunin uli ang data para may laman ang mga field -->
<?php 
if ($_SERVER['REQUEST_METHOD'] === 'POST' || $result === null) {
    $sql = "SELECT * FROM php_crud_products WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
}
?>

<!-- REPLACED: Siguraduhing ipasa ang id sa action URL para hindi mawala ang parameters -->
<form action="<?php echo $_SERVER['PHP_SELF'] . '?id=' . $id; ?>" method="POST">
  <?php if($row = $result->fetch_assoc()) { ?>
  
  <!-- REPLACED: Ginawang hidden input ang ID upang maisama ito nang ligtas sa POST request nang hindi nakikita bilang text box -->
  <input type="hidden" name="id" value="<?= htmlspecialchars($row['id']); ?>">
  
  <label for="image">Image (URL/Path):</label><br>
  <!-- REPLACED: Idinagdag ang value ng image at gumamit ng htmlspecialchars para sa seguridad -->
  <input type="text" id="image" name="image" value="<?= htmlspecialchars($row['image']?? '') ; ?>"><br><br>
  
  <label for="name">Name:</label><br>
  <!-- REPLACED: Idinagdag ang 'echo' gamit ang shorthand na  -->
  <input type="text" id="name" name="name" value="<?= htmlspecialchars($row['name'] ?? ''); ?>" ><br><br>
  
  <label for="description">Description:</label><br>
  <!-- REPLACED: Inilagay ang value sa LOOB ng textarea tag, inalis ang value attribute -->
  <textarea id="description" name="description" rows="4"><?= htmlspecialchars($row['description']?? '') ; ?></textarea><br><br>
  
  <label for="price">Price:</label><br> 
  <input type="number" id="price" name="price" step="0.01" value="<?= htmlspecialchars($row['price'] ?? ''); ?>"><br><br>
  
  <label for="quantity">Quantity:</label><br>
  <input type="number" id="quantity" name="quantity" value="<?= htmlspecialchars($row['quantity'] ?? ''); ?>"><br><br>
  
  <input type="submit" value="Update Product">
  <?php  } ?>
</form>
