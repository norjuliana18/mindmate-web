<?php
// Include the database connection
include 'config.php';
session_start();

// Check if the user is logged in
if (!isset($_SESSION['userID'])) {
    header("Location: login.php");
    exit;
}

// Define the function to fetch recommended therapists based on past appointments
function getRecommendedTherapists($conn) {
    // Get the current logged-in user's ID
    $userID = $_SESSION['userID'];
    
    // Query to fetch therapist IDs from the appointments table based on the logged-in user's past appointments
    $sql = "SELECT DISTINCT therapist.theraID 
            FROM appointment
            INNER JOIN therapist ON appointment.theraID = therapist.theraID 
            WHERE appointment.userID = $userID";
    
    // Execute the query
    $result = mysqli_query($conn, $sql);

    // Check for query errors
    if (!$result) {
        die('Error executing query: ' . mysqli_error($conn));
    }

    // Fetch therapist IDs and return them as an array
    $therapists = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $therapists[] = $row['theraID'];
    }

    return $therapists;
}

// Fetch notification count for the user
function getNotificationCount($conn) {
    $userID = $_SESSION['userID'];

    // Query to count unread notifications
    $sql = "SELECT COUNT(*) AS notificationCount 
            FROM notifications 
            WHERE userID = $userID AND isRead = FALSE";

    $result = mysqli_query($conn, $sql);

    if (!$result) {
        die('Error fetching notifications: ' . mysqli_error($conn));
    }

    $data = mysqli_fetch_assoc($result);
    return $data['notificationCount'] ?? 0;
}

// Fetch recommended therapists
$recommendedTherapists = getRecommendedTherapists($conn);

// Fetch notification count
$notificationCount = getNotificationCount($conn);
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
    <!-- Navbar Section -->
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
                    <!-- Notification Icon with Count -->
                    <a href="notifications.php" title="Notifications" class="notification-link">
                        <span class="notification-icon">🔔</span>
                        <?php if ($notificationCount > 0): ?>
                            <span class="notification-count"><?= $notificationCount ?></span>
                        <?php endif; ?>
                    </a>
                    <a href="logout.php"><button class="btnLogout-popup">Logout</button></a>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
    </section>
<!-- Navbar Section Ends Here -->

    <!-- Therapist Search Section Starts Here -->
    <?php include 'therapist-search.php'; ?>
    <section class="therapist-search text-center">
    <div class="container">
        <!-- Search Form -->
        <form action="therapist.php" method="GET">
            <input type="search" name="search" placeholder="Search for Therapist.." required>
            <input type="submit" name="submit" value="Search" class="btn btn-primary">
        </form>
		<!-- Background Image -->
        <img src="img/639c883627c0a700193e6f9f.jpg" alt="Search Background" class="background-img">
    </div>
</section>
    <!-- Therapist Search Section Ends Here -->

    <!-- Dashboard Section Starts Here -->
    <section class="dashboard">
        <div class="container">
            <h2 class="text-center">Welcome to Mindmate!</h2>

            <!-- Company Introduction -->
            <div class="company-intro">
                <p>
                    Mindmate designed to address these critical needs by developing a space that offers a range of mental health resources
					and support services tailored to students. The aim is to provide a safe and supportive environment for students
					to manage their mental health, access professional therapy, and engage in self-care prcatices such as meditation.
                </p>
            </div>

            <!-- Appointment Tutorial -->
            <div class="appointment-tutorial-container">
                <h3>How to make an appointment ?</h3>
                <p>
                    Make an appointment with your chosen therapist is quick and easy! Follow these simple steps:
                </p>
                <ol>
                    <li>Log in to your account or sign up if you're new.</li>
                    <li>Explore our diverse range of therapists and choose your preferred one.</li>
                    <li>Click on the "Book Now" button and select the date and time for your appointment.</li>
                    <li>Provide any additional details or preferences.</li>
                    <li>Confirm your appointment, and you're all set!</li>
                </ol>
            </div>
<?php
// Fetch recommended therapists based on past appointments
$recommended_therapists = getRecommendedTherapists($conn);

if (!empty($recommended_therapists)) {
    echo "<div class='container'>";
    echo "<h2 class='text-center'>Talk to These Therapists Again:</h2>";
    echo "<div class='recommended-therapists'>";

    // Iterate through the list of recommended therapist IDs
    foreach ($recommended_therapists as $theraID) {
        // Fetch therapist details from the database
        $query = "SELECT * FROM therapist WHERE theraID = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('i', $theraID);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            $therapist = $result->fetch_assoc();

            // Render therapist information
            echo "<div class='therapist-box rec-box'>";  // Use the therapist-box class for consistent styling
            echo "<div class='therapist-img rec-img'>";   // Ensure the image container matches the styles
            $theraImage = !empty($therapist['theraImage']) ? 'uploads/' . $therapist['theraImage'] : 'img/default-profile.png';
            echo "<img src='" . htmlspecialchars($theraImage) . "' alt='" . htmlspecialchars($therapist['theraName']) . "' class='img-responsive img-curve'>";
            echo "</div>";

            echo "<div class='therapist-desc rec-desc'>";
            echo "<h4>" . htmlspecialchars($therapist['theraName']) . "</h4>";
            echo "<p class='therapist-location'><strong>Location:</strong> " . htmlspecialchars($therapist['theraAdd']) . "</p>";
            echo "<p class='therapist-detail'><strong>Description:</strong> " . nl2br(htmlspecialchars($therapist['theraDesc'])) . "</p>";
            echo "<br>";
            echo "<a href='therapist_details.php?theraID=" . htmlspecialchars($therapist['theraID']) . "' class='btn btn-primary'>View Details</a>";
            echo "</div>";

            echo "</div>";  // Close therapist-box
        }
    }

    echo "</div>"; // Close recommended-therapists div
    echo "</div>"; // Close container div
} else {
    echo "<p class='text-center'>No recommendations available at the moment.</p>";
}
?>


</html>
