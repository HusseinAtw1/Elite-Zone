<?php
ob_start();
include 'header.php';
if(!$isLoggedIn) {
    $originalUrl = $_SERVER['REQUEST_URI'];
    exit('<script>alert("Please Login");window.location.href = "login.php?redirect=' . urlencode($originalUrl) . '";</script>');
}
else {
$userId = $_SESSION['user_id'];
}
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['close-chat'])){
    $chatId = $_POST['chat_id'];
    closeChat($chatId);
    header('location: chats.php');
    exit();
}
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['chat_id'])) {
    $chatId = $_POST['chat_id'];
    closeChat($chatId);
    header('location: chats.php');
    exit();
}
ob_end_flush();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inbox</title>
    <link rel="stylesheet" href="./style/chats.css">
    

</head>

<body>


    
<div class="container mt-4">
    
    <?php
        viewChats($userId);
    ?>

</div>

<script src="footer.js"></script> 
</body>
</html>


