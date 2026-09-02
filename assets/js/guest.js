

document.addEventListener("DOMContentLoaded", function () {

    setupGuestFormValidation();
    setupGuestDeleteConfirmation();
    setupGuestPrintButtons();
    setupGuestPasswordToggle();
    autoHideGuestAlerts();

});


function setupGuestFormValidation() {

    const forms = document.querySelectorAll(".guest-validate-form");

    forms.forEach(function (form) {

        form.addEventListener("submit", function (event) {

            clearGuestErrors(form);

            let valid = true;

            const requiredFields = form.querySelectorAll("[required]");

            requiredFields.forEach(function (field) {

                if (field.value.trim() === "") {
                    showGuestError(field, "This field is required");
                    valid = false;
                }

            });

            const emailFields = form.querySelectorAll("input[type='email']");

            emailFields.forEach(function (field) {

                if (field.value.trim() !== "" && !isValidGuestEmail(field.value)) {
                    showGuestError(field, "Enter a valid email address");
                    valid = false;
                }

            });

            const phoneFields = form.querySelectorAll("input[name='phone']");

            phoneFields.forEach(function (field) {

                if (field.value.trim() !== "" && !isValidGuestPhone(field.value)) {
                    showGuestError(field, "Phone can contain only numbers, +, - and spaces");
                    valid = false;
                }

            });

            const passwordFields = form.querySelectorAll("input[type='password']");

            passwordFields.forEach(function (field) {

                if (field.value.trim() !== "" && field.value.length < 6) {
                    showGuestError(field, "Password must be at least 6 characters");
                    valid = false;
                }

            });

            const checkin = form.querySelector("input[name='checkin']");
            const checkout = form.querySelector("input[name='checkout']");

            if (checkin && checkout && checkin.value && checkout.value) {

                if (new Date(checkout.value) <= new Date(checkin.value)) {
                    showGuestError(checkout, "Check-out date must be after check-in date");
                    valid = false;
                }

            }

            const guests = form.querySelector("input[name='guests'], input[name='num_guests']");

            if (guests && guests.value !== "" && Number(guests.value) < 1) {
                showGuestError(guests, "Number of guests must be at least 1");
                valid = false;
            }

            const ratingFields = form.querySelectorAll(
                "select[name='overall_rating'], select[name='cleanliness_rating'], select[name='service_rating']"
            );

            ratingFields.forEach(function (field) {

                if (field.value !== "" && (Number(field.value) < 1 || Number(field.value) > 5)) {
                    showGuestError(field, "Rating must be between 1 and 5");
                    valid = false;
                }

            });

            if (!valid) {
                event.preventDefault();
                alert("Please fix the form errors before submitting.");
            }

        });

    });

}


function showGuestError(field, message) {

    field.classList.add("input-error");

    const error = document.createElement("small");

    error.className = "guest-field-error";
    error.innerText = message;

    field.insertAdjacentElement("afterend", error);

}

function clearGuestErrors(form) {

    const errors = form.querySelectorAll(".guest-field-error");

    errors.forEach(function (error) {
        error.remove();
    });

    const fields = form.querySelectorAll(".input-error");

    fields.forEach(function (field) {
        field.classList.remove("input-error");
    });

}


function isValidGuestEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}

function isValidGuestPhone(phone) {
    return /^[0-9+\-\s]+$/.test(phone);
}


function setupGuestDeleteConfirmation() {

    const buttons = document.querySelectorAll(".delete-btn, .cancel-btn");

    buttons.forEach(function (button) {

        button.addEventListener("click", function (event) {

            const message =
                button.getAttribute("data-message") ||
                "Are you sure you want to continue?";

            if (!confirm(message)) {
                event.preventDefault();
            }

        });

    });

}



function setupGuestPrintButtons() {

    const printButtons = document.querySelectorAll(".print-btn");

    printButtons.forEach(function (button) {

        button.addEventListener("click", function () {
            window.print();
        });

    });

}

function printGuestReceipt() {
    window.print();
}


function setupGuestPasswordToggle() {

    const toggles = document.querySelectorAll(".password-toggle");

    toggles.forEach(function (toggle) {

        toggle.addEventListener("click", function () {

            const target = document.getElementById(toggle.dataset.target);

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

function autoHideGuestAlerts() {

    setTimeout(function () {

        const alerts = document.querySelectorAll(".success-message, .error-message");

        alerts.forEach(function (alert) {

            alert.style.transition = "0.4s";
            alert.style.opacity = "0";

            setTimeout(function () {
                alert.style.display = "none";
            }, 400);

        });

    }, 3500);

}