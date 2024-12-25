<?php
include('config.php');
session_start(); // Start the session

// Check if userID is set in session
if (isset($_SESSION['userID'])) {
    $userID = $_SESSION['userID'];

    // Fetch user roles based on userID
    $userRolesQuery = "SELECT userRoles FROM user WHERE userID = '$userID'";
    $userRolesResult = mysqli_query($conn, $userRolesQuery);
    $userRolesData = mysqli_fetch_assoc($userRolesResult);
    $userRoles = $userRolesData['userRoles']; // Initialize userRoles
} else {
    // If session is not set, redirect to login page
    header("Location: login.php");
    exit;
}

// Fetch resources ordered by type and category
$sql = "SELECT * FROM resource ORDER BY rsrcType, rsrcCategory, created_at DESC";
$result = mysqli_query($conn, $sql);

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
// Fetch notification count
$notificationCount = getNotificationCount($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meditation Resources</title>
    <link rel="stylesheet" href="css/style.css">
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
                    <!-- Notification Icon with Count -->
                    <a href="notifications.php" title="Notifications" class="notification-link">
                        <span class="notification-icon">🔔</span>';
                    if ($notificationCount > 0) {
                        echo '<span class="notification-count">' . $notificationCount . '</span>';
                    }
                    echo '
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
                    <!-- Notification Icon with Count -->
                    <a href="notifications.php" title="Notifications" class="notification-link">
                        <span class="notification-icon">🔔</span>';
                    if ($notificationCount > 0) {
                        echo '<span class="notification-count">' . $notificationCount . '</span>';
                    }
                    echo '
                    </a>
                    <a href="logout.php"><button class="btnLogout-popup">Logout</button></a>
                </div>
            </div>

            <div class="clearfix"></div>
        </div>
    </section>';
}
?>

</body>
</html>

    <h1 style="text-align: center; color: #000; margin-top: 10px; margin-bottom: 30px;">Meditation Resources</h1>
    <div class="resource-container">
        <?php 
			$currentType = ''; // Track current resource type
		?>
	<div class="rsrc-container">
    <?php while ($row = mysqli_fetch_assoc($result)): ?>
        <?php 
        if ($row['rsrcType'] !== $currentType): 
            if ($currentType !== '') echo "</div>"; // Close previous type section
            $currentType = $row['rsrcType']; 
        ?>
            <h2><?php echo ucfirst($currentType); ?>s</h2>
            <div class="resource-type-section">
        <?php endif; ?>
            <div class="rsrc-card">
                <h3><?php echo htmlspecialchars($row['rsrcTitle']); ?></h3>
                <p><strong>Description:</strong> <?php echo htmlspecialchars($row['rsrcDesc']); ?></p>
                <p><strong>Category:</strong> <?php echo htmlspecialchars($row['rsrcCategory']); ?></p>
                <p><a href="<?php echo htmlspecialchars($row['rsrcLink']); ?>" target="_blank" style="color: #45b6fe; font-weight: bold; text-decoration: none;">View Resource</a></p>
                <small>Uploaded on: <?php echo htmlspecialchars($row['created_at']); ?></small>
            </div>
    <?php endwhile; ?>
    <?php if ($currentType !== '') echo "</div>"; // Close final type section ?>
</div>
</body>
</html>
