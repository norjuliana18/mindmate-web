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
    <title>Mindmate Feedback</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
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

<!-- Review Display Section Starts Here -->
<div style="position: relative;">
    <!-- Scroll Button Left -->
    <button class="scroll-btn scroll-btn-left" onclick="scrollLeft()">
        &lt;
    </button>

    <div class="feedback-display">
        <?php
        // Include the database configuration file
        include 'config.php';

        // Fetch feedbacks from the database
        $sql = "SELECT feedback.feedbID, feedback.feedbRate, feedback.feedbCont, feedback.feedbImage, feedback.created_at, feedback.updated_at, 
                       feedback.userID, feedback.theraID, therapist.theraName 
                FROM feedback 
                JOIN therapist ON feedback.theraID = therapist.theraID 
                ORDER BY feedback.feedbID DESC";
        $result = mysqli_query($conn, $sql);

        if ($result) {
            if (mysqli_num_rows($result) > 0) {
                // Output data of each feedback
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<div class='feedback-post' onclick='showLargeFeedback(" . $row['feedbID'] . ")'>";

                    // Display therapist's name
                    echo "<p>Therapist: " . $row["theraName"] . "</p>";

                    // Display star icons based on the rating
                    echo "<p>Rating: ";
                    for ($i = 1; $i <= 5; $i++) {
                        if ($i <= $row["feedbRate"]) {
                            echo "<i class='fa fa-star'></i>";
                        } else {
                            echo "<i class='fa fa-star-o'></i>";
                        }
                    }
                    echo "</p>";

                    echo "<p>Feedback: " . $row["feedbCont"] . "</p>";

                    // Check if the feedback image is uploaded
                    if (!empty($row["feedbImage"])) {
                        echo "<p><a href='" . $row["feedbImage"] . "' target='_blank'><img src='" . $row["feedbImage"] . "' alt='Feedback Image' style='width: 200px; height: 150px;'></a></p>";
                    }

                    // Display created or updated time
                    $timestamp = isset($row["updated_at"]) ? $row["updated_at"] : $row["created_at"];
                    if ($timestamp !== null) {
                        $formattedTime = date("F j, Y, g:i a", strtotime($timestamp));
                        echo "<p class='small-font'>Time: " . $formattedTime . "</p>";
                    }

					// Show Edit and Delete buttons only if user role is 2
					if ($userRoles == 2) {
					echo "<p><a href='feedback_edit.php?feedbID=" . $row['feedbID'] . "' class='btn btn-primary'>Edit</a>
                      <a href='feedback_delete.php?feedbID=" . $row['feedbID'] . "' class='btn btn-primary'>Delete</a></p>";
            }


                    echo "</div>";
                }
            } else {
                echo "No feedback available";
            }
        } else {
            echo "Error fetching feedback: " . mysqli_error($conn);
        }
        // Close the database connection
        mysqli_close($conn);
        ?>
    </div>

    <!-- Scroll Button Right -->
    <button class="scroll-btn scroll-btn-right" onclick="scrollRight()">
        &gt;
    </button>
</div>

<!-- Script for scrolling -->
<script>
    function scrollLeft() {
        const feedbackDisplay = document.querySelector('.feedback-display');
        feedbackDisplay.scrollBy({
            left: -300, // Scroll by 300px to the left
            behavior: 'smooth' // Smooth scrolling
        });
    }

    function scrollRight() {
        const feedbackDisplay = document.querySelector('.feedback-display');
        feedbackDisplay.scrollBy({
            left: 300, // Scroll by 300px to the right
            behavior: 'smooth' // Smooth scrolling
        });
    }
</script>
<!-- Review Display Section Ends Here -->

<?php if ($userRoles == 2): ?>
    <section class="feedback-form">
        <div class="container">
            <h2 class="text-center">Give Feedback</h2>
            <form action="feedback_action.php" method="POST" enctype="multipart/form-data">
                <label for="therapist">Select a Therapist:</label>
                <select name="therapist" id="therapist" required>
                    <option value="" disabled selected>Select a therapist</option>
                    <?php
                    // Fetch therapist data from the database
                    include('config.php');
                    $result = mysqli_query($conn, "SELECT theraID, theraName FROM therapist");

                    if ($result) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo "<option value='{$row['theraID']}'>{$row['theraName']}</option>";
                        }
                    }

                    mysqli_close($conn);
                    ?>
                </select>
                <br>

                <label for="rating">Rating:</label>
                <input type="radio" name="rating" value="1" required> 1
                <input type="radio" name="rating" value="2" required> 2
                <input type="radio" name="rating" value="3" required> 3
                <input type="radio" name="rating" value="4" required> 4
                <input type="radio" name="rating" value="5" required> 5
                <br><br>

                <label for="content">Feedback:</label>
                <textarea name="content" id="content" rows="4" required></textarea>
                <br>

                <label for="image">Upload Picture:</label>
                <input type="file" name="image" id="image" accept="image/*">
                <br><br>

                <input type="submit" name="submit" value="Submit Feedback" style="padding: 8px 16px; background-color: #4CAF50; color: white; border: none; border-radius: 5px;">
            </form>
        </div>
    </section>
<?php else: ?>
    <p class="text-center">Therapists can only view feedback. Adding feedback is not allowed for this role.</p>
<?php endif; ?>

<?php
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $therapist_id = $_POST['therapist'];
    $rating = $_POST['rating'];
    $content = $_POST['content'];
    $image = $_FILES['image']['name'];
    $target = "uploads/" . basename($image);

    // If there's an image, upload it
    if ($image != "") {
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
            $image_status = "Image uploaded successfully!";
        } else {
            $image_status = "Failed to upload image.";
        }
    }

    // Insert the feedback into the database
    $sql = "INSERT INTO feedback (feedbRate, feedbCont, feedbImage, theraID, userID) 
            VALUES ('$rating', '$content', '$image', '$therapist_id', '$_SESSION[userID]')";

    if (mysqli_query($conn, $sql)) {
        $success_message = "Feedback submitted successfully!";
    } else {
        $error_message = "Error: " . mysqli_error($conn);
    }

    mysqli_close($conn);
}
?>

<!-- Display a popup message -->
<?php if (isset($success_message)): ?>
    <script type="text/javascript">
        alert("<?php echo $success_message; ?>");
    </script>
<?php elseif (isset($error_message)): ?>
    <script type="text/javascript">
        alert("<?php echo $error_message; ?>");
    </script>
<?php endif; ?>
</body>
</html>
