<?php
session_start();
$conn = new mysqli("localhost", "root", "", "myresume");

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.html");
    exit();
}

$sql = "
    SELECT r.upload_id, c.username, r.filename, r.upload_date, r.match_score
    FROM resume_uploads r
    JOIN cust c ON r.user_id = c.id
    ORDER BY r.upload_date DESC
";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Resume Upload History</title>
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>
<div class="container">
    <h2>Resume Upload History</h2>
    <table>
        <tr>
            <th>Upload ID</th>
            <th>User</th>
            <th>Filename</th>
            <th>Upload Date</th>
            <th>Match Score</th>
        </tr>
        <?php while($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $row['upload_id'] ?></td>
            <td><?= htmlspecialchars($row['username']) ?></td>
            <td><?= htmlspecialchars($row['filename']) ?></td>
            <td><?= $row['upload_date'] ?></td>
            <td><?= $row['match_score'] !== null ? $row['match_score'] . '%' : 'Pending' ?></td>
        </tr>
        <?php endwhile; ?>
    </table>
    <br>
    <a href="admin.php" class="logout">Back to Dashboard</a>
</div>
</body>
</html>
