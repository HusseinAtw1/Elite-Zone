<?php
include '../functions.php';
include '../classes/classproduct.php';
include '../classes/categories.php';
include '../classes/subcategories.php';


if (!isLoggedIn() || !isAdmin()) {
    echo "Unauthorized access";
    exit;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['offset'], $_POST['status'])) {
        handleOrderTableUpdate();
    }elseif (isset($_POST['removeSubcategory']) || (isset($_POST['id']) && isset($_POST['name']) && isset($_POST['categoryId']))) {
        handleSubcategoryUpdate();
    }elseif (isset($_POST['id'])) {
        handleCategoryUpdate();
    }
     elseif (isset($_POST['action']) && isset($_POST['subcategory'])) {
        $subcategoryId = isset($_POST['subcategory']) ? intval($_POST['subcategory']) : 0;

        if ($_POST['action'] == 'Add to homepage') {
            updateSelectSubCategory1($subcategoryId);
        } 
        elseif ($_POST['action'] == 'Remove from homepage') {
            updateSelectSubCategory0($subcategoryId);
        }
    } 
}






if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['searchCategory'])) {
        echo Category::getCategoriesTable($_GET['searchCategory']);
    } elseif (isset($_GET['searchSubcategory'])) {
        echo Subcategory::getSubcategoriesTable($_GET['searchSubcategory']);
    } elseif (isset($_GET['page'])) {
        handleProductLoad();
    } else {
        echo "Invalid GET request.";
    }

}





function handleOrderTableUpdate() {
    $offset = intval($_POST['offset']);
    $status = $_POST['status'];
    $validStatuses = ['processing', 'hold', 'rejected', 'accepted','completed'];
    
    if (in_array($status, $validStatuses, true)) {
        addRowsToOrderstable($offset, 5, $status);
    } else {
        echo "Invalid status";
    }
}

function handleCategoryUpdate() {
    $id = intval($_POST['id']);
    
    if (isset($_POST['name'])) {
        $name = trim($_POST['name']);
        Category::updateCategory($id, $name);
    } else {
        Category::removeCategory($id);
    }
}

function handleSubcategoryUpdate() {
    if (isset($_POST['removeSubcategory'])) {
        Subcategory::removeSubcategory($_POST['removeSubcategory']);
    } elseif (isset($_POST['id']) && isset($_POST['name']) && isset($_POST['categoryId'])) {
        Subcategory::updateSubcategory($_POST['id'], $_POST['name'], $_POST['categoryId']);
    }
}

function handleProductLoad() {
    $page = (int)$_GET['page'] ?: 1;
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';
    Product::loadProducts($page, $search);
}

function updateSelectSubCategory1($subcategoryId) {
    $conn = connectDB();
    if ($conn) {
        $stmt = $conn->prepare("UPDATE sub_categories SET selected = 1 WHERE Sub_ID = ?");
        $stmt->bind_param("i", $subcategoryId);
        $stmt->execute();
        $stmt->close();
        $conn->close();
    }
    header('location: adminhomepage.php');
    exit();
}

function updateSelectSubCategory0($subcategoryId) {
    $conn = connectDB();
    if ($conn) {
        $stmt = $conn->prepare("UPDATE sub_categories SET selected = 0 WHERE Sub_ID = ?");
        $stmt->bind_param("i", $subcategoryId);
        $stmt->execute();
        $stmt->close();
        $conn->close();
    }
    header('location: adminhomepage.php');
    exit();
}




?>



