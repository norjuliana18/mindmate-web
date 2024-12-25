<?php
include 'config.php';
include 'functions.php';
session_start();

if (!isset($_SESSION['userID'])) {
    header("Location: login.php");
    exit;
}

$userID = $_SESSION['userID'];
$userRoles = $_SESSION['userRoles'];

// Fetch notifications for the user
$sql = "SELECT notificationID, message, isRead, createdAt FROM notifications WHERE userID = ? ORDER BY createdAt DESC";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $userID);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$notifications = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $notifications[] = $row;
    }
}

// Get unread notification count
$unreadCount = 0;
foreach ($notifications as $notification) {
    if ($notification['isRead'] == 0) {
        $unreadCount++;
    }
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Notifications</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
         /* Notifications Container */
        .notifications-container {
            width: 80%;
            margin: 20px auto;
            background-color: #fff;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            font-family: 'Poppins', sans-serif;
        }

        .notifications-container h2 {
            text-align: center;
            font-size: 2rem;
            color: #333;
            margin-bottom: 25px;
        }

        .notification {
            border-bottom: 1px solid #ddd;
            padding: 15px 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .notification:last-child {
            border-bottom: none;
        }

        .notification .message {
            font-size: 1rem;
            color: #555;
        }

        .notification .timestamp {
            font-size: 0.9rem;
            color: #999;
        }

        .notification.unread .message {
            font-weight: bold;
            color: #000;
        }

        .mark-read-btn {
            background-color: #ff6f61;
            color: white;
            border: none;
            padding: 5px 10px;
            font-size: 0.9rem;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .mark-read-btn:hover {
            background-color: #e85a4f;
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
                <a href="therapist_dash.php" title="Logo">
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
                    if ($unreadCount > 0) {
                        echo '<span class="notification-count">' . $unreadCount . '</span>';
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
                <a href="dashboard.php" title="Logo">
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
                    if ($unreadCount > 0) {
                        echo '<span class="notification-count">' . $unreadCount . '</span>';
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
<div class="notifications-container">
    <h2>Your Notifications</h2>
    <?php if (empty($notifications)): ?>
        <p>You have no notifications.</p>
		<?php else: ?>
        <?php foreach ($notifications as $notification): ?>
		<div class="notification <?= $notification['isRead'] ? '' : 'unread'; ?>" data-id="<?= $notification['notificationID']; ?>">
        <div class="message"><?= htmlspecialchars($notification['message']); ?></div>
        <div>
            <span class="timestamp"><?= date("F j, Y, g:i a", strtotime($notification['createdAt'])); ?></span>
            <?php if (!$notification['isRead']): ?>
                <button class="mark-read-btn">Mark as Read</button>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<script>
$(document).ready(function(){
    $('.mark-read-btn').click(function(){
        var notificationDiv = $(this).closest('.notification');
        var notificationID = notificationDiv.data('id');

        $.ajax({
            url: 'mark_notification_read.php',
            type: 'POST',
            data: { notificationID: notificationID },
            success: function(response){
                if(response === 'success'){
                    notificationDiv.removeClass('unread');
                    notificationDiv.find('.mark-read-btn').remove();
                } else {
                    alert('Failed to mark notification as read.');
                }
            }
        });
    });
});
</script>
</body>
</html>
