<?php

require_once "../app/helpers/session.php";
require_once "../models/receptionistModel.php";

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "receptionist") {
    header("Location: ../views/auth/login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $action = $_POST["action"];

    if ($action == "check_in") {

        $bookingId = $_POST["booking_id"];
        $roomId = $_POST["room_id"];

        if (empty($bookingId) || empty($roomId)) {
            $_SESSION["error"] = "Please select a room before check-in";
            header("Location: ../views/receptionist/today_checkins.php");
            exit();
        }

        if (processCheckIn($bookingId, $roomId)) {
            addReceptionLog($_SESSION["user_id"], "Checked in booking #" . $bookingId);
            $_SESSION["success"] = "Guest checked in successfully";
        } else {
            $_SESSION["error"] = "Check-in failed";
        }

        header("Location: ../views/receptionist/today_checkins.php");
        exit();
    }
	
	if ($_POST["action"] == "approve_early_late") {

    $bookingId = intval($_POST["booking_id"]);

    updateEarlyLateRequestStatus($bookingId, "approved");

    header("Location: ../views/receptionist/early_late_requests.php?success=approved");
    exit();
}

	
	if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if ($_POST["action"] == "approve_modification") {

        $requestId = intval($_POST["request_id"]);

        approveBookingModification($requestId);

        header("Location: ../views/receptionist/booking_modifications.php?success=approved");
        exit();
    }

    if ($_POST["action"] == "decline_modification") {

        $requestId = intval($_POST["request_id"]);

        updateBookingModificationStatus($requestId, "declined");

        header("Location: ../views/receptionist/booking_modifications.php?success=declined");
        exit();
    }
}
	


if ($action == "generate_receipt") {

    $billId = $_POST["bill_id"];

    if (generateReceiptPath($billId)) {
        $_SESSION["success"] = "Receipt generated successfully";
    } else {
        $_SESSION["error"] = "Receipt generation failed";
    }

    header("Location: ../views/receptionist/payments.php");
    exit();
}

    if ($action == "check_out") {

        $bookingId = $_POST["booking_id"];
        $roomId = $_POST["room_id"];

        if (empty($bookingId) || empty($roomId)) {
            $_SESSION["error"] = "Invalid checkout request";
            header("Location: ../views/receptionist/today_checkouts.php");
            exit();
        }

        if (processCheckOut($bookingId, $roomId)) {
            addReceptionLog($_SESSION["user_id"], "Checked out booking #" . $bookingId);
            $_SESSION["success"] = "Guest checked out successfully. Room marked as dirty.";
        } else {
            $_SESSION["error"] = "Check-out failed";
        }

        header("Location: ../views/receptionist/today_checkouts.php");
        exit();
    }

    if ($action == "process_payment") {

        $billId = $_POST["bill_id"];
        $paymentMethod = $_POST["payment_method"];

        if (empty($billId) || empty($paymentMethod)) {
            $_SESSION["error"] = "Payment method is required";
            header("Location: ../views/receptionist/payments.php");
            exit();
        }

        if (processPayment($billId, $paymentMethod)) {
            addReceptionLog($_SESSION["user_id"], "Processed payment for bill #" . $billId);
            $_SESSION["success"] = "Payment completed successfully";
        } else {
            $_SESSION["error"] = "Payment failed";
        }

        header("Location: ../views/receptionist/payments.php");
        exit();
    }

    if ($action == "walkin_booking") {

        $guestName = trim($_POST["guest_name"]);
        $phone = trim($_POST["phone"]);
        $idNumber = trim($_POST["id_number"]);
        $nationality = trim($_POST["nationality"]);
        $roomTypeId = $_POST["room_type_id"];
        $numGuests = $_POST["num_guests"];
        $checkinDate = $_POST["checkin_date"];
        $checkoutDate = $_POST["checkout_date"];
        $specialRequest = trim($_POST["special_request"]);

        if (
            empty($guestName) ||
            empty($phone) ||
            empty($idNumber) ||
            empty($nationality) ||
            empty($roomTypeId) ||
            empty($checkinDate) ||
            empty($checkoutDate)
        ) {
            $_SESSION["error"] = "All required fields must be filled";
            header("Location: ../views/receptionist/walkin_booking.php");
            exit();
        }
		
		

        if (
            createWalkinBooking(
                $guestName,
                $phone,
                $idNumber,
                $nationality,
                $roomTypeId,
                $numGuests,
                $checkinDate,
                $checkoutDate,
                $specialRequest
            )
        ) {
            $_SESSION["success"] = "Walk-in booking created successfully";
        } else {
            $_SESSION["error"] = "Walk-in booking failed";
        }

        header("Location: ../views/receptionist/walkin_booking.php");
        exit();
    }
	
	if ($action == "modify_booking") {

    $bookingId = $_POST["booking_id"];
    $checkinDate = $_POST["checkin_date"];
    $checkoutDate = $_POST["checkout_date"];

    if (
        empty($bookingId) ||
        empty($checkinDate) ||
        empty($checkoutDate)
    ) {

        $_SESSION["error"] = "All fields are required";

        header("Location: ../views/receptionist/booking_modifications.php");
        exit();
    }

    if (
        modifyBookingDates(
            $bookingId,
            $checkinDate,
            $checkoutDate
        )
    ) {

        $_SESSION["success"] = "Booking updated successfully";

    } else {

        $_SESSION["error"] = "Booking update failed";
    }

    header("Location: ../views/receptionist/booking_modifications.php");
    exit();
}

    if ($action == "complete_service") {

        $serviceId = $_POST["service_id"];

        if (empty($serviceId)) {
            $_SESSION["error"] = "Invalid service request";
            header("Location: ../views/receptionist/service_requests.php");
            exit();
        }

        if (completeServiceRequest($serviceId)) {
            addReceptionLog($_SESSION["user_id"], "Completed service request #" . $serviceId);
            $_SESSION["success"] = "Service request completed successfully";
        } else {
            $_SESSION["error"] = "Failed to complete service request";
        }

        header("Location: ../views/receptionist/service_requests.php");
        exit();
    }
}

header("Location: ../views/receptionist/dashboard.php");
exit();

?>