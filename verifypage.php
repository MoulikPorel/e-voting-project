<?php
include 'connect.php';

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if form data is received
if (isset($_POST['Full_Name']) && isset($_POST['Voter_ID']) && isset($_POST['Aadhar_Number']) && isset($_POST['Polling_Station_Code']) && isset($_POST['Voter_Serial_Number']) && isset($_FILES['Photo_of_Voter_ID'])) {
    $fullName = $_POST['Full_Name'];
    $voterID = $_POST['Voter_ID'];
    $aadharNumber = $_POST['Aadhar_Number'];
    $pollingStationCode = $_POST['Polling_Station_Code'];
    $voterSerialNumber = $_POST['Voter_Serial_Number'];

    // Check if the file is uploaded
    if (is_uploaded_file($_FILES['Photo_of_Voter_ID']['tmp_name'])) {
        $photo = $_FILES['Photo_of_Voter_ID']['tmp_name'];
        $photoBlob = addslashes(file_get_contents($photo));

        // Check if Voter_ID, Aadhar_Number, or Voter_Serial_Number already exists
        $sql = "SELECT * FROM voterinfo WHERE Voter_ID = ? OR Aadhar_Number = ? OR Voter_Serial_Number = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi", $voterID, $aadharNumber, $voterSerialNumber);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Entry already exists
            echo "Error: An entry with the same Voter ID, Aadhar Number, or Voter Serial Number already exists.";
        } else {
            // Proceed with insert
            $sql = "INSERT INTO voterinfo (Full_Name, Voter_ID, Photo_of_Voter_ID, Aadhar_Number, Polling_Station_Code, Voter_Serial_Number) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssssi", $fullName, $voterID, $photoBlob, $aadharNumber, $pollingStationCode, $voterSerialNumber);

            if ($stmt->execute()) {
                echo '<script>
                alert("Successfully verified");
                window.location.href = "index.php";
              </script>';
        
            } else {
                echo "Error: " . $stmt->error;
            }
        }
        $stmt->close();
    } else {
        echo "Error: File upload failed.";
    }
} else {
    echo "Error: Form data not received.";
}

$conn->close();
?>
