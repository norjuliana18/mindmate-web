<?php
session_start();

// Assuming you process the appointment here
$appointmentBooked = false; // Change this based on your appointment processing logic
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointment Confirmation</title>
    <!-- You can include any additional styles or scripts you need -->
</head>

<body>

    <script>
        <?php if ($appointmentBooked): ?>
            // If appointment is booked successfully, show a confirmation message
            alert("Appointment successfully booked!");
            // Redirect to another page if needed, e.g., the home page or therapist page
            window.location.href = "appointment.php"; // Redirect to a success page
        <?php else: ?>
            // If appointment wasn't booked, show an error message
            alert("There was an error booking your appointment. Please try again.");
            // You can redirect to another page or stay on the same page based on the scenario
            window.location.href = "therapist_details.php"; // Stay on therapist page or redirect
        <?php endif; ?>
    </script>

</body>

</html>
