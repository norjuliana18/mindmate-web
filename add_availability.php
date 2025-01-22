<?php
include 'config.php';
session_start();

// Ensure the user is a logged-in therapist
if (!isset($_SESSION['userID']) || $_SESSION['userRoles'] != 1) {
    header("Location: login.php");
    exit;
}

$theraID = $_SESSION['userID'];

// Fetch existing availability data
$availabilityData = [];
$query = "SELECT availDate, availStart, availEnd, remarks 
          FROM availability 
          WHERE theraID = '$theraID' 
          ORDER BY availDate ASC";
$result = mysqli_query($conn, $query);

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $availabilityData[] = $row;
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dates = $_POST['dates']; // Array of dates
    $startTimes = $_POST['startTimes']; // Array of start times
    $endTimes = $_POST['endTimes']; // Array of end times
    $remarks = mysqli_real_escape_string($conn, $_POST['remarks']);

    $successCount = 0;
    $errorCount = 0;

    // Process each date and its time slots
    foreach ($dates as $index => $date) {
        $availDate = mysqli_real_escape_string($conn, $date);

        if (!empty($startTimes[$index]) && !empty($endTimes[$index])) {
            foreach ($startTimes[$index] as $slotIndex => $startTime) {
                $availStart = mysqli_real_escape_string($conn, $startTime);
                $availEnd = mysqli_real_escape_string($conn, $endTimes[$index][$slotIndex]);

                $insertQuery = "INSERT INTO availability (theraID, availDate, availStart, availEnd, remarks)
                                VALUES ('$theraID', '$availDate', '$availStart', '$availEnd', '$remarks')";

                if (mysqli_query($conn, $insertQuery)) {
                    $successCount++;
                } else {
                    $errorCount++;
                }
            }
        }
    }

    // Feedback message
    echo "<p>Successfully added $successCount availability slots.</p>";
    if ($errorCount > 0) {
        echo "<p>Failed to add $errorCount slots. Please try again.</p>";
    }

    // Refresh page to update the availability list
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}
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
    <title>Add Availability</title>
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
		/* Container Styles */
.container {
    background: rgba(255, 255, 255, 0.4);
    border-radius: 15px;
    padding: 20px;
    max-width: 1200px;
    margin: 80px auto 20px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
}

/* Table Styles */
table {
    width: 100%;
    border-collapse: collapse;
}
th, td {
    padding: 15px;
    text-align: center;
    border-bottom: 1px solid rgba(255, 255, 255, 0.5);
}
table tr:hover {
    background-color: rgba(255, 255, 255, 0.6);
    transition: background 0.3s;
}

/* Button Styles */
.btn-approve, .btn-disapprove {
    font-size: 16px;
    padding: 10px 20px;
    border-radius: 8px;
    border: none;
    color: #fff;
    cursor: pointer;
    transition: all 0.3s;
}
.btn-approve {
    background: #4CAF50;
}
.btn-approve:hover {
    background: #45a049;
}
.btn-disapprove {
    background: #f44336;
}
.btn-disapprove:hover {
    background: #da190b;
}

/* Form Styles */
/* Form Styles */
.availability-form {
    display: flex;
    flex-direction: column;
    gap: 15px;
    padding: 30px; /* Increased padding for spacing */
    background: rgba(255, 255, 255, 0.5);
    border-radius: 10px;
    max-width: 500px; /* Limit width for better structure */
    margin: 0 auto; /* Centering the form */
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    box-sizing: border-box; /* Ensures padding doesn't overflow */
}

.availability-form input,
.availability-form button {
	
    padding: 15px;
    border: none;
    border-radius: 8px;
    font-size: 16px;
    width: 90%; /* Ensuring they don't overflow */
}

.availability-form input[type="date"],
.availability-form input[type="time"] {
    border: 1px solid #ccc;
    outline: none;
    background: #fff;
}

.availability-form button {
    background: #6a5acd;
    color: white;
    cursor: pointer;
    transition: background 0.3s ease-in-out;
    font-weight: bold;
}

.availability-form button:hover {
    background: #5643cc;
}

/* Ensure spacing inside form and prevent touching borders */
.availability-form input,
.availability-form button {
    margin: 5px 0; /* Spacing between inputs */
}

/* Responsive design */
@media (max-width: 768px) {
    .availability-form {
        padding: 20px;
        max-width: 100%;
    }

    .availability-form input,
    .availability-form button {
        font-size: 14px;
    }
}


/* Responsive Design */
@media (max-width: 768px) {
    .navbar {
        flex-direction: column;
        align-items: center;
    }
    .container {
        width: 95%;
    }
    th, td {
        font-size: 14px;
        padding: 10px;
    }
}
.availability-list {
    background: rgba(255, 255, 255, 0.4);
    border-radius: 15px;
    padding: 30px;
    max-width: 1000px;
    margin: 50px auto;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    font-family: 'Roboto', sans-serif;
    color: #2b244e;
    text-align: center;
	gap: 30px;
}

.availability-list h2 {
    font-size: 28px;
    font-weight: 700;
    margin-bottom: 20px;
    color: #1a3e8c;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.availability-list ul {
    list-style: none;
    padding: 0;
}

.availability-list li {
    background: rgba(255, 255, 255, 0.7);
    padding: 15px;
    margin: 10px 0;
    border-radius: 10px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease-in-out;
}

