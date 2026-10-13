<?php
session_start();
require_once 'config.php';

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullName = $conn->real_escape_string($_POST['full_name']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $phone = $conn->real_escape_string($_POST['phone']);

    $checkEmail = $conn->query("SELECT id FROM users WHERE email = '$email'");
    if ($checkEmail->num_rows > 0) {
        $error = "Email is already registered.";
    } else {
        $sql = "INSERT INTO users (full_name, email, password, phone, airtime_balance, mb_balance) 
                VALUES ('$fullName', '$email', '$password', '$phone', 5000, 0)";
        
        if ($conn->query($sql) === TRUE) {
            // Trigger email notification
            $userId = $conn->insert_id;
            file_get_contents("http://localhost/project/m_money/api/send_email.php?type=signup&email=" . urlencode($email) . "&name=" . urlencode($fullName));
            
            $success = "Registration successful! You can now log in.";
        } else {
            $error = "Error: " . $conn->error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up | M-Money</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="https://unpkg.com/feather-icons"></script>
</head>
<body>
    <nav class="navbar">
        <a href="index.php" class="logo">
            <i data-feather="zap"></i>
            M<span>Money</span>
        </a>
    </nav>

    <main class="auth-wrapper">
        <div class="glass-container auth-card">
            <div class="auth-header">
                <h1>Create Account</h1>
                <p class="text-muted">Start converting your airtime today</p>
            </div>
            
            <?php if($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <div>
                    <label>Full Name</label>
                    <input type="text" name="full_name" required placeholder="John Doe">
                </div>
                <div>
                    <label>Email Address</label>
                    <input type="email" name="email" required placeholder="john@example.com">
                </div>
                <div>
                    <label>Phone Number</label>
                    <input type="text" name="phone" required placeholder="078...">
                </div>
                <div>
                    <label>Password</label>
                    <input type="password" name="password" required placeholder="••••••••">
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 1rem;">Sign Up</button>
            </form>
            
            <p class="text-center mt-4">
                <span class="text-muted">Already have an account?</span> 
                <a href="login.php" class="auth-link">Log In</a>
            </p>
        </div>
    </main>

    <script>
        feather.replace();
    </script>
</body>
</html>
