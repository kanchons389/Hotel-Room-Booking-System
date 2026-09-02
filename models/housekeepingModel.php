<?php

require_once __DIR__ . "/../config/database.php";

/* =========================
   DASHBOARD STATS
========================= */

function getHousekeepingDashboardStats()
{
    global $conn;

    $stats = [
        "dirty_rooms" => 0,
        "pending_inspection" => 0,
        "open_maintenance" => 0,
        "completed_today" => 0
    ];

    $status = "dirty";
    $sql = "SELECT COUNT(*) AS total FROM rooms WHERE status = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $status);
    $stmt->execute();
    $stats["dirty_rooms"] = $stmt->get_result()->fetch_assoc()["total"];

    $taskType = "inspection";
    $done = "done";
    $sql = "SELECT COUNT(*) AS total FROM housekeeping_tasks WHERE task_type = ? AND status != ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $taskType, $done);
    $stmt->execute();
    $stats["pending_inspection"] = $stmt->get_result()->fetch_assoc()["total"];

    $resolved = "resolved";
    $sql = "SELECT COUNT(*) AS total FROM maintenance_reports WHERE status != ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $resolved);
    $stmt->execute();
    $stats["open_maintenance"] = $stmt->get_result()->fetch_assoc()["total"];

    $done = "done";
    $sql = "SELECT COUNT(*) AS total FROM housekeeping_tasks WHERE status = ? AND DATE(completed_at) = CURDATE()";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $done);
    $stmt->execute();
    $stats["completed_today"] = $stmt->get_result()->fetch_assoc()["total"];

    return $stats;
}

/* =========================
   ROOM STATUS BOARD
========================= */

function getHousekeepingRoomBoard()
{
    global $conn;

    $sql = "SELECT
                rooms.id,
                rooms.room_number,
                rooms.floor,
                rooms.status,
                rooms.notes,
                room_types.name AS room_type
            FROM rooms
            INNER JOIN room_types ON rooms.room_type_id = room_types.id
            ORDER BY rooms.floor ASC, rooms.room_number ASC";

    $stmt = $conn->prepare($sql);
    $stmt->execute();

    return $stmt->get_result();
}

function getHousekeepingRoomsForDropdown()
{
    global $conn;

    $sql = "SELECT id, room_number, floor, status
            FROM rooms
            ORDER BY floor ASC, room_number ASC";

    $stmt = $conn->prepare($sql);
    $stmt->execute();

    return $stmt->get_result();
}

function updateHousekeepingRoomStatus($roomId, $status)
{
    global $conn;

    $allowed = ["available", "occupied", "dirty", "maintenance", "blocked"];

    if (!in_array($status, $allowed)) {
        return false;
    }

    $sql = "UPDATE rooms SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $status, $roomId);

    return $stmt->execute();
}

/* =========================
   TASKS
========================= */

function getHousekeepingTasks($status = "", $priority = "")
{
    global $conn;

    $sql = "SELECT
                housekeeping_tasks.*,
                rooms.room_number,
                rooms.floor
            FROM housekeeping_tasks
            INNER JOIN rooms ON housekeeping_tasks.room_id = rooms.id
            WHERE 1";

    $types = "";
    $params = [];

    if ($status != "") {
        $sql .= " AND housekeeping_tasks.status = ?";
        $types .= "s";
        $params[] = $status;
    }

    if ($priority != "") {
        $sql .= " AND housekeeping_tasks.priority = ?";
        $types .= "s";
        $params[] = $priority;
    }

    $sql .= " ORDER BY housekeeping_tasks.scheduled_date DESC, housekeeping_tasks.id DESC";

    $stmt = $conn->prepare($sql);

    if ($types != "") {
        $stmt->bind_param($types, ...$params);
    }

    $stmt->execute();

    return $stmt->get_result();
}

function createHousekeepingTask($roomId, $assignedTo, $taskType, $priority, $scheduledDate, $notes)
{
    global $conn;

    $status = "pending";

    $sql = "INSERT INTO housekeeping_tasks
            (room_id, assigned_to, task_type, priority, status, notes, scheduled_date)
            VALUES (?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iisssss", $roomId, $assignedTo, $taskType, $priority, $status, $notes, $scheduledDate);

    return $stmt->execute();
}

function updateHousekeepingTaskStatus($taskId, $status)
{
    global $conn;

    if ($status == "done") {
        $sql = "UPDATE housekeeping_tasks SET status = ?, completed_at = NOW() WHERE id = ?";
    } else {
        $sql = "UPDATE housekeeping_tasks SET status = ?, completed_at = NULL WHERE id = ?";
    }

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $status, $taskId);

    return $stmt->execute();
}

