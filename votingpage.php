<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: login.html");
    exit();
}

// Database connection
$servername = "localhost"; // Replace with your server name
$username = "root"; // Replace with your database username
$password = ""; // Replace with your database password
$dbname = "voting"; // Replace with your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set character set to utf8
$conn->set_charset("utf8");

// Get user email from session
$email = $_SESSION['email'];

// Check if the user has already voted
$stmt = $conn->prepare("SELECT * FROM votes WHERE user_email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // User has already voted, redirect them back to the index page with an error message
    echo '<script>
    window.location.href = "index.php";
    alert("You have already Voted");
          </script>'; 
    exit();
}

// Process the form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['candidate_id'])) {
    $candidate_id = intval($_POST['candidate_id']);
    
    // Get current timestamp
    $voting_time = date('Y-m-d H:i:s');


    // Insert the vote into the database only after successful submission
    $stmt = $conn->prepare("INSERT INTO votes (user_email, candidate_id, vote_time) VALUES (?, ?, ?)");
    $stmt->bind_param("sis", $email, $candidate_id, $voting_time);
    if ($stmt->execute()) {
        // Update total votes for the candidate
        $stmt = $conn->prepare("UPDATE candidates SET total_votes = total_votes + 1 WHERE id = ?");
        $stmt->bind_param("i", $candidate_id);
        $stmt->execute();
        
        // Redirect the user back to the index page with a success message
        echo '<script>
        window.location.href = "index.php";
        alert("Sucesfully Voted");
              </script>';  
        exit();
    } else {
        // If there's an error while submitting the vote, redirect back with an error message
        echo '<script>
        window.location.href = "index.php";
        alert("You have already Voted");
              </script>'; 
        exit();
    }
}

// Close the database connection
$stmt->close();
$conn->close();
?>
