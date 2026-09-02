<?php

require_once "../../app/helpers/session.php";

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "housekeeping") {
    header("Location: ../auth/login.php");
    exit();
}

require_once "../../models/housekeepingModel.php";

$rooms = getHousekeepingRoomsForDropdown();

?>
<!DOCTYPE html>
<html>
<head>
    <title>Log Maintenance Issue</title>
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
            <li><a href="create_maintenance.php" class="active">Log Maintenance</a></li>
            <li><a href="../../logout.php">Logout</a></li>
        </ul>
    </div>

    <div class="housekeeping-main">

        <div class="housekeeping-top">
            <h1>Log Maintenance Issue</h1>
            <p>Report a room issue and automatically mark the room as maintenance.</p>
        </div>

        <?php if (isset($_SESSION["error"])) { ?>
            <div class="error-message">
                <?php echo $_SESSION["error"]; unset($_SESSION["error"]); ?>
            </div>
        <?php } ?>

        <div class="housekeeping-form-box">

            <h2>New Maintenance Report</h2>

            <form method="POST" action="../../controllers/housekeepingController.php" class="housekeeping-form">

                <input type="hidden" name="action" value="create_maintenance">

                <div class="form-group">
                    <label>Room</label>
                    <select name="room_id" required>
                        <option value="">Select Room</option>

                        <?php while ($room = $rooms->fetch_assoc()) { ?>
                            <option value="<?php echo $room["id"]; ?>">
                                Room <?php echo htmlspecialchars($room["room_number"]); ?>
                                - Floor <?php echo htmlspecialchars($room["floor"]); ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Severity</label>
                    <select name="severity" required>
                        <option value="low">Low</option>
                        <option value="medium">Medium</option>
                        <option value="high">High</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" placeholder="Describe the maintenance issue..." required></textarea>
                </div>

                <button type="submit" class="housekeeping-btn">Submit Report</button>

            </form>

        </div>

    </div>

</div>

<script src="../../assets/js/housekeeping.js"></script>
</body>
</html>
