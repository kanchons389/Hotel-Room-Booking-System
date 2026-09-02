<<<<<<< HEAD
<?php

require_once "../../app/helpers/session.php";
require_once "../../models/receptionistModel.php";

header("Content-Type: application/json");

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "receptionist") {
    echo json_encode([]);
    exit();
}

$keyword = "";

if (isset($_GET["keyword"])) {
    $keyword = trim($_GET["keyword"]);
}

$checkins = getTodayCheckins();
$data = [];

if ($checkins && $checkins->num_rows > 0) {
    while ($row = $checkins->fetch_assoc()) {

        $searchText = strtolower(
            $row["id"] . " " .
            $row["guest_name"] . " " .
            $row["id_number"] . " " .
            $row["room_type"] . " " .
            $row["num_guests"] . " " .
            $row["checkin_date"] . " " .
            $row["checkout_date"]
        );

        if ($keyword == "" || strpos($searchText, strtolower($keyword)) !== false) {

            $rooms = getAvailableRoomsByType($row["room_type_id"]);
            $availableRooms = [];

            if ($rooms && $rooms->num_rows > 0) {
                while ($room = $rooms->fetch_assoc()) {
                    $availableRooms[] = [
                        "id" => $room["id"],
                        "room_number" => $room["room_number"],
                        "floor" => $room["floor"]
                    ];
                }
            }

            $data[] = [
                "id" => $row["id"],
                "guest_name" => $row["guest_name"],
                "id_number" => $row["id_number"],
                "room_type" => $row["room_type"],
                "num_guests" => $row["num_guests"],
                "checkin_date" => $row["checkin_date"],
                "checkout_date" => $row["checkout_date"],
                "rooms" => $availableRooms
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

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "receptionist") {
    echo json_encode([]);
    exit();
}

$keyword = "";

if (isset($_GET["keyword"])) {
    $keyword = trim($_GET["keyword"]);
}

$checkins = getTodayCheckins();
$data = [];

if ($checkins && $checkins->num_rows > 0) {
    while ($row = $checkins->fetch_assoc()) {

        $searchText = strtolower(
            $row["id"] . " " .
            $row["guest_name"] . " " .
            $row["id_number"] . " " .
            $row["room_type"] . " " .
            $row["num_guests"] . " " .
            $row["checkin_date"] . " " .
            $row["checkout_date"]
        );

        if ($keyword == "" || strpos($searchText, strtolower($keyword)) !== false) {

            $rooms = getAvailableRoomsByType($row["room_type_id"]);
            $availableRooms = [];

            if ($rooms && $rooms->num_rows > 0) {
                while ($room = $rooms->fetch_assoc()) {
                    $availableRooms[] = [
                        "id" => $room["id"],
                        "room_number" => $room["room_number"],
                        "floor" => $room["floor"]
                    ];
                }
            }

            $data[] = [
                "id" => $row["id"],
                "guest_name" => $row["guest_name"],
                "id_number" => $row["id_number"],
                "room_type" => $row["room_type"],
                "num_guests" => $row["num_guests"],
                "checkin_date" => $row["checkin_date"],
                "checkout_date" => $row["checkout_date"],
                "rooms" => $availableRooms
            ];
        }
    }
}

echo json_encode($data);
exit();

>>>>>>> f426c735c544c48a5a94c8e150575f14535a84e0
?>