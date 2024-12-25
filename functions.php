<?php
function createNotification($conn, $userID, $message) {
    $sql = "INSERT INTO notifications (userID, message, isRead, createdAt) VALUES (?, ?, 0, NOW())";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "is", $userID, $message);
    if (mysqli_stmt_execute($stmt)) {
        return true;
    } else {
        return false;
    }
    mysqli_stmt_close($stmt);
}
?>
