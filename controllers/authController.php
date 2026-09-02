<?php

require_once "../config/database.php";
require_once "../app/helpers/session.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $action = $_POST["action"] ?? "login";

    if ($action == "register") {

        $name = trim($_POST["name"]);
        $email = trim($_POST["email"]);
        $password = trim($_POST["password"]);
        $phone = trim($_POST["phone"]);
        $nationality = trim($_POST["nationality"]);
        $idNumber = trim($_POST["id_number"]);

        if (
            empty($name) ||
            empty($email) ||
            empty($password) ||
            empty($phone) ||
            empty($nationality) ||
            empty($idNumber)
        ) {
            $_SESSION["error"] = "All fields are required";
            header("Location: ../views/auth/register.php");
            exit();
        }

        if (strlen($password) < 6) {
            $_SESSION["error"] = "Password must be at least 6 characters";
            header("Location: ../views/auth/register.php");
            exit();
        }

        $sql = "SELECT id FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $_SESSION["error"] = "Email already exists";
            header("Location: ../views/auth/register.php");
            exit();
        }

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $role = "guest";
        $isActive = 1;

        $sql = "INSERT INTO users
                (name, email, password_hash, phone, nationality, id_number, role, is_active, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";

        $stmt = $conn->prepare($sql);

        $stmt->bind_param(
            "sssssssi",
            $name,
            $email,
            $passwordHash,
            $phone,
            $nationality,
            $idNumber,
            $role,
            $isActive
        );

        if ($stmt->execute()) {
            $_SESSION["success"] = "Registration successful. Please login.";
            header("Location: ../views/auth/login.php");
            exit();
        } else {
            $_SESSION["error"] = "Registration failed. Please try again.";
            header("Location: ../views/auth/register.php");
            exit();
        }
    }

    if ($action == "login") {

        $email = trim($_POST["email"]);
        $password = trim($_POST["password"]);

        if (empty($email) || empty($password)) {
            $_SESSION["error"] = "Email and password are required";
            header("Location: ../views/auth/login.php");
            exit();
        }

        $sql = "SELECT id, name, email, password_hash, role, is_active
                FROM users
                WHERE email = ?";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows == 1) {

            $user = $result->fetch_assoc();

            if ($user["is_active"] != 1) {
                $_SESSION["error"] = "Your account is inactive";
                header("Location: ../views/auth/login.php");
                exit();
            }

            if (password_verify($password, $user["password_hash"])) {

                $_SESSION["user_id"] = $user["id"];
                $_SESSION["name"] = $user["name"];
                $_SESSION["email"] = $user["email"];
                $_SESSION["role"] = $user["role"];

                if ($user["role"] == "admin") {
                    header("Location: ../views/admin/dashboard.php");
                } elseif ($user["role"] == "guest") {
                    header("Location: ../views/guest/dashboard.php");
                } elseif ($user["role"] == "receptionist") {
                    header("Location: ../views/receptionist/dashboard.php");
                } elseif ($user["role"] == "housekeeping") {
                    header("Location: ../views/housekeeping/dashboard.php");
                } else {
                    header("Location: ../views/auth/login.php");
                }

                exit();

            } else {
                $_SESSION["error"] = "Invalid email or password";
                header("Location: ../views/auth/login.php");
                exit();
            }

        } else {
            $_SESSION["error"] = "Invalid email or password";
            header("Location: ../views/auth/login.php");
            exit();
        }
    }

} else {
    header("Location: ../views/auth/login.php");
    exit();
}

?>