<?php
include("config.php");
session_start();

// Check if user is logged in
if (!isset($_SESSION['userID'])) {
    header("Location: login.php");
    exit;
}

$userID = $_SESSION['userID'];

// Check if goalID is passed in URL
if (isset($_GET['goalID']) && is_numeric($_GET['goalID'])) {
    $goalID = $_GET['goalID'];

    // Fetch the goal data for the specified goalID and userID
    $sql = "SELECT * FROM goal WHERE goalID = $goalID AND userID = $userID";
    $result = mysqli_query($conn, $sql);
    $goal = mysqli_fetch_assoc($result);

    // If goal not found
    if (!$goal) {
        echo "Goal not found.";
        exit;
    }

    // If goalStatus is not set, initialize with a default value (e.g., 'In Progress')
    $goalStatus = isset($goal['goalStatus']) ? $goal['goalStatus'] : 'In Progress';

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $goalTitle = mysqli_real_escape_string($conn, $_POST['goalTitle']);
        $goalDesc = mysqli_real_escape_string($conn, $_POST['goalDesc']);
        $goalDate = $_POST['goalDate'];
        $goalStatus = $_POST['goalStatus'];  // Get the selected status

        // Update the goal in the database including the status
        $updateSQL = "UPDATE goal SET goalTitle = '$goalTitle', goalCont = '$goalDesc', goalDate = '$goalDate', goalStatus = '$goalStatus' WHERE goalID = $goalID AND userID = $userID";
        if (mysqli_query($conn, $updateSQL)) {
            header("Location: goal.php");
            exit;
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    }
} else {
    echo "Goal ID not specified or invalid.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Goal</title>
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
    </section>
    <!-- Navbar Section Ends Here -->

    <!-- Edit Goal Form Section -->
    <section class="edit-goal">
        <div class="container">
            <h2>Edit Goal</h2>
            <form action="edit_goal.php?goalID=<?php echo $goalID; ?>" method="post">
                <div class="input-box">
                    <label for="goalTitle">Goal Title</label>
                    <input type="text" name="goalTitle" id="goalTitle" value="<?php echo htmlspecialchars($goal['goalTitle']); ?>" required>
                </div>

                <div class="input-box">
                    <label for="goalDesc">Goal Description</label>
                    <textarea name="goalDesc" id="goalDesc" required><?php echo htmlspecialchars($goal['goalCont']); ?></textarea>
                </div>

                <div class="input-box">
                    <label for="goalDate">Goal Date</label>
                    <input type="date" name="goalDate" id="goalDate" value="<?php echo $goal['goalDate']; ?>" required>
                </div>

                <!-- Add Goal Status Dropdown -->
                <div class="input-box">
                    <label for="goalStatus">Goal Status</label>
                    <select name="goalStatus" id="goalStatus" required>
						<option value="Not Started" <?php echo ($goalStatus == 'Not Started') ? 'selected' : ''; ?>>Not Started</option>
                        <option value="In Progress" <?php echo ($goalStatus == 'In Progress') ? 'selected' : ''; ?>>In Progress</option>
                        <option value="Completed" <?php echo ($goalStatus == 'Completed') ? 'selected' : ''; ?>>Completed</option>
                    </select>
                </div>

                <button type="submit" class="btn">Save Changes</button>
            </form>
        </div>
    </section>
</body>
</html>
