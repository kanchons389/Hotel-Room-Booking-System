<?php

require_once "../../app/helpers/session.php";

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "housekeeping") {
    header("Location: ../auth/login.php");
    exit();
}

require_once "../../models/housekeepingModel.php";

$status = $_GET["status"] ?? "";
$priority = $_GET["priority"] ?? "";

$tasks = getHousekeepingTasks($status, $priority);

?>

<!DOCTYPE html>
<html>
<head>
    <title>Housekeeping Tasks</title>
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
            <li><a href="tasks.php" class="active">Tasks</a></li>
            <li><a href="maintenance.php">Maintenance Reports</a></li>
            <li><a href="create_maintenance.php">Log Maintenance</a></li>
            <li><a href="../../logout.php">Logout</a></li>
        </ul>

    </div>

    <div class="housekeeping-main">

        <div class="housekeeping-top">
            <h1>Housekeeping Tasks</h1>
            <p>View and manage housekeeping tasks.</p>
        </div>

        <?php if (isset($_SESSION["success"])) { ?>

            <div class="success-message">
                <?php
                echo htmlspecialchars($_SESSION["success"]);
                unset($_SESSION["success"]);
                ?>
            </div>

        <?php } ?>

        <?php if (isset($_SESSION["error"])) { ?>

            <div class="error-message">
                <?php
                echo htmlspecialchars($_SESSION["error"]);
                unset($_SESSION["error"]);
                ?>
            </div>

        <?php } ?>

        <div class="housekeeping-form-box">

            <h2>Task List</h2>

            <table>

                <thead>
                    <tr>
                        <th>Room</th>
                        <th>Floor</th>
                        <th>Task Type</th>
                        <th>Priority</th>
                        <th>Status</th>
                        <th>Scheduled Date</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>

                <?php while ($task = $tasks->fetch_assoc()) { ?>

                    <tr>

                        <td>
                            Room <?php echo htmlspecialchars($task["room_number"]); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($task["floor"]); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($task["task_type"]); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($task["priority"]); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($task["status"]); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($task["scheduled_date"]); ?>
                        </td>

                        <td>
                            <?php if ($task["status"] == "pending") { ?>

                                <form method="POST" action="../../controllers/housekeepingController.php" style="display:inline;">
                                    <input type="hidden" name="action" value="update_task_status">
                                    <input type="hidden" name="task_id" value="<?php echo $task["id"]; ?>">
                                    <input type="hidden" name="status" value="in_progress">
                                    <button type="submit" class="housekeeping-btn">Start</button>
                                </form>

                                <form method="POST" action="../../controllers/housekeepingController.php" style="display:inline;">
                                    <input type="hidden" name="action" value="update_task_status">
                                    <input type="hidden" name="task_id" value="<?php echo $task["id"]; ?>">
                                    <input type="hidden" name="status" value="done">
                                    <button type="submit" class="housekeeping-btn">Mark Done</button>
                                </form>

                            <?php } elseif ($task["status"] == "in_progress") { ?>

                                <form method="POST" action="../../controllers/housekeepingController.php" style="display:inline;">
                                    <input type="hidden" name="action" value="update_task_status">
                                    <input type="hidden" name="task_id" value="<?php echo $task["id"]; ?>">
                                    <input type="hidden" name="status" value="done">
                                    <button type="submit" class="housekeeping-btn">Mark Done</button>
                                </form>

                            <?php } else { ?>

                                Completed
                                <?php if (!empty($task["completed_at"])) { ?>
                                    (<?php echo htmlspecialchars($task["completed_at"]); ?>)
                                <?php } ?>

                            <?php } ?>
                        </td>

                    </tr>

                <?php } ?>

                </tbody>

            </table>

        </div>

    </div>

</div>

<script src="../../assets/js/housekeeping.js"></script>

</body>
</html>