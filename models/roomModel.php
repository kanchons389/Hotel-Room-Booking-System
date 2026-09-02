<?php

require_once __DIR__ . "/../config/database.php";

function getAllRoomTypes()
{
    global $conn;

    $sql = "SELECT * FROM room_types ORDER BY id DESC";
    return $conn->query($sql);
}

function getRoomTypeById($id)
{
    global $conn;

    $sql = "SELECT * FROM room_types WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();

    return $stmt->get_result()->fetch_assoc();
}

function addRoomType($name, $description, $price, $capacity, $thumbnail, $amenities)
{
    global $conn;

    $sql = "INSERT INTO room_types 
            (name, description, price_per_night, max_capacity, thumbnail_path, amenities) 
            VALUES (?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssdiss", $name, $description, $price, $capacity, $thumbnail, $amenities);

    try {
        return $stmt->execute();
    } catch (mysqli_sql_exception $e) {
        return false;
    }
}

function updateRoomType($id, $name, $description, $price, $capacity, $thumbnail, $amenities)
{
    global $conn;

    $sql = "UPDATE room_types 
            SET name = ?, description = ?, price_per_night = ?, max_capacity = ?, thumbnail_path = ?, amenities = ?
            WHERE id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssdissi", $name, $description, $price, $capacity, $thumbnail, $amenities, $id);

    try {
        return $stmt->execute();
    } catch (mysqli_sql_exception $e) {
        return false;
    }
}

function deleteRoomType($id)
{
    global $conn;

    $sql = "DELETE FROM room_types WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    try {
        return $stmt->execute();
    } catch (mysqli_sql_exception $e) {
        return false;
    }
}

function getAllRooms()
{
    global $conn;

    $sql = "SELECT 
                rooms.id,
                rooms.room_number,
                rooms.floor,
                rooms.status,
                rooms.notes,
                room_types.name AS room_type
            FROM rooms
            INNER JOIN room_types ON rooms.room_type_id = room_types.id
            ORDER BY rooms.id DESC";

    return $conn->query($sql);
}

function getRoomById($id)
{
    global $conn;

    $sql = "SELECT * FROM rooms WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();

    return $stmt->get_result()->fetch_assoc();
}

function addRoom($roomTypeId, $roomNumber, $floor, $status, $notes)
{
    global $conn;

    $sql = "INSERT INTO rooms 
            (room_type_id, room_number, floor, status, notes)
            VALUES (?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isiss", $roomTypeId, $roomNumber, $floor, $status, $notes);

    try {
        return $stmt->execute();
    } catch (mysqli_sql_exception $e) {
        return false;
    }
}

function updateRoom($id, $roomTypeId, $roomNumber, $floor, $status, $notes)
{
    global $conn;

    $sql = "UPDATE rooms
            SET room_type_id = ?, room_number = ?, floor = ?, status = ?, notes = ?
            WHERE id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isissi", $roomTypeId, $roomNumber, $floor, $status, $notes, $id);

    try {
        return $stmt->execute();
    } catch (mysqli_sql_exception $e) {
        return false;
    }
}

function deleteRoom($id)
{
    global $conn;

    $sql = "DELETE FROM rooms WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    try {
        return $stmt->execute();
    } catch (mysqli_sql_exception $e) {
        return false;
    }
}

?>