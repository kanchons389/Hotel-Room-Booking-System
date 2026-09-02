<?php

require_once "../config/database.php";
require_once "../app/helpers/session.php";
require_once "../models/userModel.php";

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "admin") {
    header("Location: ../views/auth/login.php");
    exit();
}


if (isset($_GET["delete"])) {

    $id = intval($_GET["delete"]);

    if ($id == $_SESSION["user_id"]) {
        $_SESSION["error"] = "You cannot delete your own account.";
        header("Location: ../views/admin/users.php");
        exit();
    }

    if (deleteUser($id)) {
        $_SESSION["success"] = "User deleted successfully.";
    } else {
        $_SESSION["error"] = "Failed to delete user.";
    }

    header("Location: ../views/admin/users.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: ../views/admin/users.php");
    exit();
}

$action = $_POST["action"] ?? "";


if ($action == "add_user") {

    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $phone = trim($_POST["phone"]);
    $role = trim($_POST["role"]);
    $password = trim($_POST["password"]);
    $status = intval($_POST["is_active"]);

    if (empty($name) || empty($email) || empty($phone) || empty($role) || empty($password)) {
        $_SESSION["error"] = "All fields are required.";
        header("Location: ../views/admin/users.php");
        exit();
    }

    if (strlen($password) < 6) {
        $_SESSION["error"] = "Password must be at least 6 characters.";
        header("Location: ../views/admin/users.php");
        exit();
    }

    if (emailExists($email)) {
        $_SESSION["error"] = "That email is already in use by another user.";
        header("Location: ../views/admin/users.php");
        exit();
    }

    if (addUser($name, $email, $password, $phone, $role, $status)) {
        $_SESSION["success"] = "User added successfully.";
    } else {
        $_SESSION["error"] = "Failed to add user.";
    }

    header("Location: ../views/admin/users.php");
    exit();
}

/

if ($action == "update_user") {

    $id = intval($_POST["id"]);
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $phone = trim($_POST["phone"]);
    $role = trim($_POST["role"]);
    $status = intval($_POST["is_active"]);

    if (empty($name) || empty($email) || empty($phone) || empty($role)) {
        $_SESSION["error"] = "All fields are required.";
        header("Location: ../views/admin/users.php?edit=" . $id);
        exit();
    }

    if (emailExists($email, $id)) {
        $_SESSION["error"] = "That email is already in use by another user.";
        header("Location: ../views/admin/users.php?edit=" . $id);
        exit();
    }

    if (updateUser($id, $name, $email, $phone, $role, $status)) {
        $_SESSION["success"] = "User updated successfully.";
    } else {
        $_SESSION["error"] = "Failed to update user.";
    }

    header("Location: ../views/admin/users.php");
    exit();
}

header("Location: ../views/admin/users.php");
exit();

?>
