<?php
include 'adminheader.php';



$chatId = isset($_GET['chat_id']) ? intval($_GET['chat_id']) : 0;
$status = isset($_GET['status']) ? $_GET['status'] : 'ongoing';


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Chat Details</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h2>Chat Details</h2>
        <?php viewChatInfoAdmin($chatId,$status); ?>
    </div>



    <script>
    let chatId = <?php echo $chatId; ?>;
    </script> 
    <script src="../scripts/adminchatinfo.js"></script>
    
</body>
</html>