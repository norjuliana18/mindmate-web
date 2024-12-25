<?php
// Include the database connection and start the session
include 'config.php';
session_start();

// Check if the user is logged in
if (!isset($_SESSION['userID'])) {
    header("Location: login.php");
    exit;
}

// Assuming you have a 'userRoles' field in your database to distinguish user types
$userRoles = $_SESSION['userRoles'];  // Fetch the user's role from the session
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mindmate Dashboard</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
<!-- Navbar Section Starts Here -->
    <section class="navbar">
        <div class="container">
            <div class="logo">
                <a href="#" title="Logo">
                    <img src="img/mm(1).png" alt="Mindmate Logo" class="img-responsive">
                </a>
            </div>

            <div class="menu">
                <ul class="menu-left">
                    <li><a href="index.php">Home</a></li>
                    <li><a href="about.php">About</a></li>
                    <li><a href="contact.php">Contact</a></li>
                    <li><a href="register.php">SignUp</a></li>
                </ul>

                <div class="menu-right">
                    <!-- Profile Icon Link -->
                    <a href="login.php" title="Profile" class="profile-icon-link">
                        <img src="https://img.icons8.com/?size=100&id=15263&format=png&color=000000" class="profile-icon" />
                    </a>

                    <!-- Login Button -->
                    <a href="login.php"><button class="btnLogin-popup">Login</button></a>
                </div>
            </div>

            <div class="clearfix"></div>
        </div>
    </section>
</body>
    <!-- Navbar Section Ends Here -->

    <div class="body2">
        <div class="wrapper">
            <div class="form-box">
                <h2 style="color: #000">Reset Password</h2>
                <form action="changepass_action.php" method="post">
                    <!-- Email Input for Verification -->
                    <div class="input box">
                        <span class="icon"><ion-icon name="mail"></ion-icon></span>
                        <label style="color: #000;">Email</label>
                        <input type="email" name="userEmail" required>
                    </div>

                    <!-- New Password Input -->
                    <div class="input box">
                        <span class="icon"><ion-icon name="lock-open"></ion-icon></span>
						<label style="color: #000;">New Password</label>
                        <input type="password" name="userPwd" required>
                    </div>

                    <!-- Confirm New Password Input -->
                    <div class="input box">
                        <span class="icon"> <ion-icon name="lock-closed"></ion-icon></span>
						<label style="color: #000;">Confirm Password</label>
                        <input type="password" name="confirmPassword" required>
                    </div>

                    <!-- Submit Button -->
					<div class="button-container">
                    <button type="submit" class="btn btn-primary">Reset Password</button>
					</div>
					<div class="login-register">
					<p><a href="login.php">Back</a>
                    </p>
                </form>
            </div>
        </div>
    </div>

    <script src="js/script.js"></script>
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</body>
</html>