function completeHousekeepingTaskAndMakeRoomAvailable($taskId)
{
    global $conn;

    $conn->begin_transaction();

    try {
        $sql = "SELECT room_id FROM housekeeping_tasks WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $taskId);
        $stmt->execute();
        $task = $stmt->get_result()->fetch_assoc();

        if (!$task) {
            throw new Exception("Task not found");
        }

        $done = "done";
        $sql = "UPDATE housekeeping_tasks SET status = ?, completed_at = NOW() WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $done, $taskId);
        $stmt->execute();

        $available = "available";
        $sql = "UPDATE rooms SET status = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $available, $task["room_id"]);
        $stmt->execute();

        $conn->commit();
        return true;

    } catch (Exception $e) {
        $conn->rollback();
        return false;
    }
}

/* =========================
   MAINTENANCE
========================= */

function getMaintenanceReports($status = "")
{
    global $conn;

    $sql = "SELECT
                maintenance_reports.*,
                rooms.room_number,
                rooms.floor,
                users.name AS reported_by_name
            FROM maintenance_reports
            INNER JOIN rooms ON maintenance_reports.room_id = rooms.id
            LEFT JOIN users ON maintenance_reports.reported_by = users.id
            WHERE 1";

    $types = "";
    $params = [];

    if ($status != "") {
        $sql .= " AND maintenance_reports.status = ?";
        $types .= "s";
        $params[] = $status;
    }

    $sql .= " ORDER BY maintenance_reports.id DESC";

    $stmt = $conn->prepare($sql);

    if ($types != "") {
        $stmt->bind_param($types, ...$params);
    }

    $stmt->execute();

    return $stmt->get_result();
}

function createMaintenanceReport($roomId, $reportedBy, $description, $severity)
{
    global $conn;

    $conn->begin_transaction();

    try {
        $status = "open";

        $sql = "INSERT INTO maintenance_reports
                (room_id, reported_by, description, severity, status, reported_at)
                VALUES (?, ?, ?, ?, ?, NOW())";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iisss", $roomId, $reportedBy, $description, $severity, $status);
        $stmt->execute();

        $roomStatus = "maintenance";
        $sql = "UPDATE rooms SET status = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $roomStatus, $roomId);
        $stmt->execute();

        $conn->commit();
        return true;

    } catch (Exception $e) {
        $conn->rollback();
        return false;
    }
}

function updateMaintenanceStatus($reportId, $status)
{
    global $conn;

    $conn->begin_transaction();

    try {
        if ($status == "resolved") {
            $sql = "UPDATE maintenance_reports SET status = ?, resolved_at = NOW() WHERE id = ?";
        } else {
            $sql = "UPDATE maintenance_reports SET status = ?, resolved_at = NULL WHERE id = ?";
        }

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $status, $reportId);
        $stmt->execute();

        if ($status == "resolved") {
            $sql = "SELECT room_id FROM maintenance_reports WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $reportId);
            $stmt->execute();
            $report = $stmt->get_result()->fetch_assoc();

            if ($report) {
                $available = "available";
                $sql = "UPDATE rooms SET status = ? WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("si", $available, $report["room_id"]);
                $stmt->execute();
            }
        }

        $conn->commit();
        return true;

    } catch (Exception $e) {
        $conn->rollback();
        return false;
    }
}

/* =========================
   UPCOMING CHECKIN/CHECKOUT
========================= */

function getUpcomingHousekeepingCheckouts()
{
    global $conn;

    $sql = "SELECT
                bookings.id,
                bookings.checkout_date,
                bookings.status,
                users.name AS guest_name,
                rooms.room_number,
                room_types.name AS room_type
            FROM bookings
            INNER JOIN users ON bookings.guest_id = users.id
            LEFT JOIN rooms ON bookings.room_id = rooms.id
            INNER JOIN room_types ON bookings.room_type_id = room_types.id
            WHERE bookings.checkout_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 1 DAY)
            ORDER BY bookings.checkout_date ASC";

    $stmt = $conn->prepare($sql);
    $stmt->execute();

    return $stmt->get_result();
}

function getUpcomingHousekeepingCheckins()
{
    global $conn;

    $sql = "SELECT
                bookings.id,
                bookings.checkin_date,
                bookings.status,
                users.name AS guest_name,
                rooms.room_number,
                room_types.name AS room_type
            FROM bookings
            INNER JOIN users ON bookings.guest_id = users.id
            LEFT JOIN rooms ON bookings.room_id = rooms.id
            INNER JOIN room_types ON bookings.room_type_id = room_types.id
            WHERE bookings.checkin_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 1 DAY)
            ORDER BY bookings.checkin_date ASC";

    $stmt = $conn->prepare($sql);
    $stmt->execute();

    return $stmt->get_result();
}


function getHousekeepingHistory()
{
    global $conn;

    $sql = "SELECT
                housekeeping_tasks.*,
                rooms.room_number,
                users.name AS cleaner_name
            FROM housekeeping_tasks
            INNER JOIN rooms ON housekeeping_tasks.room_id = rooms.id
            LEFT JOIN users ON housekeeping_tasks.assigned_to = users.id
            WHERE housekeeping_tasks.status = 'done'
            ORDER BY housekeeping_tasks.completed_at DESC";

    $stmt = $conn->prepare($sql);
    $stmt->execute();

    return $stmt->get_result();
}

?>
