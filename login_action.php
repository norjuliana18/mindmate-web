<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include("config.php");
?>
<html>
<head>
    <title>Login Action</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="css/style.css" media="screen" />
</head>
<body>
    <?php
    // Login values from the login form
    $userEmail = mysqli_real_escape_string($conn, $_POST['userEmail']); 
    $userPwd = mysqli_real_escape_string($conn, $_POST['userPwd']);
    $sql = "SELECT * FROM user WHERE userEmail = '$userEmail' LIMIT 1";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) == 1) {    
        // Check password hash
        $row = mysqli_fetch_assoc($result);
        if (password_verify($userPwd, $row['userPwd'])) {
            $_SESSION["userID"] = $row["userID"];
            $_SESSION["username"] = $row["username"];
            $_SESSION["userRoles"] = $row["userRoles"];
            // Set logged in time
            $_SESSION['loggedin_time'] = time();

            // Redirect based on userRoles
            if ($_SESSION["userRoles"] == "1") {
                echo "<script>alert('Login successful! Redirecting to therapist dashboard.'); window.location.href = 'therapist_dash.php';</script>";
            } elseif ($_SESSION["userRoles"] == "2") {
                echo "<script>alert('Login successful! Redirecting to user dashboard.'); window.location.href = 'dashboard.php';</script>";
            } else {
                echo "<script>alert('Invalid user role.'); window.location.href = 'login.php';</script>";
            }
        } else {
            echo "<script>alert('Login error, user email and password are incorrect.'); window.location.href = 'login.php';</script>";
        }
    } else {
        echo "<script>alert('Login error, user $userEmail does not exist.'); window.location.href = 'login.php';</script>";
    } 

    mysqli_close($conn);
    ?>
</body>
</html>
