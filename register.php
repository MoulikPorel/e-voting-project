<?php
include 'connect.php';

if (isset($_POST['submit'])) {

    // Function to check if an entry exists
    function entryExists($conn, $email, $name, $number) {
        $stmt = $conn->prepare("SELECT * FROM voting WHERE email = ? OR name = ? OR number = ?");
        $stmt->bind_param("sss", $email, $name, $number);
        $stmt->execute();
        $result = $stmt->get_result();
        $exists = $result->num_rows > 0;
        $stmt->close();
        return $exists;
    }

    // Handling form submission
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Get form input values
        $name = $_POST["name"];
        $email = $_POST["email"];
        $number = $_POST["number"];
        $password = $_POST["password"];

        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        // Check if entry exists
        if (entryExists($conn, $email, $name, $number)) {
            echo '<script>
                    alert("User Data already exists!");
                    window.location.href = "login.html";
                  </script>';
        } else {
            // Prepare and bind statement to insert data
            $stmt = $conn->prepare("INSERT INTO voting (name, email, number, password) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $name, $email, $number, $hashed_password);

            // Execute the statement
            if ($stmt->execute()) {
                echo '<script>
                alert("Successfully Signed Up");
                window.location.href = "login.html";
              </script>';
            } else {
                echo '<script>
                alert("Error inserting data!");
                window.location.href = "signup.html";
              </script>';
            }

            // Close the statement
            $stmt->close();
        }
    }

    // Close the database connection
    $conn->close();
}
?>
