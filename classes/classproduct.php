<?php

class Product {
    private $productId;
    private $name;
    private $img;
    private $boughtFor;
    private $netPrice;
    private $discount;
    private $description;
    private $specification;
    private $quantity;
    private $brandId;
    private $subId;
    public $price;

    public function __construct($name, $img, $boughtFor, $netPrice, $discount, $description, $specification, $quantity, $brandId, $subId) {
        $this->name = $name;
        $this->img = $img;
        $this->boughtFor = $boughtFor;
        $this->netPrice = $netPrice;
        $this->discount = $discount;
        $this->description = $description;
        $this->specification = $specification;
        $this->quantity = $quantity;
        $this->brandId = $brandId;
        $this->subId = $subId;
        $this->price = $this->calculatePrice($netPrice, $discount);
    }
    private function calculatePrice($netPrice, $discount) {
        return $netPrice - ($netPrice * ($discount / 100));
    }

    public function getName() { return $this->name; }
    public function getImg() { return $this->img; }
    public function getBoughtFor() { return $this->boughtFor; }
    public function getNetPrice() { return $this->netPrice; }
    public function getDiscount() { return $this->discount; }
    public function getDescription() { return $this->description; }
    public function getSpecification() { return $this->specification; }
    public function getQuantity() { return $this->quantity; }
    public function getBrandId() { return $this->brandId; }
    public function getSubId() { return $this->subId; }

    public static function formForAddingProduct() {
        $conn = connectDB();
        if (!$conn) {
            die("Connection failed: " . mysqli_connect_error());
        }
    
        $stmt = $conn->prepare("SELECT * FROM categories");
        $stmt->execute();
        $categories = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
    
        $stmt = $conn->prepare("SELECT * FROM sub_categories");
        $stmt->execute();
        $subcategories = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
    
        $stmt = $conn->prepare("SELECT * FROM brands");
        $stmt->execute();
        $brands = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
    
        $subcategoriesByCategory = [];
        foreach ($subcategories as $subcategory) {
            $categoryId = $subcategory['Category_ID'];
            if (!isset($subcategoriesByCategory[$categoryId])) {
                $subcategoriesByCategory[$categoryId] = [];
            }
            $subcategoriesByCategory[$categoryId][] = $subcategory;
        }
    
        echo '<div class="container mt-4">
                <h2 class="mb-4">Add New Product</h2>
                <form action="adminproducts.php?action=add" method="POST" enctype="multipart/form-data">
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="product-name">Product Name</label>
                            <input type="text" class="form-control" id="product-name" name="product-name" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="product-brand">Brand</label>
                            <select class="form-control" id="product-brand" name="product-brand" required>
                                <option value="">Select a brand</option>';
    
        foreach ($brands as $brand) {
            echo '<option value="' . htmlspecialchars($brand["Brand_ID"]) . '">' . htmlspecialchars($brand["Name"]) . '</option>';
        }
    
        echo '      </select>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="product-sub-category">Category / Sub-Category</label>
                        <select class="form-control" id="product-sub-category" name="product-sub-category" required>
                            <option value="">Select a category</option>';
    
        foreach ($categories as $category) {
            echo '<option value="" disabled style="font-weight: bold;">' . htmlspecialchars($category["Name"]) . '</option>';
    
            if (isset($subcategoriesByCategory[$category['Category_ID']])) {
                foreach ($subcategoriesByCategory[$category['Category_ID']] as $subcategory) {
                    echo '<option value="' . htmlspecialchars($subcategory["Sub_ID"]) . '">&nbsp;&nbsp;' . htmlspecialchars($subcategory["Name"]) . '</option>';
                }
            }
        }
    
        echo '      </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="product-quantity">Quantity</label>
                        <input type="number" class="form-control" id="product-quantity" name="product-quantity" min="0" required>
                    </div>
                </div>
    
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="product-bought-for">Bought For</label>
                        <input type="number" class="form-control" id="product-bought-for" name="product-bought-for" step="0.01" min="0" required>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="product-net-price">Net Price</label>
                        <input type="number" class="form-control" id="product-net-price" name="product-net-price" step="0.01" min="0" required>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="product-discount">Discount</label>
                        <input type="number" class="form-control" id="product-discount" name="product-discount" step="0.01" min="0" max="100" required>
                    </div>
                </div>
    
                <div class="form-group">
                    <label for="product-image">Product Image</label>
                    <input type="file" class="form-control-file" id="product-image" name="product-image" accept="image/*" required>
                </div>
    
                <div class="form-group">
                    <label for="product-description">Description</label>
                    <textarea class="form-control" id="product-description" name="product-description" rows="3" required></textarea>
                </div>
    
                <div class="form-group">
                    <label for="product-specification">Specifications</label>
                    <textarea class="form-control" id="product-specification" name="product-specification" rows="3" required></textarea>
                </div>
    
                <button type="submit" class="btn btn-primary">Add Product</button>
            </form>
        </div>';
    
        $conn->close();
    }
    

