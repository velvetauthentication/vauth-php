<?php
session_start(); 

require 'sdk.php'; 

$appId = "appid";
$secret = "secret";
$version = "1.0";

$sdk = new VauthSDK($appId, $secret, $version);

if(isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $loginResponse = $sdk->login($username, $password);

    if(is_array($loginResponse) && isset($loginResponse['message'])) {
        if($loginResponse['message'] === 'Login successful') {
            // Store necessary user details in session
            $_SESSION['user_logged_in'] = true;
            $_SESSION['username'] = $username;
            $_SESSION['user_details'] = $loginResponse['data']; // Set user details

            // Redirect to welcome page
            header("Location: welcome.php");
            exit; 
        } else {
            // Handle login failure
            $loginMessage = "Login failed. Please try again.";
            // Output JSON response message
            $loginMessage .= "<br>" . $loginResponse['message'];
        }
    } else {
        // Handle other errors
        $loginMessage = "An error occurred. Please try again later.";
    }
}

$loginMessage = isset($loginMessage) ? $loginMessage : '';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
</head>
<body>
    <h2>Login</h2>
 
    <?php if(!empty($loginMessage)): ?>
        <p><?php echo $loginMessage; ?></p>
    <?php endif; ?>
    
    <form method="post" action="">
        Username: <input type="text" name="username" required><br>
        Password: <input type="password" name="password" required><br>
        <input type="submit" name="login" value="Login">
    </form>
</body>
</html>