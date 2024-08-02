<?php 
ob_start(); 
include 'header.php'; 
$userId = $_SESSION['user_id']; 

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['vieworder'])) {
    $orderId = $_POST['orderId'];
    $_SESSION['viewOrderId'] = $orderId;
    header("Location: vieworderinfo.php");
    exit(); 
}

if (isset($_SESSION['viewOrderId'])) {
    $orderId = $_SESSION['viewOrderId'];
} 
else {
    echo "No order ID specified.";
    exit();
}

ob_end_flush(); 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Info</title>
</head>
<body>
    <?php          
        viewOrderInfo($userId, $orderId);
    ?>

<script src="footer.js"></script>
</body>
</html>