<?php

require_once "../../app/helpers/session.php";

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "receptionist") {
    header("Location: ../auth/login.php");
    exit();
}

require_once "../../models/receptionistModel.php";

$checkins = getTodayCheckins();

?>

<!DOCTYPE html>
<html>
<head>
    <title>Today Check-ins</title>
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
                <h1>Today Check-ins</h1>
                <p>Assign available rooms and complete guest check-in.</p>
            </div>

            <a href="../../logout.php" class="reception-logout">Logout</a>
        </div>

        <?php if (isset($_SESSION["success"])) { ?>
            <div class="success-message">
                <?php echo $_SESSION["success"]; unset($_SESSION["success"]); ?>
            </div>
        <?php } ?>

        <?php if (isset($_SESSION["error"])) { ?>
            <div class="error-message">
                <?php echo $_SESSION["error"]; unset($_SESSION["error"]); ?>
            </div>
        <?php } ?>

        <div class="reception-table">

            <h2>Guest Arrival List</h2>

            <input
                type="text"
                id="checkinSearch"
                class="search-box"
                placeholder="Search guest, room type or booking..."
            >

            <table>
                <thead>
                    <tr>
                        <th>Booking ID</th>
                        <th>Guest</th>
                        <th>ID Number</th>
                        <th>Room Type</th>
                        <th>Guests</th>
                        <th>Dates</th>
                        <th>Assign Room</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody id="checkinTableBody">
                    <?php if ($checkins && $checkins->num_rows > 0) { ?>

                        <?php while ($row = $checkins->fetch_assoc()) { ?>

                            <?php $rooms = getAvailableRoomsByType($row["room_type_id"]); ?>

                            <tr>
                                <td>#B<?php echo $row["id"]; ?></td>
                                <td><?php echo htmlspecialchars($row["guest_name"]); ?></td>
                                <td><?php echo htmlspecialchars($row["id_number"]); ?></td>
                                <td><?php echo htmlspecialchars($row["room_type"]); ?></td>
                                <td><?php echo $row["num_guests"]; ?></td>
                                <td><?php echo $row["checkin_date"] . " to " . $row["checkout_date"]; ?></td>

                                <td>
                                    <form action="../../controllers/receptionistController.php" method="POST">
                                        <input type="hidden" name="action" value="check_in">
                                        <input type="hidden" name="booking_id" value="<?php echo $row["id"]; ?>">

                                        <select name="room_id" required>
                                            <option value="">Select Room</option>

                                            <?php if ($rooms && $rooms->num_rows > 0) { ?>
                                                <?php while ($room = $rooms->fetch_assoc()) { ?>
                                                    <option value="<?php echo $room["id"]; ?>">
                                                        Room <?php echo htmlspecialchars($room["room_number"]); ?>
                                                        - Floor <?php echo htmlspecialchars($room["floor"]); ?>
                                                    </option>
                                                <?php } ?>
                                            <?php } ?>
                                        </select>
                                </td>

                                <td>
                                        <button type="submit" class="edit-btn">Check In</button>
                                    </form>
                                </td>
                            </tr>

                        <?php } ?>

                    <?php } else { ?>

                        <tr>
                            <td colspan="8">No confirmed check-ins scheduled today.</td>
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