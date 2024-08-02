<?php
include './functions.php';

$isLoggedIn = isLoggedIn();
if($isLoggedIn) {
    header('Location: index.php');
    exit;
}

$error_message = "";
$success_message = "";

$redirectUrl = isset($_GET['redirect']) ? $_GET['redirect'] : 'index.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $password = trim($_POST['password']);
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Invalid Email Format.";
    } elseif (empty($password)) {
        $error_message = "Password is Empty.";
    } else {
        $login_result = login($email, $password);
        if ($login_result['status'] === 'success') {
            if (isAdmin()) {
                header('Location: ./admin/admindashboard.php');
                exit();
            }

            header('Location: ' . $redirectUrl);
            exit();
        } else {
            $error_message = $login_result['message'];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        html, body {
            height: 100%;
        }
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f8f9fa;
            padding: 20px;
        }
        .form-container {
            background-color: white;
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            padding: 2rem;
        }
        .btn-custom {
            background-color: #007bff;
            border-color: #007bff;
        }
        .btn-custom:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }

    </style>
    <title>LOGIN</title>
</head>

<body>
    <div class="form-container">
        <h3 class="text-center mb-4">
            <a class="text-decoration-none text-dark" href="./index.php">Elite Zone</a>
        </h3>
        <h2 class="text-center mb-4 text-primary"><b>LOGIN</b></h2>
        
        <?php if (!empty($error_message)) : ?>
            <div class="alert alert-danger text-center"><b><?php echo $error_message; ?></b></div>
        <?php elseif (!empty($success_message)) : ?>
            <div class="alert alert-success text-center"><b><?php echo $success_message; ?></b></div>
        <?php endif; ?>

        <form method="post" action="login.php?redirect=<?php echo urlencode($redirectUrl); ?>">
            <div class="mb-3">
                <label for="email" class="form-label"><b>Email:</b></label>
                <input type="email" name="email" class="form-control" required>
            </div>
            
            <div class="mb-3">
                <label for="password" class="form-label"><b>Password:</b></label>
                <input type="password" name="password" class="form-control" required autocomplete="off">
            </div>
            
            <div class="d-grid mb-4">
                <button type="submit" class="btn btn-custom text-white"><b>LOGIN</b></button>
            </div>
            
            <div class="text-center">
                <a href="./register.php" class="text-primary">New Customer? Create your account</a>
            </div>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>