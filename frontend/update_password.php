<?php
session_start();
$conn = new mysqli("localhost", "root", "", "myresume"); 

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$email = $_POST['email'];
$new_password = $_POST['new_password'];
$hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

// Update password in DB
$stmt = $conn->prepare("UPDATE cust SET password = ? WHERE email = ?");
$stmt->bind_param("ss", $hashed_password, $email);

if ($stmt->execute()) {
    unset($_SESSION['reset_token']);
    unset($_SESSION['reset_email']);
    echo "Password reset successfully. <a href='login.php'>Login</a>";
} else {
    echo "Error resetting password.";
}
?>
