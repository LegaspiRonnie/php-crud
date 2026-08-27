<!-- Dagdag: Code para ipakita ang status notification (Success o Error) sa user -->
<?php
require_once('./session.php');
?>
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