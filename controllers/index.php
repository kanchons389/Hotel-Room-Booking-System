<?php

require_once "app/helpers/session.php";

if (isset($_SESSION["user_id"])) {

    if ($_SESSION["role"] == "admin") {
        header("Location: views/admin/dashboard.php");
    }

    elseif ($_SESSION["role"] == "guest") {
        header("Location: views/guest/dashboard.php");
    }

    elseif ($_SESSION["role"] == "receptionist") {
        header("Location: views/receptionist/dashboard.php");
    }

    elseif ($_SESSION["role"] == "housekeeping") {
        header("Location: views/housekeeping/dashboard.php");
    }

    exit();
}

header("Location: views/auth/login.php");
exit();

?>