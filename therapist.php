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
    <title>Mindmate Therapist</title>
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

    <!-- Therapist Search Section Starts Here -->
	<h1 style="text-align: center; color: #000; margin-bottom: 5px;">List of Therapist</h1>
    <section class="therapist-search text-center">
        <div class="container">
            <form action="therapist.php" method="GET">
                <input type="search" name="search" placeholder="Search for Therapists.." required>
                <input type="submit" name="submit" value="Search" class="btn btn-primary">
            </form>
        </div>
    </section>
    <!-- Therapist Search Section Ends Here -->

    <?php
    // Include the database connection file
    include 'config.php';

    // Check if search term is provided in the URL
    if (isset($_GET['search'])) {
        $searchTerm = mysqli_real_escape_string($conn, $_GET['search']);

        // Query to search therapists by name
        $query = "SELECT * FROM therapist WHERE theraName LIKE '%$searchTerm%'";
        $result = mysqli_query($conn, $query);

        if (!$result) {
            die("Error: " . $query . "<br>" . mysqli_error($conn));
        }

        // Display search results
        echo "<div class='container'>";
        if ($result->num_rows > 0) {
            echo "<h2>Search Results</h2>";
            while ($row = $result->fetch_assoc()) {
                echo "<div class='therapist-box'>";
                echo "<div class='therapist-img'><img src='{$row['theraImage']}' alt='{$row['theraName']}' class='img-responsive img-curve'></div>";
                echo "<div class='therapist-desc'>";
                echo "<h4>{$row['theraName']}</h4>";
                echo "<p class='therapist-location'>{$row['theraAdd']}</p>";
                echo "<p class='therapist-detail'>{$row['theraDesc']}</p>";
                echo "<a href='therapist_details.php?theraID={$row['theraID']}' class='btn btn-primary'>View Details</a>";
                echo "</div></div>";
            }
        } else {
            echo "<p>No results found.</p>";
        }
        echo "</div>";

        // Close the search results section
        echo "<hr>";

        // Close the database connection and stop further execution
        mysqli_close($conn);
        exit();
    }
    ?>
	
    <!-- Therapist List Section Starts Here -->
	<section class="therapist-list">
    <div class="container">
        <?php
        // Include the database configuration file
        include('config.php');

        // Query to get all therapists
        $query = "SELECT * FROM therapist";
        $result = mysqli_query($conn, $query);

        // Check if $result is not null before using it
        if ($result !== null && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
        ?>
                <div class="therapist-box">
                    <div class="therapist-img">
                        <?php
                        // Set the path for the therapist image
                        $theraImage = 'uploads/' . $row['theraImage']; // Modify this if needed
                        ?>
                        <img src="<?php echo $theraImage; ?>" alt="<?php echo $row['theraName']; ?>" class="img-responsive img-curve">
                    </div>

                    <div class="therapist-desc">
                        <h4><?php echo $row['theraName']; ?></h4>
                        <p class="therapist-location"><?php echo $row['theraAdd']; ?></p>
                        <p class="therapist-detail"><?php echo $row['theraDesc']; ?></p>
                        <br>

                        <!-- Customize the link based on your application structure -->
                        <a href="therapist_details.php?theraID=<?php echo $row['theraID']; ?>" class="btn btn-primary">View Details</a>
                    </div>
                </div>
        <?php
            }
        } else {
            echo "No therapists found.";
        }

        // Close the database connection
        mysqli_close($conn);
        ?>
    </div>
</section>
<!-- Therapist List Section Ends Here -->

<?php
// Display "Add Therapist" link and form for therapist
if (isset($_SESSION['userRoles']) && $_SESSION['userRoles'] === 1) {
?>
    <section class="add-therapist-form">
        <div class="container">
            <h3>Add Therapist</h3>
            <form action="add_therapist.php" method="POST" enctype="multipart/form-data">
                <label for="theraName">Name:</label>
                <input type="text" name="theraName" required>

                <label for="theraDesc">Description:</label>
                <textarea name="theraDesc" required></textarea>

                <label for="theraLocation">Location:</label>
                <input type="text" name="theraAdd" required>

                <label for="theraContact">Contact:</label>
                <input type="text" name="theraContact" required>

                <label for="theraImage">Therapist Image:</label>
                <input type="file" name="theraImage" accept="image/*" required>

                <input type="submit" name="submit" value="Add Therapist" class="btn btn-primary">
            </form>
        </div>
    </section>
<?php
}
?>


</body>
</html>
