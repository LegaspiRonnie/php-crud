<?php
require_once('./db.php');
require_once('./session.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name             = $_POST['name'] ?? '';
    $description      = $_POST['description'] ?? '';
    $price       = $_POST['price'] ?? '';
    $quantity    = $_POST['quantity'] ?? '';

    $image_new_name = null;
    $image = $_FILES['image'] ?? null;

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
        move_uploaded_file($image['tmp_name'], $image_destination);
    }
    
    /* 
       REPLACED: Inalis ang (double) at (int) casting dito. 
       Kung i-cast agad ito, ang walang lamang input ay magiging 0 at lalaktawan ang check para sa walang laman.
    */


    if($name == '') {
        getStatus('error', 'Name is required');
        header("Location: " . $_SERVER['PHP_SELF']); // REPLACED: Nagdagdag ng redirect bago mag-exit para hindi maging blangko ang screen
        exit;
    }
    if($price == '') {
        getStatus('error', 'Price is required');
        header("Location: " . $_SERVER['PHP_SELF']); // REPLACED: Nagdagdag ng redirect
        exit;
    }
    if(!is_numeric($price)) {
        getStatus('error', 'Price must be a valid number');
        header("Location: " . $_SERVER['PHP_SELF']); // REPLACED: Nagdagdag ng redirect
        exit;
    }
    if($quantity == '') {
        getStatus('error', 'Quantity is required');
        header("Location: " . $_SERVER['PHP_SELF']); // REPLACED: Nagdagdag ng redirect
        exit;
    }
    if(!is_numeric($quantity)) {
        getStatus('error', 'Quantity must be a valid number');
        header("Location: " . $_SERVER['PHP_SELF']); // REPLACED: Nagdagdag ng redirect
        exit;
    }
    
    // SAFE CASTING: Dito na ligtas i-convert ang data pagkatapos ng validation checks
    $price = (float)$price;
    $quantity = (int)$quantity;
    
    try {
        $sql = "INSERT INTO php_crud_products (image, name, description, price, quantity)
                VALUES (?,?,?,?,?)";
        $stmt = $conn->prepare($sql);
        
        /* 
           REPLACED: Binago ang "sssii" tungo sa "sssdi".
           Ang 'd' ay para sa double/float (price), at ang 'i' ay para sa integer (quantity).
        */
        $stmt->bind_param("sssdi", $image_new_name , $name, $description, $price, $quantity);
        
        if($stmt->execute()){
            getStatus('success', 'Product Created Successfully');
            header("Location: index.php"); // REPLACED: Nagdagdag ng redirect para makita ang success message sa form
            exit;
        } else {
            getStatus('error', "Can't add product");
            header("Location: " . $_SERVER['PHP_SELF']); // REPLACED: Mas magandang itago sa session ang error kaysa mag-echo lang
            exit;
        }
       
    } catch (Exception $e) {
        getStatus('error', "Can't create Product");
        header("Location: " . $_SERVER['PHP_SELF']); // REPLACED: Nagdagdag ng redirect
        exit;
    }
}
?>
<h2>CREATE PRODUCT</h2>

<?php require_once('./alert.php'); ?>

<form action="" method="POST" enctype="multipart/form-data">
  <label for="image">Image:</label><br>
  <input type="file" id="image" name="image" ><br><br>
  <label for="name">Name:</label><br>
  <input type="text" id="name" name="name" ><br><br>
  <label for="description">Description:</label><br>
  <textarea id="description" name="description" rows="4"></textarea><br><br>
  <label for="price">Price:</label><br> <!-- REPLACED: Ginawang capital ang 'P' sa label -->
  <input type="number" id="price" name="price" step="0.01"><br><br>
  <label for="quantity">Quantity:</label><br>
  <input type="number" id="quantity" name="quantity" ><br><br>
  <input type="submit" value="Submit">
</form>
