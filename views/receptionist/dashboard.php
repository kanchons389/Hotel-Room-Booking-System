<?php

require_once "../../app/helpers/session.php";

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "receptionist") {
    header("Location: ../auth/login.php");
    exit();
}

require_once "../../models/receptionistModel.php";

$stats = getReceptionistDashboardStats(); 
$todayCheckins = getTodayCheckins(); 
$todayCheckouts = getTodayCheckouts();
$users = getReceptionistUsers();




?>

<!DOCTYPE html>
<html>
<head>
    <title>Receptionist Dashboard</title>
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
                <h1>Receptionist Dashboard</h1>
                <p>
                    Welcome, <?php echo htmlspecialchars($_SESSION["name"]); ?>.
                    Manage front-desk operations from here.
                </p>
            </div>

            <a href="../../logout.php" class="reception-logout">Logout</a>
        </div>

        <div class="reception-cards">

            <div class="reception-card">
                <h3>Today Check-ins</h3>
                <h2><?php echo $stats["checkins"]; ?></h2>
            </div>

            <div class="reception-card">
                <h3>Today Check-outs</h3>
                <h2><?php echo $stats["checkouts"]; ?></h2>
            </div>

            <div class="reception-card">
                <h3>Occupied Rooms</h3>
                <h2><?php echo $stats["occupied"]; ?></h2>
            </div>

            <div class="reception-card">
                <h3>Available Rooms</h3>
                <h2><?php echo $stats["available"]; ?></h2>
            </div>

        </div>
<div class="reception-table">

    <h2>Registered Guests</h2>

    <table>

        <thead>
            <tr>
                <th>User ID</th>
                <th>Guest Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Nationality</th>
                <th>ID Number</th>
                <th>Status</th>
            </tr>
        </thead>

        <tbody>

            <?php if ($users && $users->num_rows > 0) { ?>

                <?php while ($user = $users->fetch_assoc()) { ?>

                    <tr>

                        <td>
                            #U<?php echo $user["id"]; ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($user["name"]); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($user["email"]); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($user["phone"]); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($user["nationality"]); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($user["id_number"]); ?>
                        </td>

                        <td>

                            <?php if ($user["is_active"] == 1) { ?>

                                <span class="reception-badge badge-confirmed">
                                    Active
                                </span>

                            <?php } else { ?>

                                <span class="reception-badge badge-cancelled">
                                    Inactive
                                </span>

                            <?php } ?>

                        </td>

                    </tr>

                <?php } ?>

            <?php } else { ?>

                <tr>
                    <td colspan="7">
                        No registered guests found.
                    </td>
                </tr>

            <?php } ?>

        </tbody>

    </table>

</div>

        

        <div class="reception-table">
            <h2>Today Check-ins</h2>

            <table>
                <thead>
                    <tr>
                        <th>Booking ID</th>
                        <th>Guest</th>
                        <th>Room Type</th>
                        <th>Guests</th>
                        <th>Check-in</th>
                        <th>Check-out</th>
                        <th>Status</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if ($todayCheckins && $todayCheckins->num_rows > 0) { ?>

                        <?php while ($row = $todayCheckins->fetch_assoc()) { ?>
                            <tr>
                                <td>#B<?php echo $row["id"]; ?></td>
                                <td><?php echo htmlspecialchars($row["guest_name"]); ?></td>
                                <td><?php echo htmlspecialchars($row["room_type"]); ?></td>
                                <td><?php echo $row["num_guests"]; ?></td>
                                <td><?php echo $row["checkin_date"]; ?></td>
                                <td><?php echo $row["checkout_date"]; ?></td>
                                <td>
                                    <span class="reception-badge badge-<?php echo $row["status"]; ?>">
                                        <?php echo ucfirst(str_replace("_", " ", $row["status"])); ?>
                                    </span>
                                </td>
                            </tr>
                        <?php } ?>

                    <?php } else { ?>

                        <tr>
                            <td colspan="7">No check-ins scheduled for today.</td>
                        </tr>

                    <?php } ?>
                </tbody>
            </table>
        </div>

        <div class="reception-table">
            <h2>Today Check-outs</h2>

            <table>
                <thead>
                    <tr>
                        <th>Booking ID</th>
                        <th>Guest</th>
                        <th>Room Number</th>
                        <th>Check-in</th>
                        <th>Check-out</th>
                        <th>Status</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if ($todayCheckouts && $todayCheckouts->num_rows > 0) { ?>

                        <?php while ($row = $todayCheckouts->fetch_assoc()) { ?>
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
                            </tr>
                        <?php } ?>

                    <?php } else { ?>

                        <tr>
                            <td colspan="6">No check-outs scheduled for today.</td>
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