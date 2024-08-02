<?php
include './functions.php';

$isLoggedIn = isLoggedIn();
if($isLoggedIn){
    header('Location: index.php');
    exit;
}

$error_message = "";
$success_message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $password = trim($_POST['password']);
    
    if (empty($name)) {
        $error_message = "Name is Empty.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Invalid Email Format.";
    } elseif (empty($password)) {
        $error_message = "Password is Empty.";
    } else {
        $register_result = register($name, $email, $password);
        if ($register_result['status'] === 'success') {
            $success_message = $register_result['message'];
        } else {
            $error_message = $register_result['message'];
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
        }
        .register-container {
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
    <title>REGISTER</title>
</head>

<body>
    <div class="register-container">
        <h3 class="text-center mb-4">
            <a class="text-decoration-none text-dark" href="./index.php">Elite Zone</a>
        </h3>
        <h2 class="text-center mb-4 text-primary"><b>REGISTER</b></h2>
        
        <?php if (!empty($error_message)) : ?>
            <div class="alert alert-danger text-center"><b><?php echo $error_message; ?></b></div>
        <?php elseif (!empty($success_message)) : ?>
            <div class="alert alert-success text-center"><b><?php echo $success_message; ?></b></div>
        <?php endif; ?>

        <form method="post">
            <div class="mb-3">
                <label for="name" class="form-label"><b>Name:</b></label>
                <input type="text" name="name" class="form-control" required>
            </div>
            
            <div class="mb-3">
                <label for="email" class="form-label"><b>Email:</b></label>
                <input type="email" name="email" class="form-control" required>
            </div>
            
            <div class="mb-3">
                <label for="password" class="form-label"><b>Password:</b></label>
                <input type="password" name="password" class="form-control" required>
            </div>
            
            <div class="d-grid mb-4">
                <button type="submit" class="btn btn-custom text-white"><b>REGISTER</b></button>
            </div>
            
            <div class="text-center">
                <a href="./login.php" class="text-primary">Already have an account ? Login here</a>
            </div>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>