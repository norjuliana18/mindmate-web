<?php
// Include the database connection and start the session
include 'config.php';
session_start();

// Check if the user is logged in
if (!isset($_SESSION['userID'])) {
    header("Location: login.php");
    exit;
}

// Check if the user has the appropriate role (only role 2 can edit feedback)
if ($_SESSION['userRoles'] != 2) {
    echo "<script type='text/javascript'>
            alert('Unauthorized access. Only Student can edit feedback.');
            window.location.href = 'feedback.php';
          </script>";
    exit;
}

// Get the feedback ID from the request
if (isset($_GET['feedbID'])) {
    $feedbID = mysqli_real_escape_string($conn, $_GET['feedbID']);
    $userID = $_SESSION['userID'];

    // Check if the feedback belongs to the logged-in user
    $check_sql = "SELECT * FROM feedback WHERE feedbID = '$feedbID' AND userID = '$userID'";
    $check_result = mysqli_query($conn, $check_sql);

    if (mysqli_num_rows($check_result) > 0) {
        // Feedback belongs to the user; fetch the feedback details for editing
        $feedback = mysqli_fetch_assoc($check_result);
    } else {
        echo "<script type='text/javascript'>
                alert('Feedback not found or you don\'t have permission to edit it.');
                window.location.href = 'feedback.php';
              </script>";
        exit;
    }
} else {
    echo "<script type='text/javascript'>
            alert('Invalid request. Feedback ID is missing.');
            window.location.href = 'feedback.php';
          </script>";
    exit;
}

// Handle form submission for editing the feedback
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the new feedback content and rating from the form
    $rating = mysqli_real_escape_string($conn, $_POST['rating']);
    $content = mysqli_real_escape_string($conn, $_POST['content']);
    $feedback_image = '';

    // Handle image upload if present
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $image_path = "uploads/" . basename($_FILES['image']['name']);
        if (move_uploaded_file($_FILES['image']['tmp_name'], $image_path)) {
            $feedback_image = $image_path;
        }
    } else {
        // If no new image is uploaded, retain the current image
        $feedback_image = $feedback['feedbImage'];
    }

    // Update the feedback in the database
    $update_sql = "UPDATE feedback SET feedbRate = '$rating', feedbCont = '$content', feedbImage = '$feedback_image' 
                   WHERE feedbID = '$feedbID' AND userID = '$userID'";
    
    if (mysqli_query($conn, $update_sql)) {
        echo "<script type='text/javascript'>
                alert('Feedback updated successfully.');
                window.location.href = 'feedback.php';
              </script>";
    } else {
        echo "<script type='text/javascript'>
                alert('Error updating feedback: " . mysqli_error($conn) . "');
                window.location.href = 'feedback.php';
              </script>";
    }
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Feedback</title>
    <link rel="stylesheet" href="css/style.css">  <!-- Assuming a common style.css -->
</head>
<body>
<?php
// Conditionally display navbar based on userRoles
if ($_SESSION['userRoles'] == 1) {
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
                    <a href="logout.php"><button class="btnLogout-popup">Logout</button></a>
                </div>
            </div>

            <div class="clearfix"></div>
        </div>
    </section>';
} else if ($_SESSION['userRoles'] == 2) {
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
                    <a href="logout.php"><button class="btnLogout-popup">Logout</button></a>
                </div>
            </div>

            <div class="clearfix"></div>
        </div>
    </section>';
}
?> 

    <div class="container">
        <h2 class="text-center">Edit Your Feedback</h2>
        <form action="feedback_edit.php?feedbID=<?php echo $feedbID; ?>" method="POST" enctype="multipart/form-data" class="feedback-form">
            <label for="rating">Rating:</label><br>
            <input type="radio" name="rating" value="1" <?php echo ($feedback['feedbRate'] == 1) ? 'checked' : ''; ?>> 1
            <input type="radio" name="rating" value="2" <?php echo ($feedback['feedbRate'] == 2) ? 'checked' : ''; ?>> 2
            <input type="radio" name="rating" value="3" <?php echo ($feedback['feedbRate'] == 3) ? 'checked' : ''; ?>> 3
            <input type="radio" name="rating" value="4" <?php echo ($feedback['feedbRate'] == 4) ? 'checked' : ''; ?>> 4
            <input type="radio" name="rating" value="5" <?php echo ($feedback['feedbRate'] == 5) ? 'checked' : ''; ?>> 5
            <br><br>

            <label for="content">Feedback:</label><br>
            <textarea name="content" id="content" rows="4" required><?php echo htmlspecialchars($feedback['feedbCont']); ?></textarea>
            <br><br>

            <label for="image">Upload New Picture (Optional):</label>
            <input type="file" name="image" id="image" accept="image/*">
            <br><br>

            <input type="submit" value="Update Feedback" style="padding: 8px 16px; background-color: #4CAF50; color: white; border: none; border-radius: 5px;">
            <a href="feedback.php" style="display: inline-block; text-decoration: none; padding: 10px 20px; color: #333; text-align: center;" 
            onmouseover="this.style.backgroundColor='#003366'; this.style.color='white';" 
            onmouseout="this.style.backgroundColor=''; this.style.color='#333';">Back to Feedback</a>
        </form>
    </div>

    <script src="js/main.js"></script> 
</body>
</html>

<?php
// Close the database connection
mysqli_close($conn);
?>