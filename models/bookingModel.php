<?php

require_once __DIR__ . "/../config/database.php";


function getGuestBillingSummary($guestId)
{
    global $conn;

    $summary = [
        "total_bills" => 0,
        "total_invoice" => 0,
        "paid_amount" => 0,
        "pending_amount" => 0
    ];

    $sql = "
        SELECT
            COUNT(billing.id) AS total_bills,

            COALESCE(SUM(billing.total_amount), 0) AS total_invoice,

            COALESCE(
                SUM(
                    CASE
                        WHEN billing.payment_status = 'paid'
                        THEN billing.total_amount
                        ELSE 0
                    END
                ), 0
            ) AS paid_amount,

            COALESCE(
                SUM(
                    CASE
                        WHEN billing.payment_status = 'pending'
                        THEN billing.total_amount
                        ELSE 0
                    END
                ), 0
            ) AS pending_amount

        FROM billing

        INNER JOIN bookings b
            ON b.id = billing.booking_id

        WHERE b.guest_id = ?
    ";

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("Billing summary query failed: " . $conn->error);
    }

    $stmt->bind_param("i", $guestId);

    $stmt->execute();

    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $summary["total_bills"] = $row["total_bills"];
        $summary["total_invoice"] = $row["total_invoice"];
        $summary["paid_amount"] = $row["paid_amount"];
        $summary["pending_amount"] = $row["pending_amount"];
    }

    return $summary;
}


function createGuestBookingWithBilling(
    $guestId,
    $roomTypeId,
    $checkin,
    $checkout,
    $guests,
    $totalAmount,
    $baseAmount,
    $discountAmount
) {
    global $conn;

    mysqli_begin_transaction($conn);

    try {

        // Create booking
        $sql = "INSERT INTO bookings
                (
                    guest_id,
                    room_type_id,
                    checkin_date,
                    checkout_date,
                    num_guests,
                    total_price,
                    status,
                    source
                )
                VALUES (?, ?, ?, ?, ?, ?, 'confirmed', 'online')";

        $stmt = mysqli_prepare($conn, $sql);

        if (!$stmt) {
            throw new Exception(mysqli_error($conn));
        }

        mysqli_stmt_bind_param(
            $stmt,
            "iissid",
            $guestId,
            $roomTypeId,
            $checkin,
            $checkout,
            $guests,
            $totalAmount
        );

        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception(mysqli_stmt_error($stmt));
        }

        $bookingId = mysqli_insert_id($conn);


        // Create billing record
        $billingSql = "INSERT INTO billing
                       (
                           booking_id,
                           guest_id,
                           total_amount,
                           payment_status
                       )
                       VALUES (?, ?, ?, 'pending')";

        $billingStmt = mysqli_prepare($conn, $billingSql);

        if (!$billingStmt) {
            throw new Exception(mysqli_error($conn));
        }

        mysqli_stmt_bind_param(
            $billingStmt,
            "iid",
            $bookingId,
            $guestId,
            $totalAmount
        );

        if (!mysqli_stmt_execute($billingStmt)) {
            throw new Exception(mysqli_stmt_error($billingStmt));
        }


        // Everything successful
        mysqli_commit($conn);

        return $bookingId;

    } catch (Exception $e) {

        mysqli_rollback($conn);

        return false;
    }
}



function calculateGuestBookingPrice($roomTypeId, $checkin, $checkout, $redeemPoints = 0)
{
    global $conn;

    // Get room price
    $sql = "SELECT price_per_night
            FROM room_types
            WHERE id = ?";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $roomTypeId);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $room = mysqli_fetch_assoc($result);

    if (!$room) {
        return false;
    }

    // Calculate number of nights
    $start = new DateTime($checkin);
    $end = new DateTime($checkout);

    $days = $start->diff($end)->days;

    if ($days <= 0) {
        $days = 1;
    }

    $pricePerNight = $room["price_per_night"];

    $baseAmount = $pricePerNight * $days;

    $discount = min($redeemPoints, $baseAmount);

    $finalAmount = $baseAmount - $discount;

    return [
        "days" => $days,
        "price_per_night" => $pricePerNight,
        "base_amount" => $baseAmount,
        "discount" => $discount,
        "final_amount" => $finalAmount,
        "seasonal_label" => ""
    ];
}


