<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logout Confirmation</title>
    <!-- Include any additional styles or scripts you need -->
</head>

<body>

    <script>
        // Display confirmation pop-up
        var confirmLogout = confirm("Are you sure you want to logout?");
        
        // Redirect to login.php if confirmed, otherwise stay on the current page
        if (confirmLogout) {
            window.location.href = "login.php";
        } else {
            // Redirect to another page or perform any other action as needed
            window.location.href = "index.php";
        }
    </script>

</body>