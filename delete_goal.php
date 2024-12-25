<?php
include("config.php");
session_start();

if (!isset($_SESSION['userID'])) {
    header("Location: login.php");
    exit;
}

$userID = $_SESSION['userID'];

if (isset($_GET['goalID'])) {
    $goalID = $_GET['goalID'];

    // Delete the goal from the database
    $sql = "DELETE FROM goal WHERE goalID = $goalID AND userID = $userID";
    if (mysqli_query($conn, $sql)) {
        header("Location: goal.php");
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>
