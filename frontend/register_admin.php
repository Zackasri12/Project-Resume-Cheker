<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn = new mysqli("localhost", "root", "", "myresume");

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $fullname = $_POST['fullname'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $raw_password = $_POST['password'];
    $role = $_POST['role'];

    $password = password_hash($raw_password, PASSWORD_DEFAULT);

    // Check for duplicate email
    $check = $conn->prepare("SELECT * FROM cust WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $check_result = $check->get_result();

    if ($check_result->num_rows > 0) {
        $message = "❗ User already exists with that email.";
    } else {
        $stmt = $conn->prepare("INSERT INTO cust (fullname, username, email, password, role) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $fullname, $username, $email, $password, $role);

        if ($stmt->execute()) {
            $message = "✅ Admin/user created successfully.";
        } else {
            $message = "❌ Error: " . $stmt->error;
        }
        $stmt->close();
    }

    $check->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register Admin</title>
    <link rel="stylesheet" href="css/register_admin.css">
</head>
<body>
    <div class="form-container">
        <h2>Register New Admin/User</h2>

        <?php if (isset($message)): ?>
            <p class="message"><?= htmlspecialchars($message) ?></p>
        <?php endif; ?>

        <form action="register_admin.php" method="POST">
            <input type="text" name="fullname" placeholder="Full Name" required>
            <input type="text" name="username" placeholder="Username" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <select name="role" required>
                <option value="">--Select Role--</option>
                <option value="admin">Admin</option>
                <option value="user">User</option>
            </select>
            <button type="submit">Register</button>
        </form>

        <div class="back-link">
            <a href="admin.php">← Back to Dashboard</a>
        </div>
    </div>
</body>
</html>
