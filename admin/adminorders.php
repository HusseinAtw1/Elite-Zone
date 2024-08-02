<?php
ob_start();
include 'adminheader.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['accepted']) || isset($_POST['rejected'])) {
        $status = isset($_POST['accepted']) ? $_POST['accepted'] : $_POST['rejected'];
        $orderId = $_POST['order_id'];
        $result = updateOrder($status, $orderId);
        if ($result['success']) {
            $_SESSION['flash_message'] = $result['message'];
            $_SESSION['flash_type'] = 'success';
        } else {
            $_SESSION['flash_message'] = $result['message'];
            $_SESSION['flash_type'] = 'error';
        }
        if($status  === 'accepted') {
            header("Location: " . $_SERVER['PHP_SELF'] ."?action=rejected");
        }
        else {
            header("location: ". $_SERVER['PHP_SELF'] ."?action=accepted");
        }
        exit();
    }
    if (isset($_POST['hold'])) {
        $status = $_POST['hold'];
        $orderId = $_POST['order_id'];
        $result = updateOrder($status, $orderId);
        if ($result['success']) {
            $_SESSION['flash_message'] = $result['message'];
            $_SESSION['flash_type'] = 'success';
        } else {
            $_SESSION['flash_message'] = $result['message'];
            $_SESSION['flash_type'] = 'error';
        }
        header("Location: " . $_SERVER['PHP_SELF']."?action=hold");
        exit();
    }
    
    if (isset($_POST['completed'])) {
        $status = $_POST['completed'];
        $orderId = $_POST['order_id'];
        $result = updateOrder($status,$orderId);
        if ($result['success']) {
            $_SESSION['flash_message'] = $result['message'];
            $_SESSION['flash_type'] = 'success';
        } else {
            $_SESSION['flash_message'] = $result['message'];
            $_SESSION['flash_type'] = 'error';
        }
        header("Location: " . $_SERVER['PHP_SELF']."?action=completed");
        exit();
    }
}
    
function displayFlashMessage() {
    if (isset($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message'];
        $type = $_SESSION['flash_type'];
        unset($_SESSION['flash_message']);
        unset($_SESSION['flash_type']);
        
        echo "<div class='alert alert-{$type}'>{$message}</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders</title>
    <style>
    .alert {
        padding: 15px;
        margin-bottom: 20px;
        border: 1px solid transparent;
        border-radius: 4px;
    }
    .alert-success {
        color: #3c763d;
        background-color: #dff0d8;
        border-color: #d6e9c6;
    }
    .alert-error {
        color: #a94442;
        background-color: #f2dede;
        border-color: #ebccd1;
    }
</style>
</head>
<body>

    <?php displayFlashMessage(); ?>
    <?php

        if($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['action'])) {
            $action = $_GET['action'];
            if($action == 'processing') {
                viewOrdersAdminSideProcessing();
            }
            elseif($action == 'hold') {
                viewOrdersAdminSideHold();
            }
            elseif($action == 'accepted') {
                viewOrdersAdminSideAccepted();
            }
            elseif($action == 'rejected') {
                viewOrdersAdminSideRejected();
            }
            elseif($action == 'completed') {
                viewOrdersAdminSideCompleted();
            }
        }
        
    ob_end_flush();
    ?>


    <script src="../scripts/adminorders.js"></script>
</body>
</html>
