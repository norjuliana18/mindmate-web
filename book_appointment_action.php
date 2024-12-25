<?php
// Include database connection
include 'config.php';
session_start();

// Fetch user ID from session
$userID = $_SESSION['userID'];

// Check if the form has been submitted
if (isset($_POST['theraID'], $_POST['apptDate'], $_POST['apptTime'], $_POST['fullname'], $_POST['contact'], $_POST['email'])) {
    // Get the form data
    $theraID = mysqli_real_escape_string($conn, $_POST['theraID']);
    $apptDate = mysqli_real_escape_string($conn, $_POST['apptDate']);
    $apptTime = mysqli_real_escape_string($conn, $_POST['apptTime']);
    $fullname = mysqli_real_escape_string($conn, $_POST['fullname']);
    $contact = mysqli_real_escape_string($conn, $_POST['contact']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);

    // Insert the booking into the database
    $insertQuery = "INSERT INTO appointment (userID, theraID, apptDate, apptTime, fullname, contact, email) 
                    VALUES ('$userID', '$theraID', '$apptDate', '$apptTime', '$fullname', '$contact', '$email')";
    
    if (mysqli_query($conn, $insertQuery)) {
        echo "<p>Appointment booked successfully!</p>";
    } else {
        echo "<p>Error booking appointment: " . mysqli_error($conn) . "</p>";
    }
} else {
    echo "<p>Missing required booking details. Please try again.</p>";
}
?>
