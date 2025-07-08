<?php
session_start();
$conn = new mysqli("localhost", "root", "", "myresume");

// Ensure only admin can delete
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.html");
    exit();
}

// Check if ID is provided
if (!isset($_GET['id'])) {
    die("User ID not specified.");
}

$id = intval($_GET['id']); // Sanitize input

// Optional: prevent deleting own admin account
if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $id) {
    die("You cannot delete your own account.");
}

// Delete user
$stmt = $conn->prepare("DELETE FROM cust WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    header("Location: admin .php?status=deleted");
    exit();
} else {
    echo "Delete failed: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
