<div class="top-navbar">
    <div>
        <h3>Grand Palace Hotel</h3>
        <span>Hotel Room Booking & Management System</span>
    </div>

    <div class="navbar-right">
        <span>
            <i class="fa-solid fa-user"></i>
            <?php echo $_SESSION["name"]; ?>
        </span>

        <a href="../../logout.php" class="logout-btn">
            <i class="fa-solid fa-right-from-bracket"></i>
            Logout
        </a>
    </div>
</div>