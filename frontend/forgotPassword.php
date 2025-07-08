<!DOCTYPE html>
<html>
<head>
    <title>Forgot Password</title>
    <link rel="stylesheet" href="css/forgetPass.css">
</head>
<body>

    <!-- Top Title -->
    <h1 class="page-title">Forgot Password</h1>

    <div class="container">
        <form action="process_forgot_password.php" method="post">
            <label>Enter your registered email:</label>
            <input type="email" name="email" required>

            <input type="submit" value="Send Reset Link">
        </form>

        <!-- Navigation Buttons -->
        <a href="login.php" class="button">Back to Login</a>
        <a href="home.php" class="button">Back to Home</a>
    </div>

</body>
</html>
