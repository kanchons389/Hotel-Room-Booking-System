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


$billingId = intval($_GET["id"]);

$receipt = getGuestReceiptDetails($_SESSION["user_id"], $billingId);

require_once "../../app/layouts/header.php";
require_once "../../app/layouts/sidebar.php";
require_once "../../app/layouts/navbar.php";

?>

<div class="main-content">

    <div class="dashboard-title">
        <h1>Receipt View</h1>
        <p>Printable invoice and payment receipt details.</p>
    </div>


    <?php if (!$receipt) { ?>

        <div class="error-message">
            Receipt not found.
        </div>

    <?php } else { ?>

        <div class="dashboard-table receipt-box">

            <h2>Grand Palace Hotel Receipt</h2>

            <table>
                <tr>
                    <th>Invoice ID</th>
                    <td>#INV<?php echo $receipt["id"]; ?></td>
                </tr>

                <tr>
                    <th>Booking ID</th>
                    <td>#B<?php echo $receipt["booking_number"]; ?></td>
                </tr>

                <tr>
                    <th>Guest Name</th>
                    <td><?php echo htmlspecialchars($receipt["guest_name"]); ?></td>
                </tr>

                <tr>
                    <th>Email</th>
                    <td><?php echo htmlspecialchars($receipt["guest_email"]); ?></td>
                </tr>

                <tr>
                    <th>Phone</th>
                    <td><?php echo htmlspecialchars($receipt["guest_phone"]); ?></td>
                </tr>

                <tr>
                    <th>Nationality</th>
                    <td><?php echo htmlspecialchars($receipt["nationality"]); ?></td>
                </tr>

                <tr>
                    <th>ID Number</th>
                    <td><?php echo htmlspecialchars($receipt["id_number"]); ?></td>
                </tr>

                <tr>
                    <th>Room Type</th>
                    <td><?php echo htmlspecialchars($receipt["room_type_name"]); ?></td>
                </tr>

                <tr>
                    <th>Room Number</th>
                    <td>
                        <?php
                        if (!empty($receipt["room_number"])) {
                            echo htmlspecialchars($receipt["room_number"]);
                        } else {
                            echo "Not Assigned";
                        }
                        ?>
                    </td>
                </tr>

                <tr>
                    <th>Check In</th>
                    <td><?php echo $receipt["checkin_date"]; ?></td>
                </tr>

                <tr>
                    <th>Check Out</th>
                    <td><?php echo $receipt["checkout_date"]; ?></td>
                </tr>

                <tr>
                    <th>Guests</th>
                    <td><?php echo $receipt["num_guests"]; ?></td>
                </tr>

                <tr>
                    <th>Base Amount</th>
                    <td>৳ <?php echo number_format($receipt["base_amount"], 2); ?></td>
                </tr>

                <tr>
                    <th>Extras Amount</th>
                    <td>৳ <?php echo number_format($receipt["extras_amount"], 2); ?></td>
                </tr>

                <tr>
                    <th>Discount Amount</th>
                    <td>৳ <?php echo number_format($receipt["discount_amount"], 2); ?></td>
                </tr>

                <tr>
                    <th>Total Amount</th>
                    <td>৳ <?php echo number_format($receipt["total_amount"], 2); ?></td>
                </tr>

                <tr>
                    <th>Payment Method</th>
                    <td>
                        <?php
                        if (!empty($receipt["payment_method"])) {
                            echo ucfirst($receipt["payment_method"]);
                        } else {
                            echo "N/A";
                        }
                        ?>
                    </td>
                </tr>

                <tr>
                    <th>Payment Status</th>
                    <td>
                        <span class="status-badge status-<?php echo $receipt["payment_status"]; ?>">
                            <?php echo ucfirst($receipt["payment_status"]); ?>
                        </span>
                    </td>
                </tr>

                <tr>
                    <th>Paid At</th>
                    <td>
                        <?php
                        if (!empty($receipt["paid_at"])) {
                            echo $receipt["paid_at"];
                        } else {
                            echo "Not Paid Yet";
                        }
                        ?>
                    </td>
                </tr>
            </table>

            <br>

            <p>
                This receipt is generated from the Grand Palace Hotel room booking system.
            </p>

        </div>

    <?php } ?>

</div>

<?php require_once "../../app/layouts/footer.php"; ?>