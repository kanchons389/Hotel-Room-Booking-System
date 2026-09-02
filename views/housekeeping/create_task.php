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
    <title>Create Housekeeping Task</title>
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
            <li><a href="create_task.php" class="active">Create Task</a></li>
            <li><a href="tasks.php">Tasks</a></li>
            <li><a href="maintenance.php">Maintenance Reports</a></li>
            <li><a href="create_maintenance.php">Log Maintenance</a></li>
            <li><a href="../../logout.php">Logout</a></li>
        </ul>
    </div>

    <div class="housekeeping-main">

        <div class="housekeeping-top">
            <h1>Create Housekeeping Task</h1>
            <p>Assign cleaning, inspection, or maintenance tasks to rooms.</p>
        </div>

        <?php if (isset($_SESSION["error"])) { ?>
            <div class="error-message">
                <?php echo $_SESSION["error"]; unset($_SESSION["error"]); ?>
            </div>
        <?php } ?>

        <div class="housekeeping-form-box">

            <h2>New Task</h2>

            <form method="POST" action="../../controllers/housekeepingController.php" class="housekeeping-form">

                <input type="hidden" name="action" value="create_task">

                <div class="form-group">
                    <label>Room</label>
                    <select name="room_id" required>
                        <option value="">Select Room</option>

                        <?php while ($room = $rooms->fetch_assoc()) { ?>
                            <option value="<?php echo $room["id"]; ?>">
                                Room <?php echo htmlspecialchars($room["room_number"]); ?>
                                - Floor <?php echo htmlspecialchars($room["floor"]); ?>
                                (<?php echo htmlspecialchars($room["status"]); ?>)
                            </option>
                        <?php } ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Task Type</label>
                    <select name="task_type" required>
                        <option value="">Select Task</option>
                        <option value="cleaning">Cleaning</option>
                        <option value="inspection">Inspection</option>
                        <option value="maintenance">Maintenance</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Priority</label>
                    <select name="priority" required>
                        <option value="normal">Normal</option>
                        <option value="urgent">Urgent</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Scheduled Date</label>
                    <input type="date" name="scheduled_date" value="<?php echo date('Y-m-d'); ?>" required>
                </div>

                <div class="form-group">
                    <label>Notes</label>
                    <textarea name="notes" placeholder="Write task notes..."></textarea>
                </div>

                <button type="submit" class="housekeeping-btn">Create Task</button>

            </form>

        </div>

    </div>

</div>

<script src="../../assets/js/housekeeping.js"></script>
</body>
</html>