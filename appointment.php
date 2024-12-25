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
$userID = $_SESSION['userID'];  // Fetch the user's ID from the session

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
    <title>Appointment List</title>
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

<h2 class="text-center">List of Appointments</h2>

<div style="padding:0 10px;">
    <table border="1" class="appointment-table">
        <tr>
            <th width="5%">No</th>
            <th width="20%">Name</th>
            <th width="10%">Contact</th>
            <th width="15%">Email</th>
            <th width="10%">Appointment Date</th>
            <th width="10%">Appointment Time</th>
            <th width="10%">Therapist</th>
            <th width="10%">Status</th>
            <th width="10%">Actions</th>
        </tr>

        <?php
        // Query to retrieve appointments based on user role
        if ($userRoles == 1) {
            // Therapist: Show only appointments assigned to them
            $sql = "SELECT apptID, fullname, contact, email, apptDate, apptTime, appointment.theraID, status, therapist.theraName 
                    FROM appointment 
                    JOIN therapist ON appointment.theraID = therapist.theraID 
                    WHERE appointment.theraID = $userID"; // Filter by therapist's ID
        } else if ($userRoles == 2) {
            // User: Show only their own appointments
            $sql = "SELECT apptID, fullname, contact, email, apptDate, apptTime, appointment.theraID, status, therapist.theraName 
                    FROM appointment 
                    JOIN therapist ON appointment.theraID = therapist.theraID 
                    WHERE appointment.userID = $userID"; // Filter by user's ID
        }

        $result = mysqli_query($conn, $sql);

        if (mysqli_num_rows($result) > 0) {
            $numrow = 1;
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>";
                echo "<td>" . $numrow . "</td>";
                echo "<td>" . $row["fullname"] . "</td>";
                echo "<td>" . $row["contact"] . "</td>";
                echo "<td>" . $row["email"] . "</td>";
                echo "<td>" . $row["apptDate"] . "</td>";
                echo "<td>" . $row["apptTime"] . "</td>";
                echo "<td>" . $row["theraName"] . "</td>";
                
                // Display the status for both therapists and users
                echo "<td>" . $row["status"] . "</td>";

                // Show the approval/disapproval buttons only for therapists (userRole = 1) and if the status is 'Pending'
                if ($row["status"] == 'Pending' && $userRoles == 1) {
                    echo '<td><a href="approve.php?id=' . $row["apptID"] . '" class="btn-approve">Approve</a> | ';
                    echo '<a href="disapprove.php?id=' . $row["apptID"] . '" class="btn-disapprove">Disapprove</a></td>';
                } else {
                    // For users, no buttons will be shown, just the status in the table
                    echo '<td><a href="appointment_edit.php?id=' . $row["apptID"] . '">Edit</a> | ';
                    echo '<a href="appointment_delete.php?id=' . $row["apptID"] . '" onClick="return confirm(\'Are you sure you want to delete?\');">Delete</a></td>';
                }
                echo "</tr>";
                $numrow++;
            }
        } else {
            echo '<tr><td colspan="9">No appointments found</td></tr>';
        }

        mysqli_close($conn);
        ?>
    </table>
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
