<?php

require_once "../app/helpers/session.php";

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "housekeeping") {
    header("Location: ../views/auth/login.php");
    exit();
}


require_once __DIR__ . "/../models/userModel.php";
require_once __DIR__ . "/../models/roomModel.php";
require_once __DIR__ . "/../models/bookingModel.php";
require_once __DIR__ . "/../models/pricingModel.php";
require_once __DIR__ . "/../models/housekeepingModel.php";


if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: ../views/housekeeping/dashboard.php");
    exit();
}

$action = $_POST["action"] ?? "";

if ($action == "update_room_status") {

    $roomId = intval($_POST["room_id"] ?? 0);
    $status = trim($_POST["status"] ?? "");

    if ($roomId <= 0 || $status == "") {
        $_SESSION["error"] = "Invalid room status request.";
        header("Location: ../views/housekeeping/room_status.php");
        exit();
    }

    updateHousekeepingRoomStatus($roomId, $status);

    $_SESSION["success"] = "Room status updated successfully.";
    header("Location: ../views/housekeeping/room_status.php");
    exit();
}

if ($action == "create_task") {

    $roomId = intval($_POST["room_id"] ?? 0);
    $taskType = trim($_POST["task_type"] ?? "");
    $priority = trim($_POST["priority"] ?? "normal");
    $scheduledDate = trim($_POST["scheduled_date"] ?? "");
    $notes = trim($_POST["notes"] ?? "");
    $assignedTo = intval($_SESSION["user_id"]);

    if ($roomId <= 0 || $taskType == "" || $scheduledDate == "") {
        $_SESSION["error"] = "Room, task type, and scheduled date are required.";
        header("Location: ../views/housekeeping/create_task.php");
        exit();
    }

    createHousekeepingTask($roomId, $assignedTo, $taskType, $priority, $scheduledDate, $notes);

    $_SESSION["success"] = "Housekeeping task created successfully.";
    header("Location: ../views/housekeeping/tasks.php");
    exit();
}

if ($action == "update_task_status") {

    $taskId = intval($_POST["task_id"] ?? 0);
    $status = trim($_POST["status"] ?? "");

    if ($taskId <= 0 || $status == "") {
        $_SESSION["error"] = "Invalid task status request.";
        header("Location: ../views/housekeeping/tasks.php");
        exit();
    }

    if ($status == "done") {
        completeHousekeepingTaskAndMakeRoomAvailable($taskId);
    } else {
        updateHousekeepingTaskStatus($taskId, $status);
    }

    $_SESSION["success"] = "Task status updated successfully.";
    header("Location: ../views/housekeeping/tasks.php");
    exit();
}

if ($action == "create_maintenance") {

    $roomId = intval($_POST["room_id"] ?? 0);
    $description = trim($_POST["description"] ?? "");
    $severity = trim($_POST["severity"] ?? "low");
    $reportedBy = intval($_SESSION["user_id"]);

    if ($roomId <= 0 || $description == "") {
        $_SESSION["error"] = "Room and issue description are required.";
        header("Location: ../views/housekeeping/create_maintenance.php");
        exit();
    }

    createMaintenanceReport($roomId, $reportedBy, $description, $severity);

    $_SESSION["success"] = "Maintenance issue logged successfully.";
    header("Location: ../views/housekeeping/maintenance.php");
    exit();
}

if ($action == "update_maintenance_status") {

    $reportId = intval($_POST["report_id"] ?? 0);
    $status = trim($_POST["status"] ?? "");

    if ($reportId <= 0 || $status == "") {
        $_SESSION["error"] = "Invalid maintenance status request.";
        header("Location: ../views/housekeeping/maintenance.php");
        exit();
    }

    updateMaintenanceStatus($reportId, $status);

    $_SESSION["success"] = "Maintenance status updated successfully.";
    header("Location: ../views/housekeeping/maintenance.php");
    exit();
}

$_SESSION["error"] = "Invalid housekeeping action.";
header("Location: ../views/housekeeping/dashboard.php");
exit();

?>
