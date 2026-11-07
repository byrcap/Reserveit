<?php
  session_start();

  // If the user is not logged in, redirect to the login page
  if (!isset($_SESSION['user_id'])) {
      header("Location: accountpage.html");
      exit();
  }

  $servername = "localhost";
  $db_username = "root";
  $db_password = "";
  $database = "reserveitdb";

  $conn = new mysqli($servername, $db_username, $db_password, $database);
  if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
  }

  $user_id = $_SESSION['user_id'];
    $sql = "SELECT * FROM reservations WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        header("Location: reservestat.php");
        exit();
    }
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bulldoggy Gym Booking</title>
    <link rel="stylesheet" href="mainstyle.css">
  </head>
  <body>
    <header>
      <div class="logo">RESERVE IT! NU LAGUNA</div>
      <nav class = "nav">
        <a href="editprof.html">Edit Profile</a>
        <a href="lostandfound.html">Lost and Found</a>
        <button class="logout" onclick="location.href='logout.php'">Logout</button>
      </nav>
    </header>

    <main>
      <section class="booking-section">
        <h1><span class="italic">BOOK</span> <span class="highlight">NOW</span></h1>
        
        <form action="reserve.php" method="post">
          <div class="input-group">
            <label for="facility">Select Facility</label>
            <select id="facility" name="facility">
              <option value="">Choose a Facility</option>
              <option value="gym">High-Performance Gym</option>
              <option value="pool">Aquatic Center</option>
              <option value="tennis">Tennis Court</option>
              <option value="hoops">Hoops Center</option>
              <option value="multipurpose">Multipurpose Gym</option>
              <option value="studio">Multimedia Studio</option>
              <option value="dormitel">Inspire Dormitel</option>
            </select>
          </div>
          <p id="feeDisplay">Fee: PHP 0</p>

          <div class="input-group">
            <label for="date">Reservation date</label>
            <input type="date" id="date" name="date">
          </div>
          <div class="input-group">
            <label for="time">Reservation Time</label>
          
            <select id="time" name="time" required>
              <option value="06:00">06:00 AM</option>
              <option value="07:00">07:00 AM</option>
              <option value="08:00">08:00 AM</option>
              <option value="09:00">09:00 AM</option>
              <option value="10:00">10:00 AM</option>
              <option value="11:00">11:00 AM</option>
              <option value="12:00">12:00 PM</option>
              <option value="13:00">01:00 PM</option>
              <option value="14:00">02:00 PM</option>
              <option value="15:00">03:00 PM</option>
              <option value="16:00">04:00 PM</option>
              <option value="17:00">05:00 PM</option>
              <option value="18:00">06:00 PM</option>
              <option value="19:00">07:00 PM</option>
              <option value="20:00">08:00 PM</option>
              <option value="21:00">09:00 PM</option>
            </select>

          </div>
          <div class="input-group">
            <label for="payment">Payment method</label>
            <select id="payment" name="payment">
              <option>Select Payment Method</option>
              <option>E-Payment</option>
              <option>Over-the-Counter</option>
              <option>Debit/Credit Card</option>
            </select>
          </div>
          <button type="submit" class="confirm">Confirm and <span class="highlight">Reserve</span></button>
        </form>
      </section>

      <section class="user-profile">
        <h2>
          WELCOME, 
          <span class="highlight">
            <?php 
              // Output the user's full name
              echo htmlspecialchars($_SESSION['first_name'] . " " . $_SESSION['last_name']); 
            ?>
          </span>
        </h2>
        <img src="defaultphoto.png" alt="User Photo" class="profile-pic">
        <p>ACCOUNT TYPE: 
          <?php 
            // Output the user's account type
            echo htmlspecialchars(strtoupper($_SESSION['account_type'])); 
          ?>
        </p>
      </section>
    </main>
    <script>
      // Mapping from facility value to fee
      const feeMapping = {
        gym: 1000,
        pool: 1000,
        tennis: 5000,
        hoops: 2000,
        multipurpose: 1000,
        studio: 1000,    
        dormitel: 3000
      };

      const facilitySelect = document.getElementById('facility');
      const feeDisplay = document.getElementById('feeDisplay');

      facilitySelect.addEventListener('change', function() {
        const selected = this.value;
        // If a valid facility is selected, show the fee; otherwise, show 0.
        const fee = feeMapping[selected] || 0;
        feeDisplay.textContent = "Fee: PHP " + fee;
      });
    </script>
</body>
</html>
