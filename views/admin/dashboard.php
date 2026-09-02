<?php

require_once "../../app/helpers/session.php";

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "admin") {
    header("Location: ../auth/login.php");
    exit();
}

require_once "../../config/database.php";

$totalRooms = 0;
$availableRooms = 0;
$occupiedRooms = 0;
$maintenanceRooms = 0;
$totalBookings = 0;
$totalUsers = 0;

$result = $conn->query("SELECT COUNT(*) AS total FROM rooms");
if ($result) {
    $totalRooms = $result->fetch_assoc()["total"];
}

$result = $conn->query("SELECT COUNT(*) AS total FROM rooms WHERE status = 'available'");
if ($result) {
    $availableRooms = $result->fetch_assoc()["total"];
}

$result = $conn->query("SELECT COUNT(*) AS total FROM rooms WHERE status = 'occupied'");
if ($result) {
    $occupiedRooms = $result->fetch_assoc()["total"];
}

$result = $conn->query("SELECT COUNT(*) AS total FROM rooms WHERE status = 'maintenance'");
if ($result) {
    $maintenanceRooms = $result->fetch_assoc()["total"];
}

$result = $conn->query("SELECT COUNT(*) AS total FROM bookings");
if ($result) {
    $totalBookings = $result->fetch_assoc()["total"];
}

$result = $conn->query("SELECT COUNT(*) AS total FROM users");
if ($result) {
    $totalUsers = $result->fetch_assoc()["total"];
}

$recentBookings = $conn->query("
    SELECT 
        bookings.id,
        users.name AS guest_name,
        room_types.name AS room_type,
        bookings.checkin_date,
        bookings.checkout_date,
        bookings.status
    FROM bookings
    INNER JOIN users ON bookings.guest_id = users.id
    INNER JOIN room_types ON bookings.room_type_id = room_types.id
    ORDER BY bookings.id DESC
    LIMIT 5
");

$recentUsers = $conn->query("
    SELECT
        name,
        role,
        created_at
    FROM users
    ORDER BY id DESC
    LIMIT 5
");

$recentAnnouncements = $conn->query("
    SELECT
        title,
        created_at
    FROM announcements
    ORDER BY id DESC
    LIMIT 5
");

require_once "../../app/layouts/header.php";
require_once "../../app/layouts/sidebar.php";
require_once "../../app/layouts/navbar.php";

?>

<div class="main-content">

    <div class="dashboard-title">
        <h1>Admin Dashboard</h1>
        <p>Welcome back, <?php echo $_SESSION["name"]; ?>. Here is today’s hotel overview.</p>
    </div>

    <div class="card-container">

        <div class="dashboard-card">
            <h3>Total Rooms</h3>
            <h2><?php echo $totalRooms; ?></h2>
        </div>

        <div class="dashboard-card">
            <h3>Available Rooms</h3>
            <h2><?php echo $availableRooms; ?></h2>
        </div>

        <div class="dashboard-card">
            <h3>Occupied Rooms</h3>
            <h2><?php echo $occupiedRooms; ?></h2>
        </div>

        <div class="dashboard-card">
            <h3>Maintenance Rooms</h3>
            <h2><?php echo $maintenanceRooms; ?></h2>
        </div>

        <div class="dashboard-card">
            <h3>Total Bookings</h3>
            <h2><?php echo $totalBookings; ?></h2>
        </div>

        <div class="dashboard-card">
            <h3>Total Users</h3>
            <h2><?php echo $totalUsers; ?></h2>
        </div>

    </div>

    <div class="dashboard-table">
        <h2>Recent Bookings</h2>
        <br>

        <table>
            <thead>
                <tr>
                    <th>Booking ID</th>
                    <th>Guest</th>
                    <th>Room Type</th>
                    <th>Check-in</th>
                    <th>Check-out</th>
                    <th>Status</th>
                </tr>
            </thead>

            <tbody>
                <?php

                if ($recentBookings && $recentBookings->num_rows > 0) {

                    while ($booking = $recentBookings->fetch_assoc()) {

                        echo "<tr>";
                        echo "<td>#BK-" . $booking["id"] . "</td>";
                        echo "<td>" . $booking["guest_name"] . "</td>";
                        echo "<td>" . $booking["room_type"] . "</td>";
                        echo "<td>" . $booking["checkin_date"] . "</td>";
                        echo "<td>" . $booking["checkout_date"] . "</td>";

                        echo "<td>
                                <span class='status-badge status-" . $booking["status"] . "'>
                                    " . ucfirst($booking["status"]) . "
                                </span>
                              </td>";

                        echo "</tr>";
                    }

                } else {

                    echo "<tr>";
                    echo "<td colspan='6'>No recent bookings found.</td>";
                    echo "</tr>";
                }

                ?>
            </tbody>
        </table>
    </div>

    <br>

    <div class="dashboard-table">
        <h2>Recent Users</h2>
        <br>

        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Role</th>
                    <th>Created At</th>
                </tr>
            </thead>

            <tbody>
                <?php

                if ($recentUsers && $recentUsers->num_rows > 0) {

                    while ($user = $recentUsers->fetch_assoc()) {

                        echo "<tr>";
                        echo "<td>" . $user["name"] . "</td>";
                        echo "<td>" . ucfirst($user["role"]) . "</td>";
                        echo "<td>" . $user["created_at"] . "</td>";
                        echo "</tr>";
                    }

                } else {

                    echo "<tr>";
                    echo "<td colspan='3'>No recent users found.</td>";
                    echo "</tr>";
                }

                ?>
            </tbody>
        </table>
    </div>

</div>

<?php

require_once "../../app/layouts/footer.php";

?>