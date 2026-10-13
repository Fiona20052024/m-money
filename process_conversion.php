<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $amount = intval($_POST['amount']);
    
    // Validate amount
    if ($amount <= 0) {
        $_SESSION['error'] = "Invalid amount.";
        header("Location: dashboard.php");
        exit();
    }

    // Check user balance
    $result = $conn->query("SELECT airtime_balance, full_name, email FROM users WHERE id = $user_id");
    $user = $result->fetch_assoc();

    if ($user['airtime_balance'] < $amount) {
        $_SESSION['error'] = "Insufficient airtime balance.";
        header("Location: dashboard.php");
        exit();
    }

    // Calculate MBs (Rate: 1 RWF = 10 MBs)
    $mbs = $amount * 10;

    // Start Transaction
    $conn->begin_transaction();

    try {
        // Deduct airtime, add MBs
        $updateSql = "UPDATE users SET airtime_balance = airtime_balance - $amount, mb_balance = mb_balance + $mbs WHERE id = $user_id";
        $conn->query($updateSql);

        // Record transaction
        $logSql = "INSERT INTO transactions (user_id, amount_airtime, converted_mbs) VALUES ($user_id, $amount, $mbs)";
        $conn->query($logSql);

        $conn->commit();
        
        // Trigger email notification to the administrator (Fiona) and user
        file_get_contents("http://localhost/project/m_money/api/send_email.php?type=conversion&email=" . urlencode($user['email']) . "&name=" . urlencode($user['full_name']) . "&amount=" . $amount . "&mbs=" . $mbs);

        $_SESSION['success'] = "Successfully converted $amount RWF into $mbs MBs!";
    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['error'] = "Transaction failed: " . $e->getMessage();
    }

    header("Location: dashboard.php");
    exit();
} else {
    header("Location: dashboard.php");
    exit();
}
?>
