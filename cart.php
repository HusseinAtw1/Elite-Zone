<?php
ob_start();
include 'header.php';
if($isLoggedIn){
    $userId = $_SESSION['user_id'];
} else {
    $originalUrl = $_SERVER['REQUEST_URI'];
    exit('<script>alert("Please Login");window.location.href = "login.php?redirect=' . urlencode($originalUrl) . '";</script>');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['remove'])) {
        $cart_id = $_POST['cart_id'];
        removeItemFromCart($userId, $cart_id);
        $originalUrl = $_SERVER['REQUEST_URI'];
        header('location: cart.php');
        exit();
    }

    if (isset($_POST['update_quantities'])) {
        if (isset($_POST['quantities']) && is_array($_POST['quantities'])) {
            $unavailableProducts = array();
            foreach ($_POST['quantities'] as $cart_id => $quantity) {
                $result = updateQuantity($cart_id, $quantity);
                $unavailableProducts = array_merge($unavailableProducts, $result);
            }
            if (!empty($unavailableProducts)) {
                $message = "The following products are unavailable in the requested quantity: ";
                foreach ($unavailableProducts as $product) {
                    $message .= " {$product['name']}: requested {$product['requested']} available {$product['available']}, ";
                }
                $_SESSION['flash_message'] = $message;
            }
            header('location: cart.php');
            exit();
        }    
    }
}

$flash_message = '';
if (isset($_SESSION['flash_message'])) {
    $flash_message = $_SESSION['flash_message'];
    unset($_SESSION['flash_message']);
}

$subtotal = getSubTotal($userId);
$vat = $subtotal * 0.11;
$total = $subtotal + $vat + 2;
ob_end_flush();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart</title>
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
    <?php if ($flash_message): ?>
        <div class="flash-message">
            <?php echo htmlspecialchars($flash_message); ?>
        </div>
    <?php endif; ?>
    <div class="container mt-5">
        <div class="row">
            <div class="col-lg-8">
                <?php displayInCart($userId); ?>
            </div>
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Cart Total Bill Details</h5>
                        <p class="card-text">Subtotal: $<?php echo $subtotal; ?></p>
                        <p class="card-text">Shipping: $2</p>
                        <p class="card-text">VAT: $<?php echo $vat; ?></p>
                        <p class="card-text"><strong>Total: $<?php echo $total; ?></strong></p>
                        <form action="checkout.php" method="post">
                            <button type="submit" class="btn btn-primary btn-block">Proceed to Checkout</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <script src="./scripts/cart.js"></script>
    <script src="footer.js"></script>
</body>
</html>
