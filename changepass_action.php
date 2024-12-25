<?php
include("config.php");
?>
<!DOCTYPE html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userEmail = mysqli_real_escape_string($conn, $_POST['userEmail']);
    $userPwd = mysqli_real_escape_string($conn, $_POST['userPwd']);

    // Check if userEmail exists
    $sql = "SELECT * FROM user WHERE userEmail='$userEmail' LIMIT 1"; 
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) == 1) {
        // User exists, now hash the password
        $pwdHash = password_hash($userPwd, PASSWORD_DEFAULT); 

        // Update the password
        $sql = "UPDATE user SET userPwd = '$pwdHash' WHERE userEmail = '$userEmail'";

        if (mysqli_query($conn, $sql)) {
            echo "<script>alert('Password updated successfully!'); window.location.href = 'login.php';</script>";
        } else {
            echo "<script>alert('Failed to update password. Please try again.');</script>";
        }
    } else {
        echo "<script>alert('User does not exist'); window.location.href = 'forgotpassword.php';</script>";
    }

    mysqli_close($conn);
}
?>

</body>
</html>
