<?php
session_start();
if (!isset($_SESSION['id'])) {
    echo "You must be logged in to upload resumes.";
    exit;
}
$is_premium = ($_SESSION['payment_status'] === 'paid');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Upload Resume</title>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            background: url('css/images/image6.jpeg') no-repeat center center fixed;
            background-size: cover;
            margin: 0;
            padding: 40px 20px;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            align-items: center;
            min-height: 100vh;
        }

        .container {
            background: rgba(250, 249, 249, 0.92);
            padding: 40px 30px;
            border-radius: 18px;
            box-shadow: 0 12px 28px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 600px;
            animation: slideUp 0.6s ease-in-out;
        }

        h1 {
            text-align: center;
            color: #1e2a38;
            margin-bottom: 25px;
            font-size: 28px;
            letter-spacing: 0.5px;
        }

        label {
            font-weight: 600;
            display: block;
            margin-top: 20px;
            margin-bottom: 6px;
            color: #2c3e50;
        }

        textarea,
        input[type="file"] {
            width: 100%;
            padding: 12px 14px;
            border: 1px solid #dcdfe6;
            border-radius: 10px;
            background-color: #f9fbfd;
            font-size: 15px;
            transition: border-color 0.3s;
            box-sizing: border-box;
        }

        textarea:focus,
        input[type="file"]:focus {
            outline: none;
            border-color: #3498db;
            background-color: #ffffff;
        }

        button {
            background-color: #3498db;
            color: #fff;
            border: none;
            padding: 14px;
            font-size: 16px;
            font-weight: bold;
            border-radius: 10px;
            width: 100%;
            margin-top: 25px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #2980b9;
        }

        .premium-banner {
            background-color: #ffecb3;
            color: #6d4c41;
            padding: 14px;
            border-radius: 10px;
            text-align: center;
            margin-bottom: 20px;
            font-weight: 500;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
            animation: fadeIn 0.4s ease-in-out;
        }

        .home-button {
            display: inline-block;
            margin-top: 30px;
            padding: 12px 24px;
            background-color: #3498db;
            color: white;
            font-size: 16px;
            font-weight: bold;
            text-decoration: none;
            border-radius: 8px;
            transition: background-color 0.3s ease;
        }

        .home-button:hover {
            background-color: #2c80b4;
        }

        @keyframes slideUp {
            from { transform: translateY(30px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @media (max-width: 600px) {
            .container {
                padding: 30px 20px;
            }

            h1 {
                font-size: 24px;
            }

            button {
                font-size: 15px;
            }

            .home-button {
                font-size: 14px;
                padding: 10px 20px;
            }
        }
    </style>
</head>
<body>

    <div class="container">
        <h1>📤 Upload Your Resume</h1>

        <?php if (!$is_premium): ?>
            <div class="premium-banner">
                🔒 You are using a free account. Upgrade to premium to unlock advanced features like full report download and priority analysis.
            </div>
        <?php endif; ?>

        <form action="http://localhost:5000/" method="POST" enctype="multipart/form-data">
            <label for="job_description">Job Description:</label>
            <textarea name="job_description" id="job_description" rows="6" required placeholder="Paste the job description here..."></textarea>

            <label for="resume">Upload Resume (PDF only):</label>
            <input type="file" name="resume" id="resume" accept=".pdf" required>

            <input type="hidden" name="user_id" value="<?= $_SESSION['id'] ?>">

            <button type="submit">🔍 Analyze Resume</button>
        </form>
    </div>

    <!-- Back to Home Button -->
    <a href="home.php" class="home-button">← Back to Home</a>

</body>
</html>
