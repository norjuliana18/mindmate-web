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

$id = $fullname = $contact = $email = $apptDate = $apptTime = $therapistName = $status = "";

if (isset($_GET["id"]) && $_GET["id"] != "") {
    $sql = "SELECT a.*, t.theraName FROM appointment a
            JOIN therapist t ON a.theraID = t.theraID WHERE a.apptID = " . $_GET["id"];
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $id = $row["apptID"];
        $fullname = $row["fullname"];
        $contact = $row["contact"];
        $email = $row["email"];
        $apptDate = $row["apptDate"];
        $apptTime = $row["apptTime"];
        $therapistName = $row["theraName"];
        $status = $row["status"];
    }
}
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Appointment</title>
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
                    <a href="thera_profile.php" title="Profile" class="profile-icon-link">
                        <img src="https://img.icons8.com/?size=100&id=15263&format=png&color=000000" class="profile-icon" />
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
                    <img src="img/Screenshot_2024-03-28_091637-removebg-preview.png" alt="Mindmate Logo" class="img-responsive">
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

<h2 align="center">Edit Appointment</h2>
<div id="challengeDiv">
    <form method="POST" action="appointment_edit_action.php" id="myForm" enctype="multipart/form-data" class="appointment-form">
        <input type="hidden" name="apptID" value="<?php echo $id; ?>">
        <table border="1" id="myTable">
            <fieldset>
                <?php if ($userRoles == 2): ?>
                    <!-- Full Name -->
                    <div class="order-label">Full Name</div>
                    <input type="text" name="fullname" class="input-responsive" required value="<?php echo $fullname; ?>">

                    <!-- Phone Number -->
                    <div class="order-label">Phone Number</div>
                    <input type="tel" name="contact" class="input-responsive" required value="<?php echo $contact; ?>">

                    <!-- Email -->
                    <div class="order-label">Email</div>
                    <input type="email" name="email" class="input-responsive" required value="<?php echo $email; ?>">

                    <!-- Appointment Date -->
                    <div class="order-label">Appointment Date</div>
                    <input type="date" name="apptDate" class="input-responsive" required value="<?php echo $apptDate; ?>">

                    <!-- Appointment Time -->
                    <div class="order-label">Appointment Time</div>
                    <input type="time" name="apptTime" class="input-responsive" required value="<?php echo $apptTime; ?>">

                    <!-- Therapist Name -->
                    <div class="order-label">Therapist Name</div>
                    <input type="text" name="therapistName" class="input-responsive" required value="<?php echo $therapistName; ?>" readonly>

                <?php elseif ($userRoles == 1): ?>
                    <!-- For Therapists, make fields read-only -->
                    <!-- Full Name -->
                    <div class="order-label">Full Name</div>
                    <input type="text" name="fullname" class="input-responsive" value="<?php echo $fullname; ?>" readonly>

                    <!-- Phone Number -->
                    <div class="order-label">Phone Number</div>
                    <input type="tel" name="contact" class="input-responsive" value="<?php echo $contact; ?>" readonly>

                    <!-- Email -->
                    <div class="order-label">Email</div>
                    <input type="email" name="email" class="input-responsive" value="<?php echo $email; ?>" readonly>

                    <!-- Appointment Date -->
                    <div class="order-label">Appointment Date</div>
                    <input type="date" name="apptDate" class="input-responsive" value="<?php echo $apptDate; ?>" readonly>

                    <!-- Appointment Time -->
                    <div class="order-label">Appointment Time</div>
                    <input type="time" name="apptTime" class="input-responsive" value="<?php echo $apptTime; ?>" readonly>

                    <!-- Therapist Name -->
                    <div class="order-label">Therapist Name</div>
                    <input type="text" name="therapistName" class="input-responsive" value="<?php echo $therapistName; ?>" readonly>
                <?php endif; ?>

                <?php if ($userRoles == 1): ?>
                    <!-- Status (Only therapists can edit the status) -->
                    <div class="order-label">Status</div>
                    <select name="status" class="input-responsive" <?php echo ($userRoles != 1 ? '' : 'readonly'); ?>>
                        <option value="Approved" <?php echo ($status == 'Approved' ? 'selected' : ''); ?>>Approved</option>
                        <option value="Disapprove" <?php echo ($status == 'Disapprove' ? 'selected' : ''); ?>>Disapprove</option>
                    </select>
                <?php else: ?>
                    <!-- For regular users, make status field readonly -->
                    <div class="order-label">Status</div>
                    <input type="text" name="status" class="input-responsive" value="<?php echo $status; ?>" readonly>
                <?php endif; ?>

                <input type="submit" name="submit" value="Update Appointment" class="btn-primary">
            </fieldset>
        </table>
    </form>
</div>

<script>
    function myFunction() {
        var x = document.getElementById("myTopnav");
        if (x.className === "topnav") {
            x.className += " responsive";
        } else {
            x.className = "topnav";
        }
    }
</script>

</body>
</html>