function getGuestBookings($guestId, $filters = [])
{
    global $conn;

    $guestId = intval($guestId);

    $sql = "
        SELECT
            b.id,
            b.checkin_date,
            b.checkout_date,
            b.total_price,
            b.status,
            rt.name AS room_type_name,
            r.room_number

        FROM bookings b

        INNER JOIN room_types rt
            ON rt.id = b.room_type_id

        LEFT JOIN rooms r
            ON r.id = b.room_id

        WHERE b.guest_id = ?
    ";

    $params = [$guestId];
    $types = "i";

    // Search booking ID, room type or room number
    if (!empty($filters["search"])) {

        $sql .= "
            AND (
                b.id LIKE ?
                OR rt.name LIKE ?
                OR r.room_number LIKE ?
            )
        ";

        $search = "%" . $filters["search"] . "%";

        $params[] = $search;
        $params[] = $search;
        $params[] = $search;

        $types .= "sss";
    }

    // Status filter
    if (!empty($filters["status"])) {

        $sql .= " AND b.status = ?";

        $params[] = $filters["status"];
        $types .= "s";
    }

    // Check-in date filter
    if (!empty($filters["from_date"])) {

        $sql .= " AND b.checkin_date >= ?";

        $params[] = $filters["from_date"];
        $types .= "s";
    }

    // Check-out date filter
    if (!empty($filters["to_date"])) {

        $sql .= " AND b.checkout_date <= ?";

        $params[] = $filters["to_date"];
        $types .= "s";
    }

    $sql .= " ORDER BY b.id DESC";

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param($types, ...$params);

    $stmt->execute();

    return $stmt->get_result();
}


function getGuestBookingDetails($guestId, $bookingId)
{
    global $conn;

    $guestId = intval($guestId);
    $bookingId = intval($bookingId);

    $sql = "
        SELECT
            b.id,
            b.checkin_date,
            b.checkout_date,
            b.total_price,
            b.status,
            rt.name AS room_type_name,
            r.room_number,
            billing.payment_status

        FROM bookings b

        INNER JOIN room_types rt
            ON rt.id = b.room_type_id

        LEFT JOIN rooms r
            ON r.id = b.room_id

        LEFT JOIN billing
            ON billing.booking_id = b.id

        WHERE b.guest_id = ?
        AND b.id = ?

        LIMIT 1
    ";

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("Get guest booking details failed: " . $conn->error);
    }

    $stmt->bind_param("ii", $guestId, $bookingId);
    $stmt->execute();

    $result = $stmt->get_result();

    return $result->fetch_assoc();
}


function getGuestActiveBookings($guestId)
{
    global $conn;

    $guestId = intval($guestId);

    $sql = "
        SELECT
            b.id,
            b.room_id,
            b.room_type_id,
            b.checkin_date,
            b.checkout_date,
            b.status,

            rt.name AS room_type_name,

            r.room_number

        FROM bookings b

        INNER JOIN room_types rt
            ON rt.id = b.room_type_id

        LEFT JOIN rooms r
            ON r.id = b.room_id

        WHERE b.guest_id = $guestId
        AND b.status = 'checked_in'

        ORDER BY b.checkin_date ASC
    ";

    $result = mysqli_query($conn, $sql);

    if (!$result) {
        die("Get guest active bookings failed: " . mysqli_error($conn));
    }

    return $result;
}

function getAllBookings($filters = [])
{
    global $conn;

    $sql = "SELECT
                bookings.id,
                bookings.checkin_date,
                bookings.checkout_date,
                bookings.num_guests,
                bookings.total_price,
                bookings.status,
                bookings.source,
                bookings.created_at,
                users.name AS guest_name,
                users.email AS guest_email,
                room_types.name AS room_type_name,
                rooms.room_number,
                billing.payment_status
            FROM bookings
            INNER JOIN users ON bookings.guest_id = users.id
            INNER JOIN room_types ON bookings.room_type_id = room_types.id
            LEFT JOIN rooms ON bookings.room_id = rooms.id
            LEFT JOIN billing ON bookings.id = billing.booking_id
            WHERE 1 = 1";

    $params = [];
    $types = "";

    if (!empty($filters["search"])) {
        $sql .= " AND (
                    users.name LIKE ?
                    OR users.email LIKE ?
                    OR rooms.room_number LIKE ?
                    OR bookings.id LIKE ?
                 )";

        $search = "%" . $filters["search"] . "%";

        $params[] = $search;
        $params[] = $search;
        $params[] = $search;
        $params[] = $search;

        $types .= "ssss";
    }

    if (!empty($filters["status"])) {
        $sql .= " AND bookings.status = ?";
        $params[] = $filters["status"];
        $types .= "s";
    }

    if (!empty($filters["room_type_id"])) {
        $sql .= " AND bookings.room_type_id = ?";
        $params[] = $filters["room_type_id"];
        $types .= "i";
    }

    if (!empty($filters["source"])) {
        $sql .= " AND bookings.source = ?";
        $params[] = $filters["source"];
        $types .= "s";
    }

    if (!empty($filters["from_date"])) {
        $sql .= " AND bookings.checkin_date >= ?";
        $params[] = $filters["from_date"];
        $types .= "s";
    }

    if (!empty($filters["to_date"])) {
        $sql .= " AND bookings.checkout_date <= ?";
        $params[] = $filters["to_date"];
        $types .= "s";
    }

    $sql .= " ORDER BY bookings.id DESC";

    $stmt = $conn->prepare($sql);

    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }

    $stmt->execute();

    return $stmt->get_result();
}

