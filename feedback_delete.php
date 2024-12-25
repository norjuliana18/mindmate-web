<?php
// Include the database connection and start the session
include 'config.php';
session_start();

// Check if the user is logged in
if (!isset($_SESSION['userID'])) {
    header("Location: login.php");
    exit;
}

// Check if the user has the appropriate role
if ($_SESSION['userRoles'] != 2) {
    echo "<script type='text/javascript'>
            alert('Unauthorized access. Only Student can delete feedback.');
            window.location.href = 'feedback.php';
          </script>";
    exit;
}

// Get the feedback ID from the request
if (isset($_GET['feedbID'])) {
    $feedbID = mysqli_real_escape_string($conn, $_GET['feedbID']);
    $userID = $_SESSION['userID'];

    // Check if the feedback belongs to the logged-in user
    $check_sql = "SELECT * FROM feedback WHERE feedbID = '$feedbID' AND userID = '$userID'";
    $check_result = mysqli_query($conn, $check_sql);

    if (mysqli_num_rows($check_result) > 0) {
        // Feedback belongs to the user; show confirmation dialog
        echo "<script type='text/javascript'>
                var confirmDelete = confirm('Are you sure you want to delete this feedback?');
                if (confirmDelete) {
                    window.location.href = 'feedback_delete_confirm.php?feedbID=$feedbID';  // Proceed with deletion
                } else {
                    window.location.href = 'feedback.php';  // Redirect back to feedback page
                }
              </script>";
    } else {
        echo "<script type='text/javascript'>
                alert('Feedback not found or you don\'t have permission to delete it.');
                window.location.href = 'feedback.php';
              </script>";
    }
} else {
    echo "<script type='text/javascript'>
            alert('Invalid request. Feedback ID is missing.');
            window.location.href = 'feedback.php';
          </script>";
}

// Close the database connection
mysqli_close($conn);
?>

