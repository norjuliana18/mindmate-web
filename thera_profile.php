<?php
include("config.php");
session_start();

// Check if the user is logged in
if (!isset($_SESSION['userID'])) {
    header("Location: login.php");
    exit();
}

// Fetch user details based on user ID
$userID = $_SESSION['userID'];
$userQuery = "SELECT * FROM user WHERE userID = '$userID'";
$userResult = mysqli_query($conn, $userQuery);
$user = mysqli_fetch_assoc($userResult);

// Fetch therapist details based on user ID
$therapistQuery = "SELECT * FROM therapist WHERE theraID = '$userID'";
$therapistResult = mysqli_query($conn, $therapistQuery);
$therapist = mysqli_fetch_assoc($therapistResult);

// Check if the therapist data exists, otherwise set a default empty array
if (!$therapist) {
    $therapist = [
        'theraName' => '',
        'theraContact' => '',
        'theraDesc' => '',
        'theraImage' => '',
        'theraAdd' => ''
    ];
}
// Get message from session if any
$message = isset($_SESSION['message']) ? $_SESSION['message'] : '';
unset($_SESSION['message']);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Therapist Profile</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <!-- Navbar Section Starts Here -->
    <section class="navbar">
        <div class="container">
            <div class="logo">
                <a href="#" title="Logo">
                    <img src="img/Screenshot_2024-03-28_091637-removebg-preview.png" alt="Mindmate Logo" class="img-responsive">
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
                    <!-- Profile Icon Link -->
                    <a href="thera_profile.php" title="Profile" class="profile-icon-link">
                        <img src="https://img.icons8.com/?size=100&id=15263&format=png&color=000000" class="profile-icon" />
                    </a>

                    <!-- Logout Button -->
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
    <form id="editForm" action="thera_profile_update.php" method="POST" enctype="multipart/form-data">
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
	
	<!-- Professional Profile Details Section -->
	<div style="text-align: center; margin-top: 50px;">	
	<h1>Professional Info</h1>
	</div>
	<div class="profile-container">
        <div class="profile-info">
        <h2>Professional Profile Details</h2>
        <div class="info-box">
            <div class="info-row">
                <label>Therapist Name</label>
                <span><?php echo isset($therapist['theraName']) ? $therapist['theraName'] : 'No name provided'; ?></span>
            </div>
            <div class="info-row">
                <label>Contact</label>
                <span><?php echo isset($therapist['theraContact']) ? $therapist['theraContact'] : 'No contact provided'; ?></span>
            </div>
            <div class="info-row">
                <label>Address</label>
                <span><?php echo isset($therapist['theraAdd']) ? $therapist['theraAdd'] : 'No address provided'; ?></span>
            </div>
            <div class="info-row">
                <label>Description</label>
                <span><?php echo isset($therapist['theraDesc']) ? $therapist['theraDesc'] : 'No description provided'; ?></span>
            </div>
        </div>
        
        <!-- Display Therapist Profile Picture if available -->
        <div class="profile-picture">
            <?php if (isset($therapist['theraImage']) && $therapist['theraImage']) { ?>
                <img src="uploads/<?php echo $therapist['theraImage']; ?>" alt="Therapist Profile Picture" width="150">
            <?php } else { ?>
                <img src="uploads/default-therapist-profile.png" alt="Default Therapist Profile Picture" width="150">
            <?php } ?>
        </div>
    </div>

    <!-- Professional Profile Section -->
	<div class="profile-edit-form">
        <h3 align="center">Professional Profile</h3>
        <form method="POST" action="thera_profile_action.php" enctype="multipart/form-data" id="editForm">
            <table border="1" id="editTable">
        <div>
            <label>Therapist Name*</label>
            <input type="text" name="theraName" value="<?php echo isset($therapist['theraName']) ? $therapist['theraName'] : ''; ?>" required>
        </div>
        <div>
            <label>Contact*</label>
            <input type="text" name="theraContact" value="<?php echo isset($therapist['theraContact']) ? $therapist['theraContact'] : ''; ?>" required>
        </div>
		<div>
            <label>Address*</label>
            <input type="text" name="theraAdd" value="<?php echo isset($therapist['theraAdd']) ? $therapist['theraAdd'] : ''; ?>" required>
        </div
        <div>
            <label>Description</label>
            <textarea name="theraDesc"><?php echo isset($therapist['theraDesc']) ? $therapist['theraDesc'] : ''; ?></textarea>
        </div>
        <div>
            <label>Professional Profile Picture</label>
            <input type="file" name="theraImage" accept="image/*">
        </div>
        <button type="submit" name="updateTherapist" style="margin-top: 10px; padding: 8px 16px; background-color: #007BFF; color: white; border: none; border-radius: 5px; cursor: pointer;">
            Save Professional Profile
        </button>
    </form>
</body>

</html>

<?php mysqli_close($conn); ?>
