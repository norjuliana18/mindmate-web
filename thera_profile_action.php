<?php
include("config.php");
session_start();

// Check if the user is logged in
if (!isset($_SESSION['userID'])) {
    header("Location: login.php");
    exit();
}

// Fetch user ID from session
$userID = $_SESSION['userID'];

// Handle form submission for updating therapist profile
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['updateTherapist'])) {
    // Get form data and sanitize inputs
    $theraName = mysqli_real_escape_string($conn, $_POST['theraName']);
    $theraContact = mysqli_real_escape_string($conn, $_POST['theraContact']);
    $theraDesc = mysqli_real_escape_string($conn, $_POST['theraDesc']);
    $theraAdd = mysqli_real_escape_string($conn, $_POST['theraAdd']); // Add address field

    // Handle file upload for professional profile picture
    $theraImage = '';
    if (isset($_FILES['theraImage']) && $_FILES['theraImage']['error'] == 0) {
        $fileName = $_FILES['theraImage']['name'];
        $fileTmp = $_FILES['theraImage']['tmp_name'];
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

        // Check if the file extension is allowed
        if (in_array($fileExt, $allowedExtensions)) {
            // Generate a unique name and move file to uploads directory
            $theraImage = "therapist_" . $userID . "_" . time() . ".$fileExt";
            move_uploaded_file($fileTmp, "uploads/$theraImage");
        } else {
            echo "Invalid file type. Only JPG, JPEG, PNG, and GIF files are allowed.";
            exit();
        }
    }

    // Prepare the SQL query to insert or update the therapist profile
    $query = "INSERT INTO therapist (theraID, theraName, theraContact, theraDesc, theraAdd, theraImage, created_at)
              VALUES ('$userID', '$theraName', '$theraContact', '$theraDesc', '$theraAdd', '$theraImage', NOW())
              ON DUPLICATE KEY UPDATE 
                  theraName='$theraName', theraContact='$theraContact', theraDesc='$theraDesc', 
                  theraAdd='$theraAdd', 
                  theraImage=IF('$theraImage' != '', '$theraImage', theraImage), updated_at=NOW()";

    // Execute the query and handle success or failure
    if (mysqli_query($conn, $query)) {
        header("Location: thera_profile.php?status=success");
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}

// Close the database connection
mysqli_close($conn);
?>
