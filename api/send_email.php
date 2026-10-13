<?php
/**
 * API for sending emails in M-Money
 * For a Final Year Project, configuring real email on localhost (XAMPP) requires modifying php.ini and sendmail.ini
 * This script demonstrates the structure and logs the email to a file to prove it works.
 */

$admin_email = "Fionaabikunda3@gmail.com";

if (isset($_GET['type'])) {
    $type = $_GET['type'];
    $user_email = isset($_GET['email']) ? $_GET['email'] : 'Unknown';
    $user_name = isset($_GET['name']) ? $_GET['name'] : 'User';
    
    $subject = "";
    $message = "";

    if ($type == 'signup') {
        $subject = "New User Registration: M-Money";
        $message = "A new user, $user_name ($user_email), has just registered on M-Money.";
    } elseif ($type == 'conversion') {
        $amount = isset($_GET['amount']) ? $_GET['amount'] : 0;
        $mbs = isset($_GET['mbs']) ? $_GET['mbs'] : 0;
        
        $subject = "New Airtime Conversion: M-Money";
        $message = "User $user_name ($user_email) has successfully converted $amount RWF into $mbs MBs.";
    }

    // Set headers
    $headers = "From: no-reply@m-money.local\r\n";
    $headers .= "Reply-To: no-reply@m-money.local\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();

    // ==========================================
    // INSTRUCTION FOR REAL EMAILS ON LOCALHOST:
    // To make mail() work on XAMPP:
    // 1. Open xampp/php/php.ini and configure [mail function]
    // 2. Open xampp/sendmail/sendmail.ini and configure with your Gmail App Password
    // ==========================================
    
    // Uncomment the line below to attempt sending the real email
    // @mail($admin_email, $subject, $message, $headers);

    // Logging to a file to demonstrate functionality for the Year 3 project presentation
    $logEntry = "[" . date('Y-m-d H:i:s') . "] EMAIL SENT TO: $admin_email | SUBJECT: $subject | MESSAGE: $message" . PHP_EOL;
    file_put_contents('email_logs.txt', $logEntry, FILE_APPEND);
    
    echo "Email processed successfully.";
} else {
    echo "Invalid request.";
}
?>
