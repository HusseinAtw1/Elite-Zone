document.addEventListener("DOMContentLoaded", function() {
    const editButtons = document.querySelectorAll(".edit-brand");
    const cancelBtn = document.getElementById("cancelBtn");
    const submitBtn = document.getElementById("submitBtn");
    const brandForm = document.getElementById("brandForm");
    const brandIdInput = document.getElementById("brand_id");
    const nameInput = document.getElementById("name");
    const descriptionInput = document.getElementById("description");

    editButtons.forEach(button => {
        button.addEventListener("click", function() {
            const brandId = this.getAttribute("data-id");
            const name = this.getAttribute("data-name");
            const description = this.getAttribute("data-description");
            const images = JSON.parse(this.getAttribute("data-images"));

            brandIdInput.value = brandId;
            nameInput.value = name;
            descriptionInput.value = description;

            // Add logic to handle displaying existing images if needed
            // For example, display existing images below the form
            // and keep track of them during updates.

            submitBtn.name = "update_brand";
            submitBtn.textContent = "Update Brand";
            cancelBtn.style.display = "inline-block";
        });
    });

    cancelBtn.addEventListener("click", function() {
        brandForm.reset();
        brandIdInput.value = "";
        submitBtn.name = "add_brand";
        submitBtn.textContent = "Add Brand";
        cancelBtn.style.display = "none";
    });
});
