<?php
include './functions.php';

if ($_SERVER['REQUEST_METHOD'] == 'GET'){
    if (isset($_GET['action']) && $_GET['action'] == 'getSubcategories') {
        $id = $_GET['id'];
        getSubCategories($id);
    }
    if(isset($_GET['action']) && $_GET['action'] == 'getProductsBySubID') {
        $subCategoryId = $_GET['subCategoryId'];
        getProductsBySubCategory($subCategoryId); 
       
    }

    
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['chatId'])) {
        $chatId = $_POST['chatId'];
        if (isset($_POST['message'])) {
            $message = $_POST['message'];
            addMsg($chatId, $message);
        } 
        elseif (isset($_POST['messageadmin'])) {
            $message = $_POST['messageadmin'];
            addMsgAdmin($chatId, $message);
        } 
            fetchMessages($chatId);
        
    }
}

if (isset($_GET['action']) && $_GET['action'] == 'getProductInfo') {
    $type = $_GET['type'];
    $productId = $_GET['productId'];
    getDescriptionOrSpecs($type, $productId);
}
    

?>