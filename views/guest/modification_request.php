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


$bookingId = $_GET["id"] ?? "";
$requests = getBookingModificationRequests($_SESSION["user_id"]);

require_once "../../app/layouts/header.php";
require_once "../../app/layouts/sidebar.php";
require_once "../../app/layouts/navbar.php";

?>

<div class="main-content">

    <div class="dashboard-title">
        <h1>Booking Modification Request</h1>
        <p>Request date changes for your booking. Receptionist will review your request.</p>
    </div>

    <?php if (isset($_GET["success"])) { ?>
        <div class="success-message">
            Modification request submitted successfully.
        </div>
    <?php } ?>

    <?php if ($bookingId != "") { ?>

        <div class="form-box">
            <h2>Request New Dates</h2>

            <form method="POST" action="../../controllers/guestController.php">

                <input type="hidden" name="action" value="request_modification">
                <input type="hidden" name="booking_id" value="<?php echo htmlspecialchars($bookingId); ?>">

                <div class="form-row">

                    <div class="form-group">
                        <label>New Check-in Date</label>
                        <input type="date" name="new_checkin_date" required>
                    </div>

                    <div class="form-group">
                        <label>New Check-out Date</label>
                        <input type="date" name="new_checkout_date" required>
                    </div>

                    <div class="form-group">
                        <label>Reason</label>
                        <textarea name="reason" placeholder="Explain why you want to modify the booking"></textarea>
                    </div>

                </div>

                <button type="submit" class="print-btn">Submit Request</button>

            </form>
        </div>

    <?php } ?>

    <div class="dashboard-table">

        <h2>My Modification Requests</h2>

        <table>
            <thead>
                <tr>
                    <th>Booking ID</th>
                    <th>Old Check In</th>
                    <th>Old Check Out</th>
                    <th>Requested Check In</th>
                    <th>Requested Check Out</th>
                    <th>Reason</th>
                    <th>Status</th>
                </tr>
            </thead>

            <tbody>

                <?php if ($requests && $requests->num_rows > 0) { ?>

                    <?php while ($row = $requests->fetch_assoc()) { ?>

                        <tr>
                            <td>#B<?php echo $row["booking_id"]; ?></td>

                            <td><?php echo $row["checkin_date"]; ?></td>

                            <td><?php echo $row["checkout_date"]; ?></td>

                            <td>
                                <?php
                                echo $row["requested_checkin_date"] ?? "N/A";
                                ?>
                            </td>

                            <td>
                                <?php
                                echo $row["requested_checkout_date"] ?? "N/A";
                                ?>
                            </td>

                            <td>
                                <?php
                                echo htmlspecialchars($row["reason"] ?? "N/A");
                                ?>
                            </td>

                            <td>
                                <span class="status-badge status-<?php echo $row["status"]; ?>">
                                    <?php echo ucfirst($row["status"]); ?>
                                </span>
                            </td>
                        </tr>

                    <?php } ?>

                <?php } else { ?>

                    <tr>
                        <td colspan="7">No modification requests found.</td>
                    </tr>

                <?php } ?>

            </tbody>
        </table>

    </div>

</div>

<?php require_once "../../app/layouts/footer.php"; ?>