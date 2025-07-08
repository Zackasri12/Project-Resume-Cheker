<?php
session_start();

// ✅ 1. Check if user is logged in and is an admin
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.html");
    exit();
}

// ✅ 2. Connect to DB
$conn = new mysqli("localhost", "root", "", "myresume");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ✅ 3. Get users
$result = $conn->query("SELECT * FROM cust");
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="css/admin.css"> <!-- External CSS -->
</head>
<body>
<div class="container">
    <div class="top-bar">
        <h2>Admin Dashboard - User Management</h2>
        <a href="register_admin.php" class="register-btn">➕ Register Admin</a>
        <a href="admin_view_resume.php" class="register-btn">📄 View Resume Uploads</a>

    </div>

    <table>
        <tr>
            <th>ID</th>
            <th>Full Name</th>
            <th>Username</th>
            <th>Email</th>
            <th>Role</th>
            <th>Actions</th>
        </tr>
        <?php while($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['id']) ?></td>
            <td><?= htmlspecialchars($row['fullname']) ?></td>
            <td><?= htmlspecialchars($row['username']) ?></td>
            <td><?= htmlspecialchars($row['email']) ?></td>
            <td><?= htmlspecialchars($row['role']) ?></td>
            <td class="actions">
                <a href="edit_user.php?id=<?= $row['id'] ?>">Edit</a>
                <a href="delete_user.php?id=<?= $row['id'] ?>" onclick="return confirm('Are you sure?')">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>

    <div class="logout-wrapper">
        <a href="logout.php" class="logout">Logout</a>
    </div>
</div>
</body>
</html>
