<?php
ob_start();
include('adminheader.php');
include("../classes/classproduct.php");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Products</title>
   
    
</head>
<body>

<div class="container mt-4">
    <?php
    if (isset($_SESSION['flash_message'])) {
        echo '<div class="alert alert-success" role="alert">' . $_SESSION['flash_message'] . '</div>';
        unset($_SESSION['flash_message']);
    }

    if ($_SERVER["REQUEST_METHOD"] == 'GET' && isset($_GET['action']) && $_GET['action'] == 'add') {
        Product::formForAddingProduct();
    }
    elseif ($_SERVER["REQUEST_METHOD"] == 'POST'
    && isset($_POST['product-name'])
    && isset($_POST['product-brand'])
    && isset($_POST['product-sub-category'])
    && isset($_POST['product-bought-for'])
    && isset($_POST['product-net-price'])
    && isset($_POST['product-discount'])
    && isset($_POST['product-quantity'])
    && isset($_POST['product-description'])
    && isset($_POST['product-specification'])
    && isset($_FILES['product-image'])
    && isset($_POST['update-product']))
 {
    $name = $_POST['product-name'];
    $brand = $_POST['product-brand'];
    $subId = $_POST['product-sub-category'];
    $boughtFor = $_POST['product-bought-for'];
    $netPrice = $_POST['product-net-price'];
    $discount = $_POST['product-discount'];
    $quantity = $_POST['product-quantity'];
    $description = $_POST['product-description'];
    $specification = $_POST['product-specification'];
    $img = $_FILES['product-image'];
    $productId = intval($_POST['productid']);

    $product = new Product($name, $img, $boughtFor, $netPrice, $discount, $description, $specification, $quantity, $brand, $subId);

    $result = $product->updateProduct($productId);

    header("Location: adminproducts.php?action=edit");
    exit();
    
}
    elseif ($_SERVER["REQUEST_METHOD"] == 'POST'
        && isset($_POST['product-name'])
        && isset($_POST['product-brand'])
        && isset($_POST['product-sub-category'])
        && isset($_POST['product-bought-for'])
        && isset($_POST['product-net-price'])
        && isset($_POST['product-discount'])
        && isset($_POST['product-quantity'])
        && isset($_POST['product-description'])
        && isset($_POST['product-specification'])
        && isset($_FILES['product-image'])
    ) {
        $name = $_POST['product-name'];
        $brand = $_POST['product-brand'];
        $subId = $_POST['product-sub-category'];
        $boughtFor = $_POST['product-bought-for'];
        $netPrice = $_POST['product-net-price'];
        $discount = $_POST['product-discount'];
        $quantity = $_POST['product-quantity'];
        $description = $_POST['product-description'];
        $specification = $_POST['product-specification'];
        $img = $_FILES['product-image'];
    
        $product = new Product($name, $img, $boughtFor, $netPrice, $discount, $description, $specification, $quantity, $brand, $subId);
        $productId = $product->addProduct();
    
        header("Location: adminproducts.php?action=add");
        exit();
    }
    elseif ($_SERVER["REQUEST_METHOD"] == 'GET' && isset($_GET['action']) && $_GET['action'] == 'edit') {
        
        ?>
        <div class="container mt-4">
            <h2 class="mb-4">Edit Products</h2>
            <div class="form-group">
                <input type="text" id="search" class="form-control" placeholder="Search product by name">
            </div>
            <div id="product-list">
                
            </div>
            <button id="load-more" class="btn btn-primary mt-4">Load More</button>
        </div>
        
        <?php
    }
    elseif($_SERVER['REQUEST_METHOD'] =='POST' && isset($_POST['product-id-to-edit'])) {
        $productId = $_POST['product-id-to-edit'];
        Product::editFormProductAdmin($productId);
           

    }
    ob_end_flush();
    ?>
</div>



<script src="../scripts/adminproducts.js"></script>
</body>
</html>
