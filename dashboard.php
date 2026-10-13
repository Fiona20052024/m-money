<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$result = $conn->query("SELECT * FROM users WHERE id = $user_id");
$user = $result->fetch_assoc();

// Fetch recent transactions
$tx_result = $conn->query("SELECT * FROM transactions WHERE user_id = $user_id ORDER BY created_at DESC LIMIT 5");

$error = isset($_SESSION['error']) ? $_SESSION['error'] : '';
$success = isset($_SESSION['success']) ? $_SESSION['success'] : '';
unset($_SESSION['error']);
unset($_SESSION['success']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | M-Money</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="https://unpkg.com/feather-icons"></script>
</head>
<body>
    <nav class="navbar">
        <a href="index.php" class="logo">
            <i data-feather="zap"></i>
            M<span>Money</span>
        </a>
        <div style="display: flex; align-items: center; gap: 1.5rem;">
            <span>Welcome, <strong><?php echo htmlspecialchars($user['full_name']); ?></strong></span>
            <a href="logout.php" class="btn btn-secondary">Logout</a>
        </div>
    </nav>

    <main class="dashboard-container">
        <!-- Balances Section -->
        <div class="balance-cards">
            <div class="balance-card">
                <div class="balance-label">Available Airtime Balance</div>
                <div class="balance-amount"><?php echo number_format($user['airtime_balance']); ?> <span class="balance-unit">RWF</span></div>
                <i data-feather="phone" style="position: absolute; right: -20px; bottom: -20px; width: 120px; height: 120px; color: rgba(79, 70, 229, 0.1);"></i>
            </div>
            
            <div class="balance-card mb">
                <div class="balance-label">Internet Data Balance</div>
                <div class="balance-amount"><?php echo number_format($user['mb_balance']); ?> <span class="balance-unit">MBs</span></div>
                <i data-feather="wifi" style="position: absolute; right: -20px; bottom: -20px; width: 120px; height: 120px; color: rgba(16, 185, 129, 0.1);"></i>
            </div>
        </div>

        <!-- Action Section -->
        <div class="glass-container action-card">
            <h2 class="mb-4">Convert Airtime to MBs</h2>
            <p class="text-muted mb-4">Current Rate: <strong>1 RWF = 10 MBs</strong></p>
            
            <?php if($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>

            <form method="POST" action="process_conversion.php">
                <div>
                    <label>Amount of Airtime to Convert (RWF)</label>
                    <input type="number" name="amount" min="1" max="<?php echo $user['airtime_balance']; ?>" required placeholder="Enter amount (e.g. 500)">
                </div>
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; padding: 1rem; background: rgba(0,0,0,0.2); border-radius: 8px;">
                    <span class="text-muted">You will receive:</span>
                    <strong style="font-size: 1.25rem; color: var(--secondary);"><span id="calc-mbs">0</span> MBs</strong>
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%;">Convert Now</button>
            </form>

            <h3 class="mt-4" style="margin-top: 3rem;">Recent Transactions</h3>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Airtime Deducted</th>
                            <th>MBs Received</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($tx_result->num_rows > 0): ?>
                            <?php while($tx = $tx_result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo date('M d, Y H:i', strtotime($tx['created_at'])); ?></td>
                                    <td>-<?php echo number_format($tx['amount_airtime']); ?> RWF</td>
                                    <td class="text-secondary">+<?php echo number_format($tx['converted_mbs']); ?> MBs</td>
                                    <td><span class="badge badge-success"><?php echo $tx['status']; ?></span></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center text-muted" style="padding: 2rem;">No recent transactions.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <script>
        feather.replace();
        
        // Simple calculator for MBs
        const amountInput = document.querySelector('input[name="amount"]');
        const calcMbs = document.getElementById('calc-mbs');
        
        amountInput.addEventListener('input', (e) => {
            const amount = parseFloat(e.target.value) || 0;
            const mbs = amount * 10; // Rate: 1 RWF = 10 MBs
            calcMbs.textContent = mbs.toLocaleString();
        });
    </script>
</body>
</html>
