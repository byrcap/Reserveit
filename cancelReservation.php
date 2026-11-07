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

    $user_id = $_SESSION['user_id'];

    // remove & update the fee to 50% of its current value.
    $sql = "UPDATE reservations 
            SET facility = '', reservation_date = '', reservation_time = '', initialfee = initialfee * 0.5 
            WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);

    if ($stmt->execute()) {
        echo "Cancellation fee applied successfully.";
    } else {
        echo "Error updating fee.";
    }

    $stmt->close();
    $conn->close();
?>
