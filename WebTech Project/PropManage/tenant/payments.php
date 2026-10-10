<?php
require_once __DIR__ . '/../config/db.php';

$userName = $_SESSION['user_name'] ?? 'Mashfi Rahman';
$tenantId = $_SESSION['user_id'] ?? 3;
$currentBalance = 22000.00;
$paymentsHistory = [];

if ($db_connected && $pdo) {
    try {
        $balStmt = $pdo->prepare("SELECT COALESCE(SUM(amount), 0) FROM payments WHERE tenant_id = :tid AND payment_status = 'pending'");
        $balStmt->execute([':tid' => $tenantId]);
        $val = $balStmt->fetchColumn();
        if ($val > 0) $currentBalance = $val;

        $stmt = $pdo->prepare("SELECT * FROM payments WHERE tenant_id = :tid OR :is_demo = 1 ORDER BY payment_date DESC");
        $stmt->execute([':tid' => $tenantId, ':is_demo' => 1]);
        $paymentsHistory = $stmt->fetchAll();
    } catch (Exception $e) {}
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payments | PropManage</title>
    <link rel="stylesheet" href="../css/tenant.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>


<aside class="sidebar">
    <a href="../index.php" class="brand">
        <span class="brand-mark">◈</span>PropManage
    </a>
    <nav class="side-nav">
        <a class="side-link" href="explore.php"><span class="side-icon">▦</span>Explore</a>
        <a class="side-link" href="my-requests.php"><span class="side-icon">♧</span>My Requests</a>
        <a class="side-link" href="messages.php"><span class="side-icon">▣</span>Messages</a>
        <a class="side-link active" href="payments.php"><span class="side-icon">▣</span>Payments</a>
        <a class="side-link" href="maintenance.php"><span class="side-icon">🔧</span>Maintenance</a>
    </nav>
    <div class="settings">
        <a class="side-link" href="profile.php"><span class="side-icon">⚙</span>Settings</a>
    </div>
</aside>


<div class="main">

    <header class="topbar">
        <div class="global-search">
            <span>⌕</span>
            <input type="text" placeholder="Search transactions...">
        </div>
        <div class="profile user-dropdown-container" id="profileDropdownBtn">
            <div class="profile-name">
                <?php echo htmlspecialchars($userName); ?>
                <div class="profile-role">Tenant</div>
            </div>
            <img class="avatar" src="<?php echo htmlspecialchars(getActiveUserAvatar()); ?>" alt="Tenant">

            <div class="user-dropdown-menu" id="userMenu">
                <div class="dropdown-header">Switch Role Preview</div>
                <a class="dropdown-item" href="../landlord/dashboard.php"><span>🏠</span> Landlord Portal</a>
                <a class="dropdown-item" href="../admin/dashboard.php"><span>🛡️</span> Admin Portal</a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="profile.php"><span>👤</span> Edit Profile</a>
                <a class="dropdown-item" href="../auth/logout.php" style="color: #d02020;"><span>🚪</span> Log Out</a>
            </div>
        </div>
    </header>


    <main class="content">
        <div class="page-head">
            <h1 class="page-title">Payments</h1>
        </div>


        <div class="balance-card">
            <div class="balance-info">
                <h3>Current Balance</h3>
                <div class="balance-amount"><?php echo number_format($currentBalance, 2); ?> <small style="font-size: 18px; font-weight: 500;">BDT</small></div>
                <div class="balance-due">Due on <?php echo date('M 01, Y', strtotime('+15 days')); ?></div>
                <button type="button" class="pay-now-btn">
                    Pay Now <span>→</span>
                </button>
            </div>
            <div class="balance-icon-watermark">
                💳
            </div>
        </div>


        <section class="panel table-panel">
            <div class="table-head">
                <h2 class="table-title">Transaction History</h2>
                <div style="display: flex; gap: 12px; align-items: center;">
                    <input class="table-search" placeholder="⌕  Search description">
                    <button type="button" class="outline-btn" onclick="alert('Exporting statement as PDF/CSV...')">
                        ⭳ Export All
                    </button>
                </div>
            </div>

            <table class="table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Description</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($paymentsHistory)): ?>
                        <?php foreach ($paymentsHistory as $ph):
                            $statusClass = $ph['payment_status'] === 'completed' ? 'paid' : ($ph['payment_status'] === 'overdue' ? 'overdue' : 'pending');
                        ?>
                            <tr>
                                <td><?php echo date('d/m/Y', strtotime($ph['payment_date'])); ?></td>
                                <td>
                                    <div>
                                        <strong><?php echo htmlspecialchars($ph['payment_type']); ?></strong>
                                        <div style="font-size: 11px; color: #888;">INV-2026-0<?php echo $ph['payment_id']; ?> • PropManage</div>
                                    </div>
                                </td>
                                <td><b><?php echo number_format($ph['amount']); ?> BDT</b></td>
                                <td><span class="status-tag <?php echo $statusClass; ?>"><?php echo ucfirst($ph['payment_status']); ?></span></td>
                                <td>
                                    <?php if ($ph['payment_status'] === 'completed'): ?>
                                        <button type="button" class="outline-btn" style="padding: 6px 14px; font-size: 12px;" onclick="alert('Viewing receipt for INV-2026-0<?php echo $ph['payment_id']; ?> (<?php echo number_format($ph['amount']); ?> BDT)')">
                                            ▤ Receipt
                                        </button>
                                    <?php else: ?>
                                        <button type="button" class="primary-btn action-pay-btn" style="padding: 6px 14px; font-size: 12px;">
                                            Pay Now
                                        </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                    <tr>
                        <td>01/08/2026</td>
                        <td>
                            <div>
                                <strong>August Rent (Unit 4B)</strong>
                                <div style="font-size: 11px; color: #888;">INV-2026-0842 • The Gulshan Residences</div>
                            </div>
                        </td>
                        <td><b>22,000 BDT</b></td>
                        <td><span class="status-tag overdue">Overdue</span></td>
                        <td>
                            <button type="button" class="primary-btn action-pay-btn" style="padding: 6px 14px; font-size: 12px;">
                                Pay Now
                            </button>
                        </td>
                    </tr>
                    <tr>
                        <td>01/07/2026</td>
                        <td>
                            <div>
                                <strong>July Rent (Unit 4B)</strong>
                                <div style="font-size: 11px; color: #888;">INV-2026-0719 • The Gulshan Residences</div>
                            </div>
                        </td>
                        <td>22,000 BDT</td>
                        <td><span class="status-tag paid">Paid</span></td>
                        <td>
                            <button type="button" class="outline-btn" style="padding: 6px 14px; font-size: 12px;" onclick="alert('Viewing receipt for INV-2026-0719')">
                                ▤ Receipt
                            </button>
                        </td>
                    </tr>
                    <tr>
                        <td>01/06/2026</td>
                        <td>
                            <div>
                                <strong>June Rent (Unit 4B)</strong>
                                <div style="font-size: 11px; color: #888;">INV-2026-0604 • The Gulshan Residences</div>
                            </div>
                        </td>
                        <td>22,000 BDT</td>
                        <td><span class="status-tag paid">Paid</span></td>
                        <td>
                            <button type="button" class="outline-btn" style="padding: 6px 14px; font-size: 12px;" onclick="alert('Viewing receipt for INV-2026-0604')">
                                ▤ Receipt
                            </button>
                        </td>
                    </tr>
                    <tr>
                        <td>01/05/2026</td>
                        <td>
                            <div>
                                <strong>May Rent + Security Deposit</strong>
                                <div style="font-size: 11px; color: #888;">INV-2026-0501 • The Gulshan Residences</div>
                            </div>
                        </td>
                        <td>44,000 BDT</td>
                        <td><span class="status-tag paid">Paid</span></td>
                        <td>
                            <button type="button" class="outline-btn" style="padding: 6px 14px; font-size: 12px;" onclick="alert('Viewing receipt for INV-2026-0501')">
                                ▤ Receipt
                            </button>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <div class="table-footer">
                <span>Showing 1-4 of 16 transactions</span>
                <span>‹ Page 1 of 4 ›</span>
            </div>
        </section>
    </main>
</div>


<div class="pm-modal-overlay" id="paymentModal">
    <div class="pm-modal">
        <div class="pm-modal-header">
            <div class="pm-modal-title">Complete Rent Payment</div>
            <button type="button" class="pm-modal-close" id="closePaymentModal">&times;</button>
        </div>
        <form id="checkoutForm">
            <div class="pm-modal-body">
                <div style="background: #f4f6fb; padding: 16px; border-radius: 10px; margin-bottom: 22px;">
                    <div style="display: flex; justify-content: space-between; font-size: 14px; margin-bottom: 6px;">
                        <span>Monthly Rent (August 2026)</span>
                        <strong>22,000.00 BDT</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; font-size: 14px; margin-bottom: 8px;">
                        <span>Processing & Platform Fee</span>
                        <strong style="color: #00a66f;">FREE (0.00 BDT)</strong>
                    </div>
                    <div style="border-top: 1px dashed #c4d3ee; padding-top: 10px; display: flex; justify-content: space-between; font-size: 16px; font-weight: 800; color: #0755cf;">
                        <span>Total Payable</span>
                        <span>22,000.00 BDT</span>
                    </div>
                </div>

                <label class="form-label">Select Payment Method</label>
                <div class="payment-method-selector">
                    <div class="payment-method-card selected" data-method="bkash">
                        <span class="payment-method-icon">📱</span>
                        bKash
                    </div>
                    <div class="payment-method-card" data-method="nagad">
                        <span class="payment-method-icon">📲</span>
                        Nagad
                    </div>
                    <div class="payment-method-card" data-method="card">
                        <span class="payment-method-icon">💳</span>
                        Card / Bank
                    </div>
                </div>

                <div class="form-group-pm">
                    <label id="paymentAccountLabel">Mobile Wallet Number (01XXXXXXXXX)</label>
                    <input type="text" placeholder="e.g. 01708375128" required value="01708375128">
                </div>

                <div class="form-group-pm">
                    <label>PIN / Verification Code</label>
                    <input type="password" placeholder="••••" maxlength="6" required value="1234">
                </div>
            </div>
            <div class="pm-modal-footer">
                <button type="button" class="outline-btn" onclick="document.getElementById('paymentModal').classList.remove('active')">Cancel</button>
                <button type="submit" class="primary-btn">Confirm Payment</button>
            </div>
        </form>
    </div>
</div>

<script src="../js/tenant.js"></script>
<script>

    const profileBtn = document.getElementById("profileDropdownBtn");
    const userMenu = document.getElementById("userMenu");
    if (profileBtn && userMenu) {
        profileBtn.addEventListener("click", (e) => {
            e.stopPropagation();
            userMenu.classList.toggle("show");
        });
        document.addEventListener("click", () => userMenu.classList.remove("show"));
    }
</script>
</body>
</html>
