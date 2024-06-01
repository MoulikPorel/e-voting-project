<?php 
session_start();

// Database connection
include 'connect.php' ;

if(isset($_POST['submit'])){
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepare a statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT * FROM voting WHERE email = ?");
    if ($stmt === false) {
        die('Prepare failed: ' . htmlspecialchars($conn->error));
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    // Debugging: Check if the row is fetched correctly
    if ($row) {
        // Debugging: Check the password hash in the database
        // echo "Password hash from DB: " . $row['password']; // Uncomment for debugging

        // Verify the hashed password
        if (password_verify($password, $row['password'])) {
            // Save user details in session
            $_SESSION['name'] = $row['name'];
            $_SESSION['number'] = $row['number'];
            $_SESSION['email'] = $row['email'];
            header("Location: index.php");
            exit(); // Stop script execution after redirection
        } else {
            echo '<script>
                window.location.href = "login.html";
                alert("Login failed, Invalid username or password !!!");
            </script>';
        }
    } else {
        echo '<script>
            window.location.href = "login.html";
            alert("Login failed, Invalid username or password !!!");
        </script>';
    }

    $stmt->close();
}

$conn->close();
?>