    public function addProduct() {
        // Connect to the database
        $conn = connectDB();
        if (!$conn) {
            die("Connection failed: " . mysqli_connect_error());
        }
    
        // Handle image upload
        $imageData = null;
        if ($this->img && $this->img['error'] == UPLOAD_ERR_OK) {
            // Validate file size and type if needed
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            if (in_array($this->img['type'], $allowedTypes)) {
                $imageData = file_get_contents($this->img['tmp_name']);
            } else {
                $_SESSION['flash_message'] = "Invalid image type!";
                $conn->close();
                return false;
            }
        }
    
        // Calculate Price
        $price = $this->netPrice - ($this->netPrice * $this->discount / 100);
    
        // Prepare the SQL statement
        $stmt = $conn->prepare("INSERT INTO products (Name, product_img, bought_for, net_price, discount, Price, Description, Specifications, quantity, Brand_ID, Sub_ID) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        if ($stmt === false) {
            $_SESSION['flash_message'] = "Failed to prepare the SQL statement!";
            $conn->close();
            return false;
        }
    
        // Bind parameters and execute
        $stmt->bind_param("ssddddssiii", 
            $this->name, 
            $imageData, 
            $this->boughtFor, 
            $this->netPrice, 
            $this->discount, 
            $price, 
            $this->description, 
            $this->specification, 
            $this->quantity, 
            $this->brandId, 
            $this->subId
        );
    
        if ($stmt->execute()) {
            $_SESSION['flash_message'] = "Product added successfully!";
            $productId = $stmt->insert_id;
            $stmt->close();
            $conn->close();
            return $productId;
        } else {
            $_SESSION['flash_message'] = "Something went wrong: " . $stmt->error;
            $stmt->close();
            $conn->close();
            return false;
        }
    }
    
    



    public static function loadProducts($page, $search) {
     
        $limit = 12;
        $offset = ($page - 1) * $limit;
    
       
        $search = preg_replace('/[^a-z0-9\s-]/i', ' ', $search);
        $search = trim($search); // Remove leading and trailing spaces
    

        $conn = connectDB();
        
       
        function searchProducts($conn, $search, $limit, $offset) {
            $sql = "SELECT * FROM products WHERE Name LIKE ? LIMIT ? OFFSET ?";
            $stmt = $conn->prepare($sql);
            $searchQuery = "%" . $search . "%";
            $stmt->bind_param("sii", $searchQuery, $limit, $offset);
            $stmt->execute();
            $result = $stmt->get_result();
            $products = $result->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
            return $products;
        }
        
        
        $products = searchProducts($conn, $search, $limit, $offset);
        
        
        if (empty($products)) {
            
            
            $search = preg_replace('/\s+/', '%', $search); 
            $products = searchProducts($conn, $search, $limit, $offset);
        }
        
        $conn->close();
        
        // Output the results
        echo '<div class="container custom-container">';
        echo '<div class="row g-4">';
        foreach ($products as $product) {
            $imgSrc = !is_null($product['product_img']) ? 'data:image/jpeg;base64,' . base64_encode($product['product_img']) : 'path/to/default/image.jpg';
            
            echo '<div class="col-lg-3 col-md-4 col-sm-6 mb-4 custom-col">';
            echo '<div class="card h-100" style="max-width: 250px; margin: 0 auto;">';
            echo '<img src="'.$imgSrc.'" class="card-img-top product-img" alt="Product Image" style="height: 150px; object-fit: cover;">';
            echo '<div class="card-body d-flex flex-column">';
            echo '<h5 class="card-title" style="font-size: 0.9rem;">' . htmlspecialchars($product["Name"]) . '</h5>';
            echo '<p class="card-text" style="font-size: 0.8rem;">' . htmlspecialchars($product["Description"]) . '</p>';
            echo '<form method="post" action="adminproducts.php">';
            echo '<input type="submit" value="edit" class="btn btn-primary btn-sm mt-auto edit-product" style="width:100%">';
            echo '<input type="hidden"  value="' . htmlspecialchars($product['Product_ID']) . '" name="product-id-to-edit">'; 
            echo '</form>';
            echo '</div>';
            echo '</div>';
            echo '</div>';
        }
        echo '</div>';
        echo '</div>';
    }
    


