<?php
session_start();

if (!isset($_GET['token']) || !isset($_SESSION['reset_token']) || $_GET['token'] !== $_SESSION['reset_token']) {
    die("Invalid or expired reset link.");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Reset Password</title>
    <link rel="stylesheet" href="css/forgetPass.css">
</head>
<body>
    <h1 class="page-title">Reset Your Password</h1>

    <div class="container">
        <form action="update_password.php" method="post">
            <input type="hidden" name="email" value="<?= htmlspecialchars($_SESSION['reset_email']) ?>">

            <label>New Password:</label>
            <input type="password" name="new_password" required>

            <label>Confirm Password:</label>
            <input type="password" name="confirm_password" required>

            <input type="submit" value="Reset Password">
        </form>
        <a href="login.php" class="button">Back to Login</a>
    </div>
</body>
</html>
