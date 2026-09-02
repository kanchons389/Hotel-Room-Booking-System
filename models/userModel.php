<?php

require_once __DIR__ . "/../config/database.php";


function searchAvailableRoomTypes($checkin, $checkout, $guests)
{
    global $conn;

    $checkin = mysqli_real_escape_string($conn, $checkin);
    $checkout = mysqli_real_escape_string($conn, $checkout);
    $guests = intval($guests);

    $sql = "
        SELECT
            rt.id,
            rt.name,
            rt.description,
            rt.price_per_night,
            rt.max_capacity,
            rt.thumbnail_path,

            GREATEST(
                0,

                (
                    SELECT COUNT(*)
                    FROM rooms r
                    WHERE r.room_type_id = rt.id
                    AND r.status NOT IN ('maintenance', 'blocked', 'dirty')
                )

                -

                (
                    SELECT COUNT(DISTINCT b.room_id)
                    FROM bookings b
                    INNER JOIN rooms rb
                        ON rb.id = b.room_id
                    WHERE b.room_type_id = rt.id
                    AND b.status IN ('pending', 'confirmed', 'checked_in')
                    AND b.checkin_date < '$checkout'
                    AND b.checkout_date > '$checkin'
                )

                -

                (
                    SELECT COUNT(*)
                    FROM bookings b2
                    WHERE b2.room_type_id = rt.id
                    AND b2.room_id IS NULL
                    AND b2.status IN ('pending', 'confirmed', 'checked_in')
                    AND b2.checkin_date < '$checkout'
                    AND b2.checkout_date > '$checkin'
                )

            ) AS available_rooms

        FROM room_types rt

        WHERE rt.is_active = 1
        AND rt.max_capacity >= $guests

        HAVING available_rooms > 0

        ORDER BY rt.price_per_night ASC
    ";

    $result = mysqli_query($conn, $sql);

    if (!$result) {
        die("Room search query failed: " . mysqli_error($conn));
    }

    return $result;
}


function getRoomTypeDetails($roomTypeId)
{
    global $conn;

    $roomTypeId = intval($roomTypeId);

    $sql = "
        SELECT
            id,
            name,
            description,
            price_per_night,
            max_capacity,
            thumbnail_path,
            amenities
        FROM room_types
        WHERE id = $roomTypeId
        AND is_active = 1
        LIMIT 1
    ";

    $result = mysqli_query($conn, $sql);

    if (!$result) {
        return null;
    }

    return mysqli_fetch_assoc($result);
}

function getAllUsers()
{
    global $conn;

    $sql = "SELECT * FROM users ORDER BY id DESC";
    return $conn->query($sql);
}

function getUserById($id)
{
    global $conn;

    $sql = "SELECT * FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();

    return $stmt->get_result()->fetch_assoc();
}

function addUser($name, $email, $password, $phone, $role, $status)
{
    global $conn;

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $sql = "INSERT INTO users
            (name, email, password_hash, phone, role, is_active)
            VALUES (?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "sssssi",
        $name,
        $email,
        $hashedPassword,
        $phone,
        $role,
        $status
    );

    try {
        return $stmt->execute();
    } catch (mysqli_sql_exception $e) {
        return false;
    }
}

function updateUser($id, $name, $email, $phone, $role, $status)
{
    global $conn;

    $sql = "UPDATE users
            SET name = ?, email = ?, phone = ?, role = ?, is_active = ?
            WHERE id = ?";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param(
        "ssssii",
        $name,
        $email,
        $phone,
        $role,
        $status,
        $id
    );

    try {
        return $stmt->execute();
    } catch (mysqli_sql_exception $e) {
        return false;
    }
}

function deleteUser($id)
{
    global $conn;

    $sql = "DELETE FROM users WHERE id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    try {
        return $stmt->execute();
    } catch (mysqli_sql_exception $e) {
        return false;
    }
}

function emailExists($email, $excludeId = null)
{
    global $conn;

    if ($excludeId !== null) {
        $sql = "SELECT id FROM users WHERE email = ? AND id != ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $email, $excludeId);
    } else {
        $sql = "SELECT id FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    return $result->num_rows > 0;
}


function changeGuestPassword($guestId, $newPassword)
{
    global $conn;

    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

    $sql = "UPDATE users SET password_hash = ? WHERE id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $hashedPassword, $guestId);

    try {
        return $stmt->execute();
    } catch (mysqli_sql_exception $e) {
        return false;
    }
}

?>