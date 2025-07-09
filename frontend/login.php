<?php
session_start();

// ✅ 1. Connect to the database
$conn = new mysqli("localhost", "root", "", "myresume");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ✅ 2. Get and sanitize input
$email = trim($_POST['email']);
$password = $_POST['password'];

// ✅ 3. Prepare SQL to find user by email
$sql = "SELECT * FROM cust WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

// ✅ 4. Verify user
if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();

    if (password_verify($password, $user['password'])) {
        // ✅ 5. Save session variables
        $_SESSION['id'] = $user['id'];  // ← This is important for linking to uploads
        $_SESSION['username'] = $user['username'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['payment_status'] = $user['payment_status'];

        // ✅ 6. Redirect by role
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

// ✅ 7. Close DB connections
$stmt->close();
$conn->close();
?>
