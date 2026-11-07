<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: accountpage.html");
    exit();
}

// Database connection
$servername = "localhost";
$db_username = "root";
$db_password = "";
$database = "reserveitdb";

$conn = new mysqli($servername, $db_username, $db_password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve form data from POST
$facility = $_POST['facility'];
$reservation_date = $_POST['date'];
$reservation_time = $_POST['time'];
$payment_method = $_POST['payment'];

// Ensure time is only allowed with ":00" minutes
$time_parts = explode(":", $reservation_time);
if ($time_parts[1] !== "00") {
    echo "<script>alert('Please select a time with :00 minutes only.'); window.history.back();</script>";
    exit();
}
// Facility fees mapping
$facility_fees = [
    "dormitel" => 3000,
    "tennis" => 5000,
    "gym" => 1000,
    "pool" => 1000,
    "hoops" => 2000,
    "studio" => 1000,
    "multipurpose" => 1000
];

// Determine the facility fee based on user selection
$initialfee = isset($facility_fees[$facility]) ? $facility_fees[$facility] : 0;

// Validate facility selection
if ($initialfee == 0) {
    echo "<script>alert('Invalid facility selected!'); window.history.back();</script>";
    exit();
}

// Check if a reservation already exists for the same facility, date, and time
$sql_check = "SELECT * FROM reservations WHERE facility = ? AND reservation_date = ? AND reservation_time = ?";
$stmt = $conn->prepare($sql_check);
$stmt->bind_param("sss", $facility, $reservation_date, $reservation_time);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo "<script>alert('This facility is already reserved for that date and time.'); window.history.back();</script>";
    exit();
}

// Insert reservation into the table with fee
$sql_insert = "INSERT INTO reservations (user_id, facility, reservation_date, reservation_time, payment_method, initialfee) 
               VALUES (?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql_insert);
$stmt->bind_param("issssi", $_SESSION['user_id'], $facility, $reservation_date, $reservation_time, $payment_method, $initialfee);

if ($stmt->execute()) {
    $_SESSION['reservation_id'] = $conn->insert_id;
    echo "<script>alert('Reservation successful! Fee: PHP $initialfee'); window.location.href='main.php';</script>";
} else {
    echo "<script>alert('Reservation failed! Please try again.'); window.history.back();</script>";
}

$stmt->close();
$conn->close();
?>
