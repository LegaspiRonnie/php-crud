<?php
require_once('./db.php');
require_once('./session.php');
// checkAuth();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $confirm_password = trim($_POST['confirm-password'] ?? '');
    $age      = trim($_POST['age'] ?? '');

    $image_new_name = null;
    $image = $_FILES['picture'] ?? null;

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


    if($username == '') {
        getStatus('error', 'Username is required');
        header("Location: " . $_SERVER['PHP_SELF']); // REPLACED: Nagdagdag ng redirect bago mag-exit para hindi maging blangko ang screen
        exit;
    }
    if($email == '') {
        getStatus('error', 'Email is required');
        header("Location: " . $_SERVER['PHP_SELF']); // REPLACED: Nagdagdag ng redirect bago mag-exit para hindi maging blangko ang screen
        exit;
    }
    if($password == '') {
        getStatus('error', 'password is required');
        header("Location: " . $_SERVER['PHP_SELF']); // REPLACED: Nagdagdag ng redirect bago mag-exit para hindi maging blangko ang screen
        exit;
    }
    if($confirm_password == '') {
        getStatus('error', 'password confirmation is required');
        header("Location: " . $_SERVER['PHP_SELF']); // REPLACED: Nagdagdag ng redirect bago mag-exit para hindi maging blangko ang screen
        exit;
    }
    if($password != $confirm_password) {
        getStatus('error', 'Password confirmation did not match');
        header("Location: " . $_SERVER['PHP_SELF']); // REPLACED: Nagdagdag ng redirect bago mag-exit para hindi maging blangko ang screen
        exit;
    }
    if($age == '') {
        getStatus('error', 'Age is required');
        header("Location: " . $_SERVER['PHP_SELF']); // REPLACED: Nagdagdag ng redirect bago mag-exit para hindi maging blangko ang screen
        exit;
    }
    if(!is_numeric($age) || $age < 0) {
        getStatus('error', 'Age must be a valid number');
        header("Location: " . $_SERVER['PHP_SELF']); // REPLACED: Nagdagdag ng redirect bago mag-exit para hindi maging blangko ang screen
        exit;
    }
    
    
    try {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO php_crud_users (picture, username, email, password, age)
                VALUES (?,?,?,?,?)";
        $stmt = $conn->prepare($sql);
        
        /* 
           REPLACED: Binago ang "sssii" tungo sa "sssdi".
           Ang 'd' ay para sa double/float (price), at ang 'i' ay para sa integer (quantity).
        */
        $stmt->bind_param("ssssi", $image_new_name , $username, $email, $password_hash, $age);
        
        if($stmt->execute()){
            getStatus('success', 'Registerd Successfully');
            $_SESSION['username'] = $username;
            $_SESSION['email'] = $email;
            header("Location: index.php"); // REPLACED: Nagdagdag ng redirect para makita ang success message sa form
            exit;
        } else {
            getStatus('error', "Can't Register");
            header("Location: " . $_SERVER['PHP_SELF']); // REPLACED: Mas magandang itago sa session ang error kaysa mag-echo lang
            exit;
        }
       
    } catch (Exception $e) {
        getStatus('error', "Can't Register".$e->getMessage());
        header("Location: " . $_SERVER['PHP_SELF']); // REPLACED: Nagdagdag ng redirect
        exit;
    }
}
?>
<h2>CREATE PRODUCT</h2>

<?php require_once('./alert.php'); ?>

<form action="" method="POST" enctype="multipart/form-data">
  <label for="picture">Peicture:</label><br>
  <input type="file" id="picture" name="picture" ><br><br>
  <label for="username">Username:</label><br>
  <input type="text" id="username" name="username" ><br><br>
  <label for="email">Email:</label><br>
  <input id="email" name="email" ><br><br>
  <label for="password">password:</label><br> <!-- REPLACED: Ginawang capital ang 'P' sa label -->
  <input type="password" id="password" name="password" ><br><br>
  <label for="confirm-password">Confirm - password:</label><br> <!-- REPLACED: Ginawang capital ang 'P' sa label -->
  <input type="password" id="confirm-password" name="confirm-password" ><br><br>
  <label for="age">age:</label><br>
  <input type="number" id="age" name="age" ><br><br>
  <input type="submit" value="Submit">
</form>
