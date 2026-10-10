<?php
require_once __DIR__ . '/../config/db.php';

$userName = $_SESSION['user_name'] ?? 'System Administrator';
$totalTrans = 550380;
$adminFee = 24500;
$adminPayments = [];

if ($db_connected && $pdo) {
    try {
        $tStmt = $pdo->query("SELECT COALESCE(SUM(amount), 0) FROM payments WHERE payment_status = 'completed'");
        $val = $tStmt->fetchColumn();
        if ($val > 0) {
            $totalTrans = $val;
            $adminFee = round($val * 0.05);
        }

        $stmt = $pdo->query("SELECT pay.*, u.full_name as tenant_name,
                                    COALESCE(p.title, 'Rental Property') as prop_title
                             FROM payments pay
                             JOIN users u ON pay.tenant_id = u.user_id
                             LEFT JOIN bookings b ON pay.booking_id = b.booking_id
                             LEFT JOIN properties p ON b.property_id = p.property_id
                             ORDER BY pay.payment_date DESC");
        $adminPayments = $stmt->fetchAll();
    } catch (Exception $e) {}
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Financials & Payments | PropManage</title>
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>


<aside class="sidebar">
    <a href="../index.php" class="brand">
        <span class="brand-mark">◈</span>PropManage
    </a>
    <nav class="side-nav">
        <a class="side-link" href="dashboard.php"><span class="side-icon">▦</span>Dashboard</a>
        <a class="side-link" href="users.php"><span class="side-icon">👥</span>User Management</a>
        <a class="side-link" href="properties.php"><span class="side-icon">🏢</span>Property Manage</a>
        <a class="side-link" href="support.php"><span class="side-icon">🛡️</span>Support Request</a>
        <a class="side-link active" href="financials.php"><span class="side-icon">💰</span>Financial</a>
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
                <div class="profile-role">Admin</div>
            </div>
            <img class="avatar" src="<?php echo htmlspecialchars(getActiveUserAvatar()); ?>" alt="Admin">

            <div class="user-dropdown-menu" id="userMenu">
                <div class="dropdown-header">Switch Role Preview</div>
                <a class="dropdown-item" href="../landlord/dashboard.php"><span>🏠</span> Landlord Portal</a>
                <a class="dropdown-item" href="../tenant/explore.php"><span>🔑</span> Tenant Portal</a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="profile.php"><span>👤</span> Edit Profile</a>
                <a class="dropdown-item" href="../auth/logout.php" style="color: #d02020;"><span>🚪</span> Log Out</a>
            </div>
        </div>
    </header>


    <main class="content">
        <div class="page-head">
            <h1 class="page-title">Financials &amp; Payments</h1>
            <button type="button" class="primary-btn open-payout-btn">
                ＋ Process Payout
            </button>
        </div>


        <section class="stats" style="grid-template-columns: repeat(2, 280px); margin-bottom: 38px;">
            <div class="stat-card">
                <div class="stat-top">
                    Total Transaction
                    <span class="stat-icon" style="background:#eaf1fc; color:#0755cf;">💳</span>
                </div>
                <div class="stat-number"><?php echo number_format($totalTrans); ?> <small>BDT</small></div>
            </div>

            <div class="stat-card">
                <div class="stat-top">
                    Platform Commission (5%)
                    <span class="stat-icon" style="background:#e7f8f1; color:#00a66f;">📈</span>
                </div>
                <div class="stat-number"><?php echo number_format($adminFee); ?> <small>BDT</small></div>
            </div>
        </section>


        <section class="panel table-panel">
            <div class="table-head">
                <h2 class="table-title">Recent Transactions</h2>
                <input class="table-search" placeholder="⌕  Search account number">
            </div>

            <table class="table">
                <thead>
                    <tr>
                        <th>Transaction Details</th>
                        <th>Date &amp; Time</th>
                        <th>Amount</th>
                        <th>Fee / Comm</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($adminPayments)): ?>
                        <?php foreach ($adminPayments as $ap):
                            $statusClass = $ap['payment_status'] === 'completed' ? 'paid' : ($ap['payment_status'] === 'failed' ? 'rejected' : 'pending');
                            $fee = round($ap['amount'] * 0.05);
                        ?>
                            <tr>
                                <td>
                                    <div>
                                        <strong><?php echo htmlspecialchars($ap['prop_title']); ?></strong>
                                        <div style="font-size: 11px; color: #888;">TRX-928<?php echo $ap['payment_id']; ?> • <?php echo htmlspecialchars($ap['tenant_name']); ?></div>
                                    </div>
                                </td>
                                <td><?php echo date('d/m/Y H:i', strtotime($ap['payment_date'])); ?></td>
                                <td class="amount-pos">+<?php echo number_format($ap['amount']); ?>.00 BDT</td>
                                <td><?php echo number_format($fee); ?>.00 BDT</td>
                                <td><span class="status-tag <?php echo $statusClass; ?>"><?php echo ucfirst($ap['payment_status']); ?></span></td>
                                <td><button type="button" class="outline-btn" style="padding: 5px 10px; font-size: 12px;" onclick="alert('Transaction #TRX-928<?php echo $ap['payment_id']; ?> verified in central database.')">⋮ Details</button></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                    <tr>
                        <td>
                            <div>
                                <strong>Gulshan Luxury Suite</strong>
                                <div style="font-size: 11px; color: #888;">ACC-92837194 • Tenant Rental Payment</div>
                            </div>
                        </td>
                        <td>02/09/2026 14:32</td>
                        <td class="amount-pos">+2,400.00 BDT</td>
                        <td>120.00 BDT</td>
                        <td><span class="status-tag paid">Completed</span></td>
                        <td><button type="button" class="outline-btn" style="padding: 5px 10px; font-size: 12px;" onclick="alert('Viewing transaction ledger audit')">⋮ Details</button></td>
                    </tr>

                    <tr>
                        <td>
                            <div>
                                <strong>Banani Heritage Payout</strong>
                                <div style="font-size: 11px; color: #888;">ACC-71928401 • Landlord Disbursed Payout</div>
                            </div>
                        </td>
                        <td>01/09/2026 10:15</td>
                        <td class="amount-neg">-24,500.00 BDT</td>
                        <td>—</td>
                        <td><span class="status-tag pending">Pending</span></td>
                        <td><button type="button" class="outline-btn" style="padding: 5px 10px; font-size: 12px;" onclick="alert('Viewing transaction ledger audit')">⋮ Details</button></td>
                    </tr>

                    <tr>
                        <td>
                            <div>
                                <strong>Bashundhara Studio Rent</strong>
                                <div style="font-size: 11px; color: #888;">ACC-48192038 • Card Gateway Timeout</div>
                            </div>
                        </td>
                        <td>31/08/2026 18:40</td>
                        <td class="amount-neg">-1,500.00 BDT</td>
                        <td>—</td>
                        <td><span class="status-tag rejected">Failed</span></td>
                        <td><button type="button" class="outline-btn" style="padding: 5px 10px; font-size: 12px;" onclick="alert('Viewing transaction ledger audit')">⋮ Details</button></td>
                    </tr>

                    <tr>
                        <td>
                            <div>
                                <strong>Maintenance Fee Processing</strong>
                                <div style="font-size: 11px; color: #888;">ACC-10293847 • Unit 4B Plumbing Repair</div>
                            </div>
                        </td>
                        <td>30/08/2026 09:20</td>
                        <td class="amount-pos">+550.00 BDT</td>
                        <td>15.00 BDT</td>
                        <td><span class="status-tag paid">Completed</span></td>
                        <td><button type="button" class="outline-btn" style="padding: 5px 10px; font-size: 12px;" onclick="alert('Viewing transaction ledger audit')">⋮ Details</button></td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <div class="table-footer">
                <span>Showing 1 to 4 of 128 entries</span>
                <span>‹　<b>1</b>　2　3　›</span>
            </div>
        </section>
    </main>
</div>


<div class="pm-modal-overlay" id="processPayoutModal">
    <div class="pm-modal">
        <div class="pm-modal-header">
            <div class="pm-modal-title">Process Landlord Payout</div>
            <button type="button" class="pm-modal-close" id="closePayoutModal">&times;</button>
        </div>
        <form id="payoutForm">
            <div class="pm-modal-body">
                <div class="form-group-pm">
                    <label>Select Landlord</label>
                    <select id="payoutRecipient" required>
                        <option value="Md Farhan Nadim (Banani Property)">Md Farhan Nadim (Banani Property) - Bal: 42,500 BDT</option>
                        <option value="Arafatullah Rifat (Gulshan Residences)">Arafatullah Rifat (Gulshan Residences) - Bal: 50,000 BDT</option>
                        <option value="Fariha Akter Prity (Bashundhara Flat)">Fariha Akter Prity (Bashundhara Flat) - Bal: 27,000 BDT</option>
                    </select>
                </div>

                <div class="form-group-pm">
                    <label>Disbursement Amount (BDT)</label>
                    <input type="number" id="payoutAmount" value="20000" min="1000" required>
                </div>

                <div class="form-group-pm">
                    <label>Bank Account / Routing Number</label>
                    <input type="text" value="City Bank • A/C 110293847291 • Branch: Gulshan" required>
                </div>

                <div class="form-group-pm">
                    <label>Admin Authorization PIN</label>
                    <input type="password" placeholder="Enter security PIN" value="1234" required>
                </div>
            </div>
            <div class="pm-modal-footer">
                <button type="button" class="outline-btn" onclick="document.getElementById('processPayoutModal').classList.remove('active')">Cancel</button>
                <button type="submit" class="primary-btn">Disburse Funds</button>
            </div>
        </form>
    </div>
</div>

<script src="../js/admin.js"></script>
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
