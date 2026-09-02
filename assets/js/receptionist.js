// ======================================
// RECEPTIONIST PANEL JS - CLEAN FINAL
// File: assets/js/receptionist.js
// ======================================

document.addEventListener("DOMContentLoaded", function () {

    loadReceptionDarkMode();

    setupTableSearches();

    setupCheckinAjaxSearch();

    autoHideAlerts();

});





function setupTableSearches() {

    tableSearch("bookingSearch");
    tableSearch("guestSearch");
    tableSearch("roomSearch");
    tableSearch("serviceSearch");
    tableSearch("logSearch");

}

function tableSearch(inputId) {

    const input = document.getElementById(inputId);

    if (!input) {
        return;
    }

    input.addEventListener("keyup", function () {

        const value = this.value.toLowerCase();

        const rows = document.querySelectorAll("tbody tr");

        rows.forEach(function (row) {

            const text = row.innerText.toLowerCase();

            if (text.includes(value)) {

                row.style.display = "";

            } else {

                row.style.display = "none";

            }

        });

    });

}


function setupCheckinAjaxSearch() {

    const checkinSearch =
        document.getElementById("checkinSearch");

    const checkinTableBody =
        document.getElementById("checkinTableBody");

    if (!checkinSearch || !checkinTableBody) {
        return;
    }

    checkinSearch.addEventListener("keyup", function () {

        loadCheckins(this.value);

    });

}

function loadCheckins(keyword = "") {

    const checkinTableBody =
        document.getElementById("checkinTableBody");

    if (!checkinTableBody) {
        return;
    }

    fetch(
        "../../public/api/search_checkins.php?keyword="
        + encodeURIComponent(keyword)
    )

    .then(function (response) {

        return response.json();

    })

    .then(function (data) {

        checkinTableBody.innerHTML = "";

        if (!data || data.length === 0) {

            checkinTableBody.innerHTML = `
                <tr>
                    <td colspan="8">
                        No confirmed check-ins found.
                    </td>
                </tr>
            `;

            return;
        }

        data.forEach(function (row) {

            let roomOptions =
                `<option value="">Select Room</option>`;

            if (row.rooms && row.rooms.length > 0) {

                row.rooms.forEach(function (room) {

                    roomOptions += `
                        <option value="${escapeHtml(room.id)}">
                            Room ${escapeHtml(room.room_number)}
                            - Floor ${escapeHtml(room.floor)}
                        </option>
                    `;

                });

            }

            checkinTableBody.innerHTML += `

                <tr>

                    <td>#B${escapeHtml(row.id)}</td>

                    <td>${escapeHtml(row.guest_name)}</td>

                    <td>${escapeHtml(row.id_number)}</td>

                    <td>${escapeHtml(row.room_type)}</td>

                    <td>${escapeHtml(row.num_guests)}</td>

                    <td>
                        ${escapeHtml(row.checkin_date)}
                        to
                        ${escapeHtml(row.checkout_date)}
                    </td>

                    <td>

                        <form
                            action="../../controllers/receptionistController.php"
                            method="POST"
                        >

                            <input
                                type="hidden"
                                name="action"
                                value="check_in"
                            >

                            <input
                                type="hidden"
                                name="booking_id"
                                value="${escapeHtml(row.id)}"
                            >

                            <select name="room_id" required>

                                ${roomOptions}

                            </select>

                    </td>

                    <td>

                            <button
                                type="submit"
                                class="edit-btn"
                            >
                                Check In
                            </button>

                        </form>

                    </td>

                </tr>

            `;

        });

    })

    .catch(function () {

        checkinTableBody.innerHTML = `
            <tr>
                <td colspan="8">
                    Unable to load check-ins.
                </td>
            </tr>
        `;

    });

}



function printReceipt() {

    window.print();

}

function printPage() {

    window.print();

}


function confirmDelete(message = "Are you sure?") {

    return confirm(message);

}

function autoHideAlerts() {

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

    }, 3500);

}

function escapeHtml(value) {

    if (value === null || value === undefined) {
        return "";
    }

    return String(value)

        .replaceAll("&", "&amp;")
        .replaceAll("<", "&lt;")
        .replaceAll(">", "&gt;")
        .replaceAll('"', "&quot;")
        .replaceAll("'", "&#039;");

}

document.addEventListener("DOMContentLoaded", function () {

    loadReceptionDarkMode();

    setupTableSearches();

    setupCheckinAjaxSearch();

    setupCheckoutAjaxSearch();

    autoHideAlerts();

});

function loadReceptionDarkMode() {
    if (localStorage.getItem("reception_dark") === "on") {
        document.body.classList.add("dark-mode");
    }
}

function toggleDarkMode() {
    document.body.classList.toggle("dark-mode");

    if (document.body.classList.contains("dark-mode")) {
        localStorage.setItem("reception_dark", "on");
    } else {
        localStorage.removeItem("reception_dark");
    }
}

function setupTableSearches() {
    tableSearch("bookingSearch");
    tableSearch("guestSearch");
    tableSearch("roomSearch");
    tableSearch("serviceSearch");
    tableSearch("logSearch");
}

