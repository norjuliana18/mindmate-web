<?php
include("config.php");
session_start();

// Check if the user is logged in
if (!isset($_SESSION['userID'])) {
    header("Location: login.php"); // Redirect to login if not logged in
    exit();
}

// Check if the file is uploaded
if (isset($_FILES['userProf'])) {
    $userID = $_SESSION['userID'];
    $userProf = $_FILES['userProf']['name'];

    // Validate the file
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
    $fileSize = $_FILES['userProf']['size'];
    $fileExt = strtolower(pathinfo($userProf, PATHINFO_EXTENSION));

    // Check if file type is valid
    if (in_array($fileExt, $allowedExtensions)) {
        // Check file size (max 2MB)
        if ($fileSize <= 2 * 1024 * 1024) {
            $targetDir = "uploads/";
            $targetFile = $targetDir . basename($userProf);

            // Move the uploaded file to the target directory
            if (move_uploaded_file($_FILES["userProf"]["tmp_name"], $targetFile)) {
                // Update the user's profile picture in the database
                $update_sql = "UPDATE user SET userProf = '$userProf' WHERE userID = '$userID'";
                if (mysqli_query($conn, $update_sql)) {
                    // Redirect back to the appropriate profile page based on userRoles
                    if ($_SESSION['userRoles'] == 1) {
                        // Therapist role
                        header("Location: thera_profile.php?success=Profile picture updated successfully.");
                    } elseif ($_SESSION['userRoles'] == 2) {
                        // User role
                        header("Location: user_profile.php?success=Profile picture updated successfully.");
                    } else {
                        // Unknown role fallback
                        header("Location: index.php?error=Unknown user role.");
                    }
                    exit();
                } else {
                    echo "Error: Failed to update profile picture in the database.";
                }
            } else {
                echo "Error: Error uploading the file.";
            }
        } else {
            echo "Error: File size exceeds the 2MB limit.";
        }
    } else {
        echo "Error: Invalid file type. Only JPG, JPEG, PNG, and GIF are allowed.";
    }
} else {
    echo "Error: No file uploaded.";
}

mysqli_close($conn);
?>
