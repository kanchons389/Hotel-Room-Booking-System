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


$bookingId = intval($_GET["id"]);
$booking = getGuestBookingDetails($_SESSION["user_id"], $bookingId);

require_once "../../app/layouts/header.php";
require_once "../../app/layouts/sidebar.php";
require_once "../../app/layouts/navbar.php";

?>

<div class="main-content">

    <div class="dashboard-title">
        <h1>Booking Confirmed</h1>
        <p>Your reservation has been created successfully.</p>
    </div>

    <div class="success-message">
        Booking ID #B<?php echo $booking["id"]; ?> has been confirmed.
    </div>

    <div class="dashboard-table">

        <h2>Confirmation Details</h2>

        <table>
            <tr>
                <th>Booking ID</th>
                <td>#B<?php echo $booking["id"]; ?></td>
            </tr>

            <tr>
                <th>Room Type</th>
                <td><?php echo htmlspecialchars($booking["room_type_name"]); ?></td>
            </tr>

            <tr>
                <th>Dates</th>
                <td><?php echo $booking["checkin_date"]; ?> to <?php echo $booking["checkout_date"]; ?></td>
            </tr>

            <tr>
                <th>Total Price</th>
                <td>৳ <?php echo number_format($booking["total_price"], 2); ?></td>
            </tr>

            <tr>
                <th>Status</th>
                <td>
                    <span class="status-badge status-<?php echo $booking["status"]; ?>">
                        <?php echo ucfirst(str_replace("_", " ", $booking["status"])); ?>
                    </span>
                </td>
            </tr>
        </table>

        <br>

        <a href="my_bookings.php" class="edit-btn">View My Bookings</a>

    </div>

</div>

<?php require_once "../../app/layouts/footer.php"; ?>