<?php
session_start();
$conn = new mysqli("localhost", "root", "", "myresume");

// Only allow access if logged in as admin
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.html");
    exit();
}

// Get user ID from URL
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if (!$id) {
    die("No user ID provided.");
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = $_POST['fullname'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $role = $_POST['role'];

    $sql = "UPDATE cust SET fullname=?, username=?, email=?, role=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $fullname, $username, $email, $role, $id);

    if ($stmt->execute()) {
        header("Location: admin_dashboard.php?status=updated");
        exit();
    } else {
        echo "Update failed: " . $stmt->error;
    }
}

// Fetch current user info
$stmt = $conn->prepare("SELECT * FROM cust WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    die("User not found.");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit User</title>
    <link rel="stylesheet" href="css/editUser.css">
</head>
<body>
    <form method="POST">
        <h2>Edit User</h2>

        <label>Full Name:</label>
        <input type="text" name="fullname" value="<?= htmlspecialchars($user['fullname']) ?>" required>

        <label>Username:</label>
        <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>

        <label>Email:</label>
        <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>

        <label>Role:</label>
        <select name="role" required>
            <option value="user" <?= $user['role'] === 'user' ? 'selected' : '' ?>>User</option>
            <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
        </select>

        <button type="submit">Update</button>
        <a href="admin.php">Cancel</a>
    </form>
</body>
</html>
