<?php

require_once "../../app/helpers/session.php";

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "receptionist") {
    header("Location: ../auth/login.php");
    exit();
}

require_once "../../models/receptionistModel.php";

$billId = intval($_GET["bill_id"] ?? 0);
$bill = getReceptionistReceipt($billId);

?>

<!DOCTYPE html>
<html>
<head>
    <title>Receipt</title>
    <link rel="stylesheet" href="../../assets/css/receptionist.css?v=2">
</head>
<body>

<div class="reception-main" style="margin-left:0; width:100%;">

    <div class="reception-table">

        <h2>Grand Palace Hotel Receipt</h2>

        <?php if ($bill) { ?>

            <table>
                <tr>
                    <th>Receipt ID</th>
                    <td>#RC<?php echo $bill["id"]; ?></td>
                </tr>

                <tr>
                    <th>Guest</th>
                    <td><?php echo htmlspecialchars($bill["guest_name"]); ?></td>
                </tr>

                <tr>
                    <th>Booking ID</th>
                    <td>#B<?php echo $bill["booking_id"]; ?></td>
                </tr>

                <tr>
                    <th>Room Type</th>
                    <td><?php echo htmlspecialchars($bill["room_type"]); ?></td>
                </tr>

                <tr>
                    <th>Dates</th>
                    <td><?php echo $bill["checkin_date"] . " to " . $bill["checkout_date"]; ?></td>
                </tr>

                <tr>
                    <th>Base Amount</th>
                    <td>BDT <?php echo number_format($bill["base_amount"], 2); ?></td>
                </tr>

                <tr>
                    <th>Extras</th>
                    <td>BDT <?php echo number_format($bill["extras_amount"], 2); ?></td>
                </tr>

                <tr>
                    <th>Discount</th>
                    <td>BDT <?php echo number_format($bill["discount_amount"], 2); ?></td>
                </tr>

                <tr>
                    <th>Total</th>
                    <td>BDT <?php echo number_format($bill["total_amount"], 2); ?></td>
                </tr>

                <tr>
                    <th>Payment Status</th>
                    <td><?php echo ucfirst($bill["payment_status"]); ?></td>
                </tr>
            </table>

        <?php } else { ?>

            <div class="error-message">
                Receipt not found.
            </div>

        <?php } ?>

    </div>

</div>

</body>
</html>