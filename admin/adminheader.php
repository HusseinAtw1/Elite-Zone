<?php
include '../functions.php';

if (!isLoggedIn()) {
    header('Location: ../login.php');
    exit;
}
if (!isAdmin()) {
    header('Location: ../index.php');
    exit;
}

if (isset($_GET['logout'])) {
    $_SESSION = array();
    session_destroy();
    header('Location: ../index.php');
    exit(); 
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="admindashboard.php">Dashboard</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item active">
                    <a class="nav-link" href="adminhomepage.php">Home page layout <span class="sr-only">(current)</span></a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Orders</a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <a class="dropdown-item" href="adminorders.php?action=processing">Processing</a>
                        <a class="dropdown-item" href="adminorders.php?action=hold">Hold</a>
                        <a class="dropdown-item" href="adminorders.php?action=accepted">Accepted</a>
                        <a class="dropdown-item" href="adminorders.php?action=rejected">Rejected</a>
                        <a class="dropdown-item" href="adminorders.php?action=completed">Completed</a>
                    </div>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Chats</a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <a class="dropdown-item" href="adminchats.php?action=ongoing">Ongoing</a>
                        <a class="dropdown-item" href="adminchats.php?action=closed">Closed</a>
                    </div>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Products</a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <a class="dropdown-item" href="adminproducts.php?action=add">Add a product</a>
                        <a class="dropdown-item" href="adminproducts.php?action=edit">Edit product</a>
                    </div>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Categories</a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <a class="dropdown-item" href="admincategories.php?action=cat">Category</a>
                        <a class="dropdown-item" href="admincategories.php?action=sub-cat">Sub-Category</a>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="adminusers.php">Users</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="adminbrands.php">Brands</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="adminreviews.php">Reviews</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Contact Us</a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <a class="dropdown-item" href="admincontactus.php?action=notdone">Pending</a>
                        <a class="dropdown-item" href="admincontactus.php?action=done">Done</a>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="?logout">Logout</a>
                </li>
            </ul>
        </div>
    </nav>

    <!-- <p id="gfg"></p> -->

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // $(document).ready(() => {
        //     $.getJSON("https://api.ipify.org?format=json", function(data) {
        //         $("#gfg").html(data.ip);  
        //     });
        // });
        // $(document).ready(() => {
        //     $.getJSON("https://api.ipify.org?format=json", function(data) {
        //         if (data.ip !== '104.28.203.78') { 
        //             window.location.href = '?logout';
        //         }
        //     });
        // });

    </script>
</body>
</html>
