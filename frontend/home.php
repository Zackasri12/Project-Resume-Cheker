<?php
session_start();

// Redirect if user is not logged in or not a 'user'
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'user') {
    header("Location: login.html");
    exit();
}

// Set variables from session
$username = htmlspecialchars($_SESSION['username']);
$payment_status = $_SESSION['payment_status'] ?? 'unpaid'; // fallback to 'unpaid' if not set

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Resume</title>
    <link rel="stylesheet" href="css/home.css">
</head>
<body>
    <header class="header">
        <h2 class="logo">My Resume</h2>
        <nav class="navigation">
            <a href="home.php" class="active">Home</a>
            <a href="about.html">About Us</a>
            <a href="donate.html">Donate</a>
            <span class="welcome-message">
                Welcome, <?php echo $username; ?> (<?php echo ucfirst($payment_status); ?> Member)
            </span>
            <a href="logout.php" class="btn btn-logout">Logout</a>
        </nav>
    </header>

    <div class="content">
        <div class="top-text">
            <p>Transform your future with My Resume—tailored to help graduates rise to the top 10% of job candidates and impress any employer!</p>
        </div>

        <div class="wrapper">
            <!-- Resume Matcher -->
            <div class="services">
                <div class="top-service">
                    <h2 style="color: white;">📝 Resume Matcher</h2>
                </div>
                <span class="icon"><ion-icon name="information-circle"></ion-icon><p>Job Description Matching</p></span>
                <span class="icon"><ion-icon name="information-circle"></ion-icon><p>Suggest Important Skills</p></span>
                <span class="icon"><ion-icon name="information-circle"></ion-icon><p>Analyze ATS Compatibility</p></span>
                <span class="icon"><ion-icon name="information-circle"></ion-icon><p>Instant PDF Upload</p></span>
                <a href="<?php echo ($payment_status === 'paid') ? 'upload.php' : 'payment.php'; ?>" class="btn-buy">Start Now</a>
            </div>

            <!-- ATS Resume -->
            <div class="services">
                <div class="top-service">
                    <h2>🧾 ATS Resume</h2>
                </div>
                <span class="icon"><ion-icon name="information-circle"></ion-icon><p>Generate ATS-Friendly Resume</p></span>
                <span class="icon"><ion-icon name="information-circle"></ion-icon><p>Ensure Readability</p></span>
                <span class="icon"><ion-icon name="information-circle"></ion-icon><p>Keyword Optimization</p></span>
                <span class="icon"><ion-icon name="information-circle"></ion-icon><p>Custom Suggestions</p></span>
                <a href="<?php echo ($payment_status === 'paid') ? 'http://localhost:3000/generator/templates' : 'payment.php'; ?>" class="btn-buy">Generate</a>
            </div>

            <!-- Resume Sample ATS -->
            <div class="services">
                <div class="top-service">
                    <h2>🧾 Resume Sample ATS</h2>
                </div>
                <span class="icon"><ion-icon name="information-circle"></ion-icon><p>ATS-Friendly Formatting</p></span>
                <span class="icon"><ion-icon name="information-circle"></ion-icon><p>Designed for Easy Parsing</p></span>
                <span class="icon"><ion-icon name="information-circle"></ion-icon><p>Includes Targeted Keywords</p></span>
                <span class="icon"><ion-icon name="information-circle"></ion-icon><p>Editable & Tailored Templates</p></span>
                <a href="<?php echo ($payment_status === 'paid') ? 'resume_sample.php' : 'payment.php'; ?>" class="btn-buy">Generate</a>
            </div>
        </div>

        <footer style="color:white; text-align:center; padding:10px;">&copy; 2025 My Resume Service</footer>
    </div>

    <script src="https://unpkg.com/ionicons@4.5.10-0/dist/ionicons.js"></script>
</body>
</html>
