<?php

$role = $_SESSION["role"] ?? "";

?>

<div class="sidebar">

    <div class="sidebar-logo">
        <h2>GPH</h2>
        <p>
            <?php echo ucfirst($role); ?> Panel
        </p>
    </div>

    <ul class="sidebar-menu">



        <?php if ($role == "admin") { ?>

            <li>
                <a href="dashboard.php">
                    <i class="fa-solid fa-chart-line"></i>
                    Dashboard
                </a>
            </li>

            <li>
                <a href="room_types.php">
                    <i class="fa-solid fa-bed"></i>
                    Room Types
                </a>
            </li>

            <li>
                <a href="rooms.php">
                    <i class="fa-solid fa-door-open"></i>
                    Rooms
                </a>
            </li>

            <li>
                <a href="users.php">
                    <i class="fa-solid fa-users"></i>
                    Users
                </a>
            </li>

        <?php } ?>


        <?php if ($role == "receptionist") { ?>

            <li>
                <a href="dashboard.php">
                    <i class="fa-solid fa-chart-line"></i>
                    Dashboard
                </a>
            </li>

            <li>
                <a href="bookings.php">
                    <i class="fa-solid fa-calendar-check"></i>
                    Bookings
                </a>
            </li>

            <li>
                <a href="checkin.php">
                    <i class="fa-solid fa-right-to-bracket"></i>
                    Check In
                </a>
            </li>

            <li>
                <a href="checkout.php">
                    <i class="fa-solid fa-right-from-bracket"></i>
                    Check Out
                </a>
            </li>

            <li>
                <a href="walkin_booking.php">
                    <i class="fa-solid fa-person-walking-luggage"></i>
                    Walk-In Booking
                </a>
            </li>

            <li>
                <a href="billing.php">
                    <i class="fa-solid fa-file-invoice-dollar"></i>
                    Billing
                </a>
            </li>

            <li>
                <a href="service_requests.php">
                    <i class="fa-solid fa-bell-concierge"></i>
                    Service Requests
                </a>
            </li>

        <?php } ?>


        <?php if ($role == "guest") { ?>

            <li>
                <a href="dashboard.php">
                    <i class="fa-solid fa-house"></i>
                    Dashboard
                </a>
            </li>

        
            <li>
                <a href="search_rooms.php">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    Search Rooms
                </a>
            </li>

            <li>
                <a href="my_bookings.php">
                    <i class="fa-solid fa-calendar-check"></i>
                    My Bookings
                </a>
            </li>
            
            <li>
                <a href="billing.php">
                    <i class="fa-solid fa-file-invoice-dollar"></i>
                    Billing History
                </a>
            </li>

        <?php } ?>

    </ul>

</div>