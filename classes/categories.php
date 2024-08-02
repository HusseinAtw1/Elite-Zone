<?php

class Category {
    private $categoryId;
    private $categoryName;

    public function __constructor($categoryId,$categoryName){
        $this->categoryId = $categoryId;
        $this->categoryName = $categoryName;
    }


    public static function categoryFormsAdmin() {
        echo '
        <div class="container mt-5">
            <form method="POST" action="">
                <div class="form-group">
                    <label for="category_name">Category Name:</label>
                    <input type="text" class="form-control" id="category_name" name="category_name" required>
                </div>
                <div class="form-group">
                    <input type="submit" class="btn btn-primary" value="Add category" name="add-category">
                </div>
            </form>
    
            <div class="form-group mt-4">
                <input type="text" class="form-control" id="search_category" placeholder="Search categories...">
            </div>
    
            <div id="category_table">
                <!-- Table content will be loaded here -->
            </div>
        </div>';
    
        

    }
    

    public static function addCategory($categoryName) {
        // Establish database connection
        $conn = connectDB();
        if (!$conn) {
            die('Connection Failed: ' . mysqli_connect_error());
        }
    
        // Prepare and bind the SQL statement
        $stmt = $conn->prepare("INSERT INTO Categories (name) VALUES (?)");
        if (!$stmt) {
            die('Prepare Failed: ' . $conn->error);
        }
        $stmt->bind_param("s", $categoryName);
    
        // Execute the statement
        if ($stmt->execute()) {
            $_SESSION['flash_message'] = "Category added successfully!";
            // Get the ID of the inserted category
            $categoryId = $stmt->insert_id;
        } else {
            $_SESSION['flash_message'] = "Something went Wrong!";
            die('Execute Failed: ' . $stmt->error);
        }
    
        // Close the statement and connection
        $stmt->close();
        $conn->close();
    
        // Return the category ID
        return $categoryId;
    }
    

    public static function removeCategory($id) {    
        $conn = connectDB();
        if ($conn === false) {
            echo 'Database connection failed.';
            return;
        }
        $stmt = $conn->prepare("DELETE FROM Categories WHERE Category_ID = ?");
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

    public static function updateCategory($id, $name) {
        $conn = connectDB();
        $stmt = $conn->prepare("UPDATE Categories SET Name = ? WHERE Category_ID = ?");
        if ($stmt === false) {
            echo 'Statement preparation failed.';
            $conn->close();
            return;
        }
        $stmt->bind_param('si', $name, $id); 
        $stmt->execute();
        $stmt->close();
        $conn->close();
    }

    public static function getCategoriesTable($search = '') {
        $conn = connectDB();
    
        $stmt = $conn->prepare("SELECT * FROM Categories WHERE Name LIKE ?");
        $searchParam = '%' . $search . '%';
        $stmt->bind_param('s', $searchParam);
        $stmt->execute();
        $result = $stmt->get_result();
    
        $html = '<table class="table mt-3">
                 <thead class="thead-dark">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Actions</th>
                    </tr>
                 </thead>
                 <tbody>';
    
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $html .= '<tr>
                            <td>' . htmlspecialchars($row["Category_ID"]) . '</td>
                            <td>' . htmlspecialchars($row["Name"]) . '</td>
                            <td>
                                <button class="btn btn-warning btn-sm edit-category" data-id="' . htmlspecialchars($row["Category_ID"]) . '">Edit</button>
                                <button class="btn btn-danger btn-sm remove-category" data-id="' . htmlspecialchars($row["Category_ID"]) . '">Remove</button>
                            </td>
                          </tr>';
            }
        } else {
            $html .= '<tr><td colspan="3">No categories found.</td></tr>';
        }
    
        $html .= '</tbody>
                 </table>';
    
        $stmt->close();
        $conn->close();
    
        return $html;
    }

    
    
    


    
}

?>