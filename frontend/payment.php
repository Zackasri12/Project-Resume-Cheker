<?php
session_start();

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'user') {
    header("Location: login.html");
    exit();
}

$conn = new mysqli("localhost", "root", "", "myresume");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$username = $_SESSION['username'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Simulate payment processing
    $update_sql = "UPDATE cust SET payment_status = 'paid' WHERE username = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("s", $username);

    if ($stmt->execute()) {
        $_SESSION['payment_status'] = 'paid';
        echo "<script>alert('✅ Payment successful! Access unlocked.'); window.location.href='home.php';</script>";
        exit();
    } else {
        echo "<script>alert('❌ Payment update failed. Please try again.');</script>";
    }
    $stmt->close();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Complete Your Payment</title>
    <style>
        body {
            margin: 0;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            background: url('css/images/image2.jpg') no-repeat center center fixed;
            background-size: cover;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .payment-box {
            background-color: rgba(255, 255, 255, 0.95);
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
            max-width: 480px;
            width: 100%;
            text-align: center;
        }

        h2 {
            color: #2c3e50;
            margin-bottom: 10px;
        }

        p {
            color: #555;
            margin-bottom: 20px;
        }

        .qr-image {
            width: 220px;
            height: 220px;
            margin: 20px 0;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        }

        select, .btn-pay {
            width: 100%;
            padding: 12px;
            margin: 15px 0;
            font-size: 16px;
            border-radius: 8px;
            border: 1px solid #ccc;
        }

        .btn-pay {
            background-color: #007bff;
            color: white;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s ease;
            border: none;
        }

        .btn-pay:hover {
            background-color: #0056b3;
        }

        .back-link {
            display: block;
            margin-top: 20px;
            text-decoration: none;
            color: #333;
            font-size: 14px;
        }

        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="payment-box">
        <h2>🚫 Access Restricted</h2>
        <p>Your account is currently <strong>unpaid</strong>.<br>Please complete payment to unlock premium features.</p>

        <img src="css/images/image5.jpg" alt="QR Code" class="qr-image">

        <p><strong>OR</strong> choose a bank to simulate FPX payment:</p>
        <form method="POST">
            <select name="bank" required>
                <option value="">-- Select Bank --</option>
                <option value="maybank">Maybank</option>
                <option value="cimb">CIMB</option>
                <option value="rhb">RHB</option>
                <option value="public">Public Bank</option>
                <option value="bankislam">Bank Islam</option>
            </select>

            <button type="submit" class="btn-pay">💳 I Have Paid</button>
        </form>

        <a href="home.php" class="back-link">← Back to Home</a>
    </div>
</body>
</html>
