<?php
session_start();
$conn = new mysqli("localhost", "root", "", "myresume");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Redirect if not logged in
if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'user') {
    header("Location: login.html");
    exit();
}

$user_id = $_SESSION['id'];

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $rating = intval($_POST['rating']);
    $comment = trim($_POST['comment']);

    $stmt = $conn->prepare("INSERT INTO resume_feedback (user_id, rating, comment) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $user_id, $rating, $comment);
    $stmt->execute();
    $stmt->close();

    echo "<script>alert('Thank you for your feedback!'); window.location.href='resume_feedback.php';</script>";
    exit();
}

// Get average rating
$stmt = $conn->prepare("SELECT AVG(rating) AS avg_rating FROM resume_feedback");
$stmt->execute();
$result = $stmt->get_result();
$avgRating = $result->fetch_assoc()['avg_rating'];
$stmt->close();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Resume Feedback</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: url('css/images/image9.jpg') no-repeat center ;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 30px;
            margin: 0;
        }

        .container {
            background: #fff;
            padding: 40px 30px;
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 600px;
            animation: fadeIn 0.6s ease;
        }

        .feedback-header {
            text-align: center;
            margin-bottom: 25px;
        }

        .feedback-header h2 {
            font-size: 24px;
            color: #2c3e50;
        }

        .feedback-header p {
            font-size: 16px;
            color: #444;
        }

        label {
            font-weight: 600;
            display: block;
            margin-top: 20px;
            color: #34495e;
        }

        select, textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 8px;
            background: #f9f9f9;
            font-size: 14px;
            margin-top: 6px;
        }

        button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 14px 20px;
            font-size: 16px;
            font-weight: bold;
            border-radius: 8px;
            margin-top: 25px;
            width: 100%;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .back-btn {
    display: inline-block;
    margin-top: 20px;
    background-color: #f44336;
    color: white;
    text-decoration: none;
    padding: 10px 20px;
    border-radius: 8px;
    font-weight: bold;
    transition: background-color 0.3s ease;
}

.back-btn:hover {
    background-color: #d32f2f;
}

    </style>
</head>
<body>

<div class="container">
    <div class="feedback-header">
        <?php if ($avgRating === null): ?>
            <p>No ratings yet. Be the first to review!</p>
        <?php else: ?>
            <p>⭐ Average Rating: <?= round($avgRating, 1) ?> / 5</p>
        <?php endif; ?>
        <h2>Rate this Resume Sample</h2>
    </div>

    <form action="resume_feedback.php" method="post">
        <label for="rating">Rating:</label>
        <select name="rating" id="rating" required>
            <option value="">Select rating</option>
            <option value="5">⭐⭐⭐⭐⭐ Excellent</option>
            <option value="4">⭐⭐⭐⭐ Good</option>
            <option value="3">⭐⭐⭐ Average</option>
            <option value="2">⭐⭐ Poor</option>
            <option value="1">⭐ Very Poor</option>
        </select>

        <label for="comment">Comment (optional):</label>
        <textarea name="comment" id="comment" rows="4" placeholder="Your thoughts..."></textarea>

        <button type="submit">Submit Feedback</button>
        <br>
        <a href="home.php" class="back-btn">← Back to Home</a>

    </form>
</div>

</body>
</html>
