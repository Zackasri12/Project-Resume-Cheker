<?php
session_start();
$conn = new mysqli("localhost", "root", "", "myresume");

// Only allow admin
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.html");
    exit();
}

// Get all resume uploads
$sql = "
    SELECT r.id AS upload_id, c.fullname, c.username, r.filename, r.created_at AS upload_date, r.match_score
    FROM resume_uploads r
    JOIN cust c ON r.user_id = c.id
    ORDER BY r.created_at DESC
";

$result = $conn->query($sql);

$labels = [];
$scores = [];
$all_rows = [];

while ($row = $result->fetch_assoc()) {
    $labels[] = $row['fullname'] . " (" . $row['upload_id'] . ")";
    $scores[] = (int) $row['match_score'];
    $all_rows[] = $row;
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Resume Uploads - Admin</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background:url('css/images/image14.jpg') no-repeat;
            background-size: cover;
            padding: 30px;
        }
        .container {
            max-width: 1100px;
            margin: auto;
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            color: #2c3e50;
        }
        canvas {
            margin: 30px auto;
            display: block;
            max-width: 100%;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            margin-top: 20px;
        }
        th, td {
            padding: 12px;
            border-bottom: 1px solid #ccc;
            text-align: center;
        }
        th {
            background: #2c3e50;
            color: white;
        }
        .back-btn {
            text-decoration: none;
            background: #f44336;
            padding: 10px 15px;
            color: white;
            border-radius: 6px;
            display: inline-block;
            margin-top: 20px;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Uploaded Resumes</h2>

    <canvas id="scoreChart" height="120"></canvas>

    <table>
        <tr>
            <th>ID</th>
            <th>Full Name</th>
            <th>Username</th>
            <th>Filename</th>
            <th>Upload Date</th>
            <th>Match Score</th>
            <th>Download</th>
        </tr>
        <?php foreach ($all_rows as $row): ?>
        <tr>
            <td><?= $row['upload_id'] ?></td>
            <td><?= htmlspecialchars($row['fullname']) ?></td>
            <td><?= htmlspecialchars($row['username']) ?></td>
            <td><?= htmlspecialchars($row['filename']) ?></td>
            <td><?= $row['upload_date'] ?></td>
            <td><?= $row['match_score'] !== null ? $row['match_score'] . '%' : 'Pending' ?></td>
            <td>
                <a href="../uploads/<?= urlencode($row['filename']) ?>" download>Download</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>

    <a class="back-btn" href="admin.php">← Back to Dashboard</a>
</div>

<script>
const ctx = document.getElementById('scoreChart').getContext('2d');
const scoreChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: <?= json_encode($labels) ?>,
        datasets: [{
            label: 'Match Score (%)',
            data: <?= json_encode($scores) ?>,
            backgroundColor: 'rgba(54, 162, 235, 0.6)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false },
            title: {
                display: true,
                text: 'Resume Match Scores'
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                max: 100
            }
        }
    }
});
</script>
</body>
</html>
