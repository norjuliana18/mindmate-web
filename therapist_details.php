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

// Get therapist details
if (isset($_GET['theraID'])) {
    $theraID = mysqli_real_escape_string($conn, $_GET['theraID']);

    // Query to fetch therapist details from database
    $query = "SELECT * FROM therapist WHERE theraID = '$theraID'";
    $result = mysqli_query($conn, $query);

    if ($result && $row = mysqli_fetch_assoc($result)) {
        // Therapist details available
        $theraName = $row['theraName'];
        $theraDesc = $row['theraDesc'];
        $theraImage = 'uploads/' . $row['theraImage'];
        $theraContact = $row['theraContact'];
        $theraAdd = $row['theraAdd'];
    } else {
        // No therapist found
        echo "<p class='error-message'>No therapist found.</p>";
        exit;
    }
} else {
    // Redirect if no therapist ID is passed
    echo "<p class='error-message'>No therapist ID provided.</p>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Therapist Details</title>
    <link rel="stylesheet" href="css/style.css"> <!-- General Styles -->
    <link rel="stylesheet" href="css/therapist_details.css"> <!-- Specific Styles for Therapist Details -->
</head>

<body>

<!-- Navbar Section Starts Here -->
<?php
// Conditionally display navbar based on userRoles
if ($userRoles == 1) {
    echo '
    <section class="navbar">
        <div class="container">
            <div class="logo">
                <a href="#" title="Logo">
                    <img src="img/mm(1).png" alt="Mindmate Logo" class="img-responsive">
                </a>
            </div>

            <div class="menu">
                <ul class="menu-left">
                    <li><a href="therapist_dash.php">Home</a></li>
                    <li><a href="upload_resource.php">Resource</a></li>
                    <li><a href="goal.php">Goal</a></li>
                    <li><a href="appointment.php">Appointment</a></li>
					<li><a href="add_availability.php">Availability</a></li>
                    <li><a href="feedback.php">Feedback</a></li>
                </ul>

                <div class="menu-right">
                    <a href="thera_profile.php" title="Profile" class="profile-icon-link">
                        <img src="https://img.icons8.com/?size=100&id=15263&format=png&color=000000" class="profile-icon" />
                    </a>
                    <a href="logout.php"><button class="btnLogout-popup">Logout</button></a>
                </div>
            </div>

            <div class="clearfix"></div>
        </div>
    </section>';
} else if ($userRoles == 2) {
    echo '
    <section class="navbar">
        <div class="container">
            <div class="logo">
                <a href="#" title="Logo">
                    <img src="img/mm(1).png" alt="Mindmate Logo" class="img-responsive">
                </a>
            </div>

            <div class="menu">
                <ul class="menu-left">
                    <li><a href="dashboard.php">Home</a></li>
                    <li><a href="resource.php">Resource</a></li>
                    <li><a href="therapist.php">Therapist</a></li>
                    <li><a href="goal.php">Goal</a></li>
                    <li><a href="appointment.php">Appointment</a></li>
                    <li><a href="feedback.php">Feedback</a></li>
                </ul>

                <div class="menu-right">
                    <a href="user_profile.php" title="Profile" class="profile-icon-link">
                        <img src="https://img.icons8.com/?size=100&id=15263&format=png&color=000000" class="profile-icon" />
                    </a>
                    <a href="logout.php"><button class="btnLogout-popup">Logout</button></a>
                </div>
            </div>

            <div class="clearfix"></div>
        </div>
    </section>';
}
?> 


<!-- Therapist Profile Section -->
<section class="therapist-details">
    <div class="container">
        <div class="therapist-box">
            <div class="therapist-img">
                <img src="<?php echo htmlspecialchars($theraImage); ?>" alt="<?php echo htmlspecialchars($theraName); ?>" class="img-responsive img-curve">
            </div>
			
            <div class="therapist-desc">
                <h2><?php echo htmlspecialchars($theraName); ?></h2>
                <p class="therapist-location"><strong>Location:</strong> <?php echo htmlspecialchars($theraAdd); ?></p>
                <p class="therapist-detail"><strong>Contact:</strong> <?php echo htmlspecialchars($theraContact); ?></p>
                <p class="therapist-detail"><strong>Description:</strong> <?php echo nl2br(htmlspecialchars($theraDesc)); ?></p>
            </div>

            <div class="book-appointment">
                <form action="book_appointment.php" method="GET" class="appointment-form">
                    <input type="hidden" name="theraID" value="<?php echo htmlspecialchars($theraID); ?>">
                    <button type="submit" class="btn-primary">Book Appointment</button>
                </form>
            </div>
        </div>
    </div>
</section>
</body>
</html>