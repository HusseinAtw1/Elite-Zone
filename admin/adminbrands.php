<?php
include 'adminheader.php';
include '../classes/brands.php';

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_brand']) || isset($_POST['update_brand'])) {
        $name = trim($_POST['name']);
        $description = trim($_POST['description']);
        
        $uploadedImages = [];
        if (!empty($_FILES['images']['name'][0])) {
            $uploadDir = 'C:\\xampp\\htdocs\\elite-zone\\images\\';
            
            // Create directory if it doesn't exist
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
                $fileName = $_FILES['images']['name'][$key];
                $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
                
                // Check file type (you may want to adjust allowed types)
                $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
                if (!in_array(strtolower($fileExtension), $allowedTypes)) {
                    $error = "Invalid file type. Allowed types are: " . implode(', ', $allowedTypes);
                    continue; // Skip this file
                }
                
                $relativeFilePath = 'images/' . $fileName;
                $absoluteFilePath = $uploadDir . $fileName;
                
                // Move the uploaded file
                if (move_uploaded_file($tmp_name, $absoluteFilePath)) {
                    $uploadedImages[] = $relativeFilePath;
                } else {
                    $error = "Failed to upload file: " . $fileName;
                }
            }
        }
        
        if (isset($_POST['add_brand'])) {
            $imagesJson = json_encode($uploadedImages);
            $brand = new Brand(null, $name, $description, $imagesJson);
        } else {
            $brand = Brand::getBrandById($_POST['brand_id']);
            $brand->setName($name);
            $brand->setDescription($description);
            
            // Replace existing images with new ones
            if (!empty($uploadedImages)) {
                $brand->setImages(json_encode($uploadedImages));
            }
        }
        
        if ($brand->save()) {
            $message = isset($_POST['add_brand']) ? "Brand added successfully." : "Brand updated successfully.";
        } else {
            $error = "Failed to save brand.";
        }
    } elseif (isset($_POST['delete_brand'])) {
        $brand = Brand::getBrandById($_POST['brand_id']);
        if ($brand && $brand->delete()) {
            $message = "Brand deleted successfully.";
        } else {
            $error = "Failed to delete brand.";
        }
    }
}

$brands = Brand::getAllBrands();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Brands</title>

</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">Manage Brands</h1>
        
        <?php if (!empty($message)): ?>
            <div class="alert alert-success"><?php echo $message; ?></div>
        <?php endif; ?>
        
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <!-- Add/Edit Brand Form -->

        <form method="POST" enctype="multipart/form-data" class="mb-4" id="brandForm">
    <input type="hidden" name="brand_id" id="brand_id">
    <div class="form-group">
        <label for="name">Brand Name:</label>
        <input type="text" class="form-control" id="name" name="name" required>
    </div>
    <div class="form-group">
        <label for="description">Description:</label>
        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
    </div>
    <div class="form-group">
        <label for="images">Images:</label>
        <input type="file" class="form-control-file" id="images" name="images[]" multiple>
    </div>
    <button type="submit" class="btn btn-primary" name="add_brand" id="submitBtn">Add Brand</button>
    <button type="button" class="btn btn-secondary" id="cancelBtn" style="display: none;">Cancel</button>
</form>



                <!-- Brands Table -->
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Images</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($brands as $brand): ?>
                    <tr>
                        <td><?php echo $brand->getId(); ?></td>
                        <td><?php echo htmlspecialchars($brand->getName()); ?></td>
                        <td><?php echo htmlspecialchars($brand->getDescription()); ?></td>
                        <td>
                            <?php
                            $images = json_decode($brand->getImages(), true);
                            if (!empty($images)) {
                                foreach ($images as $image) {
                                    // Ensure the image path is correct
                                    $imagePath = '../' . $image; // Add '../' to go up one directory level
                                    echo '<img src="' . htmlspecialchars($imagePath) . '" alt="Brand Image" style="width: 50px; height: 50px; margin-right: 5px;">';
                                }
                            }
                            ?>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-primary edit-brand" data-id="<?php echo $brand->getId(); ?>" data-name="<?php echo htmlspecialchars($brand->getName()); ?>" data-description="<?php echo htmlspecialchars($brand->getDescription()); ?>">Edit</button>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="brand_id" value="<?php echo $brand->getId(); ?>">
                                <button type="submit" class="btn btn-sm btn-danger" name="delete_brand" onclick="return confirm('Are you sure you want to delete this brand?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>


    <script src="../scripts/adminbrands.js"></script>
</body>
</html>