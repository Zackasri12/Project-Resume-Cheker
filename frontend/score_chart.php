<?php
session_start();
$conn = new mysqli("localhost", "root", "", "myresume");

if (!isset($_SESSION['username'])) {
    header("Location: login.html");
    exit();
}

$user_id = $_SESSION['id'];
$sql = "SELECT filename, match_score, upload_date FROM resume_uploads WHERE user_id = ? ORDER BY upload_date DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$labels = [];
$scores = [];
while ($row = $result->fetch_assoc()) {
    $labels[] = $row['filename'] . " (" . date("M d", strtotime($row['upload_date'])) . ")";
    $scores[] = $row['match_score'];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Your Resume Match Scores</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background:url('css/images/image13.jpg')no-repeat center center fixed ;
            background-size: cover;
            padding: 30px;
        }

        .chart-container {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
            max-width: 800px;
            margin: auto;
            margin-top: 40px;
        }

        h2 {
            text-align: center;
            color: #2c3e50;
        }

        .back-button {
            display: inline-block;
            margin: 0 auto;
            background-color: #3498db;
            color: white;
            padding: 10px 20px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: bold;
            transition: background-color 0.3s ease;
            margin-left: 30px;
        }

        .back-button:hover {
            background-color: #2980b9;
        }
    </style>
</head>
<body>

<!-- Back to Home Button -->
<a href="home.php" class="back-button">← Back to Home</a>

<div class="chart-container">
    <h2>Your Resume Match Scores</h2>
    <canvas id="scoreChart"></canvas>
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
                backgroundColor: 'rgba(54, 162, 235, 0.7)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1,
                barThickness: 30
            }]
        },
        options: {
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
