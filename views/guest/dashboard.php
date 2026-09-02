<?php

require_once "../../app/helpers/session.php";

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "guest") {
    header("Location: ../auth/login.php");
    exit();
}

require_once "../../models/userModel.php";
require_once "../../models/roomModel.php";
require_once "../../models/bookingModel.php";
require_once "../../models/pricingModel.php";


$data = getGuestDashboardData($_SESSION["user_id"]);

function getGuestDashboardData($userId)
{
    global $conn;

    $userId = intval($userId);

    $data = [
        "total_bookings" => 0,
        "active_bookings" => 0,
        "loyalty_balance" => 0,
        "service_requests" => 0,
        "pending_bills" => 0
    ];

    $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM bookings WHERE guest_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $data["total_bookings"] = $stmt->get_result()->fetch_assoc()["total"];

    $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM bookings WHERE guest_id = ? AND status IN ('confirmed', 'checked_in')");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $data["active_bookings"] = $stmt->get_result()->fetch_assoc()["total"];

    $stmt = $conn->prepare("
        SELECT COUNT(*) AS total
        FROM billing
        INNER JOIN bookings b ON b.id = billing.booking_id
        WHERE b.guest_id = ? AND billing.payment_status = 'pending'
    ");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $data["pending_bills"] = $stmt->get_result()->fetch_assoc()["total"];

    $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM service_requests WHERE guest_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $data["service_requests"] = $stmt->get_result()->fetch_assoc()["total"];

    $stmt = $conn->prepare("SELECT balance FROM loyalty_points WHERE guest_id = ? ORDER BY id DESC LIMIT 1");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $data["loyalty_balance"] = $row["balance"];
    }

    return $data;
}

require_once "../../app/layouts/header.php";
require_once "../../app/layouts/sidebar.php";
require_once "../../app/layouts/navbar.php";

?>

<div class="main-content">

    <div class="dashboard-title">
        <h1>Guest Dashboard</h1>
        <p>Welcome to your hotel account. Manage bookings, services, billing and loyalty points.</p>
    </div>

    <div class="card-container">

        <div class="dashboard-card">
            <h3>Total Bookings</h3>
            <h2><?php echo $data["total_bookings"]; ?></h2>
        </div>

        <div class="dashboard-card">
            <h3>Active Bookings</h3>
            <h2><?php echo $data["active_bookings"]; ?></h2>
        </div>

        <div class="dashboard-card">
            <h3>Pending Bills</h3>
            <h2><?php echo $data["pending_bills"]; ?></h2>
        </div>

    </div>

    <div class="dashboard-table">
        <h2>Guest Quick Actions</h2>

        <table>
            <thead>
                <tr>
                    <th>Feature</th>
                    <th>Description</th>
                    <th>Open</th>
                </tr>
            </thead>

            <tbody>
                <tr>
                    <td>Search Rooms</td>
                    <td>Find available rooms by check-in, check-out and guests.</td>
                    <td><a href="search_rooms.php" class="edit-btn">Open</a></td>
                </tr>

                <tr>
                    <td>My Bookings</td>
                    <td>View upcoming and past reservations.</td>
                    <td><a href="my_bookings.php" class="edit-btn">Open</a></td>
                </tr>

                <tr>
                    <td>Billing History</td>
                    <td>View invoices, payment status and receipts.</td>
                    <td><a href="billing.php" class="edit-btn">Open</a></td>
                </tr>
            </tbody>
        </table>
    </div>

</div>

<?php require_once "../../app/layouts/footer.php"; ?>