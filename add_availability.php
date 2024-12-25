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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Add Availability</title>
    <link rel="stylesheet" href="css/style.css">
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
	
    <h1 class="page-title">Manage Your Availability</h1>
	<p style="text-align: center; margin-bottom: 20px;">Set the date when you're not available here!</p>
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