    public static function editFormProductAdmin($productId) {
        $conn = connectDB();
        if(!$conn) {
            die("Connection failed: " . mysqli_connect_error());
        }
        
        
        $stmt = $conn->prepare("SELECT * FROM products WHERE Product_ID = ?");
        if(!$stmt) {
            die("Prepare failed: " . $conn->error);
        }
        $stmt->bind_param('i', $productId);
        $stmt->execute();
        $result = $stmt->get_result();
        $product = $result->fetch_assoc();
        $stmt->close();
    
        if (!$product) {
            echo "Product not found.";
            return;
        }
    
       
        $categories = fetchAll("SELECT * FROM categories");
        $subcategories = fetchAll("SELECT * FROM sub_categories");
        $brands = fetchAll("SELECT * FROM brands");
    
        echo '<div class="container mt-4">
                <h2 class="mb-4">Edit Product</h2>
                <form action="adminproducts.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="product-id" value="' . $productId . '">
                    
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="product-name">Product Name</label>
                            <input type="text" class="form-control" id="product-name" name="product-name" value="' . htmlspecialchars($product['Name']) . '" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="product-brand">Brand</label>
                            <select class="form-control" id="product-brand" name="product-brand" required>';
        
        foreach ($brands as $brand) {
            $selected = ($brand['Brand_ID'] == $product['Brand_ID']) ? 'selected' : '';
            echo '<option value="' . $brand['Brand_ID'] . '" ' . $selected . '>' . htmlspecialchars($brand['Name']) . '</option>';
        }
        
        echo '      </select>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="product-sub-category">Category / Sub-Category</label>
                            <select class="form-control" id="product-sub-category" name="product-sub-category" required>';
        
        foreach ($categories as $category) {
            echo '<optgroup label="' . htmlspecialchars($category['Name']) . '">';
            foreach ($subcategories as $subcategory) {
                if ($subcategory['Category_ID'] == $category['Category_ID']) {
                    $selected = ($subcategory['Sub_ID'] == $product['Sub_ID']) ? 'selected' : '';
                    echo '<option value="' . $subcategory['Sub_ID'] . '" ' . $selected . '>' . htmlspecialchars($subcategory['Name']) . '</option>';
                }
            }
            echo '</optgroup>';
        }
        
        echo '      </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="product-quantity">Quantity</label>
                            <input type="number" class="form-control" id="product-quantity" name="product-quantity" value="' . $product['Quantity'] . '" min="0" required>
                        </div>
                    </div>
    
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label for="product-bought-for">Bought For</label>
                            <input type="number" class="form-control" id="product-bought-for" name="product-bought-for" step="0.01" min="0" value="' . $product['bought_for'] . '" required>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="product-net-price">Net Price</label>
                            <input type="number" class="form-control" id="product-net-price" name="product-net-price" step="0.01" min="0" value="' . $product['net_price'] . '" required>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="product-discount">Discount</label>
                            <input type="number" class="form-control" id="product-discount" name="product-discount" step="0.01" min="0" max="100" value="' . $product['discount'] . '" required>
                        </div>
                    </div>
    
                    <div class="form-group">
                        <label for="product-image">Product Image</label>
                        <input type="file" class="form-control-file" id="product-image" name="product-image" accept="image/*">
                    </div>';
    
        if ($product['product_img']) {
            echo '<div class="mb-3">
                    <img src="data:image/jpeg;base64,' . base64_encode($product['product_img']) . '" class="img-thumbnail" style="max-width: 200px;" alt="Current Product Image">
                  </div>';
        }
    
        echo '  <div class="form-group">
                    <label for="product-description">Description</label>
                    <textarea class="form-control" id="product-description" name="product-description" rows="3" required>' . htmlspecialchars($product['Description']) . '</textarea>
                </div>
    
                <div class="form-group">
                    <label for="product-specification">Specifications</label>
                    <textarea class="form-control" id="product-specification" name="product-specification" rows="3" required>' . htmlspecialchars($product['Specifications']) . '</textarea>
                </div>
                <input type="hidden" value="'.$product['Product_ID'].'" name="productid">
                <button type="submit" class="btn btn-primary" name="update-product">Update Product</button>
            </form>
        </div>';
    
        $conn->close();
    }

