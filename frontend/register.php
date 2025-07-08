<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn = new mysqli("localhost", "root", "", "myresume");

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $fullname = $_POST['fullname'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        echo "<script>
                alert('Password does not match');
                window.location.href='../register.html';
              </script>";
        exit;
    }

    // ✅ Check if email already exists
    $check_sql = "SELECT id FROM cust WHERE email = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("s", $email);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows > 0) {
        echo "<script>
                alert('Email already exists. Please use a different email.');
                window.location.href='../register.html';
              </script>";
        $check_stmt->close();
        $conn->close();
        exit;
    }
    $check_stmt->close();

    // ✅ Secure password hashing
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // ✅ Insert user
    $sql = "INSERT INTO cust (fullname, username, email, password) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $fullname, $username, $email, $hashed_password);

    if ($stmt->execute()) {
        echo "<script>
                alert('Registration successful');
                window.location.href='login.html';
              </script>";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Invalid request method.";
}
?>
