<?php
ob_start();
include 'header.php';

if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
} else {
    $originalUrl = $_SERVER['REQUEST_URI'];
    exit('<script>alert("Please Login");window.location.href = "login.php?redirect=' . urlencode($originalUrl) . '";</script>');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    $currentPassword = $_POST['currentPassword'];
    
    switch ($action) {
        case 'update_password':
            $newPassword = $_POST['newPassword'];
            $response = updatePassword($userId, $currentPassword, $newPassword);
            break;
        case 'update_name':
            $newName = $_POST['newName'];
            $response = updateName($userId, $currentPassword, $newName);
            break;
        case 'update_email':
            $newEmail = $_POST['newEmail'];
            $response = updateEmail($userId, $currentPassword, $newEmail);
            break;
    }
    
    $_SESSION['flash_message'] = $response;
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit();
}

if (isset($_SESSION['flash_message'])) {
    $response = $_SESSION['flash_message'];
    unset($_SESSION['flash_message']); 
}

$currentName = $_SESSION['user_name'];
$currentEmail = $_SESSION['user_email'];
ob_end_flush();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Profile</title>
    <link rel="stylesheet" href="./style/profile.css">
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">Update Profile</h1>
        
        <?php
        if (isset($response)) {
            $alertClass = ($response['status'] === 'success') ? 'alert-success' : 'alert-danger';
            echo "<div class='alert $alertClass'>{$response['message']}</div>";
        }
        ?>

        <!-- Display Account Details -->
        <div class="account-details">
            <h2 class="h5">Account Details</h2>
            <p><strong>Name:</strong> <?php echo htmlspecialchars($currentName); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($currentEmail); ?></p>
        </div>

        <div class="row">
            <!-- Update Email Form -->
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h2 class="h5 mb-0">Update Email</h2>
                    </div>
                    <div class="card-body">
                        <form id="emailForm" action="profile.php" method="POST">
                            <input type="hidden" name="action" value="update_email">
                            <div class="mb-3">
                                <label for="newEmail" class="form-label">New Email:</label>
                                <input type="email" class="form-control" id="newEmail" name="newEmail" required>
                            </div>
                            <div class="mb-3">
                                <label for="currentPasswordEmail" class="form-label">Current Password:</label>
                                <input type="password" class="form-control" id="currentPasswordEmail" name="currentPassword" required autocomplete="off">
                            </div>
                            <button type="submit" class="btn btn-primary">Update Email</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Update Password Form -->
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h2 class="h5 mb-0">Update Password</h2>
                    </div>
                    <div class="card-body">
                        <form id="passwordForm" action="profile.php" method="POST">
                            <input type="hidden" name="action" value="update_password">
                            <div class="mb-3">
                                <label for="currentPassword" class="form-label">Current Password:</label>
                                <input type="password" class="form-control" id="currentPassword" name="currentPassword" required autocomplete="off">
                            </div>
                            <div class="mb-3">
                                <label for="newPassword" class="form-label">New Password:</label>
                                <input type="password" class="form-control" id="newPassword" name="newPassword" required autocomplete="off">
                            </div>
                            <div class="mb-3">
                                <label for="confirmPassword" class="form-label">Confirm New Password:</label>
                                <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" required autocomplete="off">
                            </div>
                            <button type="submit" class="btn btn-primary">Update Password</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Update Name Form -->
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h2 class="h5 mb-0">Update Name</h2>
                    </div>
                    <div class="card-body">
                        <form id="nameForm" action="profile.php" method="POST">
                            <input type="hidden" name="action" value="update_name">
                            <div class="mb-3">
                                <label for="newName" class="form-label">New Name:</label>
                                <input type="text" class="form-control" id="newName" name="newName" required>
                            </div>
                            <div class="mb-3">
                                <label for="currentPasswordName" class="form-label">Current Password:</label>
                                <input type="password" class="form-control" id="currentPasswordName" name="currentPassword" required autocomplete="off">
                            </div>
                            <button type="submit" class="btn btn-primary">Update Name</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="./scripts/profile.js"></script>
    <script src="footer.js"></script>
</body>
</html>
