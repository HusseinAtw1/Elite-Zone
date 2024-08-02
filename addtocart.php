<?php
ob_start();
include('header.php');

$productId = $_GET['id'];
if($isLoggedIn){
    $userId = $_SESSION['user_id'];
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['quantity'])) {
    $quantity = intval($_POST['quantity']);
    addToCartDB($userId, $productId, $quantity);
    header('location: addtocart.php?id='.$productId);
    exit(); 
}
$flash_message = '';
if (isset($_SESSION['flash_message'])) {
    $flash_message = $_SESSION['flash_message'];
    unset($_SESSION['flash_message']);
}
$productIDjs = $_GET['id'];
echo '<script>let productIDjs ='.$productIDjs.'</script>';
ob_end_flush();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add To Cart</title>
    <style>
        .flash-message {
            text-align:center;
            padding: 10px;
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            border-radius: 5px;
            margin-bottom: 15px;
        }
    </style>
    

</head>
<body>
    <div class="product">
        <?php getProduct($productId); ?>
    </div>

    <?php if ($flash_message): ?>
        <div class="flash-message">
            <?php echo htmlspecialchars($flash_message); ?>
        </div>
    <?php endif; ?>

        
        
    <script src="./scripts/addtocart.js"></script>
    <script src="footer.js"></script> 
</body>
</html>
