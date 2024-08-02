<?php
ob_start(); // Start output buffering
include 'header.php';

if(!$isLoggedIn) {
    header('location: login.php');
    exit();
}

$userId = $_SESSION['user_id'];
$chatId = null;
$status = null;

if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['chat_id']) && isset($_GET['status'])) {
    $chatId = $_GET['chat_id'];
    $status = $_GET['status'] ;
} 
elseif ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_POST['chatId'])) {
    $chatId = $_POST['chatId'];
    $status = 'ongoing';
} 
elseif($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['create-new-chat'])){
    $newChatId = createNewChat($userId);
    if($newChatId !== null){
        $chatId = $newChatId;
        $status = 'ongoing';
        $redirectUrl = $_SERVER['PHP_SELF'] . "?chat_id=" . urlencode($newChatId) . "&status=" . urlencode($status);
        header("Location: " . $redirectUrl);
        exit();  
    }

    else {
        echo '<script>alert("You already have an ongoing chat")</script>';
        header('location: chats.php');
        exit();
    }
    
}

ob_end_flush(); // End output buffering and send output
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat</title>
    <link rel="stylesheet" href="./style/chatinfo.css">
</head>
<body>

<?php
if ($chatId !== null && $status !== null) {
    viewChatInfo($chatId, $status);
}
?>

<script>let chatId = <?php echo json_encode($chatId); ?>;</script> 
<script src="./scripts/chatinfo.js"></script>
<script src="footer.js"></script>

</body>
</html>
