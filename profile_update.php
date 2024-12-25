<?php
include("config.php");
session_start();

// Check if the user is logged in
if (!isset($_SESSION['userID'])) {
    header("Location: login.php");
    exit();
}

// Fetch user details from the database
$userID = $_SESSION['userID'];
$sql = "SELECT * FROM user WHERE userID = '$userID'";
$result = mysqli_query($conn, $sql);
$user = mysqli_fetch_assoc($result);

// Handle the profile update request
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $userEmail = mysqli_real_escape_string($conn, $_POST['userEmail']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $userPwd = $_POST['userPwd'];
    $confirmPwd = $_POST['confirmPwd'];
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $dob = $_POST['dob'];
    $gender = $_POST['gender'];
	$userContact = mysqli_real_escape_string($conn, $_POST['userContact']);
    $userProf = $_FILES['userProf']['name'];

    // Validate password if provided
    if ($userPwd && !password_verify($userPwd, $user['userPwd'])) {
        $_SESSION['message'] = "Incorrect password. Please try again.";
    } else {
        if ($userPwd && $userPwd != $confirmPwd) {
            $_SESSION['message'] = "Passwords do not match.";
        } else {
            // Process profile picture upload
            if ($userProf) {
                $target_dir = "uploads/";
                $target_file = $target_dir . basename($userProf);
                move_uploaded_file($_FILES["userProf"]["tmp_name"], $target_file);
            } else {
                $target_file = $user['userProf']; // Keep old profile picture if none uploaded
            }

            // Update the profile in the database
            $update_sql = "UPDATE user SET 
                           userEmail = '$userEmail',
                           username = '$username',
                           address = '$address',
                           dob = '$dob',
                           gender = '$gender',
						usercontact = '$userContact',
                           userProf = '$target_file' 
                           WHERE userID = '$userID'";

            if (mysqli_query($conn, $update_sql)) {
                $_SESSION['message'] = "Profile updated successfully!";
            } else {
                $_SESSION['message'] = "Failed to update profile. Please try again.";
            }
        }
    }
    header("Location: user_profile.php"); // Redirect to the profile page
    exit();
}

mysqli_close($conn);
?>
