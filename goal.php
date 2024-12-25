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
    <title>Mindmate Goal</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
<?php
// Conditionally display navbar based on userRoles
if ($userRoles == 1) {
    // Navbar for therapists (userRole = 1)
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
    // Navbar for regular users (userRole = 2)
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

<!-- User Goals Section -->
<div class="container">
    <div class="user-goals">
        <h1>The Goals</h1>

        <?php
        // Fetch user goals from the database
        $userID = $_SESSION['userID'];

        // If the user is a regular user, fetch only their goals
        if ($userRoles == 2) {
            // Get username by joining 'goal' table with 'user' table
            $sql = "SELECT g.goalID, g.goalTitle, g.goalCont, g.goalDate, g.goalStatus, u.username 
                    FROM goal g
                    JOIN user u ON g.userID = u.userID 
                    WHERE g.userID = ?";
        } else {
            // If user is a therapist, fetch all goals along with usernames
            $sql = "SELECT g.goalID, g.goalTitle, g.goalCont, g.goalDate, g.goalStatus, u.username 
                    FROM goal g
                    JOIN user u ON g.userID = u.userID";
        }

        if ($stmt = mysqli_prepare($conn, $sql)) {
            if ($userRoles == 2) {
                // Only bind userID if the user is a regular user
                mysqli_stmt_bind_param($stmt, "i", $userID);
            }
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if (mysqli_num_rows($result) > 0) {
                echo '<ul class="goals-list">';
                while ($row = mysqli_fetch_assoc($result)) {
                    echo '<li class="goal-card">';
                    echo '<div class="goal-content">';
                    echo '<h2>' . htmlspecialchars($row['goalTitle']) . '</h2>';
                    echo '<h3>' . htmlspecialchars($row['goalCont']) . '</h3>';
                    echo '<p>by ' . htmlspecialchars($row['username']) . '</p>';  // Display the username
                    echo '<p>Target Date: ' . htmlspecialchars($row['goalDate']) . '</p>';
                    echo '<p>Status: ' . htmlspecialchars($row['goalStatus']) . '</p>';
                    echo '</div>';
                    echo '<div class="goal-buttons">';
                    // Actions based on user roles
                    if ($userRoles == 2) {
                        echo "<p><a href='edit_goal.php?goalID=" . $row['goalID'] . "' class='btn btn-primary'>Edit</a>
                              <a href='delete_goal.php?goalID=" . $row['goalID'] . "' class='btn btn-primary'>Delete</a></p>";
                    } else if ($userRoles == 1) {
                        // Therapists can only view goals, no actions allowed
                    }
                    echo '</div>';
                    echo '</li>';
                }
                echo '</ul>';
            } else {
                echo '<p>No goals set yet.</p>';
            }

            mysqli_stmt_close($stmt);
        } else {
            echo 'Error: Could not prepare SQL statement.';
        }

        mysqli_close($conn);
        ?>
    </div>
</div>

<!-- Goal Form Section -->
<div class="container">
    <div class="goal-form">
        <?php if ($userRoles == 2): // Only allow users with userRoles = 2 to see this form ?>
            <h2>Create Your Goal</h2>
            <form action="goal_action.php" method="post">
                <div class="input-box">
                    <label for="goalTitle">Goal Title</label>
                    <input type="text" name="goalTitle" id="goalTitle" required>
                </div>

                <div class="input-box">
                    <label for="goalDescription">Goal Description</label>
                    <textarea name="goalDescription" id="goalDescription" rows="4" required></textarea>
                </div>

                <div class="input-box">
                    <label for="goalDate">Target Date</label>
                    <input type="date" name="goalDate" id="goalDate" required>
                </div>

                <div class="input-box">
                    <label for="goalStatus">Goal Status</label>
                    <select name="goalStatus" id="goalStatus" required>
                        <option value="In Progress">In Progress</option>
                        <option value="Completed">Completed</option>
                        <option value="Not Started">Not Started</option>
                    </select>
                </div>

                <button type="submit">Add Goal</button>
            </form>
        <?php else: ?>
          <p style="text-align: center; margin: 0;">
			Therapists can only view goals. Adding goals is not allowed for this role.</p>
        <?php endif; ?>
    </div>
</div>

<script src="js/script.js"></script>
</body>
</html>
