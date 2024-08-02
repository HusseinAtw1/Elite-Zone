<?php
include './functions.php';
$isLoggedIn = isLoggedIn();
$categories = getCategories();
$cartItemCount = 0;
$cartTotal = 0;

if ($isLoggedIn) {
    $userId = $_SESSION['user_id'];
    $isadmin = $_SESSION['is_admin'];
    if ($isadmin) {
        header('location: ./admin/admin.php');
        exit();
    }
    $cartItemCount = countCartItems($userId);
    $cartTotal = calculateCartTotal($userId);
    $cartTotal = $cartTotal + $cartTotal * 0.11 + 2;
    if ($cartTotal == 2) {
        $cartTotal = 0;
    }
}

if (isset($_GET['logout'])) {
    logout();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./style/header.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    
    



</head>
<body>

    <!-- Top Message -->
    <div class="bg-light py-2">
        <div class="container">
            <p class="mb-0"><i class="fa-solid fa-message"></i> +961 71 773 735 | Main Street, Borj Rahal</p>
        </div>
    </div>

    <!-- Header Section -->
    <div class="container mt-3">
        <div class="row justify-content-between align-items-center custom-header">
            <!-- Logo -->
            <div class="col-md-3 col-6">
                <a href="index.php">
                    <img src="./images/Desktop Screenshot 2024.06.30 - 16.48.59.64.png" alt="Logo" class="img-fluid" style="max-width: 150px;">
                </a>
            </div>

            <!-- Search Bar -->
            <div class="col-md-6 col-12">
                <form action="items.php" method="POST" class="input-group">
                    <input type="text" class="form-control" placeholder="Search for products..." name="search">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i></button>
                </form>
            </div>

            <div class="col-md-3 col-6 user-actions">
                <div class="d-flex justify-content-end align-items-center">
                    <!-- User Account Dropdown -->
                    <div class="dropdown me-2">
                        <button class="btn btn-outline-primary dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user me-2"></i>
                            <span class="d-none d-md-inline"><?php echo $isLoggedIn ? htmlspecialchars($_SESSION['user_name']) : 'Account'; ?></span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <?php if ($isLoggedIn): ?>
                                <li><a class="dropdown-item" href="profile.php"><i class="fas fa-user me-2"></i>Profile Details</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="orders.php"><i class="fas fa-list-ul me-2"></i>View Orders</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="chats.php"><i class="fas fa-comments me-2"></i>Chats</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="review.php"><i class="fas fa-star me-2"></i>Leave a Review</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="?logout"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                            <?php else: ?>
                                <li><a class="dropdown-item" href="login.php"><i class="fas fa-sign-in-alt me-2"></i>Log In</a></li>
                                <li><a class="dropdown-item" href="register.php"><i class="fas fa-user-plus me-2"></i>Register</a></li>
                            <?php endif; ?>
                        </ul>
                    </div>

                    <!-- Shopping Cart -->
                    <div>
                        <a href="cart.php" class="btn btn-primary position-relative">
                            <i class="fas fa-shopping-cart"></i>
                            <span class="d-none d-md-inline ms-1"><?php echo number_format($cartTotal, 2); ?></span>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                <?php echo $cartItemCount; ?>
                            </span>  
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Navbar Section -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white">
        <div class="container">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
                <ul class="navbar-nav">
                    
                </ul>
            </div>
        </div>
    </nav>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>                            
    <script>
        const categories = <?php echo $categories; ?>;
    </script>
    <script src="./scripts/header.js"></script> 
</body>
</html>
