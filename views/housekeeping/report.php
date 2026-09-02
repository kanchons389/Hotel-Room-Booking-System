<?php

require_once "../../app/helpers/session.php";

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "housekeeping") {
    header("Location: ../auth/login.php");
    exit();
}

require_once "../../models/housekeepingModel.php";

$report = getHousekeepingDailyReport();
$checkouts = getUpcomingHousekeepingCheckouts();
$checkins = getUpcomingHousekeepingCheckins();

?>
<!DOCTYPE html>
<html>
<head>
    <title>Daily Housekeeping Report</title>
    <link rel="stylesheet" href="../../assets/css/housekeeping.css?v=2">
</head>
<body>

<div class="housekeeping-wrapper">

    <div class="housekeeping-sidebar">
        <div class="housekeeping-logo">
            <h2>GPH</h2>
            <p>Housekeeping Panel</p>
        </div>

        <ul class="housekeeping-menu">
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="create_task.php">Create Task</a></li>
            <li><a href="maintenance.php">Maintenance Reports</a></li>
            <li><a href="create_maintenance.php">Log Maintenance</a></li>
            <li><a href="../../logout.php">Logout</a></li>
        </ul>
    </div>

    <div class="housekeeping-main">

        <div class="housekeeping-top">
            <h1>Daily Housekeeping Report</h1>
            <p>Tasks assigned, completed, pending, rooms cleared, and upcoming guest flow.</p>
            <button onclick="printPage()" class="housekeeping-btn">Print Report</button>
        </div>

        <div class="housekeeping-cards">

            <div class="housekeeping-card">
                <h3>Tasks Assigned</h3>
                <h2><?php echo $report["assigned"]; ?></h2>
            </div>

            <div class="housekeeping-card">
                <h3>Completed</h3>
                <h2><?php echo $report["completed"]; ?></h2>
            </div>

            <div class="housekeeping-card">
                <h3>Pending</h3>
                <h2><?php echo $report["pending"]; ?></h2>
            </div>

            <div class="housekeeping-card">
                <h3>Rooms Cleared</h3>
                <h2><?php echo $report["cleared_rooms"]; ?></h2>
            </div>

        </div>

        <div class="housekeeping-table">
            <h2>Upcoming Check-outs Today/Tomorrow</h2>

            <table>
                <thead>
                    <tr>
                        <th>Booking</th>
                        <th>Guest</th>
                        <th>Room</th>
                        <th>Room Type</th>
                        <th>Checkout Date</th>
                        <th>Status</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if ($checkouts && $checkouts->num_rows > 0) { ?>
                        <?php while ($row = $checkouts->fetch_assoc()) { ?>
                            <tr>
                                <td>#B<?php echo $row["id"]; ?></td>
                                <td><?php echo htmlspecialchars($row["guest_name"]); ?></td>
                                <td><?php echo htmlspecialchars($row["room_number"] ?? "Not Assigned"); ?></td>
                                <td><?php echo htmlspecialchars($row["room_type"]); ?></td>
                                <td><?php echo htmlspecialchars($row["checkout_date"]); ?></td>
                                <td><?php echo ucfirst(str_replace("_", " ", $row["status"])); ?></td>
                            </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <tr><td colspan="6">No upcoming check-outs found.</td></tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

        <div class="housekeeping-table">
            <h2>Upcoming Check-ins Today/Tomorrow</h2>

            <table>
                <thead>
                    <tr>
                        <th>Booking</th>
                        <th>Guest</th>
                        <th>Room</th>
                        <th>Room Type</th>
                        <th>Checkin Date</th>
                        <th>Status</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if ($checkins && $checkins->num_rows > 0) { ?>
                        <?php while ($row = $checkins->fetch_assoc()) { ?>
                            <tr>
                                <td>#B<?php echo $row["id"]; ?></td>
                                <td><?php echo htmlspecialchars($row["guest_name"]); ?></td>
                                <td><?php echo htmlspecialchars($row["room_number"] ?? "Not Assigned"); ?></td>
                                <td><?php echo htmlspecialchars($row["room_type"]); ?></td>
                                <td><?php echo htmlspecialchars($row["checkin_date"]); ?></td>
                                <td><?php echo ucfirst(str_replace("_", " ", $row["status"])); ?></td>
                            </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <tr><td colspan="6">No upcoming check-ins found.</td></tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

    </div>

</div>

<script src="../../assets/js/housekeeping.js"></script>
</body>
</html>
