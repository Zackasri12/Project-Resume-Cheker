<?php
session_start();
$conn = new mysqli("localhost", "root", "", "myresume");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$email = $_POST['email'] ?? '';
$new_password = $_POST['new_password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';

if (empty($new_password) || empty($confirm_password)) {
    die("Both password fields are required.");
}

if ($new_password !== $confirm_password) {
    die("Passwords do not match. <a href='reset_password.php?token=" . $_SESSION['reset_token'] . "'>Try again</a>");
}

$hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

$stmt = $conn->prepare("UPDATE cust SET password = ? WHERE email = ?");
$stmt->bind_param("ss", $hashed_password, $email);

if ($stmt->execute()) {
    unset($_SESSION['reset_token']);
    unset($_SESSION['reset_email']);
    echo "<h3>Password reset successfully!</h3>";
    echo "<a href='login.php'>Click here to login</a>";
} else {
    echo "Error updating password.";
}

$stmt->close();
$conn->close();
?>
