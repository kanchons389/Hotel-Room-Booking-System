<?php

require_once "../../app/helpers/session.php";

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "guest") {

    header("Location: ../auth/login.php");
    exit();
}
require_once "../../models/userModel.php";
require_once "../../models/roomModel.php";
require_once "../../models/bookingModel.php";
require_once "../../models/pricingModel.php";

require_once "../../app/layouts/header.php";
require_once "../../app/layouts/sidebar.php";
require_once "../../app/layouts/navbar.php";

?>

<div class="main-content">

    <div class="dashboard-title">

        <h1>Search Rooms</h1>

        <p>
            Search luxury rooms by dates and guest capacity.
        </p>

    </div>

    <div class="form-box">

        <h2>Room Availability Search</h2>

        <form id="roomSearchForm" class="filter-form">

            <input
            type="hidden"
            name="action"
            value="search_rooms">

            <div class="form-row">

                <div class="form-group">

                    <label>Check In Date</label>

                    <input
                    type="date"
                    name="checkin"
                    required>

                </div>

                <div class="form-group">

                    <label>Check Out Date</label>

                    <input
                    type="date"
                    name="checkout"
                    required>

                </div>

                <div class="form-group">

                    <label>Guests</label>

                    <input
                    type="number"
                    name="guests"
                    min="1"
                    required>

                </div>

            </div>

            <button type="submit">
                Search Available Rooms
            </button>

        </form>

    </div>

    <div id="roomResults"></div>

</div>

<script>

document.getElementById("roomSearchForm")

.addEventListener("submit", function(e){

    e.preventDefault();

    let formData = new FormData(this);

    fetch("../../controllers/guestController.php", {

        method: "POST",

        body: formData
    })

    .then(response => response.text())

    .then(data => {

        document.getElementById("roomResults").innerHTML = data;
    });
});

</script>

<?php require_once "../../app/layouts/footer.php"; ?>