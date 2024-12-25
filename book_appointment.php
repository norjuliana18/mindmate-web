<?php
// Include the database connection and start the session
include 'config.php';
session_start();

// Check if the user is logged in
if (!isset($_SESSION['userID'])) {
    header("Location: login.php");
    exit;
}

// Fetch user roles
$userRoles = $_SESSION['userRoles'];

// Fetch user and therapist IDs
$userID = $_SESSION['userID'];
$theraID = isset($_GET['theraID']) ? mysqli_real_escape_string($conn, $_GET['theraID']) : null;

// Fetch user and therapist details for the form
$userQuery = "SELECT username, userEmail, address, gender, userContact FROM user WHERE userID = '$userID'";
$userResult = mysqli_query($conn, $userQuery);
$userData = $userResult ? mysqli_fetch_assoc($userResult) : null;

$therapistQuery = "SELECT theraName FROM therapist WHERE theraID = '$theraID'";
$therapistResult = mysqli_query($conn, $therapistQuery);
$therapistData = $therapistResult ? mysqli_fetch_assoc($therapistResult) : null;

// Redirect if data is missing
if (!$userData || !$therapistData) {
    echo "<p>Invalid booking details. Please try again.</p>";
    exit;
}

// Fetch therapist's availability
$availabilityQuery = "SELECT * FROM availability WHERE theraID = '$theraID' AND availDate >= CURDATE() ORDER BY availDate, availStart";
$availabilityResult = mysqli_query($conn, $availabilityQuery);

// Prepare an array to store the unavailable dates (those that are already booked)
$unavailableDates = [];
while ($row = mysqli_fetch_assoc($availabilityResult)) {
    $unavailableDates[] = $row['availDate'];
}

// Prepare the available dates by excluding the unavailable dates
$availableDatesQuery = "SELECT DISTINCT availDate FROM availability WHERE theraID = '$theraID' AND availDate >= CURDATE() ORDER BY availDate";
$availableDatesResult = mysqli_query($conn, $availableDatesQuery);

// Prepare an array to store the available dates
$availableDates = [];
while ($row = mysqli_fetch_assoc($availableDatesResult)) {
    if (!in_array($row['availDate'], $unavailableDates)) {
        $availableDates[] = $row['availDate'];
    }
}

// Function to get available time slots for a given date
function getAvailableTimeSlots($theraID, $date) {
    global $conn;
    $query = "SELECT availStart FROM availability WHERE theraID = '$theraID' AND availDate = '$date' ORDER BY availStart";
    $result = mysqli_query($conn, $query);
    $timeSlots = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $timeSlots[] = $row['availStart'];
    }
    return $timeSlots;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Appointment</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        /* Booking Appointment Container */
        .bappt-container {
            width: 80%;
            margin: 20px auto;
            background-color: #fff;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            font-family: 'Poppins', sans-serif;
        }

        .bappt-container h2 {
            text-align: center;
            font-size: 2rem;
            color: #000;
            margin-bottom: 25px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .bappt-container form {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        /* Fun Input Styles */
        .bappt-container input,
        .bappt-container select {
            padding: 12px 15px;
            font-size: 1rem;
            border-radius: 8px;
            border: 2px solid #ddd;
            background-color: #f9f9f9;
            transition: all 0.3s ease;
        }

        .bappt-container input:focus,
        .bappt-container select:focus {
            border-color: #ff6f61;
            background-color: #fff3f2;
            box-shadow: 0 0 8px rgba(255, 111, 97, 0.5);
        }

        /* Label Styles */
        .bappt-container label {
            font-size: 1.1rem;
            color: #333;
            font-weight: 600;
        }

        /* Time Slot Dropdown */
        #apptTime {
            background-color: #fff;
            border: 2px solid #ddd;
            color: #333;
            cursor: pointer;
        }

        #apptTime option {
            padding: 10px;
        }

        /* Fun Submit Button */
        .bappt-container .btn-submit {
            background-color: #4CAF50;
            color: white;
            padding: 15px;
            font-size: 1.2rem;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }

        .bappt-container .btn-submit:hover {
            background-color: #45a049;
            transform: scale(1.05);
        }

        /* Date Selector */
        #apptDate {
            padding: 12px 15px;
            background-color: #f9f9f9;
            border-radius: 8px;
            border: 2px solid #ddd;
            font-size: 1rem;
            transition: 0.3s ease;
        }

        #apptDate:focus {
            background-color: #fff3f2;
            border-color: #ff6f61;
            box-shadow: 0 0 8px rgba(255, 111, 97, 0.5);
        }

        /* Number of People Input */
        #no_people {
            width: 100px;
            padding: 10px;
            background-color: #f9f9f9;
            border-radius: 8px;
            border: 2px solid #ddd;
            font-size: 1rem;
            transition: 0.3s ease;
        }

        #no_people:focus {
            border-color: #ff6f61;
            background-color: #fff3f2;
            box-shadow: 0 0 8px rgba(255, 111, 97, 0.5);
        }
    </style>
    <script>
        $(document).ready(function() {
            // List of unavailable dates from PHP
            const unavailableDates = <?php echo json_encode($unavailableDates); ?>;

            // Disable unavailable dates in the date picker
            $('#apptDate').on('input', function() {
                const selectedDate = $(this).val();

                if (unavailableDates.includes(selectedDate)) {
                    $(this).val('');  // Reset the input if it's an unavailable date
                    alert("The selected date is unavailable. Please choose another date.");
                } else {
                    // Fetch available time slots for the selected date using AJAX
                    $.ajax({
                        url: 'fetch_time_slots.php', // Create this PHP file for AJAX processing
                        method: 'POST',
                        data: {
                            date: selectedDate,
                            theraID: '<?php echo $theraID; ?>'
                        },
                        success: function(data) {
                            $('#apptTime').html(data); // Populate time slots dropdown
                        }
                    });
                }
            });
        });
    </script>
</head>
<body>
<!-- Navbar Section Start Here -->
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

<div class="bappt-container">
    <h2>Book Appointment with <?= $therapistData['theraName']; ?></h2>
    <form action="book_appointment_action.php" method="POST">
        <form action="book_appointment_action.php" method="POST">
		<input type="hidden" name="theraID" value="<?= $theraID; ?>">
		<label for="fullname">Full Name</label>
		<input type="text" id="fullname" name="fullname" value="<?= $userData['username']; ?>" required>
		
		 <label for="contact">Contact Number</label>
		<input type="text" id="contact" name="contact" value="<?= $userData['userContact']; ?>" required>
		
		<label for="email">Email</label>
		<input type="email" id="email" name="email" value="<?= $userData['userEmail']; ?>" required>
		
		<label for="address">Address</label>
		<input type="text" id="address" name="address" value="<?= $userData['address']; ?>" required>
		
		<label for="gender">Gender</label>
		<input type="text" id="gender" name="gender" value="<?= $userData['gender']; ?>" required>
		
		<label for="apptDate">Select Date</label>
		<input type="date" id="apptDate" name="apptDate" required>
		
		<label for="apptTime">Select Time</label>
		<select id="apptTime" name="apptTime" required>
        <option value="">Select a time</option>
    </select>
    <button type="submit" class="btn-submit">Book Appointment</button>
    </form>
</div>
</body>
</html>
