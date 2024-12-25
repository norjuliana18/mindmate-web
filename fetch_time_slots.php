<?php
// Include database connection
include 'config.php';

// Get the therapist ID and selected date
$theraID = mysqli_real_escape_string($conn, $_POST['theraID']);
$date = mysqli_real_escape_string($conn, $_POST['date']);

// Define the working hours (e.g., 9:00 AM to 5:00 PM)
$startTime = "09:00:00";
$endTime = "17:00:00";

// Generate a list of all possible time slots (e.g., every 30 minutes)
$timeSlots = [];
$currentTime = strtotime($startTime);
$endTimestamp = strtotime($endTime);

while ($currentTime < $endTimestamp) {
    $timeSlots[] = date("H:i:s", $currentTime);
    $currentTime = strtotime("+30 minutes", $currentTime);
}

// Fetch booked or unavailable time slots for the selected date
$query = "
    SELECT apptTime AS blockedTime 
    FROM appointment 
    WHERE theraID = '$theraID' AND apptDate = '$date'
    UNION
    SELECT availStart AS blockedTime 
    FROM availability 
    WHERE theraID = '$theraID' AND availDate = '$date'
";
$result = mysqli_query($conn, $query);

// Collect all blocked time slots
$blockedTimes = [];
if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $blockedTimes[] = $row['blockedTime'];
    }
}

// Generate the available time slots
$availableSlots = array_diff($timeSlots, $blockedTimes);

// Output the available time slots as options
if (!empty($availableSlots)) {
    foreach ($availableSlots as $slot) {
        echo "<option value='" . $slot . "'>" . $slot . "</option>";
    }
} else {
    echo "<option value=''>No available time slots</option>";
}
?>
