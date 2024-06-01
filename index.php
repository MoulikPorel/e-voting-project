<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: login.html");
    exit();
}

// Database connection
include 'connect.php';

// Fetch user data from the database
$email = $_SESSION['email'];
$stmt = $conn->prepare("SELECT name, number, email FROM voting WHERE email = ?");
if ($stmt === false) {
    die('Prepare failed: ' . htmlspecialchars($conn->error));
}

$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

// Check if user data is fetched correctly
if ($row) {
    $name = htmlspecialchars($row['name']);
    $number = htmlspecialchars($row['number']);
    $email = htmlspecialchars($row['email']);
} else {
    echo '<script>
        window.location.href = "login.html";
        alert("Failed to fetch user data.");
    </script>';
    exit();
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Voting Website</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #1a1a1a;
      color: #fff;
    }
    .container {
      max-width: 500px;
      margin: 0 auto;
      padding: 20px;
      text-align: center;
    }
    .btn {
      display: inline-block;
      padding: 10px 20px;
      margin-top: 20px;
      margin: 10px;
      font-size: 18px;
      cursor: pointer;
      border: none;
      border-radius: 4px;
      background-color: #007bff;
      color: #fff;
      text-decoration: none;
    }
    .btn:hover {
      background-color: #0056b3;
    }
    .card {
      background-color: #333;
      border-radius: 8px;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
      padding: 20px;
      margin-top: 20px;
      text-align: center;
    }
    .card img {
      width: 100px;
      border-radius: 50%;
      margin-right: 20px;
    }
    .input-group {
      margin-top: 20px;
    }
    .input-group input[type="text"] {
      width: 300px;
      padding: 10px;
      font-size: 16px;
    }
    .input-group input[type="file"] {
      margin-top: 10px;
    }
  </style>
</head>
<body>
  <div class="container">
    <h1>Welcome to e-Voting</h1>
    <p>Please choose an option:</p>
    <button class="btn" onclick="window.location.href='/verify-account'">Verify Account</button>

    <!-- User Information Card -->
    <div class="card">
      <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQdx7HjRXcShPt-oR-OPPYLL8IZWWiNIcnvPAASBPvGIw&s" alt="Profile Picture">
      <div>
        <h2><?php echo $name; ?></h2>
        <p>Mobile Number: <span><?php echo $number; ?></span></p>
        <p>Email: <span><?php echo $email; ?></span></p>
      </div>
    </div>

    <button class="btn" onclick="window.location.href='votingpage.html'">Vote Now</button>
    <button class="btn" onclick="window.location.href='logout.php'">Logout</button>
  </div>
</body>
</html>
