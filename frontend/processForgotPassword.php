<?php
session_start();
$conn = new mysqli("localhost", "root", "", "myresume"); // Change your DB name

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$email = $_POST['email'];

// Check if email exists
$stmt = $conn->prepare("SELECT * FROM cust WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $token = bin2hex(random_bytes(32)); // Secure reset token
    $_SESSION['reset_token'] = $token;
    $_SESSION['reset_email'] = $email;

    // Simulated email (just display reset link)
    $reset_link = "http://localhost/myresume/frontend/reset_password.php?token=$token"; // Change path as needed
    echo "Password reset link (simulated): <a href='$reset_link'>$reset_link</a>";
} else {
    echo "Email not found.";
}
?>
