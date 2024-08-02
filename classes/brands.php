<?php

class Brand {
    private $id;
    private $name;
    private $description;
    private $images; 

    public function __construct($id = null, $name = '', $description = '', $images = '') {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->images = $images;
    }

    public static function getAllBrands() {
        $conn = connectDB();
        $stmt = $conn->prepare("SELECT * FROM brands WHERE Brand_ID != 21 ORDER BY Name");
        $stmt->execute();
        $result = $stmt->get_result();
        $brands = [];
        while ($row = $result->fetch_assoc()) {
            $brands[] = new Brand($row['Brand_ID'], $row['Name'], $row['Description'], $row['Image']);
        }
        $stmt->close();
        return $brands;
    }
    

    public static function getBrandById($id) {
        $conn = connectDB();
        $stmt = $conn->prepare("SELECT * FROM brands WHERE Brand_ID = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            return new Brand($row['Brand_ID'], $row['Name'], $row['Description'], $row['Image']);
        }
        $stmt->close();
        return null;
    }

    public function delete() {
        $conn = connectDB();
        $stmt = $conn->prepare("DELETE FROM brands WHERE Brand_ID = ?");
        $stmt->bind_param("i", $this->id);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    public function save() {
        $conn = connectDB();
        if ($this->id) {
            $stmt = $conn->prepare("UPDATE brands SET Name = ?, Description = ?, Image = ? WHERE Brand_ID = ?");
            $stmt->bind_param("sssi", $this->name, $this->description, $this->images, $this->id);
        } else {
            $stmt = $conn->prepare("INSERT INTO brands (Name, Description, Image) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $this->name, $this->description, $this->images);
        }
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    // Add getter and setter for images
    public function getImages() { return $this->images; }
    public function setImages($images) { $this->images = $images; }

    // Getters and setters
    public function getId() { return $this->id; }
    public function getName() { return $this->name; }
    public function getDescription() { return $this->description; }
    public function setName($name) { $this->name = $name; }
    public function setDescription($description) { $this->description = $description; }

    public static function displayBrands() {
        $brands = Brand::getAllBrands();
        $siteBaseUrl = 'http://localhost/elite-zone/';
    
        $output = '<div class="brand-slider-container">';
        $output .= '<div class="brand-slider">';
        foreach ($brands as $brand) {
            $output .= '<div class="brand-slide">';
            $images = json_decode($brand->getImages(), true);
            
            if (!empty($images)) {
                $imagePath = $siteBaseUrl . $images[0];
                $brandUrl = 'items.php?brand=' . urlencode($brand->getId());
                $output .= '<a href="' . htmlspecialchars($brandUrl) . '">';
                $output .= '<div class="brand-logo-container">';
                $output .= '<img src="' . htmlspecialchars($imagePath) . '" alt="' . htmlspecialchars($brand->getName()) . '" class="brand-logo">';
                $output .= '</div>';
                $output .= '</a>';
            }
            $output .= '</div>';
        }
        $output .= '</div>';
        $output .= '</div>';
        return $output;
    }
    
    
}


?>
