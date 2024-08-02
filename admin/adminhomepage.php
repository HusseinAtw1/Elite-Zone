<?php
include 'adminheader.php';
include '../classes/subcategories.php';
$productIdjs = isset($_GET['productId']) ? $_GET['productId'] : 'null';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <?php 
    if($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['productId'])) {
        $productId = $_GET['productId'];
        Subcategory::getProduct($productId);
    }
    else {
        echo Subcategory::subcategorySelectForHomePage();
        echo Subcategory::getProductsTable();
    }
    ?>

    <script src="../scripts/adminhomepage.js"></script>
    <script>
        let productIdjs = <?php echo $productIdjs; ?>;

        function loadContent(type, productId) {
            $.ajax({
                type: "GET",
                url: '../handle.php',
                data: {
                    action: 'getProductInfo',
                    type: type,
                    productId: productId
                },
                success: function(response) {
                    $('#contentArea').html(response);
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                }
            });
        }

        $(document).ready(function() {
            if (productIdjs && productIdjs !== 'null') {
                loadContent('description', productIdjs);
            }
        });
    </script>
    
</body>
</html>