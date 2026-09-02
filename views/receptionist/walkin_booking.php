<?php

require_once "../../app/helpers/session.php";

if (
    !isset($_SESSION["user_id"]) ||
    $_SESSION["role"] != "receptionist"
) {

    header("Location: ../auth/login.php");
    exit();
}

require_once "../../models/receptionistModel.php";

$roomTypes = searchWalkinRoomTypes();

?>

<!DOCTYPE html>
<html>

<head>

    <title>Walk-in Booking</title>

    <link
        rel="stylesheet"
        href="../../assets/css/receptionist.css?v=2"
    >

</head>

<body>

<div class="reception-wrapper">

    <div class="reception-sidebar">

        <div class="reception-logo">

            <h2>GPH</h2>
            <p>Reception Desk</p>

        </div>

        <ul class="reception-menu">

            <li>
                <a href="dashboard.php">
                    Dashboard
                </a>
            </li>

            <li>
                <a href="today_checkins.php">
                    Today Check-ins
                </a>
            </li>

            <li>
                <a href="today_checkouts.php">
                    Today Check-outs
                </a>
            </li>

            <li>
                <a href="payments.php">
                    Payments
                </a>
            </li>

            <li>
                <a href="walkin_booking.php">
                    Walk-in Booking
                </a>
            </li>
            
            <li>
                <a href="booking_modifications.php">
                    Booking Changes
                </a>
            </li>
        </ul>


    </div>

    <div class="reception-main">

        <div class="reception-top">

            <div>

                <h1>Walk-in Booking</h1>

                <p>
                    Register a guest and create an instant booking.
                </p>

            </div>

            <a
                href="../../logout.php"
                class="reception-logout"
            >
                Logout
            </a>

        </div>

        <div class="reception-table">

            <h2>Create Walk-in Reservation</h2>

            <form
                action="../../controllers/receptionistController.php"
                method="POST"
            >

                <input
                    type="hidden"
                    name="action"
                    value="walkin_booking"
                >

                <div class="reception-cards">

                    <div class="reception-card">

                        <h3>Guest Name</h3>

                        <input
                            type="text"
                            name="guest_name"
                            class="search-box"
                            placeholder="Enter guest name"
                            required
                        >

                    </div>

                    <div class="reception-card">

                        <h3>Phone Number</h3>

                        <input
                            type="text"
                            name="phone"
                            class="search-box"
                            placeholder="Enter phone number"
                            required
                        >

                    </div>

                    <div class="reception-card">

                        <h3>ID Number</h3>

                        <input
                            type="text"
                            name="id_number"
                            class="search-box"
                            placeholder="National ID / Passport"
                            required
                        >

                    </div>

                    <div class="reception-card">

                        <h3>Nationality</h3>

                        <input
                            type="text"
                            name="nationality"
                            class="search-box"
                            placeholder="Guest nationality"
                            required
                        >

                    </div>

                    <div class="reception-card">

                        <h3>Search Room Type</h3>

                        <input
                            type="text"
                            id="roomTypeSearch"
                            class="search-box"
                            placeholder="Search room type..."
                        >

                    </div>

                    <div class="reception-card">

                        <h3>Room Type</h3>

                        <select
                            name="room_type_id"
                            id="roomTypeSelect"
                            required
                        >

                            <option value="">
                                Select Room Type
                            </option>

                            <?php

                            if (
                                $roomTypes &&
                                $roomTypes->num_rows > 0
                            ) {

                                while (
                                    $type =
                                    $roomTypes->fetch_assoc()
                                ) {

                                    ?>

                                    <option
                                        value="<?php echo $type["id"]; ?>"
                                    >

                                        <?php
                                        echo htmlspecialchars(
                                            $type["name"]
                                        );
                                        ?>

                                        -
                                        BDT

                                        <?php
                                        echo number_format(
                                            $type["price_per_night"],
                                            2
                                        );
                                        ?>

                                    </option>

                                    <?php
                                }
                            }

                            ?>

                        </select>

                    </div>

                    <div class="reception-card">

                        <h3>Guests</h3>

                        <input
                            type="number"
                            name="num_guests"
                            class="search-box"
                            min="1"
                            value="1"
                            required
                        >

                    </div>

                    <div class="reception-card">

                        <h3>Check-in Date</h3>

                        <input
                            type="date"
                            name="checkin_date"
                            class="search-box"
                            required
                        >

                    </div>

                    <div class="reception-card">

                        <h3>Check-out Date</h3>

                        <input
                            type="date"
                            name="checkout_date"
                            class="search-box"
                            required
                        >

                    </div>

                </div>

                <textarea
                    name="special_request"
                    class="search-box"
                    rows="5"
                    placeholder="Special request..."
                ></textarea>

                <br><br>

                <button
                    type="submit"
                    class="edit-btn"
                >

                    Confirm Walk-in Booking

                </button>

            </form>

        </div>

    </div>

</div>

<script src="../../assets/js/receptionist.js"></script>

</body>
</html>