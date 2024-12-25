<?php
include('config.php');

// Initialize variables
$id = $fullname = $contact = $email = $apptDate = $apptTime = $theraID = $status = "";

session_start();
$userRoles = $_SESSION['userRoles'];  // Fetch the user's role from the session

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the form data
    $id = $_POST["apptID"];
    $fullname = trim($_POST["fullname"]);
    $contact = $_POST["contact"];
    $email = $_POST["email"];
    $apptDate = $_POST["apptDate"];
    $apptTime = $_POST["apptTime"];
    $therapistName = $_POST["therapistName"];
    
    // Only therapists can edit the status
    if ($userRoles == 1) {
        $status = $_POST["status"];  // Therapist can update status
    }

    // Fetch the therapist ID based on the therapist name
    $sql = "SELECT theraID FROM therapist WHERE theraName = '$therapistName'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        // Get the therapist ID
        $row = mysqli_fetch_assoc($result);
        $theraID = $row['theraID'];
    } else {
        // Invalid therapist name
        echo "<script>
                alert('Error: Invalid therapist name.');
                window.location.href = 'appointment.php'; // Redirect to appointment page
              </script>";
        exit;
    }

    // Prepare the SQL query to update the appointment
    $sql = "UPDATE appointment SET 
                fullname = '$fullname', 
                contact = '$contact', 
                email = '$email', 
                apptDate = '$apptDate', 
                apptTime = '$apptTime', 
                theraID = '$theraID'";

    // If the user is a therapist, include the status field in the update
    if ($userRoles == 1) {
        $sql .= ", status = '$status'";
    }

    $sql .= " WHERE apptID = '$id'";

    // Execute the query and handle the result
    $status = update_DBTable($conn, $sql);

    if ($status) {
        // Success message
        echo "<script>
                alert('Appointment data updated successfully!');
                window.location.href = 'appointment.php'; // Redirect to appointment page
              </script>";
    } else {
        // Error message
        echo "<script>
                alert('Error updating appointment.');
                window.location.href = 'appointment.php'; // Redirect to appointment page
              </script>";
    }
}

mysqli_close($conn);

// Function to execute SQL queries
function update_DBTable($conn, $sql)
{
    if (mysqli_query($conn, $sql)) {
        return true;
    } else {
        echo "Error: " . $sql . " : " . mysqli_error($conn) . "<br>";
        return false;
    }
}
?>
