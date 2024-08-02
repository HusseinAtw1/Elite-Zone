<?php 
ob_start(); 
include 'header.php';  

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['productId']) && isset($_POST['addtocartoutside'])) {
        $productId = intval($_POST['productId']);
        $userId = $_SESSION['user_id'];
        $added = addToCartDB($userId, $productId, 1);
        $redirectUrl = 'items.php';
        if (isset($_GET['sub_id'])) {
            $redirectUrl .= '?sub_id=' . urlencode($_GET['sub_id']);
        } elseif (isset($_POST['searchTag'])) {
            $redirectUrl .= '?search=' . urlencode($_POST['searchTag']);
        } elseif (isset($_POST['brandid'])) {
            $redirectUrl .= '?brand=' . urlencode($_POST['brandid']);
        }
        header('Location: ' . $redirectUrl);
        exit();
    }
}
ob_end_flush(); 
?>

<!DOCTYPE html>
<html>
<head>
    <title>Items</title>
</head>
<style>
    .subcategory-title {
        font-size: 2rem; 
        font-weight: bold;
        margin-top: 2.5rem; 
        margin-bottom: 4rem; 
        text-align: center; 
        color: #333; 
    }
</style>

<body>
    <?php
    if (isset($_GET['sub_id'])) {
        $subcategoryId = $_GET['sub_id'];
        $conn = connectDB();
        $stmt = $conn->prepare("Select Name from sub_categories where Sub_ID = ?");
        $stmt->bind_param('i', $subcategoryId);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            echo '<h1 class="subcategory-title">' . htmlspecialchars($row['Name']) . 's Avaiable</h1>';
        }
        $stmt->close();
        $conn->close();
        getProductsBySubCategory($subcategoryId);
    } elseif ($_SERVER['REQUEST_METHOD'] == 'POST' || $_SERVER['REQUEST_METHOD'] == 'GET') {
        $search = isset($_POST['search']) ? $_POST['search'] : (isset($_GET['search']) ? $_GET['search'] : '');
        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
        $selectedBrand = isset($_GET['brand']) ? $_GET['brand'] : '';
        $minPrice = isset($_GET['minPrice']) ? $_GET['minPrice'] : '';
        $maxPrice = isset($_GET['maxPrice']) ? $_GET['maxPrice'] : '';

        if ($search) {
            displayProductsBySearch($search, $page, $selectedBrand, $minPrice, $maxPrice);
        } elseif (isset($_GET['brand'])) {
            $brandId = $_GET['brand'];
            $conn = connectDB();
            $stmt = $conn->prepare("select Name from brands where Brand_ID = ?");
            $stmt->bind_param('i',$brandId);
            $stmt->execute();
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {

                echo '<h1 class="subcategory-title">' . htmlspecialchars($row['Name']) . '\'s products</h1>';
            }
            viewProductByBrand($brandId);
        }
        else {
            echo '<h1 class="subcategory-title">Our Products</h1>';
            displayAllProducts();
        }
    }
  
    ?>

    <script src="footer.js"></script>
</body>
</html>
