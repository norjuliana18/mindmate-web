<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
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
	
            <div class="form-box login">
                <h1 style="text-align: center; color: #000; margin-bottom: 10px;">REGISTER</h1>
                <h2 style="color: #000;">AS</h2>
                <!-- Buttons for login and register -->
                <div class="login-register">
                    <form action="login.php" method="post">
                        <div>
                            <!-- Adds space between the buttons -->
                            <button type="submit" class="btn">Student</button>
                            <button type="submit" class="btn">Therapist</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="js/script.js"></script>
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</body>

</html>
