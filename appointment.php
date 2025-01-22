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
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&amp;display=swap" rel="stylesheet"/>
    <style>
        /* Styles for the page */
        body {
		font-family: 'Arial', sans-serif;
		background: linear-gradient(to bottom, #fedad7, #e1cfe9, #d1e7e7);
		margin: 0;
		padding: 0;
		}
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 40px;
            position: relative;
            top: 0;
            left: 0;
            width: 96%;
            z-index: 1000;
            background: none;
        }
        .navbar-left,
        .navbar-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        .navbar img {
            height: 50px;
        }
        .navbar a {
            text-decoration: none;
            color: black;
            font-size: 18px;
            font-weight: 500;
            transition: color 0.3s ease;
        }
        .navbar a:hover {
            color: #d4dcfc;
        }
        .navbar a.active {
            font-weight: bold;
        }
        .navbar-right a {
            color: black;
            font-size: 18px;
            text-decoration: none;
        }
        .navbar-right a:hover {
            color: #d4dcfc;
        }

        .sign-out {
            background: #ffe156;
            padding: 10px 20px;
            border-radius: 20px;
            font-weight: bold;
            transition: background 0.3s ease;
        }

        .sign-out:hover {
            background: #ffd700;
        }

        /* Responsive design */
        @media (max-width: 768px) {
            .navbar {
                flex-direction: column;
                padding: 20px;
            }

            .navbar-left,
            .navbar-right {
                flex-direction: column;
                gap: 20px;
            }
        }
		.container {
    background: rgba(255, 255, 255, 0.3);
    border-radius: 15px;
	padding: 15px;
    width: 100%;
    max-width: 1500px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    color: white;
    margin: 100px auto 0; /* Space below the fixed navbar */
    overflow-x: auto; /* Allows horizontal scrolling if content overflows */
    box-sizing: border-box;
}

table {
    width: 100%;
    border-collapse: collapse;
    table-layout: fixed; /* Ensures the table fits within its container */
}

th, td {
    padding: 15px;
    text-align: center;
    color: #2b244e;
    border-bottom: 1px solid rgba(255, 255, 255, 0.5);
    word-wrap: break-word; /* Breaks long words to prevent overflow */
    overflow-wrap: break-word;
}
table tr:not(:first-child):hover {
    background-color: rgba(255, 255, 255, 0.5);
    transition: background 0.3s ease-in-out;
}
.actions a {
    margin: 0 10px;
    color: #1a3e8c;
    font-size: 20px;
    text-decoration: none;
    transition: color 0.3s ease-in-out;
}

.actions a:hover {
    color: #82a0e0;
}

.status-pending {
    background-color: #ffcc00;
    padding: 5px 10px;
    border-radius: 5px;
}

.status-approved {
    background-color: #4caf50;
    padding: 5px 10px;
    border-radius: 5px;
}

.status-disapproved {
    background-color: #ff4d4d;
    padding: 5px 10px;
    border-radius: 5px;
}
.btn-approve, .btn-disapprove {
    color: #fff;
    font-size: 16px;
    text-decoration: none;
    padding: 8px 15px; /* Adjusted padding for better spacing */
    border-radius: 5px;
    background-color: #4CAF50; /* Green for approve */
    display: inline-block; /* Ensure buttons stay inline */
    margin-right: 10px; /* Space between approve and disapprove buttons */
    transition: background-color 0.3s ease;
}

.btn-approve:hover {
    background-color: #45a049; /* Darker green */
}

.btn-disapprove {
    background-color: #f44336; /* Red for disapprove */
}

.btn-disapprove:hover {
    background-color: #da190b; /* Darker red */
}

/* Smaller icons */
.btn-approve i, .btn-disapprove i {
    margin-right: 5px; /* Space between the icon and the text */
    font-size: 14px; /* Make the icons smaller */
}

.btn-approve i {
    font-size: 14px; /* Make approve icon smaller */
}

.btn-disapprove i {
    font-size: 14px; /* Make disapprove icon smaller */
}

/* Responsive design */
@media (max-width: 768px) {
    .container {
        width: 95%;
    }

    table {
        table-layout: auto; /* Allows more flexibility on smaller screens */
    }

    th, td {
        font-size: 14px;
        padding: 10px;
    }
}
</style>
</head>
<body>
<!-- Navbar -->
<div class="navbar">
    <div class="navbar-left">
        <div class="logo">
            <a href="#" title="Logo">
                <img src="img/mm(1).png" alt="Mindmate Logo">
            </a>
        </div>
        <?php
        if ($userRoles == 1) {
            echo '<a href="therapist_dash.php">Home</a>';
            echo '<a href="upload_resource.php">Resource</a>';
            echo '<a href="goal.php">Goal</a>';
            echo '<a href="appointment.php">Appointment</a>';
            echo '<a href="add_availability.php">Availability</a>';
            echo '<a href="feedback.php">Feedback</a>';
        } elseif ($userRoles == 2) {
            echo '<a href="dashboard.php">Home</a>';
            echo '<a href="resource.php">Resource</a>';
            echo '<a href="therapist.php">Therapist</a>';
            echo '<a href="goal.php">Goal</a>';
            echo '<a href="appointment.php">Appointment</a>';
            echo '<a href="feedback.php">Feedback</a>';
        }
        ?>
    </div>
    <div class="navbar-right">
        <a href="notifications.php"><i class="fas fa-bell"></i> Notifications (<?php echo $notificationCount; ?>)</a>
		<?php
        if (isset($_SESSION['userRoles'])) {
		$userRoles = $_SESSION['userRoles']; // Assuming userRoles is stored in session
    // Conditional logic to show the correct profile page based on user role
		if ($userRoles == 1) {
        // For therapist role (userRoles == 1), show thera_profile.php
        echo '<a href="thera_profile.php"><i class="fas fa-user"></i> Profile</a>';
		} elseif ($userRoles == 2) {
        // For user role (userRoles == 2), show user_profile.php
        echo '<a href="user_profile.php"><i class="fas fa-user"></i> Profile</a>';
			}
		}
	?>
        <a href="logout.php" class="sign-out">Logout</a>
    </div>
</div>

<div class="container">
    <div class="header">
        <h2 style="font-size: 42px; font-family: 'Ariel', sans-serif; color: #2b244e; font-weight: 700; text-align: center;">List of Appointments</h2>
    </div>
    <table>
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
                
				 // Display status with styling
				echo "<td class='status-" . strtolower($row["status"]) . "'>" . $row["status"] . "</td>";
				
				
                // Show the approval/disapproval buttons only for therapists (userRole = 1) and if the status is 'Pending'
                if ($row["status"] == 'Pending' && $userRoles == 1) {
                    echo '<td><a href="approve.php?id=' . $row["apptID"] . '" class="btn-approve"><i class="fas fa-check"></i> Approve</a>';
					echo '<a href="disapprove.php?id=' . $row["apptID"] . '" class="btn-disapprove"><i class="fas fa-times"></i> Disapprove</a></td>';
                } else {
                    // For users, no buttons will be shown, just the status in the table
					echo '<td class="actions">';
                    echo '<a href="appointment_edit.php?id=' . $row["apptID"] . '" title="Edit"><i class="fas fa-edit"></i></a>';
					echo '<a href="appointment_delete.php?id=' . $row["apptID"] . '" onClick="return confirm(\'Are you sure you want to delete this appointment?\');" title="Delete"><i class="fas fa-trash-alt"></i></a>';
					echo '</td>';
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
