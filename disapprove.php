<?php
// Include the database connection
include 'config.php';

// Start the session
session_start();

// Check if the therapist is logged in
if (!isset($_SESSION['userID'])) {
    header("Location: login.php");
    exit;
}

$userRoles = $_SESSION['userRoles'];  // Get the logged-in user's role

// Ensure only therapists can disapprove
if ($userRoles != 1) {
    header("Location: dashboard.php");  // Redirect non-therapists to a different page
    exit;
}

// Get the appointment ID from the query string
if (isset($_GET['id'])) {
    $apptID = intval($_GET['id']);  // Ensure it's an integer to prevent SQL injection

    // Begin a transaction
    mysqli_begin_transaction($conn);

    try {
        // Update the status of the appointment to "Disapproved"
        $sqlUpdate = "UPDATE appointment SET status = 'Disapproved' WHERE apptID = ?";
        $stmtUpdate = mysqli_prepare($conn, $sqlUpdate);
        mysqli_stmt_bind_param($stmtUpdate, "i", $apptID);
        mysqli_stmt_execute($stmtUpdate);

        if (mysqli_stmt_affected_rows($stmtUpdate) > 0) {
            // Fetch the userID from the appointment
            $sqlFetch = "SELECT userID FROM appointment WHERE apptID = ?";
            $stmtFetch = mysqli_prepare($conn, $sqlFetch);
            mysqli_stmt_bind_param($stmtFetch, "i", $apptID);
            mysqli_stmt_execute($stmtFetch);
            mysqli_stmt_bind_result($stmtFetch, $userID);
            mysqli_stmt_fetch($stmtFetch);
            mysqli_stmt_close($stmtFetch);

            // Prepare the notification message
            $message = "Your appointment (ID: $apptID) has been disapproved.";

            // Insert the notification
            $sqlNotify = "INSERT INTO notifications (userID, message) VALUES (?, ?)";
            $stmtNotify = mysqli_prepare($conn, $sqlNotify);
            mysqli_stmt_bind_param($stmtNotify, "is", $userID, $message);
            mysqli_stmt_execute($stmtNotify);
            mysqli_stmt_close($stmtNotify);
        }

        // Commit the transaction
        mysqli_commit($conn);

        // Redirect back to the appointment list with success message
        header("Location: appointment.php?status=disapproved");
    } catch (Exception $e) {
        // Rollback the transaction on error
        mysqli_roll_back($conn);
        echo "Error updating appointment status: " . $e->getMessage();
    }

    mysqli_stmt_close($stmtUpdate);
}

mysqli_close($conn);
?>
