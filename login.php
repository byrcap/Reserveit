<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// connection
$servername = "localhost";
$db_username = "root";
$db_password = "";
$database = "reserveitdb";

// create connection
$conn = new mysqli($servername, $db_username, $db_password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// get login info
$username = $_POST['username'];
$password = $_POST['password'];

// user record
$sql = "SELECT * FROM useraccs WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 1) {
    $row = $result->fetch_assoc();
    // verify password
    if (password_verify($password, $row['password'])) {
        
        $_SESSION['user_id'] = $row['id'];
        $_SESSION['first_name'] = $row['first_name'];
        $_SESSION['last_name'] = $row['last_name'];
        $_SESSION['account_type'] = $row['account_type'];
        
        // go to main page
        header("Location: main.php");
        exit();
    } else {
        echo "<script>alert('Invalid password!'); window.history.back();</script>";
        exit();
    }
} else {
    echo "<script>alert('Username not found!'); window.history.back();</script>";
    exit();
}

$stmt->close();
$conn->close();
?>
