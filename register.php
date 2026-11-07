<?php
    // database connection
    $servername = "localhost";
    $username = "root"; 
    $password = ""; 
    $database = "reserveitdb"; 

    $conn = new mysqli($servername, $username, $password, $database);

    // check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // get user input
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $account_type = $_POST['account_type'];
    $email = $_POST['email'];
    $phonenumber = $_POST['phonenumber'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // check if passwords match
    if ($password !== $confirm_password) {
        echo "<script>alert('Passwords do not match!'); window.history.back();</script>";
        exit();
    }

    // check if username or email already exists
    $sql_check = "SELECT * FROM useraccs WHERE username = ? OR email = ?";
    $stmt = $conn->prepare($sql_check);
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<script>alert('Username or Email already taken!'); window.history.back();</script>";
        exit();
    }

    // hash the password for security
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // insert user data into database
    $sql_insert = "INSERT INTO useraccs (first_name, last_name, account_type, email, phonenumber, username, password, regdate) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql_insert);
    $stmt->bind_param("sssssss", $first_name, $last_name, $account_type, $email, $phonenumber, $username, $hashed_password);

    if ($stmt->execute()) {
        echo "<script>alert('Registration successful!'); window.location.href='accountpage.html';</script>";
    } else {
        echo "<script>alert('Registration failed! Please try again.'); window.history.back();</script>";
    }

    $stmt->close();
    $conn->close();
?>
