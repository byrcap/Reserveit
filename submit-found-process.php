<?php
    session_start();
    if (!isset($_SESSION['user_id'])) {
        header("Location: accountpage.html");
        exit();
    }

    $servername   = "localhost";
    $db_username  = "root";
    $db_password  = "";
    $database     = "reserveitdb";

    $conn = new mysqli($servername, $db_username, $db_password, $database);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    
    if (isset($_POST['foundItem'], $_POST['primaryColorFound'], $_POST['dateFound'], $_POST['timeFound'])) {
        $foundItem          = trim($_POST['foundItem']);
        $brandFound         = isset($_POST['brandFound']) ? trim($_POST['brandFound']) : "";
        $primaryColorFound  = trim($_POST['primaryColorFound']);
        $dateFound          = trim($_POST['dateFound']);
        $timeFound          = trim($_POST['timeFound']);
    } else {
        echo "<script>alert('Please fill in all required fields.'); window.history.back();</script>";
        exit();
    }

    
    $imagePath = "";
    if (isset($_FILES['uploadImageFound']) && $_FILES['uploadImageFound']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = "uploads/found/";
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        $filename    = basename($_FILES['uploadImageFound']['name']);
        $newFileName = uniqid() . "-" . $filename;
        $destPath    = $uploadDir . $newFileName;
        if (move_uploaded_file($_FILES['uploadImageFound']['tmp_name'], $destPath)) {
            $imagePath = $destPath;
        }
    }

    
    $location       = "";
    $other_location = "";

    
    $type = "found";

    
    $sql = "INSERT INTO lost_and_found (type, item, brand, primary_color, location, other_location, date_event, time_event, image_path)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssss", $type, $foundItem, $brandFound, $primaryColorFound, $location, $other_location, $dateFound, $timeFound, $imagePath);

    if ($stmt->execute()) {
        echo "<script>alert('Found item submitted successfully.'); window.location.href='lostandfound.html';</script>";
    } else {
        echo "<script>alert('Error submitting found item.'); window.history.back();</script>";
    }

    $stmt->close();
    $conn->close();
?>
