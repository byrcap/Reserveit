<?php
    session_start();
    if (!isset($_SESSION['user_id'])) {
        header("Location: accountpage.html");
        exit();
    }

    // Database connection (you may use a config file for reusability)
    $servername = "localhost";
    $db_username = "root";
    $db_password = "";
    $database = "reserveitdb";
    $conn = new mysqli($servername, $db_username, $db_password, $database);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $user_id = $_SESSION['user_id'];
    // Retrieve the most recent reservation for the user
    $sql = "SELECT * FROM reservations WHERE user_id = ? ORDER BY id DESC LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $reservation = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    $conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reserve It! NU Laguna</title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Anton&family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900&family=Rubik:ital,wght@0,300..900;1,300..900&display=swap" rel="stylesheet">

    <!-- SweetAlert2 (CDN) -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Stylesheets -->
    <link rel="stylesheet" href="reservestyle.css">
    <link rel="stylesheet" href="header.css"> 
</head>
<body>
    <header>
        <div class="logo-container">
            <img src="newbgLogo.png" alt="Reserve It! NU Laguna" class="logo-img">
            <div class="logo-text">RESERVE IT! NU LAGUNA</div>
        </div>
        <nav class="nav">
            <a href="editprof.html">Edit Profile</a>
            <a href="lostandfound.html">Lost and Found</a>
            <button class="logout" onclick="location.href='logout.php'">Logout</button>
        </nav>
    </header>

    <main>
        <section class="left-section">
            <h1><span class="italic">BOOK</span> <span class="bold">NOW</span></h1>

            <!-- Reservation Status Block -->
            <details class="dropdown">
                <summary><span style="font-size:1.2em;">🛡️</span> Reservation Status</summary>
                <div class="notice">
                    <?php if($reservation): ?>
                        <?php if(empty($reservation['facility'])): ?>
                            <h3>Reservation has been cancelled.</h3>
                        <?php else: ?>
                            <h3>Current Status: Confirmed</h3>
                            <p>Your reservation for <strong><?php echo htmlspecialchars($reservation['facility']); ?></strong> on 
                                <strong><?php echo htmlspecialchars($reservation['reservation_date']); ?></strong> at 
                                <strong><?php echo htmlspecialchars($reservation['reservation_time']); ?></strong> is confirmed.</p>
                            <p>Fee: PHP <?php echo htmlspecialchars($reservation['initialfee']); ?></p>
                        <?php endif; ?>
                    <?php else: ?>
                        <h3>No current reservation.</h3>
                    <?php endif; ?>
                </div>


            </details>

            <!-- Reservation Cancellation Block -->
            <details class="dropdown">
                <summary>Reservation Cancellation</summary>
                <div class="notice">
                    <h3>Advance Notice for Cancellation</h3>
                    <ul>
                        <li>Cancellations must be made at least 48 hours before the scheduled reservation.</li>
                        <li>Less than 48 hours before the event: 50% fee.</li>
                        <li>Same-day cancellation or no-show: 100% fee.</li>
                    </ul>
                    <label>
                        <input type="checkbox" id="agreeCheckbox"> I AGREE TO THE CANCELLATION POLICY
                    </label>
                    <button id="cancelBtn" style="display: none;">Confirm Cancellation</button>

                </div>
            </details>
            
            <!-- Reschedule Dropdown -->
            <details class="dropdown" id="rescheduleDropdown">
                <summary>Reschedule</summary>
                <form id="rescheduleForm">
                    <!-- Select Facility -->
                    <div class="facility">
                        <h3>Facility</h3>
                        <select name="facility" id="facilitySelect" required>
                            <option value="">Choose a Facility</option>
                            <option value="gym" <?php if($reservation['facility'] == 'gym') echo 'selected'; ?>>High-Performance Gym</option>
                            <option value="pool" <?php if($reservation['facility'] == 'pool') echo 'selected'; ?>>Aquatic Center</option>
                            <option value="tennis" <?php if($reservation['facility'] == 'tennis') echo 'selected'; ?>>Tennis Court</option>
                            <option value="hoops" <?php if($reservation['facility'] == 'hoops') echo 'selected'; ?>>Hoops Center</option>
                            <option value="multipurpose" <?php if($reservation['facility'] == 'multipurpose') echo 'selected'; ?>>Multipurpose Gym</option>
                            <option value="studio" <?php if($reservation['facility'] == 'studio') echo 'selected'; ?>>Multimedia Studio</option>
                            <option value="dormitel" <?php if($reservation['facility'] == 'dormitel') echo 'selected'; ?>>Inspire Dormitel</option>
                        </select>
                    </div>

                    <!-- Pick a Date -->
                    <div class="calendar">
                        <h3>Pick a Date</h3>
                        <input type="date" name="date" id="newDate" required 
                            value="<?php echo htmlspecialchars($reservation['reservation_date']); ?>">
                    </div>

                    <!-- Pick a Time -->
                    <div class="input-group"> 
                        <div class="time">
                            <h3>Pick a Time</h3>
                            <select name="time" id="newTime" required>
                                <?php
                                // List time options (you can also generate these dynamically)
                                $times = [
                                    "06:00" => "06:00 AM",
                                    "07:00" => "07:00 AM",
                                    "08:00" => "08:00 AM",
                                    "09:00" => "09:00 AM",
                                    "10:00" => "10:00 AM",
                                    "11:00" => "11:00 AM",
                                    "12:00" => "12:00 PM",
                                    "13:00" => "01:00 PM",
                                    "14:00" => "02:00 PM",
                                    "15:00" => "03:00 PM",
                                    "16:00" => "04:00 PM",
                                    "17:00" => "05:00 PM",
                                    "18:00" => "06:00 PM",
                                    "19:00" => "07:00 PM",
                                    "20:00" => "08:00 PM",
                                    "21:00" => "09:00 PM"
                                ];
                                foreach ($times as $value => $label) {
                                    $selected = ($reservation['reservation_time'] == $value) ? "selected" : "";
                                    echo "<option value=\"$value\" $selected>$label</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="confirm">Confirm Reschedule</button>
                </form>
            </details>

        </section>

        <section class="right-section">
            <h2>WELCOME, <span class="highlight"><?php echo htmlspecialchars($_SESSION['first_name']); ?></span></h2>
            <h2 class="italic"><?php echo htmlspecialchars($_SESSION['last_name']); ?></h2>
            <div class="profile-pic">
                <img src="defaultphoto.png" alt="Profile Picture">
            </div>
            <p class="account-type">ACCOUNT TYPE: <?php echo htmlspecialchars(strtoupper($_SESSION['account_type'])); ?></p>
            <a href="#" class="learn-more">Learn more</a>
        </section>
    </main>

    <!-- JavaScript for show/hide and SweetAlert2 -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const agreeCheckbox = document.getElementById("agreeCheckbox");
            const rescheduleDropdown = document.getElementById("rescheduleDropdown");
        
            // Initially hide the reschedule dropdown
            rescheduleDropdown.style.display = "none";
        
            // Show or hide the Reschedule section based on checkbox
            agreeCheckbox.addEventListener("change", function () {
                if (this.checked) {
                    rescheduleDropdown.style.display = "block";
                } else {
                    rescheduleDropdown.style.display = "none";
                }
            });
        });

        document.getElementById("rescheduleForm").addEventListener("submit", function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            fetch("rescheduleReservation.php", {
                method: "POST",
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                alert(data);
                window.location.reload();  // Reload to show updated reservation details
            })
            .catch(error => console.error("Error:", error));
        });

        document.addEventListener("DOMContentLoaded", function () {
            const agreeCheckbox = document.getElementById("agreeCheckbox");
            const cancelBtn = document.getElementById("cancelBtn");
            
            agreeCheckbox.addEventListener("change", function() {
                // Show the button when the checkbox is checked, hide it when unchecked.
                cancelBtn.style.display = this.checked ? "block" : "none";
            });
        });

        document.addEventListener("DOMContentLoaded", function () {
            const cancelBtn = document.getElementById("cancelBtn");
            cancelBtn.addEventListener("click", function() {
                // Send a POST request to cancelReservation.php
                fetch("cancelReservation.php", { method: "POST" })
                    .then(response => response.text())
                    .then(data => {
                        alert(data);
                        // Reload the page to reflect the cancellation update
                        window.location.reload();
                    })
                    .catch(error => console.error("Error:", error));
            });
        });

    </script>
</body>
</html>