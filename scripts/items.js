$(document).ready(function() {
    $(document).on('click', '#load-more', function() {
        var button = $(this);
        var page = button.data('page');
        var search = button.data('search');
        $.ajax({
            url: 'items.php',
            type: 'POST',
            data: { search: search, page: page },
            success: function(response) {
                $('#products').append(response);
                if (response.indexOf('Load More') === -1) {
                    button.remove(); // Remove button if no more products
                } else {
                    button.data('page', page + 1); // Update page number
                }
            }
        });
    });
});