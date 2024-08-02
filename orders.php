<?php
include 'header.php';
$userId = $_SESSION['user_id'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders</title>
</head>
<body>
    <div>
        <?php orders($userId); ?> 
    </div>
    <script src="footer.js"></script>
</body>
</html>