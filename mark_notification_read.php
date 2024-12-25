<?php
include 'config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['notificationID'])) {
    $notificationID = intval($_POST['notificationID']);
    $userID = $_SESSION['userID'];

    $sql = "UPDATE notifications SET isRead = 1 WHERE notificationID = ? AND userID = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $notificationID, $userID);
    if (mysqli_stmt_execute($stmt)) {
        echo 'success';
    } else {
        echo 'error';
    }
    mysqli_stmt_close($stmt);
}
mysqli_close($conn);
?>
