<?php
include 'adminheader.php'; 
include '../classes/user.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $userId = $_POST['user_id'];
    $user = User::getUserById($userId);

    if ($user) {
        if (isset($_POST['update_details'])) {
            $name = $_POST['name'];
            $email = $_POST['email'];
            if ($user->updateDetails($name, $email)) {
                $message = "User details updated successfully";
            } else {
                $error = "Failed to update user details";
            }
        } elseif (isset($_POST['update_password'])) {
            $newPassword = $_POST['new_password'];
            if ($user->updatePassword($newPassword)) {
                $message = "Password updated successfully";
            } else {
                $error = "Failed to update password";
            }
        } elseif (isset($_POST['deactivate_user'])) {
            if ($user->deactivateUser()) {
                $message = "User deactivated successfully";
            } else {
                $error = "Failed to deactivate user";
            }
        } elseif (isset($_POST['reactivate_user'])) {
            if ($user->reactivateUser()) {
                $message = "User reactivated successfully";
            } else {
                $error = "Failed to reactivate user";
            }
        }
    } else {
        $error = "User not found or is an admin";
    }
}

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$users = User::searchNonAdminUsers($search);
$deactivatedUsers = User::getDeactivatedUsers();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User Details</title>
    
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">Edit User Details</h1>
        
        <?php if (isset($message)): ?>
            <div class="alert alert-success"><?php echo $message; ?></div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <!-- Search form -->
        <form method="GET" class="mb-4">
            <div class="input-group">
                <input type="text" class="form-control" name="search" placeholder="Search users..." value="<?php echo htmlspecialchars($search); ?>">
                <div class="input-group-append">
                    <button class="btn btn-outline-secondary" type="submit">Search</button>
                </div>
            </div>
        </form>

        <div id="searchResults"></div>

        <?php if (empty($users)): ?>
            <p>No active users found.</p>
        <?php else: ?>
            <form method="POST">
                <div class="form-group">
                    <label for="user_id">Select User:</label>
                    <select class="form-control" name="user_id" id="user_id">
                        <?php foreach ($users as $user): ?>
                            <option value="<?php echo $user['ID']; ?>"><?php echo htmlspecialchars($user['Name']) . ' (' . htmlspecialchars($user['Email']) . ')'; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <input type="text" class="form-control" name="name" placeholder="New Name">
                </div>
                
                <div class="form-group">
                    <input type="email" class="form-control" name="email" placeholder="New Email">
                </div>
                
                <button type="submit" class="btn btn-primary" name="update_details">Update Details</button>
                
                <hr>
                
                <div class="form-group">
                    <input type="password" class="form-control" name="new_password" placeholder="New Password">
                </div>
                
                <button type="submit" class="btn btn-warning" name="update_password">Update Password</button>
                
                <hr>
                
                <button type="submit" class="btn btn-danger" name="deactivate_user" onclick="return confirm('Are you sure you want to deactivate this user?');">Deactivate User</button>
            </form>
        <?php endif; ?>

        <h2 class="mt-5 mb-4">Deactivated Users</h2>
        <?php if (empty($deactivatedUsers)): ?>
            <p>No deactivated users found.</p>
        <?php else: ?>
            <form method="POST">
                <div class="form-group">
                    <label for="deactivated_user_id">Select Deactivated User:</label>
                    <select class="form-control" name="user_id" id="deactivated_user_id">
                        <?php foreach ($deactivatedUsers as $user): ?>
                            <option value="<?php echo $user['ID']; ?>"><?php echo htmlspecialchars($user['Name']) . ' (' . htmlspecialchars($user['Email']) . ')'; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <button type="submit" class="btn btn-success" name="reactivate_user">Reactivate User</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>