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
        <h1>Booking Details</h1>
        <p>View reservation details, billing status, cancellation policy and modification option.</p>
    </div>

    <?php if (isset($_GET["cancel"])) { ?>
        <div class="success-message">
            <?php
            if ($_GET["cancel"] == "cancelled") {
                echo "Booking cancelled successfully.";
            } elseif ($_GET["cancel"] == "too_late") {
                echo "Cancellation failed. You cannot cancel within 2 days of check-in.";
            } elseif ($_GET["cancel"] == "invalid_status") {
                echo "Only confirmed bookings can be cancelled.";
            } else {
                echo "Cancellation request could not be processed.";
            }
            ?>
        </div>
    <?php } ?>

    <div class="dashboard-table">

        <h2>Reservation Information</h2>

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
                <th>Room Number</th>
                <td><?php echo $booking["room_number"] ?? "Not Assigned"; ?></td>
            </tr>

            <tr>
                <th>Check In</th>
                <td><?php echo $booking["checkin_date"]; ?></td>
            </tr>

            <tr>
                <th>Check Out</th>
                <td><?php echo $booking["checkout_date"]; ?></td>
            </tr>

            <tr>
                <th>Total Price</th>
                <td>৳ <?php echo number_format($booking["total_price"], 2); ?></td>
            </tr>

            <tr>
                <th>Payment Status</th>
                <td><?php echo ucfirst($booking["payment_status"] ?? "Pending"); ?></td>
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

    </div>

    <div class="dashboard-table">

        <h2>Cancellation Policy</h2>

        <p>
            You can cancel a confirmed booking only before 2 days of check-in.
            Cancellation is not allowed within 2 days of arrival.
        </p>

        <br>

        <?php if ($booking["status"] == "confirmed") { ?>
            <form method="POST" action="../../controllers/guestController.php">
                <input type="hidden" name="action" value="cancel_booking">
                <input type="hidden" name="booking_id" value="<?php echo $booking["id"]; ?>">
                <button type="submit" class="delete-btn" onclick="return confirm('Are you sure you want to cancel this booking?')">
                    Cancel Booking
                </button>
            </form>
        <?php } ?>

        <br>

        <a href="modification_request.php?id=<?php echo $booking["id"]; ?>" class="edit-btn">
            Request Date Modification
        </a>

    </div>

</div>

<?php require_once "../../app/layouts/footer.php"; ?>