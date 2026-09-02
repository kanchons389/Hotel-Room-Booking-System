<?php

require_once "../../app/helpers/session.php";

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "receptionist") {
    header("Location: ../auth/login.php");
    exit();
}

require_once "../../models/receptionistModel.php";

$bookings = getModifiableBookings();

?>

<!DOCTYPE html>
<html>
<head>
    <title>Booking Changes</title>
    <link rel="stylesheet" href="../../assets/css/receptionist.css?v=2">
</head>
<body>

<div class="reception-wrapper">

    <div class="reception-sidebar">

        <div class="reception-logo">
            <h2>GPH</h2>
            <p>Reception Desk</p>
        </div>

        <ul class="reception-menu">
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="today_checkins.php">Today Check-ins</a></li>
            <li><a href="today_checkouts.php">Today Check-outs</a></li>
            <li><a href="payments.php">Payments</a></li>
            <li><a href="walkin_booking.php">Walk-in Booking</a></li>
            <li><a href="booking_modifications.php">Booking Changes</a></li>
            
        </ul>

    </div>

    <div class="reception-main">

        <div class="reception-top">
            <div>
                <h1>Booking Changes</h1>
                <p>Modify guest check-in and check-out dates.</p>
            </div>

            <a href="../../logout.php" class="reception-logout">Logout</a>
        </div>

        <?php if (isset($_SESSION["success"])) { ?>
            <div class="success-message">
                <?php
                echo $_SESSION["success"];
                unset($_SESSION["success"]);
                ?>
            </div>
        <?php } ?>

        <?php if (isset($_SESSION["error"])) { ?>
            <div class="error-message">
                <?php
                echo $_SESSION["error"];
                unset($_SESSION["error"]);
                ?>
            </div>
        <?php } ?>

        <div class="reception-table">

            <h2>Booking Modification List</h2>

            <input
                type="text"
                id="bookingSearch"
                class="search-box"
                placeholder="Search booking, guest, room type or status..."
            >

            <table>
                <thead>
                    <tr>
                        <th>Booking ID</th>
                        <th>Guest</th>
                        <th>Room Type</th>
                        <th>Current Dates</th>
                        <th>Status</th>
                        <th>New Check-in</th>
                        <th>New Check-out</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>

                    <?php if ($bookings && $bookings->num_rows > 0) { ?>

                        <?php while ($row = $bookings->fetch_assoc()) { ?>

                            <tr>
                                <td>#B<?php echo $row["id"]; ?></td>

                                <td><?php echo htmlspecialchars($row["guest_name"]); ?></td>

                                <td><?php echo htmlspecialchars($row["room_type"]); ?></td>

                                <td>
                                    <?php echo $row["checkin_date"]; ?>
                                    to
                                    <?php echo $row["checkout_date"]; ?>
                                </td>

                                <td>
                                    <span class="reception-badge badge-<?php echo $row["status"]; ?>">
                                        <?php echo ucfirst(str_replace("_", " ", $row["status"])); ?>
                                    </span>
                                </td>

                                <td>
                                    <form action="../../controllers/receptionistController.php" method="POST">
                                        <input type="hidden" name="action" value="modify_booking">
                                        <input type="hidden" name="booking_id" value="<?php echo $row["id"]; ?>">

                                        <input
                                            type="date"
                                            name="checkin_date"
                                            class="search-box"
                                            value="<?php echo $row["checkin_date"]; ?>"
                                            required
                                        >
                                </td>

                                <td>
                                        <input
                                            type="date"
                                            name="checkout_date"
                                            class="search-box"
                                            value="<?php echo $row["checkout_date"]; ?>"
                                            required
                                        >
                                </td>

                                <td>
                                        <button type="submit" class="edit-btn">
                                            Update
                                        </button>
                                    </form>
                                </td>
                            </tr>

                        <?php } ?>

                    <?php } else { ?>

                        <tr>
                            <td colspan="8">No active bookings found.</td>
                        </tr>

                    <?php } ?>

                </tbody>
            </table>

        </div>

    </div>

</div>

<script src="../../assets/js/receptionist.js"></script>

</body>
</html>