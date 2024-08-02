<?php

class Subcategory {
    private $subCategoryId;
    private $subCategoryName;
    private $categoryId;

    public function __construct($subCategoryId, $subCategoryName, $categoryId) {
        $this->subCategoryId = $subCategoryId;
        $this->subCategoryName = $subCategoryName;
        $this->categoryId = $categoryId;
    }

    public static function subcategoryFormsAdmin() {
        $categories = self::getAllCategories();
        echo '
        <div class="container mt-5">
            <form method="POST" action="">
                <div class="form-group">
                    <label for="subcategory_name">Subcategory Name:</label>
                    <input type="text" class="form-control" id="subcategory_name" name="subcategory_name" required>
                </div>
                <div class="form-group">
                    <label for="category_id">Parent Category:</label>
                    <select class="form-control" id="category_id" name="category_id" required>
                        <option value="">Select a category</option>';
        foreach ($categories as $category) {
            echo '<option value="' . $category['Category_ID'] . '">' . htmlspecialchars($category['Name']) . '</option>';
        }
        echo '
                    </select>
                </div>
                <div class="form-group">
                    <input type="submit" class="btn btn-primary" value="Add subcategory" name="add-subcategory">
                </div>
            </form>
    
            <div class="form-group mt-4">
                <input type="text" class="form-control" id="search_subcategory" placeholder="Search subcategories...">
            </div>
    
            <div id="subcategory_table">
                <!-- Table content will be loaded here -->
            </div>
        </div>';
    }

    public static function addSubcategory($subCategoryName, $categoryId) {
        $conn = connectDB();
        if (!$conn) {
            die('Connection Failed: ' . mysqli_connect_error());
        }
    
        $stmt = $conn->prepare("INSERT INTO sub_categories (Name, Category_ID) VALUES (?, ?)");
        if (!$stmt) {
            die('Prepare Failed: ' . $conn->error);
        }
        $stmt->bind_param("si", $subCategoryName, $categoryId);
    
        if ($stmt->execute()) {
            $_SESSION['flash_message'] = "Subcategory added successfully!";
            $subCategoryId = $stmt->insert_id;
        } else {
            $_SESSION['flash_message'] = "Something went Wrong!";
            die('Execute Failed: ' . $stmt->error);
        }
    
        $stmt->close();
        $conn->close();
    
        return $subCategoryId;
    }

    public static function removeSubcategory($id) {
        $conn = connectDB();
        if ($conn === false) {
            echo 'Database connection failed.';
            return;
        }
        $stmt = $conn->prepare("DELETE FROM sub_categories WHERE Sub_ID = ?");
        if ($stmt === false) {
            echo 'Statement preparation failed.';
            $conn->close();
            return;
        }
        $stmt->bind_param('i', $id);

        $stmt->execute();
        $stmt->close();
        $conn->close();
    }

    public static function updateSubcategory($id, $name, $categoryId) {
        $conn = connectDB();
        $stmt = $conn->prepare("UPDATE sub_categories SET Name = ?, Category_ID = ? WHERE Sub_ID = ?");
        if ($stmt === false) {
            echo 'Statement preparation failed.';
            $conn->close();
            return;
        }
        $stmt->bind_param('sii', $name, $categoryId, $id);
        $stmt->execute();
        $stmt->close();
        $conn->close();
    }

    public static function getSubcategoriesTable($search = '') {
        $conn = connectDB();
    
        $stmt = $conn->prepare("SELECT s.Sub_ID, s.Name AS SubcategoryName, s.Category_ID, c.Name AS CategoryName 
                                FROM sub_categories s 
                                JOIN Categories c ON s.Category_ID = c.Category_ID 
                                WHERE s.Name LIKE ?");
        $searchParam = '%' . $search . '%';
        $stmt->bind_param('s', $searchParam);
        $stmt->execute();
        $result = $stmt->get_result();
    
        $html = '<table class="table mt-3">
                 <thead class="thead-dark">
                    <tr>
                        <th>ID</th>
                        <th>Subcategory Name</th>
                        <th>Category Name</th>
                        <th>Actions</th>
                    </tr>
                 </thead>
                 <tbody>';
    
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $html .= '<tr>
                            <td id="subidtake">' . htmlspecialchars($row["Sub_ID"]) . '</td>
                            <td>' . htmlspecialchars($row["SubcategoryName"]) . '</td>
                            <td>' . htmlspecialchars($row["CategoryName"]) . '</td>
                            <td>
                                <button class="btn btn-warning btn-sm edit-subcategory" data-id="' . htmlspecialchars($row["Sub_ID"]) . '" data-category-id="' . htmlspecialchars($row["Category_ID"]) . '">Edit</button>
                                <button class="btn btn-danger btn-sm remove-subcategory" data-id="' . htmlspecialchars($row["Sub_ID"]) . '">Remove</button>
                            </td>
                          </tr>';
            }
        } else {
            $html .= '<tr><td colspan="4">No subcategories found.</td></tr>';
        }
    
