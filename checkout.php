<?php 

include 'header.php';  
if($isLoggedIn){     
    $userId = $_SESSION['user_id']; 
} 
else {
$originalUrl = $_SERVER['REQUEST_URI'];
exit('<script>
    alert("Please Login");
    window.location.href = "login.php?redirect=' . urlencode($originalUrl) . '";
    </script>');
}
$subtotal = getSubTotal($userId); 
$vat = $subtotal * 0.11; 
$total = $subtotal + $vat + 2;  

 
?> 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checking-Out</title>
    
    
</head>
<body>
<div class="container mt-5">
    <form action="index.php" method="post">
        <div class="row">
            <div class="col-md-6">
                <h3 class="mb-4">Billing Details</h3>
                <div class="mb-3 row">
                    <div class="col-md-6">
                        <label for="first-name" class="form-label">First Name</label>
                        <input type="text" class="form-control" id="first-name" name="first-name" required>
                    </div>
                    <div class="col-md-6">
                        <label for="last-name" class="form-label">Last Name</label>
                        <input type="text" class="form-control" id="last-name" name="last-name" required>
                    </div>
                </div>
                <div class="mb-3 row">
                    <div class="col-md-6">
                        <label for="phone-nb" class="form-label">Mobile Phone</label>
                        <input type="tel" class="form-control" id="phone-nb" name="phone-nb" required>
                    </div>
                    <div class="col-md-6">
                        <label for="landline" class="form-label">Landline (optional)</label>
                        <input type="tel" class="form-control" id="landline" name="landline">
                    </div>
                </div>
                <div class="mb-3 row">
                    <div class="col-md-6">
                        <label for="address" class="form-label">Address</label>
                        <input type="text" class="form-control" id="address" name="address" required>
                    </div>
                    <div class="col-md-6">
                        <label for="city" class="form-label">City/Town</label>
                        <input type="text" class="form-control" id="city" name="city" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <div class="mb-3">
                    <label for="notes" class="form-label">Additional Notes</label>
                    <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                </div>
            </div>
            <div class="col-md-6">
                <h3 class="mb-4">Your Order</h3>
                <div class="table-responsive">
                    <?php displayInCheckOut($userId)?>
                    <table class="table">
                        <tr>
                            <td>Subtotal</td>
                            <td><?php echo $subtotal?></td>
                        </tr>
                        <tr>
                            <td>Shipping</td>
                            <td>$2</td>
                        </tr>
                        <tr>
                            <td>VAT</td>
                            <td><?php echo $vat?></td>
                        </tr>
                        <tr>
                            <td><strong>Total</strong></td>
                            <td><strong><?php echo $total?></strong></td>
                        </tr>
                    </table>
                </div>
                <input type="submit" class="btn btn-primary" value="Place Order" name="ordered">
                <input type="hidden" value="<?php echo $total?>" name="total">
            </div>
        </div>
    </form>
</div>
    <script>
        if ( window.history.replaceState ) {
            window.history.replaceState( null, null, window.location.href );
        }
    </script>


<script src="footer.js"></script> 


</body>
</html>