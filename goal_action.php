<?php
// Include the database connection
include 'config.php';
session_start();

// Check if the user is logged in
if (!isset($_SESSION['userID'])) {
    header("Location: login.php");
    exit;
}

// Get the user's ID from the session
$userID = $_SESSION['userID'];

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the form data and sanitize it
    $goalTitle = mysqli_real_escape_string($conn, $_POST['goalTitle']);
    $goalDescription = mysqli_real_escape_string($conn, $_POST['goalDescription']);
    $goalDate = mysqli_real_escape_string($conn, $_POST['goalDate']);

    // Prepare the SQL statement to insert the goal into the database
    $sql = "INSERT INTO goal (userID, goalTitle, goalCont, goalDate, goalStatus) VALUES (?, ?, ?, ?, ?)";

    // Set goalStatus as "In Progress" initially
    $goalStatus = "In Progress";

    // Use prepared statements to execute the SQL query securely
    if ($stmt = mysqli_prepare($conn, $sql)) {
        // Bind parameters
        mysqli_stmt_bind_param($stmt, "issss", $userID, $goalTitle, $goalDescription, $goalDate, $goalStatus);

        // Execute the statement
        if (mysqli_stmt_execute($stmt)) {
            // Redirect back to the goal page or dashboard with a success message
            header("Location: goal.php");
            exit;
        } else {
            echo "Error: " . mysqli_error($conn);
        }

        // Close the statement
        mysqli_stmt_close($stmt);
    } else {
        echo "Error preparing statement: " . mysqli_error($conn);
    }
}

// Close the database connection
mysqli_close($conn);
?>
