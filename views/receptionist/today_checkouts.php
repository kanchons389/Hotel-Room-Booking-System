<?php

require_once "../../app/helpers/session.php";

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "receptionist") {
    header("Location: ../auth/login.php");
    exit();
}

require_once "../../models/receptionistModel.php";

$checkouts = getTodayCheckouts();

?>

<!DOCTYPE html>
<html>
<head>
    <title>Today Check-outs</title>
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
                <h1>Today Check-outs</h1>
                <p>Complete guest departure and update room status.</p>
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

            <h2>Guest Departure List</h2>

            <input
                type="text"
                id="checkoutSearch"
                class="search-box"
                placeholder="Search guest, room or checkout..."
            >

            <table>
                <thead>
                    <tr>
                        <th>Booking ID</th>
                        <th>Guest</th>
                        <th>Room Number</th>
                        <th>Check-in</th>
                        <th>Check-out</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody id="checkoutTableBody">

                    <?php if ($checkouts && $checkouts->num_rows > 0) { ?>

                        <?php while ($row = $checkouts->fetch_assoc()) { ?>

                            <tr>
                                <td>#B<?php echo $row["id"]; ?></td>

                                <td><?php echo htmlspecialchars($row["guest_name"]); ?></td>

                                <td>
                                    <?php
                                    if (!empty($row["room_number"])) {
                                        echo htmlspecialchars($row["room_number"]);
                                    } else {
                                        echo "Not Assigned";
                                    }
                                    ?>
                                </td>

                                <td><?php echo $row["checkin_date"]; ?></td>

                                <td><?php echo $row["checkout_date"]; ?></td>

                                <td>
                                    <span class="reception-badge badge-<?php echo $row["status"]; ?>">
                                        <?php echo ucfirst(str_replace("_", " ", $row["status"])); ?>
                                    </span>
                                </td>

                                <td>
                                    <?php if ($row["status"] == "checked_in" && !empty($row["room_id"])) { ?>

                                        <form action="../../controllers/receptionistController.php" method="POST">
                                            <input type="hidden" name="action" value="check_out">
                                            <input type="hidden" name="booking_id" value="<?php echo $row["id"]; ?>">
                                            <input type="hidden" name="room_id" value="<?php echo $row["room_id"]; ?>">

                                            <button
                                                type="submit"
                                                class="edit-btn"
                                                onclick="return confirmDelete('Confirm guest checkout?')"
                                            >
                                                Check Out
                                            </button>
                                        </form>

                                    <?php } else { ?>

                                        No Action

                                    <?php } ?>
                                </td>
                            </tr>

                        <?php } ?>

                    <?php } else { ?>

                        <tr>
                            <td colspan="7">No check-outs scheduled today.</td>
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