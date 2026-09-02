<?php
require_once "../../app/helpers/session.php";

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "admin") {
    header("Location: ../auth/login.php");
    exit();
}

require_once "../../models/reportModel.php";

$summary = getOccupancySummary();
$popularRooms = getPopularRoomTypes();
$peakMonths = getPeakBookingMonths();

$totalRooms = $summary["total_rooms"];
$occupiedRooms = $summary["occupied_rooms"];
$occupancyRate = 0;

if ($totalRooms > 0) {
    $occupancyRate = ($occupiedRooms / $totalRooms) * 100;
}

require_once "../../app/layouts/header.php";
require_once "../../app/layouts/sidebar.php";
require_once "../../app/layouts/navbar.php";
?>

<div class="main-content">

    <div class="dashboard-title">
        <h1>Occupancy Reports</h1>
        <p>Room occupancy rate, room availability, popular room types, and peak months.</p>
    </div>

    <button onclick="window.print()" class="print-btn">Print Report</button>

    <div class="card-container">
        <div class="dashboard-card">
            <h3>Total Rooms</h3>
            <h2><?php echo $summary["total_rooms"]; ?></h2>
        </div>

        <div class="dashboard-card">
            <h3>Occupied Rooms</h3>
            <h2><?php echo $summary["occupied_rooms"]; ?></h2>
        </div>

        <div class="dashboard-card">
            <h3>Available Rooms</h3>
            <h2><?php echo $summary["available_rooms"]; ?></h2>
        </div>

        <div class="dashboard-card">
            <h3>Occupancy Rate</h3>
            <h2><?php echo round($occupancyRate, 2); ?>%</h2>
        </div>
    </div>

    <div class="dashboard-table">
        <h2>Most Popular Room Types</h2>

        <table>
            <thead>
                <tr>
                    <th>Room Type</th>
                    <th>Total Bookings</th>
                </tr>
            </thead>

            <tbody>
                <?php while ($row = $popularRooms->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row["name"]); ?></td>
                        <td><?php echo $row["total_bookings"]; ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <div class="dashboard-table">
        <h2>Peak Booking Months</h2>

        <table>
            <thead>
                <tr>
                    <th>Month</th>
                    <th>Total Bookings</th>
                </tr>
            </thead>

            <tbody>
                <?php while ($row = $peakMonths->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo $row["booking_month"]; ?></td>
                        <td><?php echo $row["total_bookings"]; ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

</div>

<?php require_once "../../app/layouts/footer.php"; ?>