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


$roomTypeId = $_GET["id"];

$checkin = $_GET["checkin"];

$checkout = $_GET["checkout"];

$guests = $_GET["guests"];

$room = getRoomTypeDetails($roomTypeId);



require_once "../../app/layouts/header.php";
require_once "../../app/layouts/sidebar.php";
require_once "../../app/layouts/navbar.php";

?>

<div class="main-content">

    <div class="dashboard-title">

        <h1>
            <?php echo htmlspecialchars($room["name"]); ?>
        </h1>

        <p>
            Luxury room details and amenities.
        </p>

    </div>


    <div class="dashboard-table">

        <h2>Room Information</h2>

        <table>

            <tr>
                <th>Room Type</th>
                <td>
                    <?php echo htmlspecialchars($room["name"]); ?>
                </td>
            </tr>

        
            <tr>
                <th>Max Capacity</th>
                <td>
                    <?php echo $room["max_capacity"]; ?>
                </td>
            </tr>

            <tr>
                <th>Price Per Night</th>
                <td>
                    ৳
                    <?php echo number_format($room["price_per_night"],2); ?>
                </td>
            </tr>

           

        </table>

    </div>

    
    <a

    class="print-btn"

    href="book_room.php?id=<?php echo $roomTypeId; ?>&checkin=<?php echo $checkin; ?>&checkout=<?php echo $checkout; ?>&guests=<?php echo $guests; ?>">

        Continue Booking

    </a>

</div>

<?php require_once "../../app/layouts/footer.php"; ?>