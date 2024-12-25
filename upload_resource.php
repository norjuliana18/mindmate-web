<?php
include('config.php');
session_start();

if (!isset($_SESSION['userID'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $theraID = $_SESSION['userID']; // Therapist ID
    $rsrcType = mysqli_real_escape_string($conn, $_POST['rsrcType']);
    $rsrcTitle = mysqli_real_escape_string($conn, $_POST['rsrcTitle']);
    $rsrcDesc = mysqli_real_escape_string($conn, $_POST['rsrcDesc']);
    $rsrcCategory = mysqli_real_escape_string($conn, $_POST['rsrcCategory']);
    $rsrcLink = mysqli_real_escape_string($conn, $_POST['rsrcLink']);

    $sql = "INSERT INTO resource (theraID, rsrcType, rsrcTitle, rsrcDesc, rsrcCategory, rsrcLink) 
            VALUES ('$theraID', '$rsrcType', '$rsrcTitle', '$rsrcDesc', '$rsrcCategory', '$rsrcLink')";

    if (mysqli_query($conn, $sql)) {
        $successMessage = "Resource uploaded successfully!";
    } else {
        $errorMessage = "Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Resource</title>
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
<div class="upload_rscr">
    <h2>Upload a New Meditation Resource</h2>

    <?php if (isset($successMessage)): ?>
        <p style="color: green;"><?php echo $successMessage; ?></p>
    <?php elseif (isset($errorMessage)): ?>
        <p style="color: red;"><?php echo $errorMessage; ?></p>
    <?php endif; ?>

    <form action="" method="POST">
    <label for="rsrcType">Resource Type:</label>
    <select name="rsrcType" id="rsrcType" required>
        <option value="video">Video</option>
        <option value="article">Article</option>
        <option value="tip">Tip</option>
    </select>

    <label for="rsrcTitle">Title:</label>
    <input type="text" id="rsrcTitle" name="rsrcTitle" maxlength="50" required>

    <label for="rsrcDesc">Description:</label>
    <input type="text" id="rsrcDesc" name="rsrcDesc" maxlength="100" required>

    <label for="rsrcCategory">Category:</label>
    <input type="text" id="rsrcCategory" name="rsrcCategory" maxlength="100" required>

    <label for="rsrcLink">Resource Link:</label>
    <textarea id="rsrcLink" name="rsrcLink" rows="4" required></textarea>

    <button type="submit" name="submit">Upload</button>
</form>

</body>
</html>
