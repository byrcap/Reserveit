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

    // Retrieve 
    if (isset($_POST['lostItem'], $_POST['primaryColor'], $_POST['dateLost'], $_POST['timeLost'])) {
        $lostItem      = trim($_POST['lostItem']);
        $brand         = isset($_POST['brand']) ? trim($_POST['brand']) : "";
        $primaryColor  = trim($_POST['primaryColor']);
        $dateLost      = trim($_POST['dateLost']);
        $timeLost      = trim($_POST['timeLost']);
    } else {
        echo "<script>alert('Please fill in all required fields.'); window.history.back();</script>";
        exit();
    }

    // Process 
    $imagePath = "";
    if (isset($_FILES['uploadImage']) && $_FILES['uploadImage']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = "uploads/lost/";
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        $filename    = basename($_FILES['uploadImage']['name']);
        $newFileName = uniqid() . "-" . $filename;
        $destPath    = $uploadDir . $newFileName;
        if (move_uploaded_file($_FILES['uploadImage']['tmp_name'], $destPath)) {
            $imagePath = $destPath;
        }
    }

    
    $location       = "";
    $other_location = "";

    
    $type = "lost";

    
    $sql = "INSERT INTO lost_and_found (type, item, brand, primary_color, location, other_location, date_event, time_event, image_path)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssss", $type, $lostItem, $brand, $primaryColor, $location, $other_location, $dateLost, $timeLost, $imagePath);

    if ($stmt->execute()) {
        echo "<script>alert('Lost item submitted successfully.'); window.location.href='lostandfound.html';</script>";
    } else {
        echo "<script>alert('Error submitting lost item.'); window.history.back();</script>";
    }

    $stmt->close();
    $conn->close();
?>
