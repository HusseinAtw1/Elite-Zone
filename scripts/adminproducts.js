$(document).ready(function() {
    var page = 1;

    function loadProducts(searchQuery = '') {
        $.ajax({
            url: 'adminhandle.php',
            method: 'GET',
            data: {page: page, search: searchQuery},
            success: function(data) {
                if (page === 1) {
                    $('#product-list').html(data);
                } else {
                    $('#product-list').append(data);
                }
            }
        });
    }

    loadProducts();

    $('#search').on('keyup', function() {
        page = 1;
        loadProducts($(this).val());
    });

    $('#load-more').on('click', function() {
        page++;
        loadProducts($('#search').val());
    });
});