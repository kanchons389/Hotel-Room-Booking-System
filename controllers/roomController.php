<?php

require_once "../app/helpers/session.php";

require_once "../models/userModel.php";
require_once "../models/roomModel.php";
require_once "../models/bookingModel.php";

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "admin") {
    header("Location: ../views/auth/login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $action = $_POST["action"];

    if ($action == "add_room_type" || $action == "update_room_type") {

        $name = trim($_POST["name"]);
        $description = trim($_POST["description"]);
        $price = trim($_POST["price_per_night"]);
        $capacity = trim($_POST["max_capacity"]);
        $thumbnail = trim($_POST["thumbnail_path"]);
        $amenitiesInput = trim($_POST["amenities"]);

        if (empty($name) || empty($description) || empty($price) || empty($capacity)) {
            $_SESSION["error"] = "Required fields cannot be empty";
            header("Location: ../views/admin/room_types.php");
            exit();
        }

        $amenitiesArray = array_map("trim", explode(",", $amenitiesInput));
        $amenities = json_encode($amenitiesArray);

        if ($action == "add_room_type") {
            if (addRoomType($name, $description, $price, $capacity, $thumbnail, $amenities)) {
                $_SESSION["success"] = "Room type added successfully";
            } else {
                $_SESSION["error"] = "Failed to add room type";
            }
        }

        if ($action == "update_room_type") {
            $id = $_POST["id"];

            if (updateRoomType($id, $name, $description, $price, $capacity, $thumbnail, $amenities)) {
                $_SESSION["success"] = "Room type updated successfully";
            } else {
                $_SESSION["error"] = "Failed to update room type";
            }
        }

        header("Location: ../views/admin/room_types.php");
        exit();
    }

    if ($action == "add_room" || $action == "update_room") {

        $roomTypeId = $_POST["room_type_id"];
        $roomNumber = trim($_POST["room_number"]);
        $floor = trim($_POST["floor"]);
        $status = $_POST["status"];
        $notes = trim($_POST["notes"]);

        if (empty($roomTypeId) || empty($roomNumber) || empty($floor) || empty($status)) {
            $_SESSION["error"] = "Required fields cannot be empty";
            header("Location: ../views/admin/rooms.php");
            exit();
        }

        if ($action == "add_room") {
            if (addRoom($roomTypeId, $roomNumber, $floor, $status, $notes)) {
                $_SESSION["success"] = "Room added successfully";
            } else {
                $_SESSION["error"] = "Failed to add room. Room number may already exist.";
            }
        }

        if ($action == "update_room") {
            $id = $_POST["id"];

            if (updateRoom($id, $roomTypeId, $roomNumber, $floor, $status, $notes)) {
                $_SESSION["success"] = "Room updated successfully";
            } else {
                $_SESSION["error"] = "Failed to update room";
            }
        }

        header("Location: ../views/admin/rooms.php");
        exit();
    }
}

if (isset($_GET["delete_room_type"])) {
    $id = $_GET["delete_room_type"];

    if (deleteRoomType($id)) {
        $_SESSION["success"] = "Room type deleted successfully";
    } else {
        $_SESSION["error"] = "Cannot delete this room type because it may be used by rooms/bookings";
    }

    header("Location: ../views/admin/room_types.php");
    exit();
}

if (isset($_GET["delete_room"])) {
    $id = $_GET["delete_room"];

    if (deleteRoom($id)) {
        $_SESSION["success"] = "Room deleted successfully";
    } else {
        $_SESSION["error"] = "Cannot delete this room because it may be used by bookings";
    }

    header("Location: ../views/admin/rooms.php");
    exit();
}

header("Location: ../views/admin/dashboard.php");
exit();

?>