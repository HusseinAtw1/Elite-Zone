function subQuantity(button) {
    var quantityInput = button.parentElement.querySelector(".quantityInput");
    var quantity = parseInt(quantityInput.value);
    if (quantity > 1) {
        quantity--;
        quantityInput.value = quantity;
        quantityInput.dispatchEvent(new Event('change'));
    }
}

function addQuantity(button) {
    var quantityInput = button.parentElement.querySelector(".quantityInput");
    var quantity = parseInt(quantityInput.value);
    quantity++;
    quantityInput.value = quantity;
    quantityInput.dispatchEvent(new Event('change'));
}

