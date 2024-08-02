$(document).ready(function() {
    $("#product-search-form").on("submit", function(e) {
        e.preventDefault();
        var searchTerm = $("#product-search-input").val();
        updateProductTable(searchTerm);
    });

    function updateProductTable(searchTerm) {
        $.ajax({
            url: "handler2.php",
            method: "GET",
            data: { action: "search_products", search: searchTerm },
            success: function(response) {
                $("#product-table-container").html(response);
            },
            error: function(xhr, status, error) {
                console.error("Error occurred: " + error);
            }
        });
    }

    $(document).on("click", ".toggle-homepage", function() {
        var $button = $(this);
        var productId = $button.data("product-id");
        var action = $button.data("action");

        $.ajax({
            url: "handler2.php",
            method: "POST",
            data: {
                action: "toggle_homepage",
                productId: productId,
                toggleAction: action
            },
            success: function(response) {

                var newAction = action === 'Add to homepage' ? 'Remove from homepage' : 'Add to homepage';
                var newButtonClass = action === 'Add to homepage' ? 'btn-outline-danger' : 'btn-outline-success';
                var newIcon = action === 'Add to homepage' ? 'minus' : 'plus';

                $button.data('action', newAction);
                $button.removeClass('btn-outline-danger btn-outline-success').addClass(newButtonClass);
                $button.html('<i class="fas fa-' + newIcon + '-circle me-1"></i>' + newAction);


                if ($("#product-search-input").length) {
                    updateProductTable($("#product-search-input").val());
                }
            },
            error: function(xhr, status, error) {
                console.error("Error occurred: " + error);
            }
        });
    });
});