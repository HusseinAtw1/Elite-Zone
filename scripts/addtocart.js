function subQuantity() {
    let quantityInput = document.getElementById("quantity");
    let quantity = parseInt(quantityInput.value);
    if (quantity > 1) {
        quantity--;
        quantityInput.value = quantity;
    }
}
function addQuantity() {
    let quantityInput = document.getElementById("quantity");
    let quantity = parseInt(quantityInput.value);
    quantity++;
    quantityInput.value = quantity;
}

function loadContent(type, productId) {
    $.ajax({
        type: "GET",
        url: 'handle.php',
        data: {
            action: 'getProductInfo',
            type: type,
            productId: productId
        },
        success: function(response) {
            $('#contentArea').html(response);
        },
        error: function(xhr, status, error) {
            console.error(xhr.responseText);
        }
    });
}


loadContent('description',productIDjs);





