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

$types = searchWalkinRoomTypes($keyword);

$data = [];

if ($types && $types->num_rows > 0) {

    while ($row = $types->fetch_assoc()) {

        $data[] = [

            "id" => $row["id"],
            "name" => $row["name"],
            "price" => $row["price_per_night"]

        ];
    }
}

echo json_encode($data);
exit();

?>