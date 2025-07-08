<?php
session_start();

// Connect to database
$conn = new mysqli("localhost", "root", "", "myresume");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Sanitize input
$email = trim($_POST['email']);
$password = $_POST['password'];

// Fetch user by email
$sql = "SELECT * FROM cust WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

// Check if user exists
if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();

    // Verify password
    if (password_verify($password, $user['password'])) {

        // Set session values
        $_SESSION['username'] = $user['username'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $user['role']; // 'user' or 'admin'
        $_SESSION['payment_status'] = $user['payment_status']; // 'paid' or 'unpaid'

        // Redirect by role
        if ($user['role'] === 'admin') {
            header("Location: admin.php");
        } else {
            header("Location: home.php");
        }
        exit();
    } else {
        echo "<script>alert('Incorrect password!'); window.location.href='login.html';</script>";
    }
} else {
    echo "<script>alert('User not found!'); window.location.href='login.html';</script>";
}

$stmt->close();
$conn->close();
?>
