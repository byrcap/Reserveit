<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "reserveit_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = $_POST["first_name"];
    $password = $_POST["password"];

    $sql = "SELECT * FROM users WHERE first_name='$first_name'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        if (password_verify($password, $row["password"])) {
            $_SESSION["first_name"] = $row["first_name"];
            echo "Login successful! <a href='dashboard.php'>Go to Dashboard</a>";
        } else {
            echo "Incorrect password!";
        }
    } else {
        echo "User not found!";
    }
}

$conn->close();
?>
