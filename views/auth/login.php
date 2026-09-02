<?php
require_once "../../app/helpers/session.php";

if (isset($_SESSION["user_id"])) {
    if ($_SESSION["role"] == "admin") {
        header("Location: ../admin/dashboard.php");
    } elseif ($_SESSION["role"] == "guest") {
        header("Location: ../guest/dashboard.php");
    } elseif ($_SESSION["role"] == "receptionist") {
        header("Location: ../receptionist/dashboard.php");
    } elseif ($_SESSION["role"] == "housekeeping") {
        header("Location: ../housekeeping/dashboard.php");
    }
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login | Grand Palace Hotel</title>

    <link rel="stylesheet" href="../../assets/css/style.css?v=21">

    <link rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>

<body>

<div class="auth-container">

    <div class="auth-box">

        <div class="auth-header">
            <h1>Grand Palace Hotel</h1>
            <p>Login to continue to your hotel account</p>
        </div>

        <?php
        if (isset($_SESSION["error"])) {
            echo "<div class='error-message'>" . $_SESSION["error"] . "</div>";
            unset($_SESSION["error"]);
        }

        if (isset($_SESSION["success"])) {
            echo "<div class='success-message'>" . $_SESSION["success"] . "</div>";
            unset($_SESSION["success"]);
        }
        ?>

        <form action="../../controllers/authController.php" method="POST">

            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" placeholder="Enter your email" required>
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="Enter your password" required>
            </div>

            <button type="submit" class="auth-btn">
                <i class="fa-solid fa-right-to-bracket"></i>
                Login
            </button>

        </form>

        <div class="auth-footer">
            New guest?
            <a href="register.php">Create Guest Account</a>
        </div>

        <div class="demo-info">
            <p><b>Admin:</b> Md.jalal@grandpalacehotel.com</p>
            <p><b>Guest:</b> tasmin.tashu@grandpalacehotel.com</p>
            <p><b>Receptionist:</b>kanchon.sd@grandpalacehotel.com</p>
            <p><b>Housekeeping:</b> antu.roy@grandpalacehotel.com</p>
        </div>

    </div>

</div>

</body>
</html>