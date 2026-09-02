<?php

require_once "../../app/helpers/session.php";

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "housekeeping") {
    header("Location: ../auth/login.php");
    exit();
}

require_once "../../models/housekeepingModel.php";

$status = $_GET["status"] ?? "";
$reports = getMaintenanceReports($status);

?>
<!DOCTYPE html>
<html>
<head>
    <title>Maintenance Reports</title>
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
            <li><a href="maintenance.php" class="active">Maintenance Reports</a></li>
            <li><a href="create_maintenance.php">Log Maintenance</a></li>
            <li><a href="../../logout.php">Logout</a></li>
        </ul>
    </div>

    <div class="housekeeping-main">

        <div class="housekeeping-top">
            <h1>Maintenance Reports</h1>
            <p>Track room maintenance issues and update resolution status.</p>
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

        <div class="housekeeping-table">

            <h2>Maintenance Issue List</h2>

            <form method="GET" class="filter-form">
                <select name="status">
                    <option value="">All Status</option>
                    <option value="open" <?php if($status=="open") echo "selected"; ?>>Open</option>
                    <option value="in_progress" <?php if($status=="in_progress") echo "selected"; ?>>In Progress</option>
                    <option value="resolved" <?php if($status=="resolved") echo "selected"; ?>>Resolved</option>
                </select>

                <button type="submit" class="housekeeping-btn">Filter</button>
                <a href="maintenance.php" class="clear-btn">Clear</a>
            </form>

            <input type="text" id="maintenanceSearch" class="search-box" placeholder="Search room, severity, status or description...">

            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Room</th>
                        <th>Description</th>
                        <th>Severity</th>
                        <th>Status</th>
                        <th>Reported By</th>
                        <th>Reported At</th>
                        <th>Resolved At</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody id="maintenanceTableBody">
                    <?php if ($reports && $reports->num_rows > 0) { ?>

                        <?php while ($row = $reports->fetch_assoc()) { ?>

                            <tr>
                                <td>#M<?php echo $row["id"]; ?></td>
                                <td>Room <?php echo htmlspecialchars($row["room_number"]); ?></td>
                                <td><?php echo htmlspecialchars($row["description"]); ?></td>
                                <td>
                                    <span class="housekeeping-badge badge-<?php echo $row["severity"]; ?>">
                                        <?php echo ucfirst($row["severity"]); ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="housekeeping-badge badge-<?php echo $row["status"]; ?>">
                                        <?php echo ucfirst(str_replace("_", " ", $row["status"])); ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars($row["reported_by_name"] ?? "N/A"); ?></td>
                                <td><?php echo htmlspecialchars($row["reported_at"]); ?></td>
                                <td><?php echo htmlspecialchars($row["resolved_at"] ?? "Not resolved"); ?></td>
                                <td>
                                    <?php if ($row["status"] != "resolved") { ?>
                                        <form method="POST" action="../../controllers/housekeepingController.php">
                                            <input type="hidden" name="action" value="update_maintenance_status">
                                            <input type="hidden" name="report_id" value="<?php echo $row["id"]; ?>">

                                            <select name="status" required>
                                                <option value="open">Open</option>
                                                <option value="in_progress">In Progress</option>
                                                <option value="resolved">Resolved</option>
                                            </select>

                                            <button type="submit" class="housekeeping-btn">Update</button>
                                        </form>
                                    <?php } else { ?>
                                        Resolved
                                    <?php } ?>
                                </td>
                            </tr>

                        <?php } ?>

                    <?php } else { ?>
                        <tr>
                            <td colspan="9">No maintenance reports found.</td>
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
