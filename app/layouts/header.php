<?php

$role = $_SESSION["role"] ?? "";

echo '<link rel="stylesheet" href="../../assets/css/dashboard.css?v=1201">';

if ($role == "guest") {
    echo '<link rel="stylesheet" href="../../assets/css/guest.css?v=11">';
} elseif ($role == "receptionist") {
    echo '<link rel="stylesheet" href="../../assets/css/receptionist.css?v=2">';
}

?>