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



$search = $_GET["search"] ?? "";
$status = $_GET["status"] ?? "";
$fromDate = $_GET["from_date"] ?? "";
$toDate = $_GET["to_date"] ?? "";

$summary = getGuestBillingSummary($_SESSION["user_id"]);

$bills = getGuestBillingHistory(
    $_SESSION["user_id"],
    $search,
    $status,
    $fromDate,
    $toDate
);

require_once "../../app/layouts/header.php";
require_once "../../app/layouts/sidebar.php";
require_once "../../app/layouts/navbar.php";

?>

<div class="main-content">

    <div class="dashboard-title">
        <h1>Billing History</h1>
        <p>View invoices, payment status and printable receipt details.</p>
    </div>

    <div class="card-container">

        <div class="dashboard-card">
            <h3>Total Bills</h3>
            <h2><?php echo $summary["total_bills"]; ?></h2>
        </div>

        <div class="dashboard-card">
            <h3>Total Invoice</h3>
            <h2>৳ <?php echo number_format($summary["total_invoice"], 2); ?></h2>
        </div>

        <div class="dashboard-card">
            <h3>Paid Amount</h3>
            <h2>৳ <?php echo number_format($summary["paid_amount"], 2); ?></h2>
        </div>

        <div class="dashboard-card">
            <h3>Pending Amount</h3>
            <h2>৳ <?php echo number_format($summary["pending_amount"], 2); ?></h2>
        </div>

    </div>

    <div class="dashboard-table">

        <h2>Search Billing History</h2>

        <form method="GET" class="filter-form">

            <input
                type="text"
                name="search"
                placeholder="Search booking ID, room type, room number, method..."
                value="<?php echo htmlspecialchars($search); ?>"
            >

            <select name="status">
                <option value="">All Payment Status</option>

                <option value="pending" <?php if ($status == "pending") echo "selected"; ?>>
                    Pending
                </option>

                <option value="paid" <?php if ($status == "paid") echo "selected"; ?>>
                    Paid
                </option>
            </select>

            <input
                type="date"
                name="from_date"
                value="<?php echo htmlspecialchars($fromDate); ?>"
            >

            <input
                type="date"
                name="to_date"
                value="<?php echo htmlspecialchars($toDate); ?>"
            >

            <button type="submit">Search</button>

            <a href="billing.php" class="clear-btn">Clear</a>

        </form>

    </div>

    <div class="dashboard-table">

        <h2>Invoices</h2>

        <table>
            <thead>
                <tr>
                    <th>Invoice</th>
                    <th>Booking</th>
                    <th>Room Type</th>
                    <th>Room</th>
                    <th>Base</th>
                    <th>Extras</th>
                    <th>Discount</th>
                    <th>Total</th>
                    <th>Method</th>
                    <th>Status</th>
                    <th>Receipt</th>
                </tr>
            </thead>

            <tbody>

                <?php if ($bills->num_rows > 0) { ?>

                    <?php while ($row = $bills->fetch_assoc()) { ?>

                        <tr>
                            <td>#INV<?php echo $row["id"]; ?></td>

                            <td>#B<?php echo $row["booking_number"]; ?></td>

                            <td><?php echo htmlspecialchars($row["room_type_name"]); ?></td>

                            <td>
                                <?php
                                if (!empty($row["room_number"])) {
                                    echo htmlspecialchars($row["room_number"]);
                                } else {
                                    echo "Not Assigned";
                                }
                                ?>
                            </td>

                            <td>৳ <?php echo number_format($row["base_amount"], 2); ?></td>

                            <td>৳ <?php echo number_format($row["extras_amount"], 2); ?></td>

                            <td>৳ <?php echo number_format($row["discount_amount"], 2); ?></td>

                            <td>৳ <?php echo number_format($row["total_amount"], 2); ?></td>

                            <td>
                                <?php
                                if (!empty($row["payment_method"])) {
                                    echo ucfirst($row["payment_method"]);
                                } else {
                                    echo "N/A";
                                }
                                ?>
                            </td>

                            <td>
                                <span class="status-badge status-<?php echo $row["payment_status"]; ?>">
                                    <?php echo ucfirst($row["payment_status"]); ?>
                                </span>
                            </td>

                            <td>
                                <a href="receipt.php?id=<?php echo $row["id"]; ?>" class="edit-btn">
                                    View
                                </a>
                            </td>
                        </tr>

                    <?php } ?>

                <?php } else { ?>

                    <tr>
                        <td colspan="11">No billing records found.</td>
                    </tr>

                <?php } ?>

            </tbody>
        </table>

    </div>

</div>

<?php require_once "../../app/layouts/footer.php"; ?>