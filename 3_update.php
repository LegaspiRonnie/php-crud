<?php
require_once('./db.php');
require_once('./session.php');

/* 
   REPLACED: Binago ang pagkuha ng ID. 
   Tinitingnan muna kung ito ay galing sa $_GET (kapag unang loading ng page) 
   o galing sa $_POST (kapag isinubmit na ang form).
*/
$id = '';
$image_path = 'uploads/images/';
if (isset($_GET['id'])) {
    $id = trim($_GET['id']);
} elseif (isset($_POST['id'])) {
    $id = trim($_POST['id']);
}

// Validation para sa ID (Tatakbo pareho sa GET at POST)
if($id === '') {
    getStatus('error', 'ID is required');
    // REPLACED: Inalis ang auto-redirect dito upang maiwasan ang infinite loop kapag nag-error ang ID habang walang form
    echo "Error: ID is required.";
    exit;
}

if(!is_numeric($id) || (int)$id <= 0) { // REPLACED: Pinagsama ang numeric at positive validation
    getStatus('error', 'Id must be a valid positive number');
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
        getStatus('error', 'Product could not be found');
        echo "Product not found.";
        exit;
    }

} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // REPLACED: Inayos ang pagkakasunod-sunod ng ?? at trim para hindi mag-error ang trim kung null ang $_POST key
    $name        = isset($_POST['name']) ? trim($_POST['name']) : '';
    $description = isset($_POST['description']) ? trim($_POST['description']) : '';
    $price       = isset($_POST['price']) ? trim($_POST['price']) : '';
    $quantity    = isset($_POST['quantity']) ? trim($_POST['quantity']) : '';
    $image = $_FILES['image'] ?? null;
    $image_new_name = null;

    $current_stmt = $conn->prepare("SELECT image FROM php_crud_products WHERE id = ?");
    $current_stmt->bind_param('i', $id);
    $current_stmt->execute();
    $current_product = $current_stmt->get_result()->fetch_assoc();
    $image_new_name = $current_product['image'] ?? null;

    if ($image && $image['error'] !== UPLOAD_ERR_NO_FILE) {
        if ($image['error'] !== UPLOAD_ERR_OK) {
            getStatus('error', 'There is an error in your image file');
            header("Location: " . $_SERVER['PHP_SELF']); // REPLACED: Nagdagdag ng redirect bago mag-exit para hindi maging blangko ang screen
            exit;
        }

        $image_ext = strtolower(pathinfo($image['name'], PATHINFO_EXTENSION));
        $allowed_image = ['jpg', 'png', 'jpeg'];
        if (!in_array($image_ext, $allowed_image, true)) {
            getStatus('error', 'Image type not allowed; upload only JPG, JPEG, or PNG');
            header("Location: " . $_SERVER['PHP_SELF']); // REPLACED: Nagdagdag ng redirect bago mag-exit para hindi maging blangko ang screen
            exit;
        }

        if ($image['size'] > 1000001) {
            getStatus('error', 'Image size too large');
            header("Location: " . $_SERVER['PHP_SELF']); // REPLACED: Nagdagdag ng redirect bago mag-exit para hindi maging blangko ang screen
            exit;
        }

        $image_new_name = uniqid('', true) . '.' . $image_ext;
        $image_destination = 'uploads/images/' . $image_new_name;
        if (!move_uploaded_file($image['tmp_name'], $image_destination)) {
            getStatus('error', 'Image could not be uploaded');
            header("Location: " . $_SERVER['PHP_SELF'] . "?id=" . $id);
            exit;
        }
    }

    if($name === '') {
        getStatus('error', 'Name is required');
        header("Location: " . $_SERVER['PHP_SELF'] . "?id=" . $id); // REPLACED: Idinagdag ang ?id=$id sa redirect
        exit;
    }
    if($price === '') {
        getStatus('error', 'Price is required');
        header("Location: " . $_SERVER['PHP_SELF'] . "?id=" . $id);
        exit;
    }
    if(!is_numeric($price)) {
        getStatus('error', 'Price must be a valid number');
        header("Location: " . $_SERVER['PHP_SELF'] . "?id=" . $id);
        exit;
    }
    if($quantity === '') {
        getStatus('error', 'Quantity is required');
        header("Location: " . $_SERVER['PHP_SELF'] . "?id=" . $id);
        exit;
    }
    if(!is_numeric($quantity)) {
        getStatus('error', 'Quantity must be a valid number');
        header("Location: " . $_SERVER['PHP_SELF'] . "?id=" . $id);
        exit;
    }

    $sql = "UPDATE php_crud_products 
            SET image=?, name=?, description=?, price=?, quantity=? 
            WHERE id=?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssdii", $image_new_name, $name, $description, $price, $quantity, $id);
    
    if($stmt->execute()) { // REPLACED: Dapat ang `$stmt->execute()` ang suriin, hindi ang `$stmt` object mismo
        getStatus('success', 'Product updated successfully');
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
<form action="<?php echo $_SERVER['PHP_SELF'] . '?id=' . $id; ?>" method="POST" enctype="multipart/form-data">
  <?php if($row = $result->fetch_assoc()) { ?>
  
  <!-- REPLACED: Ginawang hidden input ang ID upang maisama ito nang ligtas sa POST request nang hindi nakikita bilang text box -->
  <input type="hidden" name="id" value="<?= htmlspecialchars($row['id']); ?>">
  
  <label for="image">Image (URL/Path):</label><br>
  <!-- REPLACED: Idinagdag ang value ng image at gumamit ng htmlspecialchars para sa seguridad -->
  <?php if($row['image'] === null || $row['image'] === '') {?>
            <img 
            id="image-preview"
            width="50"
            height="50"
            src="<?= $image_path . 'product_placeholder.png' ?>" 
            alt="Product Image"
        >
        <?php } else { ?>

        <img 
            id="image-preview"
            width="50"
            height="50"
            src="<?= $image_path . htmlspecialchars($row['image'] ?? null) ?>" 
            alt="Product Image"
        >
        <?php } ?>
    <input type="file" id="image" name="image" accept=".jpg,.jpeg,.png" onchange="previewImage(event)"><br><br>
  
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

<script>
function previewImage(event) {
    const file = event.target.files[0];
    if (file) {
        document.getElementById('image-preview').src = URL.createObjectURL(file);
    }
}
</script>
