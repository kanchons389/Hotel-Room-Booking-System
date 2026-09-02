<?php

require_once __DIR__ . "/../app/helpers/session.php";

header("Content-Type: application/json");

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "housekeeping") {
    echo json_encode([]);
    exit();
}

require_once __DIR__ . "/../models/housekeepingModel.php";

$rooms = getHousekeepingRoomBoard();

$data = [];

if ($rooms && $rooms->num_rows > 0) {
    while ($row = $rooms->fetch_assoc()) {
        $data[] = [
            "id" => $row["id"],
            "room_number" => $row["room_number"],
            "floor" => $row["floor"],
            "room_type" => $row["room_type"],
            "status" => $row["status"],
            "notes" => $row["notes"] ?? ""
        ];
    }
}

echo json_encode($data);
exit();

?>
