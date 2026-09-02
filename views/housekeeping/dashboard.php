<?php

require_once "../../app/helpers/session.php";

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "housekeeping") {
    header("Location: ../auth/login.php");
    exit();
}

require_once "../../models/housekeepingModel.php";

$stats = getHousekeepingDashboardStats();

?>
<!DOCTYPE html>
<html>
<head>
    <title>Housekeeping Dashboard</title>
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
            <li><a href="dashboard.php" class="active">Dashboard</a></li>
            <li><a href="create_task.php">Create Task</a></li>
            <li><a href="tasks.php">Tasks</a></li>
            <li><a href="maintenance.php">Maintenance Reports</a></li>
            <li><a href="create_maintenance.php">Log Maintenance</a></li>
            <li><a href="../../logout.php">Logout</a></li>
        </ul>
    </div>

    <div class="housekeeping-main">

        <div class="housekeeping-top">
            <h1>Housekeeping Dashboard</h1>
            <p>Monitor room readiness, cleaning tasks, inspections, and maintenance issues.</p>
        </div>

        <div class="housekeeping-cards">

            <div class="housekeeping-card">
                <h3>Dirty Rooms</h3>
                <h2><?php echo $stats["dirty_rooms"]; ?></h2>
            </div>

            <div class="housekeeping-card">
                <h3>Pending Inspection</h3>
                <h2><?php echo $stats["pending_inspection"]; ?></h2>
            </div>

            <div class="housekeeping-card">
                <h3>Open Maintenance</h3>
                <h2><?php echo $stats["open_maintenance"]; ?></h2>
            </div>

            <div class="housekeeping-card">
                <h3>Completed Today</h3>
                <h2><?php echo $stats["completed_today"]; ?></h2>
            </div>

        </div>

        <div class="housekeeping-table">

            <h2>Live Room Status Board</h2>

            <input type="text" id="roomSearch" class="search-box" placeholder="Search room, type, floor or status...">

            <table>
                <thead>
                    <tr>
                        <th>Room Number</th>
                        <th>Floor</th>
                        <th>Room Type</th>
                        <th>Status</th>
                        <th>Notes</th>
                    </tr>
                </thead>

                <tbody id="roomStatusBody">
                    <tr>
                        <td colspan="5">Loading rooms...</td>
                    </tr>
                </tbody>
            </table>

        </div>

    </div>

</div>

<script src="../../assets/js/housekeeping.js"></script>
</body>
</html>