<?php 

include 'adminheader.php';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>

    <?php   
        if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['order_id'])){
            $orderId = $_POST['order_id'];
            adminOrderInfo($orderId);  
        }
    ?>
    
</body>
</html>