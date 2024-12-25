<?php
// Include the database connection
include 'config.php';
session_start();

// Check if the user is logged in
if (!isset($_SESSION['userID'])) {
    header("Location: login.php");
    exit;
}

// Query to fetch all resources for the logged-in therapist
$sql = "SELECT * FROM resource WHERE theraID = {$_SESSION['userID']} ORDER BY updated_at DESC"; // Sort by most recent first

// Execute the query
$result = mysqli_query($conn, $sql);

if (!$result) {
    die('Error executing query: ' . mysqli_error($conn));
}

// Fetch the results and store them in an array
$resources = [];
while ($row = mysqli_fetch_assoc($result)) {
    $resources[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Therapist Dashboard</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <!-- Navbar Section Starts Here -->
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
    </section>
    <!-- Navbar Section Ends Here -->

    <!-- Therapist Search Section Starts Here -->
    <section class="therapist-search text-center">
        <div class="container">
            <!-- Search Form -->
            <form action="therapist.php" method="GET">
                <input type="search" name="search" placeholder="Search for Therapist.." required>
                <input type="submit" name="submit" value="Search" class="btn btn-primary">
            </form>
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
                Mindmate is designed to address critical mental health needs by developing a space that offers resources
                and support services tailored to students. It provides a safe environment for students to manage their mental health,
                access professional therapy, and engage in self-care practices such as meditation.
            </p>
        </div>

        <!-- Display All Resources Section -->
	<div class="new-resources">
    <h3>All Resources</h3>

    <?php if (count($resources) > 0): ?>
        <div class="resource-wrapper">
            <div class="resource-container" id="resourceContainer">
                <?php foreach ($resources as $index => $resource): ?>
                    <div class="resource-card" data-index="<?php echo $index; ?>" style="display: <?php echo $index === 0 ? 'block' : 'none'; ?>;">
                        <strong><?php echo htmlspecialchars($resource['rsrcTitle']); ?></strong><br>
                        Type: <?php echo htmlspecialchars($resource['rsrcType']); ?><br>
                        Category: <?php echo htmlspecialchars($resource['rsrcCategory']); ?><br>
                        Description: <?php echo htmlspecialchars($resource['rsrcDesc']); ?><br>
                        <a href="<?php echo htmlspecialchars($resource['rsrcLink']); ?>" target="_blank">View Resource</a>
                    </div>
                <?php endforeach; ?>
            </div>
            <!-- Scroll button -->
            <button class="scroll-right" onclick="showNextResource()">&#8594;</button>
        </div>
    <?php else: ?>
        <p>No resources have been uploaded yet.</p>
    <?php endif; ?>
</div>
<script>
    let currentIndex = 0;

    function showNextResource() {
        const resources = document.querySelectorAll('.resource-card');
        const totalResources = resources.length;

        // Hide the current resource
        resources[currentIndex].style.display = 'none';

        // Increment the index (loop back to the start if necessary)
        currentIndex = (currentIndex + 1) % totalResources;

        // Show the next resource
        resources[currentIndex].style.display = 'block';
    }
</script>

</body>

</html>