        $html .= '</tbody>
                 </table>';
    
        $stmt->close();
        $conn->close();
    
        return $html;
    }

    public static function getAllCategories() {
        $conn = connectDB();
        $stmt = $conn->prepare("SELECT Category_ID, Name FROM Categories");
        $stmt->execute();
        $result = $stmt->get_result();
        $categories = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        $conn->close();
        return $categories;
    }

    public static function subcategorySelectForHomePage() {
        $conn = connectDB();
        if (!$conn) {
            die('Connection Failed: ' . mysqli_connect_error());
        }
    
        $query = "SELECT c.Category_ID, c.Name AS CategoryName, s.Sub_ID, s.Name AS SubcategoryName, s.selected
                   FROM Categories c
                   LEFT JOIN sub_categories s ON c.Category_ID = s.Category_ID
                   ORDER BY c.Name, s.Name";
    
        $result = $conn->query($query);
    
        if (!$result) {
            die('Query Failed: ' . $conn->error);
        }
    
        $categories = [];
        while ($row = $result->fetch_assoc()) {
            $categoryId = $row['Category_ID'];
            $categoryName = $row['CategoryName'];
            $subId = $row['Sub_ID'];
            $subName = $row['SubcategoryName'];
            $selected = $row['selected'];
    
            if (!isset($categories[$categoryId])) {
                $categories[$categoryId] = [
                    'Name' => $categoryName,
                    'subcategories' => []
                ];
            }
    
            if ($subId !== null) {
                $categories[$categoryId]['subcategories'][] = [
                    'Sub_ID' => $subId,
                    'Name' => $subName,
                    'selected' => $selected
                ];
            }
        }
    
        $conn->close();
    
        $html = '
        <div class="container mt-4">
            <form id="subcategory-form" method="post" action="adminhandle.php" class="bg-light p-4 rounded shadow-sm">
                <h3 class="mb-4 text-primary">Subcategory Management</h3>
                <div class="mb-3">
                    <input type="text" id="subcategory-search" class="form-control" placeholder="Search subcategories...">
                </div>
                <div class="mb-3">
                    <select id="subcategory-select" name="subcategory" class="form-select">
                        <option value="">Select a subcategory</option>';
    
        foreach ($categories as $categoryId => $category) {
            $html .= '<optgroup label="' . htmlspecialchars($category['Name']) . '">';
            foreach ($category['subcategories'] as $subcategory) {
                $selected = $subcategory['selected'] ? ' selected' : '';
                $html .= '<option value="' . $subcategory['Sub_ID'] . '"' . $selected . '>' . htmlspecialchars($subcategory['Name']) . '</option>';
            }
            $html .= '</optgroup>';
        }
    
        $html .= '
                    </select>
                </div>
                <div class="d-flex justify-content-between">
                    <button type="submit" class="btn btn-primary" name="action" value="Add to homepage">
                        <i class="fas fa-plus-circle me-2"></i>Add to homepage
                    </button>
                    <button type="submit" class="btn btn-danger" name="action" value="Remove from homepage">
                        <i class="fas fa-minus-circle me-2"></i>Remove from homepage
                    </button>
                </div>
            </form>
        </div>';
    
        return $html;
    }

    public static function getProductsTable($search = '') {
        $conn = connectDB();
        if (!$conn) {
            die('Connection Failed: ' . mysqli_connect_error());
        }
    
        $query = "SELECT p.Product_ID, p.Name, p.Price, p.product_img, p.selected
                   FROM products p
                   WHERE p.Name LIKE ?
                   ORDER BY p.Name";
    
        $stmt = $conn->prepare($query);
        $searchParam = "%$search%";
        $stmt->bind_param("s", $searchParam);
        $stmt->execute();
        $result = $stmt->get_result();
    
        $html = '
        <div class="container mt-4">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h3 class="mb-0">Products</h3>
                    <form class="d-flex" id="product-search-form">
                        <input class="form-control me-2" type="search" placeholder="Search products" aria-label="Search" id="product-search-input" value="' . htmlspecialchars($search) . '">
                        <button class="btn btn-outline-light" type="submit">Search</button>
                    </form>
                </div>
                <div class="card-body">
                    <div id="product-table-container">
                        ' . self::generateProductTable($result) . '
                    </div>
                </div>
            </div>
        </div>';
    
        $stmt->close();
        $conn->close();
    
        return $html;
    }
    
    public static function generateProductTable($result) {
        $tableHtml = '
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Price</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>';
    
        while ($row = $result->fetch_assoc()) {
            $selected = $row['selected'] ? 'Remove from homepage' : 'Add to homepage';
            $buttonClass = $row['selected'] ? 'btn-outline-danger' : 'btn-outline-success';
    
            $imageData = base64_encode($row['product_img']);
            $imageSrc = 'data:image/jpeg;base64,' . $imageData;
    
            $tableHtml .= '
            <tr>
                <td><img src="' . $imageSrc . '" alt="' . htmlspecialchars($row['Name']) . '" class="img-thumbnail" style="width: 50px; height: 50px;"></td>
                <td>' . htmlspecialchars($row['Name']) . '</td>
                <td>$' . number_format($row['Price'], 2) . '</td>
                <td>
                    <div class="btn-group" role="group">
                        <form action="" method="GET" class="me-2">
                            <button class="btn btn-outline-primary btn-sm view-product" type="submit">
                                <i class="fas fa-eye me-1"></i>View
                            </button>
                            <input type="hidden" name="productId" value="'.$row['Product_ID'].'">
                        </form>
                        <button class="btn ' . $buttonClass . ' btn-sm toggle-homepage" data-product-id="' . $row['Product_ID'] . '" data-action="' . $selected . '">
                            <i class="fas fa-' . ($row['selected'] ? 'minus' : 'plus') . '-circle me-1"></i>' . $selected . '
                        </button>
                    </div>
                </td>
            </tr>';
        }
    
        $tableHtml .= ' 
                </tbody>
            </table>
        </div>';
    
        return $tableHtml;
    }    

    public static function getProduct($productId) {
        $conn = connectDB();
        $stmt = $conn->prepare("SELECT * FROM products WHERE Product_ID = ?");
        $stmt->bind_param('i', $productId);
        $stmt->execute();
        $result = $stmt->get_result();
    
        while ($row = $result->fetch_assoc()) {
            if (!is_null($row['product_img'])) {
                $imgData = base64_encode($row['product_img']);
                $imgSrc = 'data:image/jpeg;base64,' . $imgData;
            } else {
                $imgSrc = 'path/to/default/image.jpg';
            }
    
            $selected = $row['selected'] ? 'Remove from homepage' : 'Add to homepage';
            $buttonClass = $row['selected'] ? 'btn-outline-danger' : 'btn-outline-success';
    
            echo '<div class="container mt-5 mb-3" style="max-width: 50%; background-color: white; border-radius: 10px; padding: 20px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);">
                    <div class="row g-4">
                        <div class="col-md-6 text-center">
                            <img src="'.$imgSrc.'" class="img-fluid rounded" style="max-width: 100%; height: auto;" alt="item">
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <h2 style="color: black;">'.$row['Name'].'</h2>
                                <hr style="border-top: 1px solid #007bff;">
                            </div>
                            <div class="mb-3">
                                <h4 class="text-primary">'.$row['Price'].'$</h4>
                            </div>
                            <div class="mb-3">
                                <button class="btn ' . $buttonClass . ' btn-sm toggle-homepage" data-product-id="' . $row['Product_ID'] . '" data-action="' . $selected . '">
                                    <i class="fas fa-' . ($row['selected'] ? 'minus' : 'plus') . '-circle me-1"></i>' . $selected . '
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="container mt-4" style="max-width: 50%;">
                    <div class="btn-group w-100 mb-3" role="group">
                        <button type="button" class="btn btn-outline-primary" onclick="loadContent(\'description\', ' . $productId . ')">Description</button>
                        <button type="button" class="btn btn-outline-primary" onclick="loadContent(\'specifications\', ' . $productId . ')">Specifications</button>
                    </div>
                    <div id="contentArea"></div>
                </div>';
        }
    
        $stmt->close();
        $conn->close();
    }


    public static function searchProducts($search) {
        $conn = connectDB();
        if (!$conn) {
            die('Connection Failed: ' . mysqli_connect_error());
        }

        $query = "SELECT p.Product_ID, p.Name, p.Price, p.product_img, p.selected
                   FROM products p
                   WHERE p.Name LIKE ?
                   ORDER BY p.Name";

        $stmt = $conn->prepare($query);
        $searchParam = "%$search%";
        $stmt->bind_param("s", $searchParam);
        $stmt->execute();
        $result = $stmt->get_result();

        $stmt->close();
        $conn->close();

        return $result;
    }

    public static function toggleHomepageProduct($productId, $action) {
        $conn = connectDB();
        if (!$conn) {
            die('Connection Failed: ' . mysqli_connect_error());
        }

        $selected = ($action === 'Add to homepage') ? 1 : 0;

        $query = "UPDATE products SET selected = ? WHERE Product_ID = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ii", $selected, $productId);
        $stmt->execute();

        $stmt->close();
        $conn->close();
    }
}







    


?>