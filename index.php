<?php 
ob_start();
include 'header.php';

if($isLoggedIn){
    $userId = $_SESSION['user_id'];
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ordered'])) {
    $userId = $_SESSION['user_id'];
    $total = $_POST['total'];
    $firstName = $_POST['first-name'];
    $lastName = $_POST['last-name'];
    $mobilePhone = $_POST['phone-nb'];
    $landline = isset($_POST['landline']) ? $_POST['landline'] : null;
    $address = $_POST['address'];
    $city = $_POST['city'];
    $email = $_POST['email'];
    $notes = isset($_POST['notes']) ? $_POST['notes'] : null;
    $total = $_POST['total'];
    $orderId = insertOrder($userId,$total,$firstName, $lastName, $mobilePhone, $landline, $address, $city, $email, $notes);
    insertOrderInfo($orderId, $userId);
    deleteCart($userId); 
    header("Location: index.php"); 
    exit();
}
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['productId']) && isset($_POST['addtocartoutside'])) {
    $productId = intval($_POST['productId']);
    $userId = $_SESSION['user_id'];
    addToCartDB($userId, $productId, 1);
    header('location: index.php');
    exit();

}

$flash_message = '';
$flash_message_class = 'flash-message'; // Default class

if (isset($_SESSION['flash_message'])) {
    $flash_message = $_SESSION['flash_message'];
    if ($flash_message === 'Order placed successfully!') {
        $flash_message_class .= ' success';
    }
    unset($_SESSION['flash_message']);
}

ob_end_flush();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Brands</title>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css"/>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css"/>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>
    <link rel="stylesheet" href="./style/index.css">
    <style>
        .flash-message {
            text-align: center;
            padding: 10px;
            color: #721c24;
            border: 1px solid #f5c6cb;
            border-radius: 5px;
            margin-bottom: 15px;
        }

        .flash-message.success {
            background-color: #d4edda;
            color: #155724;
            border-color: #c3e6cb;
        }
    </style>


</head>
<body>
    <?php if ($flash_message): ?>
        <div class="<?php echo htmlspecialchars($flash_message_class); ?>">
            <?php echo htmlspecialchars($flash_message); ?>
        </div>
    <?php endif; ?>

    <div class="container" style="margin-top:1rem">
        <?php
            include_once'./classes/brands.php';
            echo '<div><h2>Our Brands:<h2></div>';
            echo Brand::displayBrands(); 
            echo displayLatestProducts();
            echo displayFeaturedSubCategories();
            echo displaySelectedProducts();
            echo displayMostSoldProducts();
            echo '<hr>';
            echo '<div><h1>Reviews:<h1></div>';
            echo getReviews();
        ?>
    </div>

    
    

    <script src="./scripts/index.js"></script>
    <script src="footer.js"></script>
</body>
</html>