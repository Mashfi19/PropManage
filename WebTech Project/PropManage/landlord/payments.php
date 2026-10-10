<?php
require_once __DIR__ . '/../config/db.php';

$userName = $_SESSION['user_name'] ?? 'Md Farhan Nadim';
$totalRevenue = 42500;
$pendingRevenue = 20000;
$paymentsList = [];

if ($db_connected && $pdo) {
    try {
        $totStmt = $pdo->query("SELECT COALESCE(SUM(amount), 0) FROM payments WHERE payment_status = 'completed'");
        $val = $totStmt->fetchColumn();
        if ($val > 0) $totalRevenue = $val;

        $penStmt = $pdo->query("SELECT COALESCE(SUM(amount), 0) FROM payments WHERE payment_status = 'pending'");
        $val2 = $penStmt->fetchColumn();
        if ($val2 > 0) $pendingRevenue = $val2;

        $stmt = $pdo->query("SELECT pay.*, u.full_name as tenant_name, u.profile_image,
                                COALESCE(p.title, 'Family Flat') as property_title,
                                COALESCE(p.address, 'Banani') as address
                             FROM payments pay
                             JOIN users u ON pay.tenant_id = u.user_id
                             LEFT JOIN bookings b ON pay.booking_id = b.booking_id
                             LEFT JOIN properties p ON b.property_id = p.property_id
                             ORDER BY pay.payment_date DESC");
        $paymentsList = $stmt->fetchAll();
    } catch (Exception $e) {}
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>Payments &amp; Billing | PropManage</title>
    <link rel="stylesheet" href="../css/landlord.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>


<aside class="sidebar">
    <a href="../index.php" class="brand">
        <span class="brand-mark">◈</span>PropManage
    </a>
    <nav class="side-nav">
        <a class="side-link" href="dashboard.php"><span class="side-icon">▦</span>Dashboard</a>
        <a class="side-link" href="properties.php"><span class="side-icon">▦</span>Properties</a>
        <a class="side-link" href="rent-requests.php"><span class="side-icon">♧</span>Rent Requests</a>
        <a class="side-link" href="messages.php"><span class="side-icon">▣</span>Messages</a>
        <a class="side-link" href="tenants.php"><span class="side-icon">♧</span>Tenants</a>
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
            <input type="text" placeholder="Search...">
        </div>
        <div class="profile user-dropdown-container" id="profileDropdownBtn">
            <div class="profile-name">
                <?php echo htmlspecialchars($userName); ?>
                <div class="profile-role">Landlord</div>
            </div>
            <img class="avatar" src="<?php echo htmlspecialchars(getActiveUserAvatar()); ?>" alt="Landlord">

            <div class="user-dropdown-menu" id="userMenu">
                <div class="dropdown-header">Switch Role Preview</div>
                <a class="dropdown-item" href="../tenant/explore.php"><span>🔑</span> Tenant Portal</a>
                <a class="dropdown-item" href="../admin/dashboard.php"><span>🛡️</span> Admin Portal</a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="profile.php"><span>👤</span> Edit Profile</a>
                <a class="dropdown-item" href="../auth/logout.php" style="color: #d02020;"><span>🚪</span> Log Out</a>
            </div>
        </div>
    </header>

    <main class="content">
        <div class="page-head">
            <h1 class="page-title">Payments &amp; Billing</h1>
            <button type="button" class="primary-btn" id="openSendBillsBtn">
                ▷ Send Bills
            </button>
        </div>


        <section class="stats" style="grid-template-columns:repeat(2,278px);margin-bottom:48px">
            <div class="stat-card">
                <div class="stat-top">
                    TOTAL REVENUE (BDT)
                    <span class="stat-icon" style="background:#dbeaff;color:#426da8">▣</span>
                </div>
                <div class="stat-number"><?php echo number_format($totalRevenue); ?> BDT</div>
            </div>
            <div class="stat-card">
                <div class="stat-top">
                    PENDING PROCESSING
                    <span class="stat-icon gray">⌛</span>
                </div>
                <div class="stat-number"><?php echo number_format($pendingRevenue); ?> BDT</div>
            </div>
        </section>


        <section class="panel table-panel">
            <div class="table-head">
                <h2 class="table-title">Current Billing Cycle</h2>
                <input class="table-search" placeholder="⌕  Search tenant or unit">
            </div>

            <table class="table billing-table">
                <thead>
                    <tr>
                        <th>Tenant</th>
                        <th>Unit</th>
                        <th>Amount</th>
                        <th>Due Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($paymentsList)): ?>
                        <?php foreach ($paymentsList as $pay):
                            $stClass = $pay['payment_status'] === 'completed' ? 'paid' : ($pay['payment_status'] === 'overdue' ? 'overdue' : 'pending');
                        ?>
                            <tr>
                                <td>
                                    <div class="person">
                                        <img src="<?php echo htmlspecialchars($pay['profile_image']); ?>">
                                        <b><?php echo htmlspecialchars($pay['tenant_name']); ?></b>
                                    </div>
                                </td>
                                <td><?php echo htmlspecialchars($pay['property_title']); ?></td>
                                <td><b><?php echo number_format($pay['amount']); ?> BDT</b></td>
                                <td><?php echo date('d/m/Y', strtotime($pay['payment_date'])); ?></td>
                                <td><span class="status <?php echo $stClass; ?>"><?php echo strtoupper($pay['payment_status']); ?></span></td>
                                <td>
                                    <button type="button" class="outline-btn" style="padding: 4px 8px;" title="View Receipt" onclick="alert('Viewing receipt #RCP-<?php echo $pay['payment_id']; ?> (<?php echo number_format($pay['amount']); ?> BDT)')">▤</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                    <tr>
                        <td>
                            <div class="person">
                                <img src="https://images.unsplash.com/photo-1531123897727-8f129e1688ce?w=100&q=80">
                                <b>Mashfi</b>
                            </div>
                        </td>
                        <td>Family • 7B</td>
                        <td>22,000 BDT</td>
                        <td>01/09/2026</td>
                        <td><span class="status paid">PAID</span></td>
                        <td>
                            <button type="button" class="outline-btn" style="padding: 4px 8px;" title="View Receipt" onclick="alert('Viewing receipt #RCP-8819 for Mashfi (22,000 BDT)')">▤</button>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <div class="person">
                                <img src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=100&q=80">
                                <b style="color:#c51e16">Fariha Prity</b>
                            </div>
                        </td>
                        <td>Bachelor • 9C</td>
                        <td style="color:#c51e16"><b>19,000 BDT</b></td>
                        <td>15/08/2026</td>
                        <td><span class="status overdue">OVERDUE</span></td>
                        <td>
                            <button type="button" class="outline-btn" style="padding: 4px 8px; color: #0755cf;" title="Send Reminder" onclick="alert('Payment reminder email & notification dispatched to Fariha Prity.')">✉ Send Reminder</button>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <div class="person">
                                <img src="https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?w=100&q=80">
                                <b>Rifat</b>
                            </div>
                        </td>
                        <td>Mansion • 2A</td>
                        <td>20,000 BDT</td>
                        <td>05/09/2026</td>
                        <td><span class="status pending">PENDING</span></td>
                        <td>
                            <button type="button" class="outline-btn" style="padding: 4px 8px;" title="View Invoice" onclick="alert('Viewing invoice #INV-4921 for Rifat')">▤</button>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <div class="person">
                                <img src="https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=100&q=80">
                                <b>Nadim</b>
                            </div>
                        </td>
                        <td>Bachelor • 9A</td>
                        <td>27,000 BDT</td>
                        <td>10/09/2026</td>
                        <td><span class="status unpaid">UNPAID</span></td>
                        <td>
                            <button type="button" class="outline-btn" style="padding: 4px 8px;" title="Send Notice" onclick="alert('Notice sent to Nadim')">✉</button>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <div class="table-footer">
                <span>Showing 1-4 of 24 tenants</span>
                <span>‹　›</span>
            </div>
        </section>
    </main>
</div>


<div class="pm-modal-overlay" id="sendBillsModal">
    <div class="pm-modal">
        <div class="pm-modal-header">
            <div class="pm-modal-title">Generate & Send Rent Invoices</div>
            <button type="button" class="pm-modal-close" onclick="document.getElementById('sendBillsModal').classList.remove('active')">&times;</button>
        </div>
        <form id="sendBillsForm" onsubmit="event.preventDefault(); alert('Monthly bills generated and sent to all active tenants!'); document.getElementById('sendBillsModal').classList.remove('active');">
            <div class="pm-modal-body">
                <div class="form-group-pm">
                    <label>Billing Target</label>
                    <select required>
                        <option value="all">All Active Tenants (4 Tenants)</option>
                        <option value="mashfi">Mashfi Rahman (Unit 7B)</option>
                        <option value="fariha">Fariha Prity (Unit 9C)</option>
                        <option value="rifat">Arafatullah Rifat (Unit 2A)</option>
                        <option value="nadim">Farhan Nadim (Unit 9A)</option>
                    </select>
                </div>

                <div class="form-row-2">
                    <div class="form-group-pm">
                        <label>Billing Month</label>
                        <select required>
                            <option>September 2026</option>
                            <option>October 2026</option>
                            <option>November 2026</option>
                        </select>
                    </div>
                    <div class="form-group-pm">
                        <label>Payment Due Date</label>
                        <input type="date" value="2026-09-10" required>
                    </div>
                </div>

                <div class="form-group-pm">
                    <label>Additional Utility Charges (BDT)</label>
                    <input type="number" value="1500" placeholder="e.g. Gas/Water/Electricity">
                </div>
            </div>
            <div class="pm-modal-footer">
                <button type="button" class="outline-btn" onclick="document.getElementById('sendBillsModal').classList.remove('active')">Cancel</button>
                <button type="submit" class="primary-btn">Dispatch Invoices</button>
            </div>
        </form>
    </div>
</div>

<script src="../js/landlord.js"></script>
<script>
    const sendBillsBtn = document.getElementById("openSendBillsBtn");
    const sendBillsModal = document.getElementById("sendBillsModal");
    if (sendBillsBtn && sendBillsModal) {
        sendBillsBtn.addEventListener("click", () => sendBillsModal.classList.add("active"));
    }


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