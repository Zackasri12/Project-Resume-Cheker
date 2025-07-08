<?php
session_start();
if (!isset($_GET['token']) || $_GET['token'] !== $_SESSION['reset_token']) {
    die("Invalid or expired reset link.");
}
?>

<!DOCTYPE html>
<html>
<head><title>Reset Password</title></head>
<body>
    <h2>Reset Your Password</h2>
    <form action="update_password.php" method="post">
        <input type="hidden" name="email" value="<?= $_SESSION['reset_email'] ?>">
        <label>New Password:</label><br>
        <input type="password" name="new_password" required><br><br>
        <input type="submit" value="Reset Password">
    </form>
</body>
</html>
