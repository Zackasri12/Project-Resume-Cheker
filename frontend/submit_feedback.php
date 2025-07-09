<?php
session_start();
$conn = new mysqli("localhost", "root", "", "myresume");

if (!isset($_SESSION['id'])) {
    die("You must be logged in.");
}

$user_id = $_SESSION['id'];
$sample_name = $_POST['sample_name'];
$rating = (int)$_POST['rating'];
$comment = trim($_POST['comment']);

// Insert into resume_feedback
$stmt = $conn->prepare("INSERT INTO sample_feedback (user_id, sample_name, rating, comment) VALUES (?, ?, ?, ?)");
$stmt->bind_param("isis", $user_id, $sample_name, $rating, $comment);

if ($stmt->execute()) {
    echo "<script>alert('Thank you for your feedback!'); window.location.href='resume_sample.php';</script>";
} else {
    echo "Failed to save feedback: " . $stmt->error;
}
?>
