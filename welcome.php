<?php
session_start(); 

if(!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['user_details']['username'];
$email = $_SESSION['user_details']['email'];
$expiryDate = $_SESSION['user_details']['expiry_date'];
$userLevel = $_SESSION['user_details']['user_level'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Welcome</title>
</head>
<body>
    <h2>Welcome, <?php echo $username; ?></h2>
    <p>Your email: <?php echo $email; ?></p>
    <p>Expiry Date: <?php echo $expiryDate; ?></p>
    <p>User Level: <?php echo $userLevel; ?></p>
    <p><a href="logout.php">Logout</a></p>
</body>
</html>