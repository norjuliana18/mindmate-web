<?php
include('config.php');
session_start();

// Check if the user is logged in
if (!isset($_SESSION['userID']) || !isset($_SESSION['userRoles'])) {
    header("Location: login.php");
    exit;
}

// Fetch the logged-in user's ID and role from the session
$loggedInUserID = $_SESSION['userID']; 
$userRoles = $_SESSION['userRoles'];

// Ensure that only users with userRoles = 2 can delete appointments
if ($userRoles != 2) {
    echo "You do not have permission to delete appointments.<br>";
    echo '<a href="appointment.php">Back</a>';
    exit;
}

// This action is called when the Delete link is clicked
if (isset($_GET["id"]) && $_GET["id"] != "") {
    $id = $_GET["id"];
    
    // Check if the appointment exists
    $sql = "SELECT * FROM appointment WHERE apptID = '$id'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        // Proceed with deleting the appointment
        $deleteSQL = "DELETE FROM appointment WHERE apptID = '$id'";

        if (mysqli_query($conn, $deleteSQL)) {
            echo "Appointment deleted successfully!<br>";
            echo '<a href="appointment.php">Back</a>';
        } else {
            echo "Error deleting appointment: " . mysqli_error($conn) . "<br>";
            echo '<a href="appointment.php">Back</a>';
        }
    } else {
        echo "Appointment not found.<br>";
        echo '<a href="appointment.php">Back</a>';
    }
}

mysqli_close($conn);
?>