    public function updateProduct($productId) {
        $conn = connectDB();
        if (!$conn) {
            die("Connection failed: " . mysqli_connect_error());
        }
    
        $stmt = $conn->prepare("SELECT * FROM products WHERE Product_ID = ?");
        $stmt->bind_param("i", $productId);
        $stmt->execute();
        $result = $stmt->get_result();
        $currentProduct = $result->fetch_assoc();
        $stmt->close();
    
        $query = "UPDATE products SET ";
        $types = "";
        $params = array();
    
        if ($this->name !== $currentProduct['Name']) {
            $query .= "Name = ?, ";
            $types .= "s";
            $params[] = $this->name;
        }
    
        if ($this->img && $this->img['error'] == 0) {
            $imageData = file_get_contents($this->img['tmp_name']);
            $query .= "product_img = ?, ";
            $types .= "b";
            $params[] = $imageData;
        }
    
        if ($this->boughtFor != $currentProduct['bought_for']) {
            $query .= "bought_for = ?, ";
            $types .= "d";
            $params[] = $this->boughtFor;
        }
    
        if ($this->netPrice != $currentProduct['net_price']) {
            $query .= "net_price = ?, ";
            $types .= "d";
            $params[] = $this->netPrice;
        }
    
        if ($this->discount != $currentProduct['discount']) {
            $query .= "discount = ?, ";
            $types .= "d";
            $params[] = $this->discount;
        }
    
        if ($this->description !== $currentProduct['Description']) {
            $query .= "Description = ?, ";
            $types .= "s";
            $params[] = $this->description;
        }
    
        if ($this->specification !== $currentProduct['Specifications']) {
            $query .= "Specifications = ?, ";
            $types .= "s";
            $params[] = $this->specification;
        }
    
        if ($this->quantity != $currentProduct['Quantity']) {
            $query .= "quantity = ?, ";
            $types .= "i";
            $params[] = $this->quantity;
        }
    
        if ($this->brandId != $currentProduct['Brand_ID']) {
            $query .= "Brand_ID = ?, ";
            $types .= "i";
            $params[] = $this->brandId;
        }
    
        if ($this->subId != $currentProduct['Sub_ID']) {
            $query .= "Sub_ID = ?, ";
            $types .= "i";
            $params[] = $this->subId;
        }
    
        // Calculate the new price
        $newPrice = $this->netPrice - ($this->netPrice * ($this->discount / 100));
    
        if ($newPrice != $currentProduct['Price']) {
            $query .= "Price = ?, ";
            $types .= "d";
            $params[] = $newPrice;
        }
    
        if (empty($params)) {
            $_SESSION['flash_message'] = "No changes to update.";
            $conn->close();
            return true;
        }
    
        $query = rtrim($query, ", ");
    
        $query .= " WHERE Product_ID = ?";
        $types .= "i";
        $params[] = $productId;
    
        $stmt = $conn->prepare($query);
        if (!$stmt) {
            $_SESSION['flash_message'] = "Prepare failed: " . $conn->error;
            $conn->close();
            return false;
        }
    
        $stmt->bind_param($types, ...$params);
        if ($stmt->execute()) {
            $_SESSION['flash_message'] = "Product updated successfully!";
            $stmt->close();
            $conn->close();
            return true;
        } else {
            $_SESSION['flash_message'] = "Error updating product: " . $stmt->error;
            $stmt->close();
            $conn->close();
            return false;
        }
    }
    

    



    
}


    



 
    
    
    
    
    
    
    
    
    
    


    




?>