.availability-list li:hover {
    background: #e1cfe9;
    transform: translateY(-3px);
}

.availability-list strong {
    color: #1a3e8c;
    font-weight: 600;
}

.availability-list p {
    font-size: 18px;
    font-style: italic;
    color: #555;
}

</style>
    <script>
        function addTimeSlot(dateIndex) {
            const timeSlotContainer = document.getElementById(`timeSlots-${dateIndex}`);

            const timeSlotGroup = document.createElement('div');
            timeSlotGroup.className = 'time-slot-group';

            const startTimeInput = document.createElement('input');
            startTimeInput.type = 'time';
            startTimeInput.name = `startTimes[${dateIndex}][]`;
            startTimeInput.required = true;

            const endTimeInput = document.createElement('input');
            endTimeInput.type = 'time';
            endTimeInput.name = `endTimes[${dateIndex}][]`;
            endTimeInput.required = true;

            const removeSlotButton = document.createElement('button');
            removeSlotButton.type = 'button';
            removeSlotButton.textContent = 'Remove Slot';
            removeSlotButton.className = 'btn-remove-slot';
            removeSlotButton.onclick = function () {
                this.parentElement.remove();
            };

            timeSlotGroup.appendChild(startTimeInput);
            timeSlotGroup.appendChild(endTimeInput);
            timeSlotGroup.appendChild(removeSlotButton);
            timeSlotContainer.appendChild(timeSlotGroup);
        }

        function addDateGroup() {
            const dateIndex = document.getElementsByClassName('date-group').length;
            const dateGroup = document.createElement('div');
            dateGroup.className = 'date-group';

            const dateLabel = document.createElement('label');
            dateLabel.textContent = 'Date:';
            const dateInput = document.createElement('input');
            dateInput.type = 'date';
            dateInput.name = 'dates[]';
            dateInput.required = true;

            const timeSlotContainer = document.createElement('div');
            timeSlotContainer.id = `timeSlots-${dateIndex}`;
            timeSlotContainer.className = 'time-slot-container';

            addTimeSlot(dateIndex);

            const addSlotButton = document.createElement('button');
            addSlotButton.type = 'button';
            addSlotButton.textContent = 'Add Another Time Slot';
            addSlotButton.className = 'btn-add-slot';
            addSlotButton.onclick = function () {
                addTimeSlot(dateIndex);
            };

            const removeDateButton = document.createElement('button');
            removeDateButton.type = 'button';
            removeDateButton.textContent = 'Remove Date';
            removeDateButton.className = 'btn-remove-date';
            removeDateButton.onclick = function () {
                this.parentElement.remove();
            };

            dateGroup.appendChild(dateLabel);
            dateGroup.appendChild(dateInput);
            dateGroup.appendChild(timeSlotContainer);
            dateGroup.appendChild(addSlotButton);
            dateGroup.appendChild(removeDateButton);

            document.getElementById('dateContainer').appendChild(dateGroup);
        }
    </script>
</head>
<body>
    <!-- Navbar Section Starts Here -->
    <div class="navbar">
    <div class="navbar-left">
        <div class="logo">
            <a href="#" title="Logo">
                <img src="img/mm(1).png" alt="Mindmate Logo" class="img-responsive">
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
    <!-- Navbar Section Ends Here -->
    <!-- Display Existing Availability -->
    <div class="availability-list">
        <h2>List Date Not Available</h2>
        <?php if (!empty($availabilityData)): ?>
            <ul>
                <?php foreach ($availabilityData as $availability): ?>
                    <li>
                        <strong>Date:</strong> <?= $availability['availDate']; ?>
                        <strong>Time:</strong> <?= $availability['availStart']; ?> - <?= $availability['availEnd']; ?>
                        <strong>Remarks:</strong> <?= htmlspecialchars($availability['remarks']); ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>No availability slots added yet.</p>
        <?php endif; ?>
    </div>
	
    <h1 style="font-size: 52px; font-family: 'Ariel', sans-serif; color: #2b244e; font-weight: 700; text-align: center; margin-bottom: 20px;">Manage Your Availability</h1>
	<p style="font-size: 22px; font-family: 'Ariel',sans-serif; color: #2b244e; text-align: center; margin-bottom: 20px;">Set the date when you're not available here!</p>
    <!-- Form to Add Availability -->
    <form method="POST" class="availability-form">
        <div id="dateContainer" class="date-container">
            <div class="date-group">
                <label>Date:</label>
                <input type="date" name="dates[]" required>
                <div class="time-slot-container" id="timeSlots-0">
                    <div class="time-slot-group">
                        <input type="time" name="startTimes[0][]" required>
                        <input type="time" name="endTimes[0][]" required>
                        <button type="button" class="btn-remove-slot" onclick="this.parentElement.remove()">Remove Slot</button>
                    </div>
                </div>
                <button type="button" class="btn-add-slot" onclick="addTimeSlot(0)">Add Another Time Slot</button>
                <button type="button" class="btn-remove-date" onclick="this.parentElement.remove()">Remove Date</button>
            </div>
        </div>
        <label>Remarks:</label>
        <input type="text" name="remarks">
        <button type="submit" class="btn-submit">Submit</button>
    </form>
</body>
</html>
