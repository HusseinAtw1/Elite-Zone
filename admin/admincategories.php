<?php 
ob_start();
include 'adminheader.php'; 
include '../classes/categories.php';
include '../classes/subcategories.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Categories and Subcategories</title>
</head>
<body>
    <?php
    if (isset($_SESSION['flash_message'])) {
        echo '<div class="alert alert-success" role="alert">' . $_SESSION['flash_message'] . '</div>';
        unset($_SESSION['flash_message']);
    }

    if($_SERVER['REQUEST_METHOD'] == 'GET'){
        if(isset($_GET['action'])) {
            $action = $_GET['action'];
            if($action == 'cat') {
                Category::categoryFormsAdmin();
            }
            elseif($action == 'sub-cat') {
                Subcategory::subcategoryFormsAdmin();
            }
        }
    }
    elseif($_SERVER['REQUEST_METHOD'] == 'POST') {
        if(isset($_POST['add-category'])) {
            $categoryName = $_POST['category_name'];
            $categoryId = Category::addCategory($categoryName);
            $c1 = new Category($categoryId, $categoryName);
            header('location: admincategories.php?action=cat');
            exit();
        }
        elseif(isset($_POST['add-subcategory'])) {
            $subCategoryName = $_POST['subcategory_name'];
            $categoryId = $_POST['category_id'];
            $subCategoryId = Subcategory::addSubcategory($subCategoryName, $categoryId);
            $s1 = new Subcategory($subCategoryId, $subCategoryName, $categoryId);
            header('location: admincategories.php?action=sub-cat');
            exit();
        }
    }
    ob_end_flush();
    ?>
    <script src="../scripts/admincategories.js"></script>
</body>
</html>