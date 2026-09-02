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

$bookings = getGuestBookings(
    $_SESSION["user_id"],
    [
        "search" => $search,
        "status" => $status,
        "from_date" => $fromDate,
        "to_date" => $toDate
    ]
);


require_once "../../app/layouts/header.php";
require_once "../../app/layouts/sidebar.php";
require_once "../../app/layouts/navbar.php";

?>

<div class="main-content">

    <div class="dashboard-title">
        <h1>My Bookings</h1>
        <p>View, search and filter your upcoming and past reservations.</p>
    </div>

    <div class="dashboard-table">

        <h2>Search Bookings</h2>

        <form method="GET" class="filter-form">

            <input
                type="text"
                name="search"
                placeholder="Search booking ID, room type, room number..."
                value="<?php echo htmlspecialchars($search); ?>"
            >

            <select name="status">
                <option value="">All Status</option>

                <option value="pending" <?php if ($status == "pending") echo "selected"; ?>>
                    Pending
                </option>

                <option value="confirmed" <?php if ($status == "confirmed") echo "selected"; ?>>
                    Confirmed
                </option>

                <option value="checked_in" <?php if ($status == "checked_in") echo "selected"; ?>>
                    Checked In
                </option>

                <option value="checked_out" <?php if ($status == "checked_out") echo "selected"; ?>>
                    Checked Out
                </option>

                <option value="cancelled" <?php if ($status == "cancelled") echo "selected"; ?>>
                    Cancelled
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

            <a href="my_bookings.php" class="clear-btn">Clear</a>

        </form>

    </div>

    <div class="dashboard-table">

        <h2>Booking History</h2>

        <table>
            <thead>
                <tr>
                    <th>Booking ID</th>
                    <th>Room Type</th>
                    <th>Room</th>
                    <th>Check In</th>
                    <th>Check Out</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Details</th>
                </tr>
            </thead>

            <tbody>
                <?php if ($bookings->num_rows > 0) { ?>

                    <?php while ($row = $bookings->fetch_assoc()) { ?>

                        <tr>
                            <td>#B<?php echo $row["id"]; ?></td>

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

                            <td><?php echo $row["checkin_date"]; ?></td>

                            <td><?php echo $row["checkout_date"]; ?></td>

                            <td>৳ <?php echo number_format($row["total_price"], 2); ?></td>

                            <td>
                                <span class="status-badge status-<?php echo $row["status"]; ?>">
                                    <?php echo ucfirst(str_replace("_", " ", $row["status"])); ?>
                                </span>
                            </td>

                            <td>
                                <a href="booking_details.php?id=<?php echo $row["id"]; ?>" class="edit-btn">
                                    View
                                </a>
                            </td>
                        </tr>

                    <?php } ?>

                <?php } else { ?>

                    <tr>
                        <td colspan="8">No bookings found.</td>
                    </tr>

                <?php } ?>
            </tbody>
        </table>

    </div>

</div>

<?php require_once "../../app/layouts/footer.php"; ?>