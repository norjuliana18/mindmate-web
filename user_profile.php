<?php
include("config.php");
session_start();

// Check if the user is logged in
if (!isset($_SESSION['userID'])) {
    header("Location: login.php");
    exit();
}

// Fetch user details from the database
$userID = $_SESSION['userID'];
$sql = "SELECT * FROM user WHERE userID = '$userID'";
$result = mysqli_query($conn, $sql);
$user = mysqli_fetch_assoc($result);

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

// Get message from session if any
$message = isset($_SESSION['message']) ? $_SESSION['message'] : '';
unset($_SESSION['message']);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mindmate Dashboard</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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

    <div class="profile-container">
        <!-- Display success/error message in a pop-up -->
        <?php if ($message): ?>
        <script>
            alert("<?php echo $message; ?>");
        </script>
        <?php endif; ?>

        <div class="profile-info">
    <h2>Profile Information</h2>
    <div class="info-box">
        <div class="info-row">
            <label>Email</label>
            <span><?php echo $user['userEmail']; ?></span>
        </div>
        <div class="info-row">
            <label>Username</label>
            <span><?php echo $user['username']; ?></span>
        </div>
        <div class="info-row">
            <label>Address</label>
            <span><?php echo $user['address']; ?></span>
        </div>
        <div class="info-row">
            <label>Date of Birth</label>
            <span><?php echo $user['dob']; ?></span>
        </div>
        <div class="info-row">
            <label>Gender</label>
            <span><?php echo $user['gender']; ?></span>
        </div>
        <div class="info-row">
            <label>Contact</label>
            <span><?php echo $user['userContact']; ?></span>
        </div>
    </div>
</div>

        <!-- Display Profile Picture -->
        <div class="profile-picture">
            <?php
            $userProf = 'uploads/' . $user['userProf'];
            if ($user['userProf'] && file_exists($userProf)) {
                echo '<img src="' . $userProf . '" alt="Profile Picture" width="150" height="150" class="img-responsive img-curve">';
            } else {
                echo '<img src="img/T0266FRGM-U2Q173U05-g863c2a865d7-512.png" alt="Default Profile Picture" width="150" height="150" class="img-responsive img-curve">';
            }
            ?>
        </div>
    </div>

    <!-- Edit Profile Form Section -->
    <div style="text-align: center; margin-top: 30px; margin-bottom: 10px;">
        <h2>Edit Profile</h2>
    </div>
    <form id="editForm" action="profile_update.php" method="POST" enctype="multipart/form-data">
        <table border="1" id="editTable">
            <tr>
                <td>Email*</td>
                <td>:</td>
                <td>
                    <input type="email" name="userEmail" value="<?php echo $user['userEmail']; ?>" required>
                </td>
            </tr>
            <tr>
                <td>Username*</td>
                <td>:</td>
                <td>
                    <input type="text" name="username" value="<?php echo $user['username']; ?>" required>
                </td>
            </tr>
            <tr>
                <td>Address *</td>
                <td>:</td>
                <td>
                    <input type="text" name="address" value="<?php echo $user['address']; ?>">
                </td>
            </tr>
            <tr>
                <td>Date of Birth*</td>
                <td>:</td>
                <td>
                    <input type="date" name="dob" value="<?php echo $user['dob']; ?>">
                </td>
            </tr>
            <tr>
                <td>Gender*</td>
                <td>:</td>
                <td>
                    <select name="gender">
                        <option value="Male" <?php echo ($user['gender'] == 'Male') ? 'selected' : ''; ?>>Male</option>
                        <option value="Female" <?php echo ($user['gender'] == 'Female') ? 'selected' : ''; ?>>Female</option>
                        <option value="Other" <?php echo ($user['gender'] == 'Other') ? 'selected' : ''; ?>>Other</option>
                    </select>
                </td>
            </tr>
			<tr>
				<td>Contact *</td>
				<td>:</td>
				<td>
					<input type="text" name="userContact" value="<?php echo $user['userContact']; ?>" required>
				</td>
				</tr>
			<tr>
                <td>Change Password</td>
                <td>:</td>
                <td>
                    <input type="password" name="newPwd" placeholder="Enter new password (optional)">
                </td>
            </tr>
            <tr>
                <td>Confirm New Password</td>
                <td>:</td>
                <td>
                    <input type="password" name="confirmPwd" placeholder="Confirm new password">
                </td>
            </tr>
            <tr>
                <td>Profile Picture*</td>
                <td>:</td>
                <td>
                    <label for="userProf">Choose a new profile picture:</label>
                    <input type="file" name="userProf" id="userProf" accept="image/*">
                </td>
            </tr>
        </table>
        <div style="text-align: center; margin-top: 20px;">
            <button type="button" id="confirmUpdateBtn" style="padding: 8px 16px; background-color: #4CAF50; color: white; border: none; border-radius: 5px; cursor: pointer;">Update</button>
        </div>
    </form>

    <script>
        document.getElementById('confirmUpdateBtn').addEventListener('click', function() {
            // Display confirmation pop-up
            var confirmUpdate = confirm("Are you sure you want to update your profile?");
            
            // If the user confirms, submit the form
            if (confirmUpdate) {
                document.getElementById('editForm').submit(); // Submit the form
            }
            // If the user cancels, do nothing (the form won't be submitted)
        });
    </script>
</body>

</html>
