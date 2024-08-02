<?php
include 'config.php';

function login($email, $password) {
    $response = array();
    $mysqli = connectDB();
    $stmt = $mysqli->prepare("SELECT * FROM accounts WHERE email = ?");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if ($user['activated'] == 0) {
            $response['status'] = 'error';
            $response['message'] = 'Your account is deactivated. If you want to reactivate, please <a href="contactus.php">contact us</a>.';
        } elseif (password_verify($password, $user['Password'])) {
            $_SESSION['user_id'] = $user['ID'];
            $_SESSION['user_name'] = $user['Name'];
            $_SESSION['user_email'] = $user['Email'];
            $_SESSION['is_admin'] = $user['is_admin'];
            $response['status'] = 'success';
            $response['message'] = 'Login Successful.';
        } else {
            $response['status'] = 'error';
            $response['message'] = 'Invalid Password.';
        }
    } else {
        $response['status'] = 'error';
        $response['message'] = 'User not Found.';
    }
    $stmt->close();
    $mysqli->close();
    return $response;
}

function register($name, $email, $password){
    $response = array();
    $mysqli = connectDB();
    $stmt = $mysqli->prepare("SELECT * FROM accounts WHERE email = ?");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $response['status'] = 'error';
        $response['message'] = 'Email Already Exists.';
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $mysqli->prepare("INSERT INTO accounts (name, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param('sss', $name, $email, $hashed_password);

        if ($stmt->execute()) {
            $response['status'] = 'success';
            $response['message'] = 'Registration Successful.';
        } else {
            $response['status'] = 'error';
            $response['message'] = 'Registration Failed. Please try again later.';
        }
    }
    $stmt->close();
    $mysqli->close();
    
    return $response;
}

function logout(){    
    $_SESSION = array();
    session_destroy();
    header('Location: index.php');
    exit;
}

function isLoggedIn(){
    session_start();
    return isset($_SESSION['user_id']);
}
function isAdmin(){ 
    return isset($_SESSION['is_admin']) && $_SESSION['is_admin'];
}



function getCategories(){
    $conn = connectDB();
    $sql = "SELECT * FROM categories";
    $result = mysqli_query($conn, $sql);
    $categories = array();
    if(mysqli_num_rows($result) > 0){
        $categories = $result->fetch_all(MYSQLI_ASSOC);
        
    }
    
    return json_encode($categories);
  }

  function getSubCategories($category_id) {
    $subCategories = [];
    $conn = connectDB();
    $stmt = $conn->prepare("SELECT * FROM sub_categories WHERE Category_ID = ?");
    $stmt->bind_param('i', $category_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $subCategories[] = $row;
    }
    $stmt->close();
    $conn->close();
    header('Content-Type: application/json');
    echo json_encode($subCategories);
}

function getProductsBySubCategory($subCategoryId) {
    $products = [];
    $conn = connectDB();
    $stmt = $conn->prepare("SELECT * FROM products WHERE Sub_ID = ?");
    $stmt->bind_param('i', $subCategoryId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $isLoggedIn = isset($_SESSION['user_id']);
    
    if ($result->num_rows > 0) {
        echo '<div class="container custom-container">';
        echo '<div class="row g-4">';
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
            
            if (!is_null($row['product_img'])) {
                $imgData = base64_encode($row['product_img']);
                $imgSrc = 'data:image/jpeg;base64,' . $imgData;
            } else {
                $imgSrc = 'path/to/default/image.jpg';
            }
            
            echo '<div class="col-lg-3 col-md-4 col-sm-6 mb-4 custom-col">';
            echo '<div class="card h-100" style="max-width: 250px; margin: 0 auto;">';
            echo '<a href="addtocart.php?id='.$row['Product_ID'].'">';
            echo '<img src="'.$imgSrc.'" class="card-img-top product-img" alt="Product Image" style="height: 150px; object-fit: cover;">';
            echo '</a>';
            echo '<div class="card-body d-flex flex-column">';
            echo '<a href="addtocart.php?id='.$row['Product_ID'].'" style="text-decoration:none; font-size: 0.9rem;">' . htmlspecialchars($row['Name']) . '</a>';
            echo '<p class="card-text" style="font-size: 0.8rem;">$' . number_format($row['Price'], 2) . '</p>';
            
            if ($row['discount'] > 0) {
                echo '<span class="badge bg-success mb-2">On Sale ' . $row['discount'] . '%</span>';
            }
            
            if ($row['Quantity'] <= 0) {
                echo '<span class="badge bg-danger mb-2">Out of Stock</span>';
            } elseif ($isLoggedIn) {
                echo '<form method="post" action="">';
                echo '<input type="submit" class="btn btn-primary btn-sm mt-auto" name="addtocartoutside" value="Add to Cart" style="width:100%;">';
                echo '<input type="hidden" value="'.$row['Product_ID'].'" name="productId">';
                echo '</form>';
            } else {
                $currentPage = urlencode($_SERVER['REQUEST_URI']);
                $loginUrl = 'login.php?return=' . urlencode($currentPage);
                echo '<a href="'.$loginUrl.'" class="btn btn-primary btn-sm mt-auto" style="width:100%;">Login to Add to Cart</a>';
            }
            
            echo '</div>';
            echo '</div>';
            echo '</div>';
        }
        echo '</div>';
        echo '</div>';
    } else {
        echo '<script>alert("No products found")</script>';
    }
    
    $stmt->close();
    $conn->close();
}


function viewProductByBrand($brandId) {
    $products = [];
    $conn = connectDB();
    $stmt = $conn->prepare("SELECT * FROM brands JOIN products ON products.Brand_ID = brands.Brand_ID WHERE brands.Brand_ID = ?");
    $stmt->bind_param('i', $brandId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $isLoggedIn = isset($_SESSION['user_id']);
    
    if ($result->num_rows > 0) {
        echo '<div class="container custom-container">';
        echo '<div class="row g-4">';
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
            
            if (!is_null($row['product_img'])) {
                $imgData = base64_encode($row['product_img']);
                $imgSrc = 'data:image/jpeg;base64,' . $imgData;
            } else {
                $imgSrc = 'path/to/default/image.jpg';
            }
            
            echo '<div class="col-lg-3 col-md-4 col-sm-6 mb-4 custom-col">';
            echo '<div class="card h-100" style="max-width: 250px; margin: 0 auto;">';
            echo '<a href="addtocart.php?id='.$row['Product_ID'].'">';
            echo '<img src="'.$imgSrc.'" class="card-img-top product-img" alt="Product Image" style="height: 150px; object-fit: cover;">';
            echo '</a>';
            echo '<div class="card-body d-flex flex-column">';
            echo '<a href="addtocart.php?id='.$row['Product_ID'].'" style="text-decoration:none; font-size: 0.9rem;">' . htmlspecialchars($row['Name']) . '</a>';
            echo '<p class="card-text" style="font-size: 0.8rem;">$' . number_format($row['Price'], 2) . '</p>';
            
            if ($row['discount'] > 0) {
                echo '<span class="badge bg-success mb-2">On Sale '.$row['discount'].'%</span>';
            }
            
            if ($row['Quantity'] == 0) {
                echo '<span class="badge bg-danger mb-2">Out of Stock</span>';
            } elseif ($isLoggedIn) {
                echo '<form method="post" action="">';
                echo '<input type="submit" class="btn btn-primary btn-sm mt-auto" name="addtocartoutside" value="Add to Cart" style="width:100%;">';
                echo '<input type="hidden" value="'.$row['Product_ID'].'" name="productId">';
                echo '<input type="hidden" value="'.$row['Brand_ID'].'" name="brandid">';
                echo '</form>';
            } else {
                $currentPage = urlencode($_SERVER['REQUEST_URI']);
                $loginUrl = 'login.php?return=' . urlencode($currentPage);
                echo '<a href="'.$loginUrl.'" class="btn btn-primary btn-sm mt-auto" style="width:100%;">Login to Add to Cart</a>';
            }
            
            echo '</div>';
            echo '</div>';
            echo '</div>';
        }
        echo '</div>';
        echo '</div>';
    } else {
        echo '<script>alert("No products found")</script>';
    }
    
    $stmt->close();
    $conn->close();
}


