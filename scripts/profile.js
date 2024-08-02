$(document).ready(function() {
    // Email form validation
    $("#emailForm").submit(function(e) {
        var newEmail = $("#newEmail").val();
        var currentPassword = $("#currentPasswordEmail").val();

        if (newEmail === "" || currentPassword === "") {
            alert("Please fill in all fields.");
            e.preventDefault();
        }
    });

    // Password form validation
    $("#passwordForm").submit(function(e) {
        var currentPassword = $("#currentPassword").val();
        var newPassword = $("#newPassword").val();
        var confirmPassword = $("#confirmPassword").val();

        if (currentPassword === "" || newPassword === "" || confirmPassword === "") {
            alert("Please fill in all fields.");
            e.preventDefault();
        } else if (newPassword !== confirmPassword) {
            alert("New password and confirm password do not match.");
            e.preventDefault();
        }
    });

    // Name form validation
    $("#nameForm").submit(function(e) {
        var newName = $("#newName").val();
        var currentPassword = $("#currentPasswordName").val();

        if (newName === "" || currentPassword === "") {
            alert("Please fill in all fields.");
            e.preventDefault();
        }
    });
});