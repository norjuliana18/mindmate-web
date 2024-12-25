<?php
include("config.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userEmail = mysqli_real_escape_string($conn, $_POST['userEmail']);
    $userPwd = mysqli_real_escape_string($conn, $_POST['userPwd']);
    $confirmPwd = mysqli_real_escape_string($conn, $_POST['confirmPwd']);
    $userRoles = mysqli_real_escape_string($conn, $_POST['userRoles']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);

    // Validate password and confirm password
    if ($userPwd !== $confirmPwd) {
        echo "<script>alert('Password and confirm password do not match.'); window.location.href = 'register.php';</script>";
        exit;
    }

    // Check if userEmail or username already exists
    $sql = "SELECT * FROM user WHERE userEmail='$userEmail' OR username='$username' LIMIT 1"; 
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        echo "<script>alert('Error: User exists, please register a new user.'); window.location.href = 'register.php';</script>";
    } else {
        // User does not exist, insert new user record, hash the password
        $pwdHash = password_hash($userPwd, PASSWORD_DEFAULT);
        $sql = "INSERT INTO user (userEmail, userPwd, userRoles, username) VALUES ('$userEmail', '$pwdHash', '$userRoles', '$username')";

        if (mysqli_query($conn, $sql)) {
            echo "<script>alert('New user record created successfully.'); window.location.href = 'login.php';</script>";
        } else {
            echo "<script>alert('Error: " . mysqli_error($conn) . "'); window.location.href = 'register.php';</script>";
        }
    }
}
mysqli_close($conn);
?>

</body>
</html>