function getProduct($productId) {
    $conn = connectDB();
    $stmt = $conn->prepare("SELECT * FROM products WHERE Product_ID = ?");
    $stmt->bind_param('i', $productId);
    $stmt->execute();
    $result = $stmt->get_result();
    $isLoggedIn = isset($_SESSION['user_id']);
    
    while ($row = $result->fetch_assoc()) {
        if (!is_null($row['product_img'])) {
            $imgData = base64_encode($row['product_img']);
            $imgSrc = 'data:image/jpeg;base64,' . $imgData;
        } else {
            $imgSrc = 'path/to/default/image.jpg';
        }
        
        echo '<div class="container mt-5 mb-3" style="max-width: 50%; background-color: white; border-radius: 10px; padding: 20px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);">
                <div class="row g-4">
                    <div class="col-md-6 text-center">
                        <img src="'.$imgSrc.'" class="img-fluid rounded" style="max-width: 100%; height: auto;" alt="item">
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <h2 style="color: black;">'.$row['Name'].'</h2>';
        
        
        if ($row['discount'] > 0) {
            echo '<p class="badge bg-success mb-2">On Sale '.$row["discount"].'%</p>';
        }
        
        echo '          <hr style="border-top: 1px solid #007bff;">
                        </div>
                        <div class="mb-3">
                            <h4 class="text-primary">'.$row['Price'].'$</h4>
                        </div>';
        
        if ($isLoggedIn) {
            if ($row['Quantity'] > 0) {
                echo '<form method="POST" action="">
                    <div class="mb-3">
                        <label for="quantity" class="form-label" style="color: black;">Quantity</label>
                        <div class="input-group">
                            <button type="button" class="btn btn-outline-primary" onclick="subQuantity()">-</button>
                            <input type="text" class="form-control text-center" id="quantity" name="quantity" value="1" min="1">
                            <button type="button" class="btn btn-outline-primary" onclick="addQuantity()">+</button>
                        </div>
                    </div>
                    <button class="btn btn-primary w-100" type="submit">Add to cart</button>
                </form>';
            } else {
                echo '<div class="alert alert-warning mt-3" role="alert">Out of Stock</div>';
            }
        } else {
            echo '<div class="alert alert-info mt-3" role="alert">
                    Please <a href="login.php" class="alert-link">Log In</a> to add items to your cart.
                  </div>';
        }
        
        echo '</div>
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

function getDescriptionOrSpecs($type, $productId) {
    $conn = connectDB();
    $stmt = $conn->prepare("SELECT * FROM products WHERE Product_ID = ?");
    $stmt->bind_param('i', $productId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row) {
        if ($type == 'description') {
            echo '<h3 style="color: black;">Description</h3>';
            echo '<p>' . htmlspecialchars($row['Description'], ENT_QUOTES, 'UTF-8') . '</p>';
        } elseif ($type == 'specifications') {
            echo '<h3 style="color: black;">Specifications</h3>';
            echo '<pre>' . htmlspecialchars($row['Specifications'], ENT_QUOTES, 'UTF-8') . '</pre>';
        }
    } else {
        echo '<p>No data found for the requested product.</p>';
    }

    $stmt->close();
    $conn->close();
}

    




function addToCartDB($userId, $productId, $quantity) {
    $conn = connectDB();

    $productStmt = $conn->prepare("SELECT Quantity FROM products WHERE Product_ID = ?");
    if ($productStmt === false) {
        die('Prepare failed: ' . $conn->error);
    }
    $productStmt->bind_param('i', $productId);
    $productStmt->execute();
    $productStmt->bind_result($availableQuantity);
    $productStmt->fetch();
    $productStmt->close();

    if ($quantity > $availableQuantity) {
        $_SESSION['flash_message'] = "Cannot add to cart. Only $availableQuantity of this product left in stock.";
        return; // Stop further execution
    }

    // Check if the product is already in the cart
    $checkStmt = $conn->prepare("SELECT cart_quantity FROM cart WHERE ID = ? AND Product_ID = ?");
    if ($checkStmt === false) {
        die('Prepare failed: ' . $conn->error);
    }
    $checkStmt->bind_param('ii', $userId, $productId);
    $checkStmt->execute();
    $checkStmt->store_result();

    if ($checkStmt->num_rows > 0) {
        $checkStmt->bind_result($currentQuantity);
        $checkStmt->fetch();
        $newQuantity = $currentQuantity + $quantity;

        // Check if the new quantity exceeds available quantity
        if ($newQuantity > $availableQuantity) {
            $_SESSION['flash_message'] = "Cannot add to cart. Only $availableQuantity of this product left in stock.";
            return; // Stop further execution
        } else {
            // Update the cart with the new quantity
            $updateStmt = $conn->prepare("UPDATE cart SET cart_quantity = ? WHERE ID = ? AND Product_ID = ?");
            if ($updateStmt === false) {
                die('Prepare failed: ' . $conn->error);
            }
            $updateStmt->bind_param('iii', $newQuantity, $userId, $productId);
            $updateStmt->execute();
            $updateStmt->close();
        }
    } else {
        // Insert the new item into the cart
        $insertStmt = $conn->prepare("INSERT INTO cart (ID, Product_ID, cart_quantity) VALUES (?, ?, ?)");
        if ($insertStmt === false) {
            die('Prepare failed: ' . $conn->error);
        }
        $insertStmt->bind_param('iii', $userId, $productId, $quantity);
        $insertStmt->execute();
        $insertStmt->close();
    }

    $checkStmt->close();
    $conn->close();
}





function displayInCart($userId){
    $conn = connectDB();
    $stmt = $conn->prepare("SELECT c.*, p.Name, p.Price, (c.cart_quantity * p.Price) as total , p.product_img
                            FROM cart c
                            JOIN products p ON c.Product_ID = p.Product_ID
                            WHERE c.ID = ?");
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    
    echo '<form method="post" action="">
            <table class="table table-bordered text-center">
                <thead class="thead-dark">
                    <tr>
                        <th scope="col">Product</th>
                        <th scope="col">Price</th>
                        <th scope="col">Quantity</th>
                        <th scope="col">Subtotal</th>
                    </tr>
                </thead>
                <tbody>';

    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $imgSrc = !is_null($row['product_img']) 
            ? 'data:image/jpeg;base64,' . base64_encode($row['product_img']) 
            : 'path/to/default/image.jpg';

        echo '<tr>
                <td class="product align-middle">
                    <div class="d-flex align-items-center">
                        <div class="product-remove mr-2">
                            <form action="" method="post" class="m-0 p-0">
                                <input type="hidden" name="cart_id" value="'.$row['cart_id'].'">
                                <button type="submit" name="remove" class="btn btn-link p-0 text-danger">
                                    <i class="fas fa-times"></i>
                                </button>
                            </form>
                        </div>
                        <div class="product-img mx-2">
                            <img src="'.$imgSrc.'" alt="product-img" class="img-fluid" style="width: 100px;">
                        </div>
                        <div class="product-name">
                            <pre class="mb-0">'.$row['Name'].'</pre>
                        </div>
                    </div>
                </td>
                <td class="price-item align-middle">
                    <pre class="mb-0">$'.$row['Price'].'</pre>
                </td>
                <td class="quantity-item align-middle"> 
                    <div class="d-flex align-items-center justify-content-center">
                        <button type="button" class="btn btn-secondary btn-sm mr-2" onclick="subQuantity(this)">-</button>
                        <input type="text" name="quantities['.$row['cart_id'].']" class="form-control quantityInput text-center mx-1" value="'.$row['cart_quantity'].'" style="width: 50px;">
                        <button type="button" class="btn btn-secondary btn-sm ml-2" onclick="addQuantity(this)">+</button>
                    </div>
                </td>
                <td class="total-item align-middle">
                    <pre class="mb-0">$'.$row['total'].'</pre>
                </td>
            </tr>';     
    }
    echo '</tbody></table>
          <div class="d-flex justify-content-between">
              <a href="index.php" class="btn btn-secondary">Continue Shopping</a>
              <button type="submit" name="update_quantities" class="btn btn-primary">Update All Quantities</button>
          </div>
          </form>';


}






function removeItemFromCart($userId, $cart_id) {
    $conn = connectDB();
    $stmt = $conn->prepare("DELETE FROM cart WHERE cart_id = ? AND ID = ?");
    $stmt->bind_param('ii', $cart_id, $userId);

    $stmt->execute();
   

    $stmt->close();
    $conn->close();
}

function updateQuantity($cart_id, $quantity) {
    $conn = connectDB();
    $unavailableProducts = array();

    // Fetch the product ID from the cart
    $cartStmt = $conn->prepare("SELECT Product_ID FROM cart WHERE cart_id = ?");
    if ($cartStmt === false) {
        die('Prepare failed: ' . $conn->error);
    }
    $cartStmt->bind_param('i', $cart_id);
    $cartStmt->execute();
    $cartStmt->bind_result($productId);
    $cartStmt->fetch();
    $cartStmt->close();

    // Fetch product name and check available product quantity
    $productStmt = $conn->prepare("SELECT Name, Quantity FROM products WHERE Product_ID = ?");
    if ($productStmt === false) {
        die('Prepare failed: ' . $conn->error);
    }
    $productStmt->bind_param('i', $productId);
    $productStmt->execute();
    $productStmt->bind_result($productName, $availableQuantity);
    $productStmt->fetch();
    $productStmt->close();

    if ($quantity > $availableQuantity) {
        // Add the product name and available quantity to the unavailable products array
        $unavailableProducts[] = array(
            'name' => $productName,
            'requested' => $quantity,
            'available' => $availableQuantity
        );
    } else {
        // Update the cart with the new quantity
        $stmt = $conn->prepare("UPDATE cart SET cart_quantity = ? WHERE cart_id = ?");
        if ($stmt === false) {
            die('Prepare failed: ' . $conn->error);
        }
        $stmt->bind_param('ii', $quantity, $cart_id);
        $stmt->execute();
        $stmt->close();
    }

    $conn->close();
    
    return $unavailableProducts;
}




  function getSubTotal($userId){
    $conn = connectDB();
    $sql = "SELECT c.cart_quantity, p.Price FROM cart c JOIN products p ON c.Product_ID = p.Product_ID WHERE c.ID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $subTotal = 0;
    while($row = $result->fetch_assoc()) {
        $subTotal += $row['cart_quantity'] * $row['Price'];
    }
    $stmt->close();
    return $subTotal;
}


function displayInCheckOut($userId){
    $conn = connectDB();
    $stmt = $conn->prepare("SELECT c.*, p.Name, p.Price, (c.cart_quantity * p.Price) as total
                            FROM cart c
                            JOIN products p ON c.Product_ID = p.Product_ID
                            WHERE c.ID = ?");
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    echo '<table class="table">
            <thead>
                <tr>
                    <th>PRODUCT</th>
                    <th>SUBTOTAL</th>
                </tr>
            </thead>
            <tbody>';
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        echo '<tr>
                <td>'.$row['Name'].' x '.$row['cart_quantity'].'</td>
                <td>'.$row['total'].'</td>
              </tr>';
    }
    echo '</tbody></table>';
    $stmt->close();
}
    
  
function insertOrder($userId, $firstName, $lastName, $mobilePhone, $landline, $address, $cityTown, $emailAddress, $additionalNotes) {
    $conn = connectDB();
    
    // Calculate subtotal
    $stmt = $conn->prepare("SELECT SUM(c.cart_quantity * p.Price) as subtotal 
                            FROM cart c 
                            JOIN products p ON c.Product_ID = p.Product_ID 
                            WHERE c.ID = ?");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $subtotal = $row['subtotal'];
    $vat = $subtotal * 0.11;
    $shipping = 2;
    $total = $subtotal + $vat + $shipping;
    
    // Determine order status
    $status = ($subtotal <= 300) ? "completed" : "processing";
    
    // Insert order
    $stmt = $conn->prepare("INSERT INTO orders (ID, total, order_date, first_name, last_name, mobile_phone, landline, address, city_town, email_address, additional_notes, status) VALUES (?, ?, NOW(), ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("idsssssssss", $userId, $total, $firstName, $lastName, $mobilePhone, $landline, $address, $cityTown, $emailAddress, $additionalNotes, $status);
    $stmt->execute();
    $orderId = $stmt->insert_id;
    $stmt->close();
    
    $conn->close();
    
    return $orderId;
}

function insertOrderInfo($orderId, $userId) {
    $conn = connectDB();

    // Start a transaction
    $conn->begin_transaction();

    // Query to get cart items along with product prices, bought_for values, and product names
    $cartQuery = $conn->prepare("
        SELECT cart.Product_ID, cart.cart_quantity, products.price, products.bought_for, products.Name, products.quantity
        FROM cart
        JOIN products ON cart.Product_ID = products.Product_ID
        WHERE cart.ID = ?
    ");
    $cartQuery->bind_param("i", $userId);
    $cartQuery->execute();
    $cartResult = $cartQuery->get_result();

    // Check product quantities
    $errors = [];
    $productQuantities = [];

    if ($cartResult->num_rows > 0) {
        while ($row = $cartResult->fetch_assoc()) {
            $productID = $row['Product_ID'];
            $quantity = $row['cart_quantity'];
            $price = $row['price'];
            $boughtFor = $row['bought_for'];
            $productName = $row['Name'];
            $availableQuantity = $row['quantity'];

            if ($availableQuantity < $quantity) {
                $itemDifference = $quantity - $availableQuantity;
                $itemWord = $itemDifference > 1 ? "items" : "item";
                $errors[] = "The product '{$productName}' has only {$availableQuantity} " . 
                            ($availableQuantity == 1 ? "item" : "units") . " available, but you requested {$quantity}. " .
                            "It seems someone purchased {$itemDifference} {$itemWord} while you were placing your order. " .
                            "Please reduce the quantity.";
            } else {
                // Record the quantities to be updated later
                $productQuantities[$productID] = $quantity;

                // Insert the order item with price and bought_for
                $stmt = $conn->prepare("
                    INSERT INTO orderinfo (order_id, Product_ID, quantity, price, bought_for)
                    VALUES (?, ?, ?, ?, ?)
                ");
                $stmt->bind_param("iiidd", $orderId, $productID, $quantity, $price, $boughtFor);
                $stmt->execute();
                $stmt->close();
            }
        }
    }

    $cartQuery->close();

    if (!empty($errors)) {
        // Roll back the transaction
        $conn->rollback();

        // Set flash message in session
        $_SESSION['flash_message'] = "The following products are not available in the requested quantities: " . implode("  ", $errors);

        // Redirect to cart with error messages
        header("Location: cart.php");
        exit;
    } else {
        // Update product quantities
        foreach ($productQuantities as $productID => $quantity) {
            $stmt = $conn->prepare("UPDATE products SET quantity = quantity - ? WHERE Product_ID = ?");
            $stmt->bind_param("ii", $quantity, $productID);
            $stmt->execute();
            $stmt->close();
        }

        // Commit the transaction
        $conn->commit();

        // Set success flash message in session
        $_SESSION['flash_message'] = "Order placed successfully!";
    }

    $conn->close();
}




    function deleteCart($userId) {
        $conn = connectDB();
        $stmt = $conn->prepare("DELETE FROM cart WHERE ID = ?");
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $stmt->close();
        $conn->close();
    }
    function orders($userId) {
        $conn = connectDB();
        $stmt = $conn->prepare("SELECT order_id, DATE(order_date) as order_date, status, total FROM orders WHERE ID = ? ORDER BY order_date DESC");
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        echo '<div class="container table-container mt-5">';
        echo '<table class="table table-striped">
                <thead>
                    <tr>
                        <th>Order#</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Total</th>
                        <th>Details</th>
                    </tr>
                </thead>
                <tbody>';
        
        while ($row = $result->fetch_assoc()) {
            echo '<tr>
                    <td>' . htmlspecialchars($row['order_id']) . '</td>
                    <td>' . htmlspecialchars($row['order_date']) . '</td>
                    <td>' . htmlspecialchars($row['status']) . '</td>
                    <td>$' . htmlspecialchars($row['total']) . '</td>
                    <td>
                        <form action="vieworderinfo.php" method="post">
                            <input type="hidden" name="orderId" value="' . htmlspecialchars($row['order_id']) . '">
                            <button type="submit" name="vieworder" class="btn btn-primary btn-sm">View</button>
                        </form>
                    </td>
                </tr>';
        }
        
        echo '</tbody></table>';
        
        if ($result->num_rows == 0) {
            echo '<p class="alert alert-info">No orders found.</p>';
        }
        
        echo '</div>'; 
        
        $stmt->close();
        $conn->close();
    }
    
    
    
    function viewOrderInfo($userId, $orderId) {
        $conn = connectDB();
        
        if (!$conn) {
            die("Connection failed: " . mysqli_connect_error());
        }
    
        $stmt = $conn->prepare("
            SELECT o.*, oi.Product_ID, oi.quantity, p.Name, p.Price
            FROM orders o
            JOIN orderinfo oi ON o.order_id = oi.order_id
            JOIN products p ON oi.Product_ID = p.Product_ID
            WHERE o.ID = ? AND o.order_id = ?
        ");
        
        if (!$stmt) {
            die("Prepare failed: " . $conn->error);
        }
    
        $stmt->bind_param('ii', $userId, $orderId);
        $stmt->execute();
        
        $result = $stmt->get_result();
        $subTotal = 0;
    
        if ($result->num_rows > 0) {
            $orderInfo = $result->fetch_assoc();
            echo '<div class="container mt-5">';
            
            // Determine the correct status message
            $statusMessage = "";
            switch ($orderInfo['status']) {
                case 'accepted':
                    $statusMessage = "has been accepted";
                    break;
                case 'rejected':
                    $statusMessage = "has been rejected";
                    break;
                case 'processing':
                    $statusMessage = "is currently being processed";
                    break;
                case 'hold':
                    $statusMessage = "is currently on hold";
                    break;
                case 'completed':
                    $statusMessage = "is completed";
                    break;
                default:
                    $statusMessage = "has an unknown status";
                    break;
            }
            
            echo '<div class="alert alert-info">Order #' . htmlspecialchars($orderId) . ' was placed on ' . htmlspecialchars($orderInfo['order_date']) . ' and ' . $statusMessage . '.</div>';
            
            echo '<h2>Order Details</h2>';
            echo '<table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>PRODUCT</th>
                            <th>TOTAL</th>
                        </tr>
                    </thead>
                    <tbody>';
            
            do {
                $productTotal = $orderInfo['Price'] * $orderInfo['quantity'];
                echo '<tr>
                        <td>' . htmlspecialchars($orderInfo['Name']) . ' x ' . htmlspecialchars($orderInfo['quantity']) . '</td>
                        <td>$' . htmlspecialchars($productTotal) . '</td>
                    </tr>';
                $subTotal += $productTotal;
            } while ($orderInfo = $result->fetch_assoc());
    
            $vat = $subTotal * 0.11;
            $shipping = 2;
            $total = $subTotal + $vat + $shipping;
    
            echo '<tr>
                    <td>Subtotal:</td>
                    <td>$' . htmlspecialchars($subTotal) . '</td>
                  </tr>
                  <tr>
                    <td>Shipping:</td>
                    <td>$' . htmlspecialchars($shipping) . '</td>
                  </tr>
                  <tr>
                    <td>VAT:</td>
                    <td>$' . htmlspecialchars($vat) . '</td>
                  </tr>
                  <tr>
                    <td>Total:</td>
                    <td>$' . htmlspecialchars($total) . '</td>
                  </tr>
                </tbody>
              </table>';
    
            // Fetch customer information
            $stmt = $conn->prepare("SELECT * FROM orders WHERE order_id = ?");
            $stmt->bind_param('i', $orderId);
            $stmt->execute();
            $result = $stmt->get_result();
            $customerInfo = $result->fetch_assoc();
    
            echo '<h2>Billing Information</h2>
                  <div class="card">
                    <div class="card-body">
                      <p><strong>Name:</strong> ' . htmlspecialchars($customerInfo['first_name']) . ' ' . htmlspecialchars($customerInfo['last_name']) . '</p>
                      <p><strong>Mobile Phone:</strong> ' . htmlspecialchars($customerInfo['mobile_phone']) . '</p>
                      <p><strong>Landline:</strong> ' . htmlspecialchars($customerInfo['landline']) . '</p>
                      <p><strong>Address:</strong> ' . htmlspecialchars($customerInfo['address']) . '</p>
                      <p><strong>City/Town:</strong> ' . htmlspecialchars($customerInfo['city_town']) . '</p>
                      <p><strong>Email:</strong> ' . htmlspecialchars($customerInfo['email_address']) . '</p>
                      <p><strong>Additional Notes:</strong> ' . htmlspecialchars($customerInfo['additional_notes']) . '</p>
                    </div>
                  </div>
                </div>';     
    
        } else {
            echo '<script>alert("You cannot access this order because it does not exist or does not belong to you.");</script>';
            echo '<script>window.location.href = "index.php";</script>';
            exit();
        }
    
        $stmt->close();
        $conn->close();
    }
    


    function viewChats($userId) {
        $conn = connectDB();
    
        // Query for ongoing chats
        $query_ongoing = "SELECT chat_id, started_at, status 
                          FROM chats 
                          WHERE ID = ? AND status = 'ongoing' 
                          ORDER BY started_at DESC";
        $stmt_ongoing = $conn->prepare($query_ongoing);
        $stmt_ongoing->bind_param('i', $userId);
        $stmt_ongoing->execute();
        $result_ongoing = $stmt_ongoing->get_result();
    
        // Query for closed chats
        $query_closed = "SELECT chat_id, started_at, status 
                         FROM chats 
                         WHERE ID = ? AND status = 'closed' 
                         ORDER BY started_at DESC";
        $stmt_closed = $conn->prepare($query_closed);
        $stmt_closed->bind_param('i', $userId);
        $stmt_closed->execute();
        $result_closed = $stmt_closed->get_result();
    
        // Display ongoing chats table
        echo '<h2>Ongoing Chats</h2>';
        if ($result_ongoing->num_rows > 0) {
            echo '<table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Chat ID</th>
                            <th>Started At</th>
                            <th>Status</th>
                            <th>View</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>';
            while($row = $result_ongoing->fetch_assoc()) {
                echo '<tr>
                        <td>' . htmlspecialchars($row['chat_id']) . '</td>
                        <td>' . htmlspecialchars($row['started_at']) . '</td>
                        <td>' . htmlspecialchars($row['status']) . '</td>
                        <td>
                            <a href="chatinfo.php?chat_id=' . htmlspecialchars($row['chat_id']) . '&status=' . htmlspecialchars($row['status']) . '" class="btn btn-primary btn-sm">View Chat</a>
                        </td>
                        <td>
                            <form method="POST" action="">
                                <input type="hidden" name="chat_id" value="' . htmlspecialchars($row['chat_id']) . '">
                                <button type="submit" name="close-chat" class="btn btn-danger btn-sm">Close Chat</button>
                            </form>
                        </td>
                      </tr>';
            }
            echo '</tbody></table>';
        } else {
            echo '<p class="alert alert-info">No ongoing chats found.</p>';
        }
        echo'<div class="mt-4 " style="margin:1rem 0">
                <form action="chatinfo.php" method="post">
                    <button type="submit" name="new-chat" class="btn btn-success">Create New Chat</button>
                    <input type="hidden" value="create-new-chat" name="create-new-chat">
                </form>
            </div>';
        // Display closed chats table
        echo '<h2>Closed Chats</h2>';
        if ($result_closed->num_rows > 0) {
            echo '<table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Chat ID</th>
                            <th>Started At</th>
                            <th>Status</th>
                            <th>View</th>
                        </tr>
                    </thead>
                    <tbody>';
            while($row = $result_closed->fetch_assoc()) {
                echo '<tr>
                        <td>' . htmlspecialchars($row['chat_id']) . '</td>
                        <td>' . htmlspecialchars($row['started_at']) . '</td>
                        <td>' . htmlspecialchars($row['status']) . '</td>
                        <td>
                            <a href="chatinfo.php?chat_id=' . htmlspecialchars($row['chat_id']) . '&status=' . htmlspecialchars($row['status']) . '" class="btn btn-primary btn-sm">View Chat</a>
                        </td>
                      </tr>';
            }
            echo '</tbody></table>';
        } else {
            echo '<p class="alert alert-info">No closed chats found.</p>';
        }
    
        $stmt_ongoing->close();
        $stmt_closed->close();
        $conn->close();
    }
    
    


    function createNewChat($userId) {
        $conn = connectDB();
        $checkQuery = "SELECT chat_id FROM chats WHERE ID = ? AND status = 'ongoing' LIMIT 1";
        $checkStmt = $conn->prepare($checkQuery);
        $checkStmt->bind_param('i', $userId);
        $checkStmt->execute();
        $result = $checkStmt->get_result(); 
        if ($result->num_rows > 0) {
            $checkStmt->close();
            $conn->close();
            echo '<script>alert("You already have an ongoing chat.");</script>';
        } 
        else {
            $checkStmt->close();  
            $status = 'ongoing';
            $currentDate = date('Y-m-d H:i:s');           
            $query = "INSERT INTO chats (ID, started_at, status) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param('iss', $userId, $currentDate, $status);           
            if ($stmt->execute()) {
                $newChatId = $stmt->insert_id; // Fetch the newly inserted chat ID
                $stmt->close();
                $conn->close();
                return $newChatId;
            } else {
                echo "<script>alert('Issue is from our end. Sorry.');</script>";
                $stmt->close();
                $conn->close();
            }
        }
    }
    
    

    function viewChatInfo($chatId, $status) {
        $conn = connectDB();
        $stmt = $conn->prepare("SELECT * FROM chat_info WHERE chat_id = ? ORDER BY time_of_message ASC");
        $stmt->bind_param('i', $chatId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        echo '<div class="container mt-3">';
        echo '<div class="chat-window card">';
        echo '<div class="card-body chat-content" style="height: 400px; overflow-y: auto;">';
        
        while($row = $result->fetch_assoc()){
            if (!empty($row['sent_message'])) {
                echo '<div class="chat-message user-message text-right mb-2">
                        <div class="message-content d-inline-block bg-light p-2 rounded">
                            ' . htmlspecialchars($row['sent_message']) . '
                        </div>
                        <div class="message-time small text-muted mt-1">
                            ' . htmlspecialchars($row['time_of_message']) . '
                        </div>
                      </div>';
            }
            if (!empty($row['reply_message'])) {
                echo '<div class="chat-message admin-message text-left mb-2">
                        <div class="message-content d-inline-block bg-primary text-white p-2 rounded">
                            ' . htmlspecialchars($row['reply_message']) . '
                        </div>
                        <div class="message-time small text-muted mt-1">
                            ' . htmlspecialchars($row['time_of_message']) . '
                        </div>
                      </div>';
            }
        }
        
        echo '</div>'; // Close card-body
        
        echo '<div class="card-footer">';
        echo '<form method="post" action="" id="messageform">
                <div class="input-group">';
        
        if ($status == 'closed') {
            echo '<input type="text" name="message" class="form-control" placeholder="Chat is closed" disabled>
                  <div class="input-group-append">
                      <button class="btn btn-primary" type="submit" name="send_message" disabled>Send</button>
                  </div>';
        } else {
            echo '<input type="text" name="message" class="form-control" placeholder="Type a message...">
                  <div class="input-group-append">
                      <button class="btn btn-primary" type="submit" name="send_message">Send</button>
                  </div>';
        }
    
        echo '</div>
              </form>';
        
        if ($status != 'closed') {
            echo '<form method="post" action="chats.php" class="mt-3">
                    <button type="submit" name="close_chat" class="btn btn-danger">Close Chat</button>
                    <input type="hidden" value="close-chat" name="close-chat">
                    <input type="hidden" value="' . $chatId . '" name="chat_id">
                  </form>';
        }
    
        echo '</div>'; // Close card-footer
        
        echo '</div>'; // Close chat-window
        echo '</div>'; // Close container
        
        $stmt->close();
        $conn->close();
    }
    
    
    
    
    
    function addMsg($chatId, $message) {
        $conn = connectDB();
        $currentTime = date('Y-m-d H:i:s');
        $stmt = $conn->prepare("INSERT INTO chat_info (chat_id, sent_message, time_of_message) VALUES (?, ?, ?)");
        $stmt->bind_param('iss', $chatId, $message, $currentTime);
        $result = $stmt->execute();
        $stmt->close();
        $conn->close();
        return $result;
    }


    function viewChatsAdminOngoing() {
        $conn = connectDB();
        $query_ongoing = "SELECT c.chat_id, c.ID, c.started_at, c.status, u.Name 
                          FROM chats c 
                          JOIN accounts u ON c.ID = u.id 
                          WHERE c.status = 'ongoing' 
                          ORDER BY c.started_at DESC";
        $result_ongoing = $conn->query($query_ongoing);
        echo '<h2>Ongoing Chats</h2>';
        if ($result_ongoing->num_rows > 0) {
            echo '<table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Chat ID</th>
                            <th>User ID</th>
                            <th>Username</th>
                            <th>Started At</th>
                            <th>Status</th>
                            <th>View</th>
                            <th>Close</th>
                        </tr>
                    </thead>
                    <tbody>';
            while($row = $result_ongoing->fetch_assoc()) {
                echo '<tr>
                        <td>' . htmlspecialchars($row['chat_id']) . '</td>
                        <td>' . htmlspecialchars($row['ID']) . '</td>
                        <td>' . htmlspecialchars($row['Name']) . '</td>
                        <td>' . htmlspecialchars($row['started_at']) . '</td>
                        <td>' . htmlspecialchars($row['status']) . '</td>
                        <td>
                            <a href="adminchatinfo.php?chat_id=' . htmlspecialchars($row['chat_id']) . '&status=' . htmlspecialchars($row['status']) . '" class="btn btn-primary btn-sm">View Chat</a>
                        </td>
                        <td>
                            <form method="POST" action="">
                                <input type="hidden" name="chat_id" value="' . htmlspecialchars($row['chat_id']) . '">
                                <button type="submit" name="close_chat" class="btn btn-danger btn-sm">Close Chat</button>
                            </form>
                        </td>
                      </tr>';
            }
            echo '</tbody></table>';
        } else {
            echo '<p class="alert alert-info">No ongoing chats found.</p>';
        }
        $conn->close();
    }
    
    function viewChatsAdminClosed() {
        $conn = connectDB();
        $query_closed = "SELECT c.chat_id, c.ID, c.started_at, c.status, u.Name 
                         FROM chats c 
                         JOIN accounts u ON c.ID = u.id 
                         WHERE c.status = 'closed' 
                         ORDER BY c.started_at DESC";
        $result_closed = $conn->query($query_closed);
        echo '<h2>Closed Chats</h2>';
        if ($result_closed->num_rows > 0) {
            echo '<table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Chat ID</th>
                            <th>User ID</th>
                            <th>Username</th>
                            <th>Started At</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>';
            while($row = $result_closed->fetch_assoc()) {
                echo '<tr>
                        <td>' . htmlspecialchars($row['chat_id']) . '</td>
                        <td>' . htmlspecialchars($row['ID']) . '</td>
                        <td>' . htmlspecialchars($row['Name']) . '</td>
                        <td>' . htmlspecialchars($row['started_at']) . '</td>
                        <td>' . htmlspecialchars($row['status']) . '</td>
                        <td>
                            <a href="adminchatinfo.php?chat_id=' . htmlspecialchars($row['chat_id']) . '&status=' . htmlspecialchars($row['status']) . '" class="btn btn-primary btn-sm">View Chat</a>
                        </td>
                      </tr>';
            }
            echo '</tbody></table>';
        } else {
            echo '<p class="alert alert-info">No closed chats found.</p>';
        }
        $conn->close();
    }
    

    function closeChat($chatId) {
        $conn = connectDB();        
        $stmt = $conn->prepare("UPDATE chats SET status = 'closed' WHERE chat_id = ?");       
        $stmt->bind_param('i', $chatId);        
        $stmt->execute();
        $stmt->close();
        $conn->close();    
    }
    
    

    function addMsgAdmin($chatId, $message) {
        $conn = connectDB();
        $currentTime = date('Y-m-d H:i:s');
        $stmt = $conn->prepare("INSERT INTO chat_info (chat_id, reply_message, time_of_message) VALUES (?, ?, ?)");
        $stmt->bind_param('iss', $chatId, $message, $currentTime);
        $result = $stmt->execute();
        $stmt->close();
        $conn->close();
        return $result;
    }

    function viewChatInfoAdmin($chatId, $status) {
        $conn = connectDB();
        $stmt = $conn->prepare("SELECT * FROM chat_info WHERE chat_id = ? ORDER BY time_of_message ASC");
        $stmt->bind_param('i', $chatId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        echo '<div class="container mt-3">';
        echo '<div class="chat-window card">';
        echo '<div class="card-body chat-content" style="height: 400px; overflow-y: auto;">';
        
        while($row = $result->fetch_assoc()){
            if (!empty($row['sent_message'])) {
                echo '<div class="chat-message user-message text-left mb-2">
                        <div class="message-content d-inline-block bg-light p-2 rounded">
                            ' . htmlspecialchars($row['sent_message']) . '
                        </div>
                        <div class="message-time small text-muted mt-1">
                            ' . htmlspecialchars($row['time_of_message']) . '
                        </div>
                      </div>';
            }
            if (!empty($row['reply_message'])) {
                echo '<div class="chat-message admin-message text-right mb-2">
                        <div class="message-content d-inline-block bg-primary text-white p-2 rounded">
                            ' . htmlspecialchars($row['reply_message']) . '
                        </div>
                        <div class="message-time small text-muted mt-1">
                            ' . htmlspecialchars($row['time_of_message']) . '
                        </div>
                      </div>';
            }
        }
        
        echo '</div>'; // Close card-body
        
        echo '<div class="card-footer">';
        echo '<form method="post" action="" id="messageform">
                <div class="input-group">';
        
        if ($status == 'closed') {
            echo '<input type="text" name="message" class="form-control" placeholder="Chat is closed" disabled>
                  <div class="input-group-append">
                      <button class="btn btn-primary" type="submit" name="send_message" disabled>Send</button>
                  </div>';
        } else {
            echo '<input type="text" name="message" class="form-control" placeholder="Type a message...">
                  <div class="input-group-append">
                      <button class="btn btn-primary" type="submit" name="send_message">Send</button>
                  </div>';
        }
    
        echo '</div>
              </form>';
        
        if ($status != 'closed') {
            echo '<form method="post" action="chats.php" class="mt-3">
                    <button type="submit" name="close_chat" class="btn btn-danger">Close Chat</button>
                    <input type="hidden" value="close-chat" name="close-chat">
                    <input type="hidden" value="' . $chatId . '" name="chat_id">
                  </form>';
        }
    
        echo '</div>'; // Close card-footer
        
        echo '</div>'; // Close chat-window
        echo '</div>'; // Close container
        
        $stmt->close();
        $conn->close();
    }
    
    

    function fetchMessages($chatId) {
        $conn = connectDB();
        $stmt = $conn->prepare("SELECT * FROM chat_info WHERE chat_id = ? ORDER BY time_of_message ASC");
        $stmt->bind_param('i', $chatId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $messages = array();
        while($row = $result->fetch_assoc()){
            $messages[] = $row;
        }
    
        header('Content-Type: application/json');
        echo json_encode($messages);
    
        $stmt->close();
        $conn->close();
    }
    
    function countCartItems($userId) {
        $conn = connectDB();
    
        $sql = "SELECT COUNT(*) as item_count FROM cart WHERE ID = ?";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        $stmt->close();
        
        return $row['item_count'];
    }

    function calculateCartTotal($userId) {
        $conn = connectDB(); 
    
        $sql = "SELECT SUM(c.cart_quantity * p.price) as total_price 
                FROM cart c
                JOIN products p ON c.Product_ID = p.Product_ID
                WHERE c.ID = ?";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        $stmt->close();
        
        return $row['total_price'] ?: 0; 
    }


    function viewOrdersAdminSideCompleted() {
        $conn = connectDB();
        $stmt = $conn->prepare("SELECT order_id, order_date, status, total, CONCAT(first_name, ' ', last_name) as name
                                FROM orders
                                WHERE status = 'completed'
                                ORDER BY order_date DESC
                                LIMIT 5 ");
        $stmt->execute();
        $result = $stmt->get_result();
        
        echo '<div class="container mt-4">
                <h2 class="mb-4">Completed Orders</h2>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead class="thead-dark">
                            <tr>
                                <th>Order#</th>
                                <th>Customer</th>
                                <th>Date</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Actions</th>
                                <th>Details</th>
                            </tr>
                        </thead>
                        <tbody id="orderTableBody5">';
        
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>" . htmlspecialchars($row['order_id']) . "</td>
                    <td>" . htmlspecialchars($row['name']) . "</td>
                    <td>" . htmlspecialchars($row['order_date']) . "</td>
                    <td>$" . htmlspecialchars($row['total']) . "</td>
                    <td><span class='badge badge-success'>" . htmlspecialchars($row['status']) . "</span></td>
                    <td>No actions available</td>
                    <td>
                        <form method='post' action='adminordersinfo.php'>
                            <input class='btn btn-info btn-sm' value='View Details' type='submit'>
                            <input type='hidden' value='" . htmlspecialchars($row['order_id']) . "' name='order_id'>
                        </form>
                    </td>
                  </tr>";
        }
        
        echo '        </tbody>
                    </table>
                    <button id="loadMore5" class="btn btn-primary btn-block">Load More</button>
                </div>
            </div>';
        
        $stmt->close();
        $conn->close();
    }
    

    

function viewOrdersAdminSideProcessing() {
    $conn = connectDB();
    $stmt = $conn->prepare("SELECT order_id, order_date, status, total, CONCAT(first_name, ' ', last_name) as name
                            FROM orders
                            WHERE status = 'processing'
                            LIMIT 5");
    $stmt->execute();
    $result = $stmt->get_result();
    
    echo '<div class="container mt-4">
            <h2 class="mb-4">Processing Orders</h2>
            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead class="thead-dark">
                        <tr>
                            <th>Order#</th>
                            <th>Customer</th>
                            <th>Date</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Actions</th>
                            <th>Details</th>
                        </tr>
                    </thead>
                    <tbody id="orderTableBody1">';
    
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>" . htmlspecialchars($row['order_id']) . "</td>
                <td>" . htmlspecialchars($row['name']) . "</td>
                <td>" . htmlspecialchars($row['order_date']) . "</td>
                <td>$" . htmlspecialchars($row['total']) . "</td>
                <td><span class='badge badge-warning'>" . htmlspecialchars($row['status']) . "</span></td>
                <td>
                    <div class='d-flex'>
                        <form method='post' action='' class='mr-2'>
                            <input class='btn btn-success btn-sm' value='Accept' type='submit'>
                            <input type='hidden' value='" . htmlspecialchars($row['order_id']) . "' name='order_id'>
                            <input type='hidden' value='accepted' name='accepted'>
                        </form>
                        <form method='post' action='' class='mr-2'>
                            <input class='btn btn-danger btn-sm' value='Reject' type='submit'>
                            <input type='hidden' value='" . htmlspecialchars($row['order_id']) . "' name='order_id'>
                            <input type='hidden' value='rejected' name='rejected'>
                        </form>
                        <form method='post' action=''>
                            <input class='btn btn-secondary btn-sm' value='Hold' type='submit'>
                            <input type='hidden' value='" . htmlspecialchars($row['order_id']) . "' name='order_id'>
                            <input type='hidden' value='hold' name='hold'>
                        </form>
                    </div>
                </td>
                <td>
                    <form method='post' action='adminordersinfo.php'> 
                        <input class='btn btn-info btn-sm' value='View Details' type='submit'>
                        <input type='hidden' value='" . htmlspecialchars($row['order_id']) . "' name='order_id'>
                    </form>
                </td>
              </tr>";
    }
    
    echo '        </tbody>
                </table>
                <button id="loadMore1" class="btn btn-primary btn-block">Load More</button>
            </div>
        </div>';
    
    $stmt->close();
    $conn->close();
}


    function viewOrdersAdminSideRejected() {
        $conn = connectDB();
        $stmt = $conn->prepare("SELECT order_id, order_date, status, total, CONCAT(first_name, ' ', last_name) as name 
                                FROM orders
                                WHERE status = 'rejected'
                                ORDER BY order_date DESC
                                LIMIT 5 ");
        $stmt->execute();
        $result = $stmt->get_result();
        
        echo '<div class="container mt-4">
                <h2 class="mb-4">Rejected Orders</h2>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead class="thead-dark">
                            <tr>
                                <th>Order#</th>
                                <th>Customer</th>
                                <th>Date</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Actions</th>
                                <th>Details</th> 
                            </tr>
                        </thead>
                        <tbody id="orderTableBody2">';
        
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>" . htmlspecialchars($row['order_id']) . "</td>
                    <td>" . htmlspecialchars($row['name']) . "</td>
                    <td>" . htmlspecialchars($row['order_date']) . "</td>
                    <td>$" . htmlspecialchars($row['total']) . "</td>
                    <td><span class='badge badge-warning'>" . htmlspecialchars($row['status']) . "</span></td>
                    <td>
                        <div class='d-flex'>
                            <form method='post' action='' class='mr-2'>
                                <input class='btn btn-success btn-sm' value='Accept' type='submit'>
                                <input type='hidden' value='" . htmlspecialchars($row['order_id']) . "' name='order_id'>
                                <input type='hidden' value='accepted' name='accepted'>
                            </form>
                            <form method='post' action=''>
                                <input class='btn btn-secondary btn-sm' value='hold' type='submit'>
                                <input type='hidden' value='" . htmlspecialchars($row['order_id']) . "' name='order_id'>
                                <input type='hidden' value='hold' name='hold'>
                            </form>
                        </div>
                    </td>
                    <td>
                        <form method='post' action='adminordersinfo.php'> 
                            <input class='btn btn-info btn-sm' value='View Details' type='submit'>
                            <input type='hidden' value='" . htmlspecialchars($row['order_id']) . "' name='order_id'>
                        </form>
                    </td>
                  </tr>";
        }
        
        echo '        </tbody>
                    </table>
                    <button id="loadMore2" class="btn btn-primary btn-block">Load More</button>
                </div>
            </div>';
        
        $stmt->close();
        $conn->close();
    }
    function viewOrdersAdminSideAccepted() {
        $conn = connectDB();
        $stmt = $conn->prepare("SELECT order_id, order_date, status, total, CONCAT(first_name, ' ', last_name) as name
                                FROM orders
                                WHERE status = 'accepted'
                                ORDER BY order_date DESC
                                LIMIT 5 ");
        $stmt->execute();
        $result = $stmt->get_result();
        
        echo '<div class="container mt-4">
                <h2 class="mb-4">Accepted Orders</h2>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead class="thead-dark">
                            <tr>
                                <th>Order#</th>
                                <th>Customer</th>
                                <th>Date</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Actions</th>
                                <th>Details</th>
                            </tr>
                        </thead>
                        <tbody id="orderTableBody3">';
        
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>" . htmlspecialchars($row['order_id']) . "</td>
                    <td>" . htmlspecialchars($row['name']) . "</td>
                    <td>" . htmlspecialchars($row['order_date']) . "</td>
                    <td>$" . htmlspecialchars($row['total']) . "</td>
                    <td><span class='badge badge-warning'>" . htmlspecialchars($row['status']) . "</span></td>
                    <td>
                        <div class='d-flex flex-column flex-md-row'>
                            <form method='post' action='' class='mb-2 mb-md-0 mr-md-2'>
                                <input class='btn btn-success btn-sm' value='Complete' type='submit'>
                                <input type='hidden' value='" . htmlspecialchars($row['order_id']) . "' name='order_id'>
                                <input type='hidden' value='completed' name='completed'>
                            </form>
                            <form method='post' action='' class='mb-2 mb-md-0 mr-md-2'>
                                <input class='btn btn-danger btn-sm' value='Reject' type='submit'>
                                <input type='hidden' value='" . htmlspecialchars($row['order_id']) . "' name='order_id'>
                                <input type='hidden' value='rejected' name='rejected'>
                            </form>
                            <form method='post' action=''>
                                <input class='btn btn-secondary btn-sm' value='Hold' type='submit'>
                                <input type='hidden' value='" . htmlspecialchars($row['order_id']) . "' name='order_id'>
                                <input type='hidden' value='hold' name='hold'>
                            </form>
                        </div>
                    </td>
                    <td>
                        <form method='post' action='adminordersinfo.php'>
                            <input class='btn btn-info btn-sm' value='View Details' type='submit'>
                            <input type='hidden' value='" . htmlspecialchars($row['order_id']) . "' name='order_id'>
                        </form>
                    </td>
                  </tr>";
        }
        
        echo '    </tbody>
                    </table>
                    <button id="loadMore3" class="btn btn-primary btn-block">Load More</button>
                </div>
            </div>';
        
        $stmt->close();
        $conn->close();
    }
    

    function viewOrdersAdminSideHold() {
        $conn = connectDB();
        $stmt = $conn->prepare("SELECT order_id, order_date, status, total, CONCAT(first_name, ' ', last_name) as name
                                FROM orders
                                WHERE status = 'hold'
                                ORDER BY order_date DESC
                                LIMIT 5 ");
        $stmt->execute();
        $result = $stmt->get_result();
        
        echo '<div class="container mt-4">
                <h2 class="mb-4">Orders on Hold</h2>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead class="thead-dark">
                            <tr>
                                <th>Order#</th>
                                <th>Customer</th>
                                <th>Date</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Actions</th>
                                <th>Details</th>
                            </tr>
                        </thead>
                        <tbody id="orderTableBody4">';
        
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>" . htmlspecialchars($row['order_id']) . "</td>
                    <td>" . htmlspecialchars($row['name']) . "</td>
                    <td>" . htmlspecialchars($row['order_date']) . "</td>
                    <td>$" . htmlspecialchars($row['total']) . "</td>
                    <td><span class='badge badge-warning'>" . htmlspecialchars($row['status']) . "</span></td>
                    <td>
                        <div class='d-flex'>
                            <form method='post' action='' class='mr-2'>
                                <input class='btn btn-success btn-sm' value='Accept' type='submit'>
                                <input type='hidden' value='" . htmlspecialchars($row['order_id']) . "' name='order_id'>
                                <input type='hidden' value='accepted' name='accepted'>
                            </form>
                            <form method='post' action='' class='mr-2'>
                                <input class='btn btn-danger btn-sm' value='Reject' type='submit'>
                                <input type='hidden' value='" . htmlspecialchars($row['order_id']) . "' name='order_id'>
                                <input type='hidden' value='rejected' name='rejected'>
                            </form>
                        </div>
                    </td>
                    <td>
                        <form method='post' action='adminordersinfo.php'>
                            <input class='btn btn-info btn-sm' value='View Details' type='submit'>
                            <input type='hidden' value='" . htmlspecialchars($row['order_id']) . "' name='order_id'>
                        </form>
                    </td>
                  </tr>";
        }
        
        echo '        </tbody>
                    </table>
                    <button id="loadMore4" class="btn btn-primary btn-block">Load More</button>
                </div>
            </div>';
        
        $stmt->close();
        $conn->close();
    }




    function addRowsToOrderstable($offset, $limit, $status) {
        $conn = connectDB();
        $stmt = $conn->prepare("SELECT order_id, order_date, status, total, CONCAT(first_name, ' ', last_name) as name 
                        FROM orders
                        WHERE status = ?
                        LIMIT ?, ?");
        $stmt->bind_param("sii", $status, $offset, $limit);
        $stmt->execute();
        $result = $stmt->get_result();
    
        $orders = [];
        while ($row = $result->fetch_assoc()) {
            $orders[] = $row;
        }
    
        echo json_encode($orders);
    
        $stmt->close();
        $conn->close();
    }


    function updateOrder($newStatus, $orderId) {
        $conn = connectDB();
        $conn->begin_transaction();
    
        try {
            // Get the current status of the order
            $stmt = $conn->prepare("SELECT status FROM orders WHERE order_id = ?");
            $stmt->bind_param("i", $orderId);
            $stmt->execute();
            $result = $stmt->get_result();
            $currentStatus = $result->fetch_assoc()['status'];
            $stmt->close();
    
            if ($currentStatus === 'rejected' && $newStatus === 'accepted') {
                // Check product availability
                $stmt = $conn->prepare("
                    SELECT oi.Product_ID, oi.quantity, p.quantity AS available_quantity, p.Name
                    FROM orderinfo oi
                    JOIN products p ON oi.Product_ID = p.Product_ID
                    WHERE oi.order_id = ?
                ");
                $stmt->bind_param("i", $orderId);
                $stmt->execute();
                $result = $stmt->get_result();
                $stmt->close();
    
                $unavailableProducts = [];
    
                while ($row = $result->fetch_assoc()) {
                    if ($row['quantity'] > $row['available_quantity']) {
                        $unavailableProducts[] = "{$row['Name']} (requested: {$row['quantity']}, available: {$row['available_quantity']})";
                    }
                }
    
                if (!empty($unavailableProducts)) {
                    throw new Exception("Cannot accept order. The following products are not available in sufficient quantities: " . implode(", ", $unavailableProducts));
                }
    
                // If all products are available, update quantities
                $stmt = $conn->prepare("
                    UPDATE products p
                    JOIN orderinfo oi ON p.Product_ID = oi.Product_ID
                    SET p.quantity = p.quantity - oi.quantity
                    WHERE oi.order_id = ?
                ");
                $stmt->bind_param("i", $orderId);
                $stmt->execute();
                $stmt->close();
            } elseif ($currentStatus === 'accepted' && $newStatus === 'rejected') {
                // Revert quantities if order is being rejected after being accepted
                revertOrderQuantities($orderId, $conn);
            }
    
            // Update the order status
            $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE order_id = ?");
            $stmt->bind_param("si", $newStatus, $orderId);
            $stmt->execute();
            $stmt->close();
    
            $conn->commit();
            return ["success" => true, "message" => "Order status updated successfully to {$newStatus}."];
        } catch (Exception $e) {
            $conn->rollback();
            return ["success" => false, "message" => $e->getMessage()];
        } finally {
            $conn->close();
        }
    }
    
    function revertOrderQuantities($orderId, $conn) {
        // Get the details of the order
        $stmt = $conn->prepare("SELECT Product_ID, quantity FROM orderinfo WHERE order_id = ?");
        $stmt->bind_param("i", $orderId);
        $stmt->execute();
        $result = $stmt->get_result();
    
        while ($row = $result->fetch_assoc()) {
            $productId = $row['Product_ID'];
            $quantity = $row['quantity'];
    
            // Update the product quantity in the products table
            $updateStmt = $conn->prepare("UPDATE products SET Quantity = Quantity + ? WHERE Product_ID = ?");
            $updateStmt->bind_param("ii", $quantity, $productId);
            $updateStmt->execute();
            $updateStmt->close();
        }
    
        $stmt->close();
    }
    


    function adminOrderInfo($orderId) {
        $conn = connectDB();
    
        if (!$conn) {
            die("Connection failed: " . mysqli_connect_error());
        }
    
        $stmt = $conn->prepare("
            SELECT o.*, oi.Product_ID, oi.quantity, p.Name, p.Price
            FROM orders o
            JOIN orderinfo oi ON o.order_id = oi.order_id
            JOIN products p ON oi.Product_ID = p.Product_ID
            WHERE o.order_id = ?
        ");
    
        if (!$stmt) {
            die("Prepare failed: " . $conn->error);
        }
    
        $stmt->bind_param('i', $orderId);
        $stmt->execute();
    
        $result = $stmt->get_result();
        $subTotal = 0;
    
        if ($result->num_rows > 0) {
            echo '<div class="container mt-5">';
            $orderInfo = $result->fetch_assoc();
            
            // Determine the correct status message
            $statusMessage = "";
            switch ($orderInfo['status']) {
                case 'accepted':
                    $statusMessage = "was accepted";
                    break;
                case 'rejected':
                    $statusMessage = "was rejected";
                    break;
                case 'processing':
                    $statusMessage = "is currently being processed";
                    break;
                case 'hold':
                    $statusMessage = "is currently on hold";
                    break;
                case 'completed':
                    $statusMessage = "is completed";
                    break;
                default:
                    $statusMessage = "has an unknown status";
                    break;
            }
            
            echo '<div class="alert alert-info d-flex justify-content-between align-items-center">
                    <div>
                        Order #' . htmlspecialchars($orderId) . ' was placed on ' . htmlspecialchars($orderInfo['order_date']) . ' and ' . htmlspecialchars($statusMessage) . '
                    </div>';
    
            // Conditionally display buttons based on order status
            echo '<div>';
            if ($orderInfo['status'] == 'rejected') {
                echo '<form method="post" action="adminorders.php" class="d-inline-block mr-2">
                        <input class="btn btn-success btn-sm" value="Accept" type="submit">
                        <input type="hidden" value="' . htmlspecialchars($orderId) . '" name="order_id">
                        <input type="hidden" value="accepted" name="accepted">
                    </form>
                    <form method="post" action="adminorders.php" class="d-inline-block">
                        <input class="btn btn-secondary btn-sm" value="Hold" type="submit">
                        <input type="hidden" value="' . htmlspecialchars($orderId) . '" name="order_id">
                        <input type="hidden" value="hold" name="hold">
                    </form>';
            } elseif ($orderInfo['status'] == 'accepted') {
                echo '<form method="post" action="adminorders.php" class="d-inline-block mr-2">
                        <input class="btn btn-danger btn-sm" value="Reject" type="submit">
                        <input type="hidden" value="' . htmlspecialchars($orderId) . '" name="order_id">
                        <input type="hidden" value="rejected" name="rejected">
                    </form>
                    <form method="post" action="adminorders.php" class="d-inline-block">
                        <input class="btn btn-secondary btn-sm" value="Hold" type="submit">
                        <input type="hidden" value="' . htmlspecialchars($orderId) . '" name="order_id">
                        <input type="hidden" value="hold" name="hold">
                    </form>';
            }
            echo '</div>';
            
            echo '</div>';
            
            echo '<h2>Order Details</h2>';
            echo '<table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>PRODUCT</th>
                            <th>TOTAL</th>
                        </tr>
                    </thead>
                    <tbody>';
            
            do {
                $productTotal = $orderInfo['Price'] * $orderInfo['quantity'];
                echo '<tr>
                        <td>' . htmlspecialchars($orderInfo['Name']) . ' x ' . htmlspecialchars($orderInfo['quantity']) . '</td>
                        <td>$' . htmlspecialchars($productTotal) . '</td>
                    </tr>';
                $subTotal += $productTotal;
            } while ($orderInfo = $result->fetch_assoc());
    
            $vat = $subTotal * 0.11;
            $shipping = 2;
            $total = $subTotal + $vat + $shipping;
    
            echo '<tr>
                    <td>Subtotal:</td>
                    <td>$' . htmlspecialchars($subTotal) . '</td>
                  </tr>
                  <tr>
                    <td>Shipping:</td>
                    <td>$' . htmlspecialchars($shipping) . '</td>
                  </tr>
                  <tr>
                    <td>VAT:</td>
                    <td>$' . htmlspecialchars($vat) . '</td>
                  </tr>
                  <tr>
                    <td>Total:</td>
                    <td>$' . htmlspecialchars($total) . '</td>
                  </tr>
                </tbody>
              </table>';
    
            // Fetch customer information
            $stmt = $conn->prepare("SELECT * FROM orders WHERE order_id = ?");
            $stmt->bind_param('i', $orderId);
            $stmt->execute();
            $result = $stmt->get_result();
            $customerInfo = $result->fetch_assoc();
    
            echo '<h2>Customer Information</h2>
                  <div class="card">
                    <div class="card-body">
                      <p><strong>Name:</strong> ' . htmlspecialchars($customerInfo['first_name']) . ' ' . htmlspecialchars($customerInfo['last_name']) . '</p>
                      <p><strong>Mobile Phone:</strong> ' . htmlspecialchars($customerInfo['mobile_phone']) . '</p>
                      <p><strong>Landline:</strong> ' . htmlspecialchars($customerInfo['landline']) . '</p>
                      <p><strong>Address:</strong> ' . htmlspecialchars($customerInfo['address']) . '</p>
                      <p><strong>City/Town:</strong> ' . htmlspecialchars($customerInfo['city_town']) . '</p>
                      <p><strong>Email:</strong> ' . htmlspecialchars($customerInfo['email_address']) . '</p>
                      <p><strong>Additional Notes:</strong> ' . htmlspecialchars($customerInfo['additional_notes']) . '</p>
                    </div>
                  </div>
                </div>';
    
        } else {
            echo '<script>alert("You can\'t access this because the order does not exist!");</script>';
            header('Location: index.php');
            exit();
        }       
    
        $stmt->close();
        $conn->close();
    }


   


function updatePassword($userId, $currentPassword, $newPassword) {
    $response = array();
    $mysqli = connectDB();

    // Verify current password
    $stmt = $mysqli->prepare("SELECT Password FROM accounts WHERE ID = ?");
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($currentPassword, $user['Password'])) {
            // Update password
            $hashed_password = password_hash($newPassword, PASSWORD_DEFAULT);
            $updateStmt = $mysqli->prepare("UPDATE accounts SET Password = ? WHERE ID = ?");
            $updateStmt->bind_param('si', $hashed_password, $userId);
            
            if ($updateStmt->execute()) {
                $response['status'] = 'success';
                $response['message'] = 'Password updated successfully.';
            } else {
                $response['status'] = 'error';
                $response['message'] = 'Error updating password. Please try again.';
            }
            $updateStmt->close();
        } else {
            $response['status'] = 'error';
            $response['message'] = 'Current password is incorrect.';
        }
    } else {
        $response['status'] = 'error';
        $response['message'] = 'User not found.';
    }

    $stmt->close();
    $mysqli->close();
    return $response;
}

function updateName($userId, $currentPassword, $newName) {
    $response = array();
    $mysqli = connectDB();

    // Verify current password
    $stmt = $mysqli->prepare("SELECT Password FROM accounts WHERE ID = ?");
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($currentPassword, $user['Password'])) {
            // Update name
            $updateStmt = $mysqli->prepare("UPDATE accounts SET Name = ? WHERE ID = ?");
            $updateStmt->bind_param('si', $newName, $userId);
            
            if ($updateStmt->execute()) {
                $_SESSION['user_name'] = $newName; // Update session
                $response['status'] = 'success';
                $response['message'] = 'Name updated successfully.';
            } else {
                $response['status'] = 'error';
                $response['message'] = 'Error updating name. Please try again.';
            }
            $updateStmt->close();
        } else {
            $response['status'] = 'error';
            $response['message'] = 'Current password is incorrect.';
        }
    } else {
        $response['status'] = 'error';
        $response['message'] = 'User not found.';
    }

    $stmt->close();
    $mysqli->close();
    return $response;
}

function updateEmail($userId, $currentPassword, $newEmail) {
    $response = array();
    $mysqli = connectDB();

    // Verify current password
    $stmt = $mysqli->prepare("SELECT Password FROM accounts WHERE ID = ?");
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($currentPassword, $user['Password'])) {
            // Check if email already exists
            $checkStmt = $mysqli->prepare("SELECT ID FROM accounts WHERE Email = ?");
            $checkStmt->bind_param('s', $newEmail);
            $checkStmt->execute();
            $checkResult = $checkStmt->get_result();
            
            if ($checkResult->num_rows > 0) {
                $response['status'] = 'error';
                $response['message'] = 'Email already in use.';
            } else {
                // Update email
                $updateStmt = $mysqli->prepare("UPDATE accounts SET Email = ? WHERE ID = ?");
                $updateStmt->bind_param('si', $newEmail, $userId);
                
                if ($updateStmt->execute()) {
                    $_SESSION['user_email'] = $newEmail; // Update session
                    $response['status'] = 'success';
                    $response['message'] = 'Email updated successfully.';
                } else {
                    $response['status'] = 'error';
                    $response['message'] = 'Error updating email. Please try again.';
                }
                $updateStmt->close();
            }
            $checkStmt->close();
        } else {
            $response['status'] = 'error';
            $response['message'] = 'Current password is incorrect.';
        }
    } else {
        $response['status'] = 'error';
        $response['message'] = 'User not found.';
    }

    $stmt->close();
    $mysqli->close();
    return $response;
}




function displayProductsBySearch($search, $page = 1, $selectedBrand = '', $minPrice = '', $maxPrice = '') {
    $limit = 20; // Number of products per page
    $offset = ($page - 1) * $limit;

    // Sanitize the search input
    $search = preg_replace('/[^a-z0-9\s-]/i', ' ', $search);
    $search = trim($search); // Remove leading and trailing spaces

    $conn = connectDB();

    // Define a function to perform the search
    function searchProducts($conn, $search, $limit, $offset, $selectedBrand, $minPrice, $maxPrice) {
        $sql = "SELECT p.*, b.Name as BrandName 
                FROM products p 
                JOIN brands b ON p.Brand_ID = b.Brand_ID 
                WHERE (p.Name LIKE ? OR p.Specifications LIKE ?)";
        $params = ["%$search%", "%$search%"];
        $types = "ss";

        if ($selectedBrand) {
            $sql .= " AND b.Name = ?";
            $params[] = $selectedBrand;
            $types .= "s";
        }

        if ($minPrice !== '') {
            $sql .= " AND p.Price >= ?";
            $params[] = $minPrice;
            $types .= "d";
        }

        if ($maxPrice !== '') {
            $sql .= " AND p.Price <= ?";
            $params[] = $maxPrice;
            $types .= "d";
        }

        $sql .= " LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
        $types .= "ii";

        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            printf("Error: %s.\n", $conn->error);
            return [];
        }
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
        $products = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $products;
    }

    // Get total number of products matching the search
    $totalSql = "SELECT COUNT(*) AS total 
                 FROM products p 
                 JOIN brands b ON p.Brand_ID = b.Brand_ID 
                 WHERE (p.Name LIKE ? OR p.Specifications LIKE ?)";
    $totalParams = ["%$search%", "%$search%"];
    $totalTypes = "ss";

    if ($selectedBrand) {
        $totalSql .= " AND b.Name = ?";
        $totalParams[] = $selectedBrand;
        $totalTypes .= "s";
    }

    if ($minPrice !== '') {
        $totalSql .= " AND p.Price >= ?";
        $totalParams[] = $minPrice;
        $totalTypes .= "d";
    }

    if ($maxPrice !== '') {
        $totalSql .= " AND p.Price <= ?";
        $totalParams[] = $maxPrice;
        $totalTypes .= "d";
    }

    $totalStmt = $conn->prepare($totalSql);
    if (!$totalStmt) {
        printf("Error: %s.\n", $conn->error);
        return [];
    }
    $totalStmt->bind_param($totalTypes, ...$totalParams);
    $totalStmt->execute();
    $totalResult = $totalStmt->get_result();
    $totalRow = $totalResult->fetch_assoc();
    $totalProducts = $totalRow['total'];
    $totalStmt->close();

    // Perform the search
    $products = searchProducts($conn, $search, $limit, $offset, $selectedBrand, $minPrice, $maxPrice);

    // Get all unique brands
    $brandsSql = "SELECT Name FROM brands ORDER BY Name";
    $brandsResult = $conn->query($brandsSql);
    $brands = $brandsResult->fetch_all(MYSQLI_ASSOC);

    $conn->close();

    $isLoggedIn = isset($_SESSION['user_id']); // Check if user is logged in

    // Output HTML
    echo '<div class="container mt-4">';
    if ($search) {
        echo '<h2 class="mb-4 mt-3" style="font-size: 1.5rem; font-weight: 600; color: #333;">Search Results for "' . htmlspecialchars($search) . '"</h2>';
    }

    // Filters
    echo '<form action="" method="get" class="mb-4">';
    echo '<input type="hidden" name="search" value="' . htmlspecialchars($search) . '">';
    echo '<div class="row">';
    echo '<div class="col-md-4">';
    echo '<select name="brand" class="form-select">';
    echo '<option value="">All Brands</option>';
    foreach ($brands as $brand) {
        $selected = ($brand['Name'] == $selectedBrand) ? 'selected' : '';
        echo '<option value="' . htmlspecialchars($brand['Name']) . '" ' . $selected . '>' . htmlspecialchars($brand['Name']) . '</option>';
    }
    echo '</select>';
    echo '</div>';
    echo '<div class="col-md-3">';
    echo '<input type="number" name="minPrice" class="form-control" placeholder="Min Price" value="' . htmlspecialchars($minPrice) . '">';
    echo '</div>';
    echo '<div class="col-md-3">';
    echo '<input type="number" name="maxPrice" class="form-control" placeholder="Max Price" value="' . htmlspecialchars($maxPrice) . '">';
    echo '</div>';
    echo '<div class="col-md-2">';
    echo '<button type="submit" class="btn btn-primary">Apply Filters</button>';
    echo '</div>';
    echo '</div>';
    echo '</form>';

    if (!empty($products)) {
        echo '<div class="container custom-container">';
        echo '<div class="row g-4">';
        foreach ($products as $product) {
            $imgSrc = !is_null($product['product_img']) ? 'data:image/jpeg;base64,' . base64_encode($product['product_img']) : 'path/to/default/image.jpg';
            $productId = htmlspecialchars($product['Product_ID']);
            $productName = htmlspecialchars($product["Name"]);
            $productSpecifications = htmlspecialchars($product["Specifications"]);
            $brandName = htmlspecialchars($product["BrandName"]);
            $productPrice = htmlspecialchars($product["Price"]);
            $productQuantity = intval($product["Quantity"]);
            $productDiscount = floatval($product["discount"]);

            echo '<div class="col-lg-3 col-md-4 col-sm-6 mb-4 custom-col">';
            echo '<div class="card h-100" style="max-width: 250px; margin: 0 auto;">';

            // Image with link
            echo '<a href="addtocart.php?id=' . $productId . '">';
            echo '<img src="' . $imgSrc . '" class="card-img-top product-img" alt="Product Image" style="height: 150px; object-fit: cover;">';
            echo '</a>';

            // Product name with link
            echo '<a href="addtocart.php?id=' . $productId . '">';
            echo '<div class="card-body d-flex flex-column">';
            echo '<h5 class="card-title" style="font-size: 0.9rem;">' . $productName . '</h5>';
            echo '</a>';

            // Hidden fields for brand and specifications
            echo '<h5 class="card-title" style="font-size: 0.9rem;">$' . $productPrice . '</h5>';
            echo '<input type="hidden" name="brandName" value="' . $brandName . '">';
            echo '<input type="hidden" name="productSpecifications" value="' . $productSpecifications . '">';

            // Display "On Sale" badge if discount > 0
            if ($productDiscount > 0) {
                echo '<span class="badge bg-success mb-2">On Sale ' . $product['discount'] . '%</span>';
            }

            // Display "Out of Stock" badge if quantity == 0
            if ($productQuantity == 0) {
                echo '<span class="badge bg-danger mb-2">Out of Stock</span>';
            } elseif ($isLoggedIn) {
                // Logged-in user can add to cart
                echo '<form action="" method="post" class="mt-auto">';
                echo '<input type="hidden" name="searchTag" value="' . $search . '">';
                echo '<input type="hidden" name="productId" value="' . $productId . '">';
                echo '<button type="submit" class="btn btn-primary btn-sm" style="width:100%;" name="addtocartoutside" value="addtocartoutside">Add to Cart</button>';
                echo '</form>';
            } else {
                // Not logged-in user
                $currentPage = urlencode($_SERVER['REQUEST_URI']);
                $loginUrl = 'login.php?return=' . urlencode($currentPage);
                echo '<a href="'.$loginUrl.'" class="btn btn-primary btn-sm mt-auto" style="width:100%;">Login to Add to Cart</a>';
            }

            echo '</div>';
            echo '</div>';
            echo '</div>';
        }
        echo '</div>';
        echo '</div>';
    } else {
        echo '<p>No products found matching your search.</p>';
    }

    // Pagination
    $totalPages = ceil($totalProducts / $limit);

    if ($totalPages > 1) {
        echo '<div class="pagination mt-4 d-flex justify-content-center">';
        echo '<ul class="pagination pagination-rounded">';
        for ($i = 1; $i <= $totalPages; $i++) {
            $pageUrl = "items.php?search=" . urlencode($search) . "&page=" . $i;
            if ($selectedBrand) $pageUrl .= "&brand=" . urlencode($selectedBrand);
            if ($minPrice !== '') $pageUrl .= "&minPrice=" . urlencode($minPrice);
            if ($maxPrice !== '') $pageUrl .= "&maxPrice=" . urlencode($maxPrice);
            
            if ($i == $page) {
                echo '<li class="page-item active"><span class="page-link">' . $i . '</span></li>';
            } else {
                echo '<li class="page-item"><a href="' . $pageUrl . '" class="page-link">' . $i . '</a></li>';
            }
        }
        echo '</ul>';
        echo '</div>';
    }

    echo '</div>';
}





function fetchAll($query) {
    $conn = connectDB();
    $result = $conn->query($query);
    return $result->fetch_all(MYSQLI_ASSOC);
}


function displayLatestProducts() {
    $db = connectDB();
    
    $query = "SELECT Product_ID, Name, product_img, Price, Quantity, discount FROM products ORDER BY date_added DESC LIMIT 5";
    $stmt = $db->prepare($query);
    $stmt->execute();
    
    $result = $stmt->get_result();
    $products = $result->fetch_all(MYSQLI_ASSOC);
    if (count($products) === 0) {
        return ''; 
    }

    $isLoggedIn = isset($_SESSION['user_id']);

    $output = '<style>
        .product-card {
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        .product-card .card-body {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            padding: 1rem; /* Adjust as needed */
        }
        .product-card .card-img-top {
            height: 200px; /* Adjust this value as needed */
            object-fit: contain;
        }
        .form-add-to-cart {
            margin-top: auto;
            width: 100%;
        }
        .form-add-to-cart button {
            width: 100%;
            margin-top: 1rem; /* Space between price and button */
        }
    </style>';
    
    $output .= '<div class="container mt-4">';
    $output .= '<h2 class="mb-3">Latest Products</h2>';
    $output .= '<div class="row row-cols-1 row-cols-md-5 g-4">';
    
    foreach ($products as $product) {
        $output .= '<div class="col">';
        $output .= '<div class="card product-card">';
        $imgData = base64_encode($product['product_img']);
        $output .= '<a href="addtocart.php?id=' . $product['Product_ID'] . '">';
        $output .= '<img src="data:image/jpeg;base64,' . $imgData . '" class="card-img-top" alt="' . htmlspecialchars($product['Name']) . '">';
        $output .= '</a>';
        $output .= '<div class="card-body">';
        $output .= '<h5 class="card-title">' . htmlspecialchars($product['Name']) . '</h5>';
        $output .= '<p class="card-text">$' . number_format($product['Price'], 2) . '</p>';
        
        if ($product['discount'] > 0) {
            $output .= '<span class="badge bg-success mb-2">On Sale ' . $product['discount'] . '%</span>';
        }
        
        if ($product['Quantity'] == 0) {
            $output .= '<span class="badge bg-danger mb-2">Out of Stock</span>';
        } elseif ($isLoggedIn) {
            $output .= '<form method="post" action="" class="form-add-to-cart">';
            $output .= '<button class="btn btn-primary btn-sm" type="submit">Add to cart</button>';
            $output .= '<input type="hidden" name="addtocartoutside" value="addtocartoutside">';
            $output .= '<input type="hidden" name="productId" value="'.$product['Product_ID'].'">';
            $output .= '</form>';
        } else {
            $currentPage = urlencode($_SERVER['REQUEST_URI']);
            $loginUrl = 'login.php?return=' . urlencode($currentPage);
            $output .= '<a href="'.$loginUrl.'" class="btn btn-primary btn-sm mt-auto" style="width:100%;">Login to Add to Cart</a>';
        }
        
        $output .= '</div></div></div>';
    }
    
    $output .= '</div></div>';
    
    $stmt->close();
    return $output;
}





function displayFeaturedSubCategories() {
    $db = connectDB();
    
    $query = "SELECT sc.Sub_ID, sc.Name AS SubCategoryName, p.Product_ID, p.Name AS ProductName, p.product_img, p.Price, p.Quantity, p.discount 
              FROM sub_categories sc
              JOIN products p ON sc.Sub_ID = p.Sub_ID
              WHERE sc.selected = 1
              ORDER BY sc.Sub_ID, p.date_added DESC";
    $stmt = $db->prepare($query);
    $stmt->execute();
    
    $result = $stmt->get_result();
    $products = $result->fetch_all(MYSQLI_ASSOC);
    if (count($products) === 0) {
        return ''; 
    }

    $isLoggedIn = isset($_SESSION['user_id']);

    $output = '<style>
        .product-card {
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        .product-card .card-body {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            padding: 1rem; /* Adjust as needed */
        }
        .product-card .card-img-top {
            height: 200px;
            object-fit: contain;
        }
        .product-card .form-add-to-cart {
            margin-top: auto;
            width: 100%;
        }
        .product-card .form-add-to-cart button {
            width: 100%;
            margin-top: 1rem; /* Space between price and button */
        }
        .slick-prev:before, .slick-next:before {
            color: #000;
        }
    </style>';

    $output .= '<div class="container mt-4">';
    $currentSubID = null;

    foreach ($products as $product) {
        if ($product['Sub_ID'] !== $currentSubID) {
            if ($currentSubID !== null) {
                $output .= '</div></div>';
            }

            $output .= '<div class="subcategory-section mt-4">';
            $output .= '<h2 class="mb-3"><a href="items.php?sub_id=' . $product['Sub_ID'] . '">' . htmlspecialchars($product['SubCategoryName']) . '</a></h2>';
            $output .= '<div class="featured-products-slider">';
            
            $currentSubID = $product['Sub_ID'];
        }

        $output .= '<div class="px-2">';
        $output .= '<div class="card product-card">';
        $imgData = base64_encode($product['product_img']);
        $output .= '<a href="addtocart.php?id=' . $product['Product_ID'] . '">';
        $output .= '<img src="data:image/jpeg;base64,' . $imgData . '" class="card-img-top" alt="' . htmlspecialchars($product['ProductName']) . '">';
        $output .= '</a>';
        $output .= '<div class="card-body">';
        $output .= '<p class="card-text">' . htmlspecialchars($product['ProductName']) . '</p>';
        $output .= '<p class="card-text">$' . number_format($product['Price'], 2) . '</p>';
        
        if ($product['discount'] > 0) {
            $output .= '<span class="badge bg-success mb-2">On Sale ' . $product['discount'] . '%</span>';
        }
        
        if ($product['Quantity'] == 0) {
            $output .='<span class="badge bg-danger mb-2">Out of Stock</span>';
        } elseif ($isLoggedIn) {
            $output .= '<form method="post" action="" class="form-add-to-cart">';
            $output .= '<button class="btn btn-primary btn-sm" type="submit">Add to cart</button>';
            $output .= '<input type="hidden" name="addtocartoutside" value="addtocartoutside">';
            $output .= '<input type="hidden" name="productId" value="'.$product['Product_ID'].'">';
            $output .= '</form>';
        } else {
            $currentPage = urlencode($_SERVER['REQUEST_URI']);
            $loginUrl = 'login.php?return=' . urlencode($currentPage);
            $output .= '<a href="'.$loginUrl.'" class="btn btn-primary btn-sm mt-auto" style="width:100%;">Login to Add to Cart</a>';
        }
        
        $output .= '</div></div></div>';
    }

    if ($currentSubID !== null) {
        $output .= '</div></div>';
    }

    $output .= '</div>';
    
    $output .= '<script>
        $(document).ready(function(){
            $(".featured-products-slider").slick({
                dots: true,
                infinite: true,
                speed: 300,
                slidesToShow: 5,
                slidesToScroll: 1,
                autoplay: true,
                autoplaySpeed: 3000,
                responsive: [
                    {
                        breakpoint: 1024,
                        settings: {
                            slidesToShow: 3,
                            slidesToScroll: 1
                        }
                    },
                    {
                        breakpoint: 600,
                        settings: {
                            slidesToShow: 2,
                            slidesToScroll: 1
                        }
                    },
                    {
                        breakpoint: 480,
                        settings: {
                            slidesToShow: 1,
                            slidesToScroll: 1
                        }
                    }
                ]
            });
        });
    </script>';
    
    $stmt->close();
    return $output;
}



function displaySelectedProducts() {
    $db = connectDB();
    
    $query = "SELECT p.Product_ID, p.Name AS ProductName, p.product_img, p.Price, sc.Name AS SubCategoryName, p.Quantity, p.discount
              FROM products p
              JOIN sub_categories sc ON p.Sub_ID = sc.Sub_ID
              WHERE p.selected = 1
              ORDER BY p.date_added DESC";
    $stmt = $db->prepare($query);
    $stmt->execute();
    
    $result = $stmt->get_result();
    $products = $result->fetch_all(MYSQLI_ASSOC);

    if (count($products) === 0) {
        return ''; 
    }
    
    $isLoggedIn = isset($_SESSION['user_id']);
    
    $output = '<style>
        .product-card {
            height: 100%;
            display: flex;
            flex-direction: column;
            border: 1px solid #ddd;
            border-radius: 4px;
            overflow: hidden;
            margin-bottom: 20px;
        }
        .product-card .card-body {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            padding: 15px;
        }
        .product-card .card-img-top {
            height: 200px;
            object-fit: contain;
            width: 100%;
        }
        .product-card .form-add-to-cart {
            margin-top: auto;
            width: 100%;
        }
        .product-card .form-add-to-cart button {
            width: 100%;
            margin-top: 1rem; /* Space between price and button */
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        @media (min-width: 1200px) {
            .product-card {
                margin-bottom: 30px;
            }
        }
    </style>';

    $output .= '<div class="container mt-4">';
    $output .= '<h2 class="mb-4">Featured Products</h2>';
    $output .= '<div class="row">'; 

    foreach ($products as $product) {
        $output .= '<div class="col-lg-3 col-md-4 col-sm-6 mb-4">';
        $output .= '<div class="product-card">';
        $imgData = base64_encode($product['product_img']);
        $output .= '<a href="addtocart.php?id=' . $product['Product_ID'] . '">';
        $output .= '<img src="data:image/jpeg;base64,' . $imgData . '" class="card-img-top" alt="' . htmlspecialchars($product['ProductName']) . '">';
        $output .= '</a>';
        $output .= '<div class="card-body">';
        $output .= '<h5 class="card-title">' . htmlspecialchars($product['ProductName']) . '</h5>';
        $output .= '<p class="card-text">$' . number_format($product['Price'], 2) . '</p>';
        
        if ($product['discount'] > 0) {
            $output .= '<span class="badge bg-success mb-2">On Sale ' . $product['discount'] . '%</span>';
        }
        
        if ($product['Quantity'] == 0) {
            $output .= '<span class="badge bg-danger mb-2">Out of Stock</span>';
        } elseif ($isLoggedIn) {
            $output .= '<form method="post" action="" class="form-add-to-cart">';
            $output .= '<button class="btn btn-primary btn-sm" type="submit">Add to cart</button>';
            $output .= '<input type="hidden" name="addtocartoutside" value="addtocartoutside">';
            $output .= '<input type="hidden" name="productId" value="'.$product['Product_ID'].'">';
            $output .= '</form>';
        } else {
            $currentPage = urlencode($_SERVER['REQUEST_URI']);
            $loginUrl = 'login.php?return=' . urlencode($currentPage);
            $output .= '<a href="'.$loginUrl.'" class="btn btn-primary btn-sm mt-auto" style="width:100%;">Login to Add to Cart</a>';
        }
        
        $output .= '</div></div></div>';
    }

    $output .= '</div>'; 
    $output .= '</div>'; 

    $stmt->close();
    return $output;
}





function displayMostSoldProducts() {
    $db = connectDB();
    
    $query = "SELECT oi.Product_ID, p.Name AS ProductName, p.product_img,
                      SUM(oi.quantity) AS TotalQuantity, p.Price, p.Quantity, p.discount
              FROM orderinfo oi
              JOIN products p ON oi.Product_ID = p.Product_ID
              JOIN orders o ON oi.order_id = o.order_id
              WHERE o.status = 'completed'
              GROUP BY oi.Product_ID
              ORDER BY TotalQuantity DESC
              LIMIT 4";
    $stmt = $db->prepare($query);
    $stmt->execute();
    
    $result = $stmt->get_result();
    $products = $result->fetch_all(MYSQLI_ASSOC);
    
    $isLoggedIn = isset($_SESSION['user_id']);
    
    $output = '<style>
        .product-card {
            height: 90%;
            display: flex;
            flex-direction: column;
        }
        .product-card .card-body {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            padding: 1rem;
        }
        .product-card .card-img-top {
            height: 200px;
            object-fit: contain;
        }
        .product-card .form-add-to-cart {
            margin-top: auto;
            width: 100%;
        }
        .product-card .form-add-to-cart button {
            width: 100%;
            margin-top: 1rem;
        }
    </style>';
    
    $output .= '<div class="container mt-4">';
    $output .= '<h2 class="mb-3">Most Sold Products</h2>';
    $output .= '<div class="row row-cols-1 row-cols-md-4 g-4">';
    
    foreach ($products as $product) {
        $output .= '<div class="col">';
        $output .= '<div class="card product-card">';
        $imgData = base64_encode($product['product_img']);
        $output .= '<a href="addtocart.php?id=' . $product['Product_ID'] . '">';
        $output .= '<img src="data:image/jpeg;base64,' . $imgData . '" class="card-img-top" alt="' . htmlspecialchars($product['ProductName']) . '">';
        $output .= '</a>';
        $output .= '<div class="card-body">';
        $output .= '<h5 class="card-title">' . htmlspecialchars($product['ProductName']) . '</h5>';
        $output .= '<p class="card-text">$' . number_format($product['Price'], 2) . '</p>';
        
        if ($product['discount'] > 0) {
            $output .= '<span class="badge bg-success mb-2">On Sale ' . $product['discount'] . '%</span>';
        }
        
        if ($product['Quantity'] == 0) {
            $output .= '<span class="badge bg-danger mb-2">Out of Stock</span>';
        } elseif ($isLoggedIn) {
            $output .= '<form method="post" action="" class="form-add-to-cart">';
            $output .= '<button class="btn btn-primary btn-sm" type="submit">Add to cart</button>';
            $output .= '<input type="hidden" name="addtocartoutside" value="addtocartoutside">';
            $output .= '<input type="hidden" name="productId" value="'.$product['Product_ID'].'">';
            $output .= '</form>';
        } else {
            $currentPage = urlencode($_SERVER['REQUEST_URI']);
            $loginUrl = 'login.php?return=' . urlencode($currentPage);
            $output .= '<a href="'.$loginUrl.'" class="btn btn-primary btn-sm mt-auto" style="width:100%;">Login to Add to Cart</a>';
        }
        
        $output .= '</div></div></div>';
    }
    
    $output .= '</div></div>';
    
    $stmt->close();
    return $output;
}





function getReviews() {
    $connection = connectDB();
    $query = "
                SELECT r.title, r.review_text, r.star_rating, a.Name
                FROM reviews r
                JOIN accounts a ON r.ID = a.ID
                WHERE r.star_rating IS NOT NULL
                ORDER BY r.star_rating DESC, r.created_at DESC
                LIMIT 3
            ";


    
    $result = mysqli_query($connection, $query);
    
    $output = '<div class="container"><div class="row">';
    
    while ($row = mysqli_fetch_assoc($result)) {
        $output .= '
            <div class="col-12 col-md-4 review-item">
                <div class="profile-icon">
                
                    <i class="fas fa-user-circle fa-3x"></i>
                    
                </div>
                <h3>' . htmlspecialchars($row['title']) . '</h3>
                <p>' . htmlspecialchars($row['review_text']) . '</p><div class="user-name">' . htmlspecialchars($row['Name']) . '</div>
                <div class="star-rating">Rating: ' . htmlspecialchars($row['star_rating']) . ' / 5</div>
                
            </div>
        ';
    }
    
    $output .= '</div></div>';
    
    $output .= '
        <style>
            .review-item {
                text-align: center;
                padding: 20px;
                border: 1px solid #ddd;
                border-radius: 10px;
                margin-bottom: 20px;
            }
            .profile-icon {
                margin-bottom: 15px;
            }
            .user-name {
                margin-top: 5px;
                font-weight: bold;
            }
            .star-rating {
                margin-top: 10px;
                font-weight: bold;
            }
            @media (min-width: 768px) {
                .review-item {
                    margin-bottom: 0;
                }
            }
        </style>
    ';
    
    return $output;
}


function displayAllProducts() {
    $conn = connectDB();
    $sql = "SELECT * FROM products";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $isLoggedIn = isset($_SESSION['user_id']);
    
    if ($result->num_rows > 0) {
        echo '<div class="container custom-container">';
        echo '<div class="row g-4">';
        while ($row = $result->fetch_assoc()) {
            if (!is_null($row['product_img'])) {
                $imgData = base64_encode($row['product_img']);
                $imgSrc = 'data:image/jpeg;base64,' . $imgData;
            } else {
                $imgSrc = 'path/to/default/image.jpg';
            }
            
            echo '<div class="col-lg-3 col-md-4 col-sm-6 mb-4 custom-col">';
            echo '<div class="card h-100" style="max-width: 250px; margin: 0 auto;">';
            echo '<a href="addtocart.php?id='.$row['Product_ID'].'">';
            echo '<img src="'.$imgSrc.'" class="card-img-top product-img" alt="Product Image" style="height: 150px; object-fit: cover;">';
            echo '</a>';
            echo '<div class="card-body d-flex flex-column">';
            echo '<a href="addtocart.php?id='.$row['Product_ID'].'" style="text-decoration:none; font-size: 0.9rem;">' . htmlspecialchars($row['Name']) . '</a>';
            echo '<p class="card-text" style="font-size: 0.8rem;">$' . number_format($row['Price'], 2) . '</p>';
            
            // Check for "On Sale" and display it
            if ($row['discount'] > 0) {
                echo '<span class="badge bg-success mb-2">On Sale '.$row['discount'].'%</span>';
            }
            
            // Check for "Out of Stock" and display it
            if ($row['Quantity'] == 0) {
                echo '<span class="badge bg-danger mb-2">Out of Stock</span>';
            } else if ($isLoggedIn) {
                echo '<form method="post" action="">';
                echo '<input type="submit" class="btn btn-primary btn-sm mt-auto" name="addtocartoutside" value="Add to Cart" style="width:100%;">';
                echo '<input type="hidden" value="'.$row['Product_ID'].'" name="productId">';
                echo '</form>';
            } else {
                $currentPage = urlencode($_SERVER['REQUEST_URI']);
                $loginUrl = 'login.php?return=' . urlencode($currentPage);
                echo '<a href="'.$loginUrl.'" class="btn btn-primary btn-sm mt-auto" style="width:100%;">Login to Add to Cart</a>';
            }
            
            echo '</div>';
            echo '</div>';
            echo '</div>';
        }
        echo '</div>';
        echo '</div>';
    } else {
        echo '<p>No products available.</p>';
    }
    
    $stmt->close();
    $conn->close();
}
























    
    
    
    
    


    
    

    
    
    
    
    



    
    
    
    
    










?>

