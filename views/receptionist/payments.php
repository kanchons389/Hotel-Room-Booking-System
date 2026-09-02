<?php

require_once "../../app/helpers/session.php";

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "receptionist") {
    header("Location: ../auth/login.php");
    exit();
}

require_once "../../models/receptionistModel.php";

$payments = getPendingPayments();

?>

<!DOCTYPE html>
<html>
<head>
    <title>Payments</title>
    <link rel="stylesheet" href="../../assets/css/receptionist.css?v=2">
</head>

<body>

<div class="reception-wrapper">

    <div class="reception-sidebar">

        <div class="reception-logo">
            <h2>GPH</h2>
            <p>Reception Desk</p>
        </div>

        <ul class="reception-menu">
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="today_checkins.php">Today Check-ins</a></li>
            <li><a href="today_checkouts.php">Today Check-outs</a></li>
            <li><a href="payments.php">Payments</a></li>
            <li><a href="walkin_booking.php">Walk-in Booking</a></li>
            <li><a href="booking_modifications.php">Booking Changes</a></li>
           
        </ul>

    </div>

    <div class="reception-main">

        <div class="reception-top">

            <div>
                <h1>Payment Management</h1>
                <p>Process guest bills and manage payment records.</p>
            </div>

            <a href="../../logout.php" class="reception-logout">Logout</a>

        </div>

        <?php if (isset($_SESSION["success"])) { ?>
            <div class="success-message">
                <?php
                echo $_SESSION["success"];
                unset($_SESSION["success"]);
                ?>
            </div>
        <?php } ?>

        <?php if (isset($_SESSION["error"])) { ?>
            <div class="error-message">
                <?php
                echo $_SESSION["error"];
                unset($_SESSION["error"]);
                ?>
            </div>
        <?php } ?>

        <div class="reception-table">

            <h2>Billing Records</h2>

            <input
                type="text"
                id="guestSearch"
                class="search-box"
                placeholder="Search guest, booking or payment..."
            >

            <table>
                <thead>
                    <tr>
                        <th>Bill ID</th>
                        <th>Booking</th>
                        <th>Guest</th>
                        <th>Base</th>
                        <th>Extras</th>
                        <th>Discount</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Payment</th>
                    </tr>
                </thead>

                <tbody>

                    <?php if ($payments && $payments->num_rows > 0) { ?>

                        <?php while ($row = $payments->fetch_assoc()) { ?>

                            <tr>
                                <td>#BL<?php echo $row["bill_id"]; ?></td>

                                <td>#B<?php echo $row["booking_id"]; ?></td>

                                <td><?php echo htmlspecialchars($row["guest_name"]); ?></td>

                                <td>৳ <?php echo number_format($row["base_amount"], 2); ?></td>

                                <td>৳ <?php echo number_format($row["extras_amount"], 2); ?></td>

                                <td>৳ <?php echo number_format($row["discount_amount"], 2); ?></td>

                                <td>৳ <?php echo number_format($row["total_amount"], 2); ?></td>

                                <td>
                                    <span class="reception-badge badge-<?php echo $row["payment_status"]; ?>">
                                        <?php echo ucfirst($row["payment_status"]); ?>
                                    </span>
                                </td>

                                <td>
                                    <?php if ($row["payment_status"] == "pending") { ?>

                                        <form action="../../controllers/receptionistController.php" method="POST" style="margin-bottom:10px;">

                                            <input type="hidden" name="action" value="process_payment">
                                            <input type="hidden" name="bill_id" value="<?php echo $row["bill_id"]; ?>">

                                            <select name="payment_method" required>
                                                <option value="">Method</option>
                                                <option value="cash">Cash</option>
                                                <option value="card">Card</option>
                                                <option value="bkash">bKash</option>
                                                <option value="nagad">Nagad</option>
                                                <option value="bank_transfer">Bank Transfer</option>
                                            </select>

                                            <button type="submit" class="edit-btn">Pay</button>

                                        </form>

                                        <a href="receipt.php?bill_id=<?php echo $row["bill_id"]; ?>" class="reception-logout">
                                            View Receipt
                                        </a>

                                    <?php } else { ?>

                                        <span class="reception-badge badge-confirmed">
                                            Paid
                                        </span>

                                        <br><br>

                                        <a href="receipt.php?bill_id=<?php echo $row["bill_id"]; ?>" class="reception-logout">
                                            View Receipt
                                        </a>

                                    <?php } ?>
                                </td>
                            </tr>

                        <?php } ?>

                    <?php } else { ?>

                        <tr>
                            <td colspan="9">No billing records found.</td>
                        </tr>

                    <?php } ?>

                </tbody>
            </table>

        </div>

    </div>

</div>

<script src="../../assets/js/receptionist.js"></script>

</body>
</html>