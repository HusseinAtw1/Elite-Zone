$(document).ready(function() {
    // Load categories on page load
    loadCategories();

    // Search functionality
    $("#search_category").on("keyup", function() {
        loadCategories($(this).val());
    });

    // Edit category
    $(document).on("click", ".edit-category", function() {
        let categoryId = $(this).data("id");
        let currentName = $(this).closest("tr").find("td:nth-child(2)").text();
        let newName = prompt("Enter new name for category:", currentName);
        if (newName !== null && newName !== "") {
            $.ajax({
                url: "adminhandle.php",
                method: "POST",
                data: { id: categoryId, name: newName },
                success: function(response) {
                    loadCategories();
                }
            });
        }
    });

    // Remove category
    $(document).on("click", ".remove-category", function() {
        let categoryId = $(this).data("id");
        if (confirm("Are you sure you want to remove this category?")) {
            $.ajax({
                url: "adminhandle.php",
                method: "POST",
                data: { id: categoryId },
                success: function(response) {
                    console.log(response)
                    loadCategories();
                }
            });
        }
    });

    function loadCategories(search = "") {
        $.ajax({
            url: "adminhandle.php",
            method: "GET",
            data: { searchCategory: search },
            success: function(response) {
                $("#category_table").html(response);
            }
        });
    }
});


$(document).ready(function() {
    // Load categories and subcategories on page load
    loadCategories();
    loadSubcategories();

    // Search functionality
    $("#search_category").on("keyup", function() {
        loadCategories($(this).val());
    });

    $("#search_subcategory").on("keyup", function() {
        loadSubcategories($(this).val());
    });





    $(document).on("click", ".edit-subcategory", function() {
        let subcategoryId = $(this).data("id");
        let currentName = $(this).closest("tr").find("td:nth-child(2)").text();
        let categoryId = $(this).data("category-id");
    
        let newName = prompt("Enter new name for subcategory:", currentName);
    
        // Check if the user pressed cancel or entered an empty string
        if (newName === null || newName.trim() === "") {
            
            return; // Exit the function without making the AJAX call
        }
    
        // If we get here, we have a valid new name, so proceed with the update
        $.ajax({
            url: "adminhandle.php",
            method: "POST",
            data: {
                id: subcategoryId,
                name: newName,
                categoryId: categoryId
            },
            success: function(response) {
                loadSubcategories();
            },
            error: function(xhr, status, error) {
                console.error("Error updating subcategory:", error);
                alert("Failed to update subcategory. Please try again.");
            }
        });
    });

    // Remove subcategory
    $(document).on("click", ".remove-subcategory", function() {
        let subcategoryId = $(this).data("id");
        if (confirm("Are you sure you want to remove this subcategory?")) {
            $.ajax({
                url: "adminhandle.php",
                method: "POST",
                data: { removeSubcategory: subcategoryId },
                success: function(response) {
                    console.log(response)
                    loadSubcategories();
                }
            });
        }
    });

    function loadCategories(search = "") {
        $.ajax({
            url: "adminhandle.php",
            method: "GET",
            data: { searchCategory: search },
            success: function(response) {
                $("#category_table").html(response);
            }
        });
    }

    function loadSubcategories(search = "") {
        $.ajax({
            url: "adminhandle.php",
            method: "GET",
            data: { searchSubcategory: search },
            success: function(response) {
                $("#subcategory_table").html(response);
            }
        });
    }
});
