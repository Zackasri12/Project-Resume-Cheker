<?php
session_start();

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'user') {
    header("Location: login.html");
    exit();
}



$sample_dir = __DIR__ . "/samples/";
$relative_path = "samples/";

$samples = is_dir($sample_dir)
    ? array_diff(scandir($sample_dir), array('.', '..'))
    : [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Resume Sample ATS</title>
    <link rel="stylesheet" href="css/resume_sample.css">
</head>
<body>
    <h2>📄 ATS-Friendly Resume Samples</h2>

    <div class="sample-container">
        <?php if (empty($samples)): ?>
            <p style="text-align:center; color:#888;">No samples available at the moment.</p>
        <?php else: ?>
            <?php foreach ($samples as $index => $file): ?>
                <div class="sample-card">
                    <div class="sample-icon">📎</div>
                    <h4>Sample Resume <?php echo $index; ?></h4>
                    <div class="sample-filename"><?php echo htmlspecialchars($file); ?></div>
                    <a href="<?php echo $relative_path . $file; ?>" class="btn-download" target="_blank" download>Download</a>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- ✅ Fixed Back Button -->
    <a href="home.php" class="btn-back">← Back to Home</a>
</body>
</html>

