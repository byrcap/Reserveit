<?php
    session_start();
    if (!isset($_SESSION['user_id'])) {
        header("Location: accountpage.html");
        exit();
    }

    // new details
    $new_facility = $_POST['facility'];  // New facility (if selected)
    $new_date = $_POST['date'];
    $new_time = $_POST['time'];

    // validate
    if(empty($new_date) || empty($new_time)){
        echo "Date and time must be provided.";
        exit();
    }

    // connection
    $servername = "localhost";
    $db_username = "root";
    $db_password = "";
    $database = "reserveitdb";
    $conn = new mysqli($servername, $db_username, $db_password, $database);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $user_id = $_SESSION['user_id'];

    // update reservation, increase fee by 10%
    $sql = "UPDATE reservations 
            SET facility = ?, reservation_date = ?, reservation_time = ?, initialfee = initialfee * 1.1 
            WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $new_facility, $new_date, $new_time, $user_id);

    if ($stmt->execute()) {
        echo "Reschedule successful.";
    } else {
        echo "Error rescheduling.";
    }

    $stmt->close();
    $conn->close();
?>