function tableSearch(inputId) {
    const input = document.getElementById(inputId);

    if (!input) {
        return;
    }

    input.addEventListener("keyup", function () {
        const value = this.value.toLowerCase();
        const rows = document.querySelectorAll("tbody tr");

        rows.forEach(function (row) {
            const text = row.innerText.toLowerCase();
            row.style.display = text.includes(value) ? "" : "none";
        });
    });
}

function setupCheckinAjaxSearch() {
    const checkinSearch = document.getElementById("checkinSearch");
    const checkinTableBody = document.getElementById("checkinTableBody");

    if (!checkinSearch || !checkinTableBody) {
        return;
    }

    checkinSearch.addEventListener("keyup", function () {
        loadCheckins(this.value);
    });
}

function loadCheckins(keyword = "") {
    const checkinTableBody = document.getElementById("checkinTableBody");

    if (!checkinTableBody) {
        return;
    }

    fetch("../../public/api/search_checkins.php?keyword=" + encodeURIComponent(keyword))
        .then(function (response) {
            return response.json();
        })
        .then(function (data) {
            checkinTableBody.innerHTML = "";

            if (!data || data.length === 0) {
                checkinTableBody.innerHTML = `
                    <tr>
                        <td colspan="8">No confirmed check-ins found.</td>
                    </tr>
                `;
                return;
            }

            data.forEach(function (row) {
                let roomOptions = `<option value="">Select Room</option>`;

                if (row.rooms && row.rooms.length > 0) {
                    row.rooms.forEach(function (room) {
                        roomOptions += `
                            <option value="${escapeHtml(room.id)}">
                                Room ${escapeHtml(room.room_number)} - Floor ${escapeHtml(room.floor)}
                            </option>
                        `;
                    });
                }

                checkinTableBody.innerHTML += `
                    <tr>
                        <td>#B${escapeHtml(row.id)}</td>
                        <td>${escapeHtml(row.guest_name)}</td>
                        <td>${escapeHtml(row.id_number)}</td>
                        <td>${escapeHtml(row.room_type)}</td>
                        <td>${escapeHtml(row.num_guests)}</td>
                        <td>${escapeHtml(row.checkin_date)} to ${escapeHtml(row.checkout_date)}</td>

                        <td>
                            <form action="../../controllers/receptionistController.php" method="POST">
                                <input type="hidden" name="action" value="check_in">
                                <input type="hidden" name="booking_id" value="${escapeHtml(row.id)}">
                                <select name="room_id" required>
                                    ${roomOptions}
                                </select>
                        </td>

                        <td>
                                <button type="submit" class="edit-btn">Check In</button>
                            </form>
                        </td>
                    </tr>
                `;
            });
        });
}

function setupCheckoutAjaxSearch() {
    const checkoutSearch = document.getElementById("checkoutSearch");
    const checkoutTableBody = document.getElementById("checkoutTableBody");

    if (!checkoutSearch || !checkoutTableBody) {
        return;
    }

    checkoutSearch.addEventListener("keyup", function () {
        loadCheckouts(this.value);
    });
}

function loadCheckouts(keyword = "") {
    const checkoutTableBody = document.getElementById("checkoutTableBody");

    if (!checkoutTableBody) {
        return;
    }

    fetch("../../public/api/search_checkouts.php?keyword=" + encodeURIComponent(keyword))
        .then(function (response) {
            return response.json();
        })
        .then(function (data) {
            checkoutTableBody.innerHTML = "";

            if (!data || data.length === 0) {
                checkoutTableBody.innerHTML = `
                    <tr>
                        <td colspan="7">No check-outs found.</td>
                    </tr>
                `;
                return;
            }

            data.forEach(function (row) {
                let roomNumber = row.room_number ? row.room_number : "Not Assigned";
                let action = "No Action";

                if (row.status === "checked_in" && row.room_id) {
                    action = `
                        <form action="../../controllers/receptionistController.php" method="POST">
                            <input type="hidden" name="action" value="check_out">
                            <input type="hidden" name="booking_id" value="${escapeHtml(row.id)}">
                            <input type="hidden" name="room_id" value="${escapeHtml(row.room_id)}">

                            <button type="submit" class="edit-btn" onclick="return confirmDelete('Confirm guest checkout?')">
                                Check Out
                            </button>
                        </form>
                    `;
                }

                checkoutTableBody.innerHTML += `
                    <tr>
                        <td>#B${escapeHtml(row.id)}</td>
                        <td>${escapeHtml(row.guest_name)}</td>
                        <td>${escapeHtml(roomNumber)}</td>
                        <td>${escapeHtml(row.checkin_date)}</td>
                        <td>${escapeHtml(row.checkout_date)}</td>
                        <td>
                            <span class="reception-badge badge-${escapeHtml(row.status)}">
                                ${escapeHtml(row.status)}
                            </span>
                        </td>
                        <td>${action}</td>
                    </tr>
                `;
            });
        });
}

function printReceipt() {
    window.print();
}

function printPage() {
    window.print();
}

function confirmDelete(message = "Are you sure?") {
    return confirm(message);
}

function autoHideAlerts() {
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

function escapeHtml(value) {
    if (value === null || value === undefined) {
        return "";
    }

    return String(value)
        .replaceAll("&", "&amp;")
        .replaceAll("<", "&lt;")
        .replaceAll(">", "&gt;")
        .replaceAll('"', "&quot;")
        .replaceAll("'", "&#039;");
}