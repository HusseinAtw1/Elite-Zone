<?php
ob_start();
include 'adminheader.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['chat_id'])) {
    $chatId = $_POST['chat_id'];
    closeChat($chatId);
    header('location: adminchats.php?action=ongoing');
    exit();
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Chat Overview</title>
    
</head>
<body>
    <div class="container mt-4">
        
        <?php 
        if($_SERVER['REQUEST_METHOD'] == 'GET') {
            if(isset($_GET['action'])) {
                $action = $_GET['action'];
                if($action == "ongoing") {
                    viewChatsAdminOngoing();
                }
                elseif($action == "closed") {
                    viewChatsAdminClosed();
                }
            }
        }
        ob_end_flush();
        ?>
    </div>
</body>
</html>