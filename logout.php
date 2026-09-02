<?php

require_once "app/helpers/session.php";

session_unset();
session_destroy();

header("Location: views/auth/login.php");
exit();

?>