function cancelGuestBooking($guestId, $bookingId)
{
    global $conn;

    $guestId = intval($guestId);
    $bookingId = intval($bookingId);

    $sql = "
        SELECT
            id,
            guest_id,
            room_id,
            checkin_date,
            status
        FROM bookings
        WHERE id = ?
        AND guest_id = ?
        LIMIT 1
    ";

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        return "failed";
    }

    $stmt->bind_param("ii", $bookingId, $guestId);
    $stmt->execute();

    $result = $stmt->get_result();
    $booking = $result->fetch_assoc();

    if (!$booking) {
        return "not_found";
    }


    if ($booking["status"] !== "confirmed") {
        return "invalid_status";
    }


    $settingSql = "
        SELECT setting_value
        FROM system_settings
        WHERE setting_key = 'cancellation_days_before_checkin'
        LIMIT 1
    ";

    $settingStmt = $conn->prepare($settingSql);

    if (!$settingStmt) {
        return "failed";
    }

    $settingStmt->execute();

    $settingResult = $settingStmt->get_result();
    $setting = $settingResult->fetch_assoc();

    $cancellationDays = 2;

    if ($setting) {
        $cancellationDays = intval($setting["setting_value"]);
    }

    $today = new DateTime("today");
    $checkin = new DateTime($booking["checkin_date"]);

    $interval = $today->diff($checkin);

    $daysRemaining = (int) $interval->format("%r%a");


    if ($daysRemaining < $cancellationDays) {
        return "too_late";
    }

    $updateSql = "
        UPDATE bookings
        SET status = 'cancelled'
        WHERE id = ?
        AND guest_id = ?
        AND status = 'confirmed'
    ";

    $updateStmt = $conn->prepare($updateSql);

    if (!$updateStmt) {
        return "failed";
    }

    $updateStmt->bind_param(
        "ii",
        $bookingId,
        $guestId
    );

    if (!$updateStmt->execute()) {
        return "failed";
    }



    if ($updateStmt->affected_rows > 0) {
        return "cancelled";
    }

    return "failed";
}

    
function createBookingModificationRequest($guestId, $bookingId, $newCheckin, $newCheckout, $reason)
{
    global $conn;

    $sql = "INSERT INTO booking_modification_requests
            (booking_id, guest_id, requested_checkin_date, requested_checkout_date, reason, status)
            VALUES (?, ?, ?, ?, ?, 'pending')";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "iisss",
        $bookingId,
        $guestId,
        $newCheckin,
        $newCheckout,
        $reason
    );

    try {
        return $stmt->execute();
    } catch (mysqli_sql_exception $e) {
        return false;
    }
}

function getBookingModificationRequests($guestId)
{
    global $conn;

    $guestId = intval($guestId);

    $sql = "
        SELECT
            bmr.id,
            bmr.booking_id,
            bmr.requested_checkin_date,
            bmr.requested_checkout_date,
            bmr.reason,
            bmr.status,
            b.checkin_date,
            b.checkout_date

        FROM booking_modification_requests bmr

        INNER JOIN bookings b
            ON b.id = bmr.booking_id

        WHERE bmr.guest_id = ?

        ORDER BY bmr.id DESC
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $guestId);
    $stmt->execute();

    return $stmt->get_result();
}

  
function getBookingStatistics()
{
    global $conn;

    $stats = [
        "total" => 0,
        "confirmed" => 0,
        "pending" => 0,
        "checked_in" => 0,
        "completed" => 0,
        "cancelled" => 0
    ];

    $sql = "SELECT status, COUNT(*) AS total
            FROM bookings
            GROUP BY status";

    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        if ($row["status"] == "confirmed") {
            $stats["confirmed"] = $row["total"];
        }

        if ($row["status"] == "pending") {
            $stats["pending"] = $row["total"];
        }

        if ($row["status"] == "checked_in") {
            $stats["checked_in"] = $row["total"];
        }

        if ($row["status"] == "checked_out") {
            $stats["completed"] = $row["total"];
        }

        if ($row["status"] == "cancelled") {
            $stats["cancelled"] = $row["total"];
        }

        $stats["total"] += $row["total"];
    }

    return $stats;
}

function getRoomTypesForBookingFilter()
{
    global $conn;

    $sql = "SELECT id, name FROM room_types ORDER BY name ASC";

    $stmt = $conn->prepare($sql);
    $stmt->execute();

    return $stmt->get_result();
}



?>