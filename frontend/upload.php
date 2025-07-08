<?php
// upload.php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $jobDescription = $_POST['job_description'] ?? '';
    $file = $_FILES['resume'] ?? null;

    if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
        die('Error uploading file.');
    }

    $filePath = $file['tmp_name'];
    $fileName = $file['name'];

    $curl = curl_init();

    $postFields = [
        'job_description' => $jobDescription,
        'resume' => new CURLFile($filePath, 'application/pdf', $fileName)
    ];

    curl_setopt_array($curl, [
        CURLOPT_URL => 'http://127.0.0.1:5000/',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $postFields
    ]);

    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);

    if ($err) {
        echo "Curl Error: $err";
    } else {
        echo $response; // Flask returns full HTML (analytics.html)
    }

    exit();
}
?>

<!-- Upload Form UI -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Upload Resume for Analysis</title>
    <link rel="stylesheet" href="css/upload.css">
</head>
<body>
    <div class="upload-container">
        <h2>📤 Upload Resume for Analysis</h2>
        <form action="http://127.0.0.1:5000/" method="POST" enctype="multipart/form-data">
            <label for="job_description">💼 Job Description</label>
            <textarea name="job_description" rows="6" placeholder="Paste the job description here..." required></textarea>

            <label for="resume">📄 Resume (PDF only)</label>
            <input type="file" name="resume" accept=".pdf" required>

            <button type="submit">Analyze Resume</button>
            <a href="home.php" class="back-btn">← Back to Home</a>
        </form>
    </div>
</body>
</html>
