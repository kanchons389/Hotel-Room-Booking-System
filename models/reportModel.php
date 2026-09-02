<?php

require_once __DIR__ . "/../config/database.php";

function getFinancialSummary()
{
    global $conn;

    $sql = "SELECT 
                COALESCE(SUM(total_amount),0) AS total_revenue,
                COALESCE(SUM(base_amount),0) AS room_revenue,
                COALESCE(SUM(extras_amount),0) AS extras_revenue,
                COALESCE(SUM(discount_amount),0) AS discount_total
            FROM billing
            WHERE payment_status = ?";

    $status = "paid";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $status);
    $stmt->execute();

    return $stmt->get_result()->fetch_assoc();
}

function getOccupancySummary()
{
    global $conn;

    $sql = "SELECT 
                COUNT(*) AS total_rooms,
                SUM(status = 'occupied') AS occupied_rooms,
                SUM(status = 'available') AS available_rooms,
                SUM(status = 'dirty') AS dirty_rooms,
                SUM(status = 'maintenance') AS maintenance_rooms
            FROM rooms";

    $stmt = $conn->prepare($sql);
    $stmt->execute();

    return $stmt->get_result()->fetch_assoc();
}

function getPopularRoomTypes()
{
    global $conn;

    $sql = "SELECT room_types.name,
                   COUNT(bookings.id) AS total_bookings
            FROM bookings
            INNER JOIN room_types ON bookings.room_type_id = room_types.id
            GROUP BY room_types.id, room_types.name
            ORDER BY total_bookings DESC";

    $stmt = $conn->prepare($sql);
    $stmt->execute();

    return $stmt->get_result();
}

function getPeakBookingMonths()
{
    global $conn;

    $sql = "SELECT DATE_FORMAT(created_at, '%M %Y') AS booking_month,
                   COUNT(*) AS total_bookings
            FROM bookings
            GROUP BY YEAR(created_at), MONTH(created_at)
            ORDER BY YEAR(created_at) DESC, MONTH(created_at) DESC";

    $stmt = $conn->prepare($sql);
    $stmt->execute();

    return $stmt->get_result();
}

function getLoyaltySummary()
{
    global $conn;

    $sql = "SELECT 
                COALESCE(SUM(points_earned),0) AS total_issued,
                COALESCE(SUM(points_used),0) AS total_redeemed
            FROM loyalty_points";

    $stmt = $conn->prepare($sql);
    $stmt->execute();

    return $stmt->get_result()->fetch_assoc();
}

function getServiceSummary()
{
    global $conn;

    $sql = "SELECT 
                COUNT(*) AS total_requests,
                SUM(status = 'pending') AS pending_requests,
                SUM(status = 'in_progress') AS progress_requests,
                SUM(status = 'completed') AS completed_requests
            FROM service_requests";

    $stmt = $conn->prepare($sql);
    $stmt->execute();

    return $stmt->get_result()->fetch_assoc();
}

function getServiceTypeSummary()
{
    global $conn;

    $sql = "SELECT service_type,
                   COUNT(*) AS total
            FROM service_requests
            GROUP BY service_type
            ORDER BY total DESC";

    $stmt = $conn->prepare($sql);
    $stmt->execute();

    return $stmt->get_result();
}

function getMaintenanceSummary()
{
    global $conn;

    $sql = "SELECT severity,
                   status,
                   COUNT(*) AS total
            FROM maintenance_reports
            GROUP BY severity, status
            ORDER BY total DESC";

    $stmt = $conn->prepare($sql);
    $stmt->execute();

    return $stmt->get_result();
}

?>