<<<<<<< HEAD
<?php

require_once "../../app/helpers/session.php";
require_once "../../models/receptionistModel.php";

header("Content-Type: application/json");

if (
    !isset($_SESSION["user_id"]) ||
    $_SESSION["role"] != "receptionist"
) {

    echo json_encode([]);
    exit();
}

$keyword = "";

if (isset($_GET["keyword"])) {
    $keyword = trim($_GET["keyword"]);
}

$checkouts = getTodayCheckouts();

$data = [];

if ($checkouts && $checkouts->num_rows > 0) {

    while ($row = $checkouts->fetch_assoc()) {

        $searchText = strtolower(

            "#B" . $row["id"] . " " .
            $row["guest_name"] . " " .
            $row["room_number"] . " " .
            $row["room_type"] . " " .
            $row["checkin_date"] . " " .
            $row["checkout_date"] . " " .
            $row["status"]

        );

        if (
            $keyword == "" ||
            strpos($searchText, strtolower($keyword)) !== false
        ) {

            $data[] = [

                "id" => $row["id"],

                "room_id" => $row["room_id"],

                "guest_name" => $row["guest_name"],

                "room_number" => $row["room_number"],

                "room_type" => $row["room_type"],

                "checkin_date" => $row["checkin_date"],

                "checkout_date" => $row["checkout_date"],

                "status" => $row["status"]

            ];
        }
    }
}

echo json_encode($data);

exit();

=======
<?php

require_once "../../app/helpers/session.php";
require_once "../../models/receptionistModel.php";

header("Content-Type: application/json");

if (
    !isset($_SESSION["user_id"]) ||
    $_SESSION["role"] != "receptionist"
) {

    echo json_encode([]);
    exit();
}

$keyword = "";

if (isset($_GET["keyword"])) {
    $keyword = trim($_GET["keyword"]);
}

$checkouts = getTodayCheckouts();

$data = [];

if ($checkouts && $checkouts->num_rows > 0) {

    while ($row = $checkouts->fetch_assoc()) {

        $searchText = strtolower(

            "#B" . $row["id"] . " " .
            $row["guest_name"] . " " .
            $row["room_number"] . " " .
            $row["room_type"] . " " .
            $row["checkin_date"] . " " .
            $row["checkout_date"] . " " .
            $row["status"]

        );

        if (
            $keyword == "" ||
            strpos($searchText, strtolower($keyword)) !== false
        ) {

            $data[] = [

                "id" => $row["id"],

                "room_id" => $row["room_id"],

                "guest_name" => $row["guest_name"],

                "room_number" => $row["room_number"],

                "room_type" => $row["room_type"],

                "checkin_date" => $row["checkin_date"],

                "checkout_date" => $row["checkout_date"],

                "status" => $row["status"]

            ];
        }
    }
}

echo json_encode($data);

exit();

>>>>>>> f426c735c544c48a5a94c8e150575f14535a84e0
?>