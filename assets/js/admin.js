
document.addEventListener("DOMContentLoaded", function () {

    setupAdminTableSearch();
    setupAdminFormValidation();
    setupDeleteConfirmation();
    setupPrintButtons();
    setupPasswordToggle();
    autoHideAdminAlerts();

});

function setupAdminTableSearch() {

    const searchInputs = document.querySelectorAll(".admin-search");

    searchInputs.forEach(function (input) {

        input.addEventListener("keyup", function () {

            const keyword = input.value.toLowerCase();

            const rows = document.querySelectorAll("tbody tr");

            rows.forEach(function (row) {

                const text = row.innerText.toLowerCase();

                if (text.includes(keyword)) {
                    row.style.display = "";
                } else {
                    row.style.display = "none";
                }

            });

        });

    });

}

function setupAdminFormValidation() {

    const forms = document.querySelectorAll(".admin-validate-form");

    forms.forEach(function (form) {

        form.addEventListener("submit", function (event) {

            clearValidationErrors(form);

            let valid = true;

            const requiredFields = form.querySelectorAll("[required]");

            requiredFields.forEach(function (field) {

                if (field.value.trim() === "") {

                    showFieldError(field, "This field is required");
                    valid = false;

                }

            });

            const emailFields = form.querySelectorAll("input[type='email']");

            emailFields.forEach(function (field) {

                if (
                    field.value.trim() !== "" &&
                    !validateEmail(field.value)
                ) {

                    showFieldError(field, "Invalid email");
                    valid = false;

                }

            });

            const passwordFields =
                form.querySelectorAll("input[type='password']");

            passwordFields.forEach(function (field) {

                if (
                    field.value.trim() !== "" &&
                    field.value.length < 6
                ) {

                    showFieldError(
                        field,
                        "Password must be at least 6 characters"
                    );

                    valid = false;
                }

            });

            if (!valid) {

                event.preventDefault();

                showAdminAlert(
                    "Please fix the errors before submitting",
                    "error"
                );

            }

        });

    });

}

function showFieldError(field, message) {

    field.classList.add("input-error");

    const error = document.createElement("small");

    error.className = "admin-field-error";

    error.innerText = message;

    field.insertAdjacentElement("afterend", error);

}

function clearValidationErrors(form) {

    const errors = form.querySelectorAll(".admin-field-error");

    errors.forEach(function (error) {
        error.remove();
    });

    const fields = form.querySelectorAll(".input-error");

    fields.forEach(function (field) {
        field.classList.remove("input-error");
    });

}

function validateEmail(email) {

    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);

}

function setupDeleteConfirmation() {

    const buttons =
        document.querySelectorAll(".delete-btn");

    buttons.forEach(function (button) {

        button.addEventListener("click", function (event) {

            const confirmDelete = confirm(
                "Are you sure you want to delete this item?"
            );

            if (!confirmDelete) {
                event.preventDefault();
            }

        });

    });

}

function setupPrintButtons() {

    const printButtons =
        document.querySelectorAll(".print-btn");

    printButtons.forEach(function (button) {

        button.addEventListener("click", function () {

            window.print();

        });

    });

}

function setupPasswordToggle() {

    const toggles =
        document.querySelectorAll(".password-toggle");

    toggles.forEach(function (toggle) {

        toggle.addEventListener("click", function () {

            const target =
                document.getElementById(
                    toggle.dataset.target
                );

            if (!target) {
                return;
            }

            if (target.type === "password") {

                target.type = "text";
                toggle.innerText = "Hide";

            } else {

                target.type = "password";
                toggle.innerText = "Show";

            }

        });

    });

}


function showAdminAlert(message, type = "success") {

    let alertBox =
        document.querySelector(".admin-js-alert");

    if (!alertBox) {

        alertBox = document.createElement("div");

        alertBox.className = "admin-js-alert";

        document.body.prepend(alertBox);

    }

    alertBox.className =
        "admin-js-alert " + type;

    alertBox.innerText = message;

    alertBox.style.display = "block";

    setTimeout(function () {

        alertBox.style.opacity = "0";

        setTimeout(function () {

            alertBox.style.display = "none";
            alertBox.style.opacity = "1";

        }, 400);

    }, 3000);

}

function autoHideAdminAlerts() {

    setTimeout(function () {

        const alerts = document.querySelectorAll(
            ".success-message, .error-message"
        );

        alerts.forEach(function (alert) {

            alert.style.transition = "0.4s";
            alert.style.opacity = "0";

            setTimeout(function () {

                alert.style.display = "none";

            }, 400);

        });

    }, 3000);

}