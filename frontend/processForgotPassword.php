<?php
session_start();
$conn = new mysqli("localhost", "root", "", "myresume");

if ($conn->connect_error) {
    die("<div class='error'>❌ Connection failed: " . $conn->connect_error . "</div>");
}

$email = trim($_POST['email']);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Password Reset</title>
    <link rel="stylesheet" href="css/forgetPass.css">
    <style>
        .message-box {
            max-width: 500px;
            margin: 80px auto;
            background: #fff;
            padding: 30px 40px;
            border-radius: 12px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .message-box h2 {
            color: #2c3e50;
        }

        .message-box p {
            font-size: 16px;
            color: #333;
            margin-bottom: 20px;
        }

        .link-box {
            background: #f1f1f1;
            padding: 15px;
            border-radius: 8px;
            word-break: break-all;
            margin: 15px 0;
        }

        .btn {
            display: inline-block;
            margin: 10px 5px;
            padding: 10px 16px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
        }

        .btn:hover {
            background-color: #0056b3;
        }

        .btn.red {
            background-color: #f44336;
        }

        .btn.red:hover {
            background-color: #d32f2f;
        }
    </style>
</head>
<body>

<div class="message-box">
<?php
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "<h2>⚠️ Invalid Email Format</h2>";
    echo "<a class='btn red' href='forgetPassword.php'>Try Again</a>";
    exit();
}

$stmt = $conn->prepare("SELECT id FROM cust WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $token = bin2hex(random_bytes(32));
    $_SESSION['reset_token'] = $token;
    $_SESSION['reset_email'] = $email;

    $reset_link = "http://localhost/myresume/frontend/reset_password.php?token=$token";

    echo "<h2>✅ Reset Link Generated</h2>";
    echo "<p>Click the link below to reset your password:</p>";
    echo "<div class='link-box'><a href='$reset_link'>$reset_link</a></div>";
    echo "<a class='btn' href='login.php'>Back to Login</a>";
    echo "<a class='btn red' href='home.php'>Home</a>";
} else {
    echo "<h2>❌ Email Not Found</h2>";
    echo "<p>Please make sure you used the registered email.</p>";
    echo "<a class='btn red' href='forgetPassword.php'>Try Again</a>";
}

$stmt->close();
$conn->close();
?>
</div>

</body>
</html>
