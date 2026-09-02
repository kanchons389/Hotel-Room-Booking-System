<?php

require_once "../../app/helpers/session.php";

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Guest Registration</title>

<link rel="stylesheet" href="../../assets/css/style.css?v=6">

    <link rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

</head>

<body>

<div class="auth-container">

    <div class="auth-box">

        <div class="auth-header">

            <h1>Grand Palace Hotel</h1>

            <p>Create Your Guest Account</p>

        </div>

        <?php if (isset($_SESSION["error"])) { ?>

            <div class="error-message">

                <?php
                    echo $_SESSION["error"];
                    unset($_SESSION["error"]);
                ?>

            </div>

        <?php } ?>

        <form method="POST"
        action="../../controllers/authController.php">

            <input
            type="hidden"
            name="action"
            value="register">

            <div class="form-group">

                <label>Full Name</label>

                <input
                type="text"
                name="name"
                required>

            </div>

            <div class="form-group">

                <label>Email Address</label>

                <input
                type="email"
                name="email"
                required>

            </div>

            <div class="form-group">

                <label>Password</label>

                <input
                type="password"
                name="password"
                required>

            </div>

            <div class="form-group">

                <label>Phone Number</label>

                <input
                type="text"
                name="phone"
                required>

            </div>

            <div class="form-group">

                <label>Nationality</label>

                <input
                type="text"
                name="nationality"
                required>

            </div>

            <div class="form-group">

                <label>ID / Passport Number</label>

                <input
                type="text"
                name="id_number"
                required>

            </div>

            <button type="submit" class="auth-btn">

                <i class="fa-solid fa-user-plus"></i>

                Create Account

            </button>

        </form>

        <div class="auth-footer">

            Already have an account?

            <a href="login.php">
                Login Here
            </a>

        </div>

    </div>

</div>

</body>
</html>