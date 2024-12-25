<?php
include 'config.php';
session_start();

// Check if the user is logged in and has a valid role
if (!isset($_SESSION['userID']) || $_SESSION['userRoles'] != 2) {
    echo "Unauthorized access. Only users can edit or delete feedback.";
    echo "<br><a href='feedback.php'>Back to Feedback</a>";
    exit;
}

$userID = $_SESSION['userID'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Action type: Add, Edit, or Delete
    $action = isset($_POST['action']) ? $_POST['action'] : 'add';

    if ($action === 'add') {
        // Escape user inputs to prevent SQL injection
        $therapist = mysqli_real_escape_string($conn, $_POST['therapist']);
        $rating = mysqli_real_escape_string($conn, $_POST['rating']);
        $content = mysqli_real_escape_string($conn, $_POST['content']);

        // Check if an image file is uploaded
        if (!empty($_FILES["image"]["name"])) {
            // Set maximum file size (e.g., 2MB)
            $maxFileSize = 2 * 1024 * 1024; // 2MB
            $fileSize = $_FILES["image"]["size"];
            
            // Check if the uploaded image size exceeds the limit
            if ($fileSize > $maxFileSize) {
                echo "Error: File size exceeds the 2MB limit.";
                exit;
            }

            // Proceed with file upload if the size is valid
            $target_dir = "uploads/";
            $target_file = $target_dir . basename($_FILES["image"]["name"]);
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            // Check if the file is an actual image
            $check = getimagesize($_FILES["image"]["tmp_name"]);
            if ($check !== false) {
                if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                    // Insert feedback with image
                    $sql = "INSERT INTO feedback (userID, theraID, feedbRate, feedbCont, feedbImage, created_at)
                            VALUES ('$userID', '$therapist', $rating, '$content', '$target_file', NOW())";

                    if (mysqli_query($conn, $sql)) {
                        echo "Feedback submitted successfully.";
                    } else {
                        echo "Error: " . mysqli_error($conn);
                    }
                } else {
                    echo "Error uploading file.";
                }
            } else {
                echo "File is not a valid image.";
            }
        } else {
            // Insert feedback without image
            $sql = "INSERT INTO feedback (userID, theraID, feedbRate, feedbCont, created_at)
                    VALUES ('$userID', '$therapist', $rating, '$content', NOW())";

            if (mysqli_query($conn, $sql)) {
                echo "Feedback submitted successfully.";
            } else {
                echo "Error: " . mysqli_error($conn);
            }
        }
    } elseif ($action === 'edit') {
        $feedbID = mysqli_real_escape_string($conn, $_POST['feedbID']);
        $rating = mysqli_real_escape_string($conn, $_POST['rating']);
        $content = mysqli_real_escape_string($conn, $_POST['content']);

        $sql = "UPDATE feedback 
                SET feedbRate = '$rating', feedbCont = '$content', updated_at = NOW() 
                WHERE feedbID = '$feedbID' AND userID = '$userID'";

        if (mysqli_query($conn, $sql)) {
            echo "Feedback updated successfully.";
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    } elseif ($action === 'delete') {
        $feedbID = mysqli_real_escape_string($conn, $_POST['feedbID']);

        $sql = "DELETE FROM feedback WHERE feedbID = '$feedbID' AND userID = '$userID'";

        if (mysqli_query($conn, $sql)) {
            echo "Feedback deleted successfully.";
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    }
    
    // Back button
    echo "<br><a href='feedback.php'>Back to Feedback</a>";
}

// Close the database connection
mysqli_close($conn);
?>
