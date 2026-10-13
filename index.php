<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>M-Money | Airtime to MB Converter</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="https://unpkg.com/feather-icons"></script>
</head>
<body>
    <nav class="navbar">
        <a href="index.php" class="logo">
            <i data-feather="zap"></i>
            M<span>Money</span>
        </a>
        <div class="nav-links">
            <a href="login.php" class="btn btn-secondary" style="margin-right: 1rem;">Login</a>
            <a href="signup.php" class="btn btn-primary">Sign Up</a>
        </div>
    </nav>

    <main class="hero">
        <h1>Transform Your Airtime<br>Into Fast MBs instantly.</h1>
        <p>The smartest and fastest way to convert your unused airtime into internet data bundles. Get more value for your money with M-Money.</p>
        <div class="hero-buttons">
            <a href="signup.php" class="btn btn-primary">Get Started Now</a>
            <a href="#how-it-works" class="btn btn-secondary">Learn More</a>
        </div>
    </main>

    <script>
        feather.replace();
    </script>
</body>
</html>
