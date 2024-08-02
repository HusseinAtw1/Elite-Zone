<?php
include '../functions.php';
include '../classes/subcategories.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'search_products') {
    $search = isset($_GET['search']) ? $_GET['search'] : '';
    echo Subcategory::generateProductTable(Subcategory::searchProducts($search));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'toggle_homepage') {
    $productId = isset($_POST['productId']) ? $_POST['productId'] : null;
    $toggleAction = isset($_POST['toggleAction']) ? $_POST['toggleAction'] : null;
    
    if ($productId && $toggleAction) {
        Subcategory::toggleHomepageProduct($productId, $toggleAction);
        echo 'success';
    } else {
        echo 'error';
    }
}

?>