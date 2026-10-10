<?php
require_once __DIR__ . '/../config/db.php';

$userName = $_SESSION['user_name'] ?? 'Md Farhan Nadim';
$userId = $_SESSION['user_id'] ?? 2;
$contacts = [];

if ($db_connected && $pdo) {
    try {
        $stmt = $pdo->prepare("SELECT user_id, full_name, role, profile_image FROM users WHERE user_id != :uid ORDER BY full_name ASC");
        $stmt->execute([':uid' => $userId]);
        $contacts = $stmt->fetchAll();
    } catch (Exception $e) {}
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages | PropManage</title>
    <link rel="stylesheet" href="../css/landlord.css">
    <link rel="stylesheet" href="../css/messages.css">
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
        <a class="side-link active" href="messages.php"><span class="side-icon">▣</span>Messages</a>
        <a class="side-link" href="tenants.php"><span class="side-icon">♧</span>Tenants</a>
        <a class="side-link" href="payments.php"><span class="side-icon">▣</span>Payments</a>
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


    <main class="content" style="max-width: 1200px; padding: 24px 34px;">
        <div class="messages-container">

            <div class="conversations-panel">
                <div class="conversations-header">
                    <h2>Active Conversations</h2>
                    <div class="conversations-search">
                        <span class="search-icon">⌕</span>
                        <input type="text" id="conversationSearch" placeholder="Search messages">
                    </div>
                </div>

                <ul class="conversations-list">
                    <li class="conversation-item active">
                        <div class="conversation-avatar">
                            <img src="https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?w=100&q=80" alt="Arafatullah Rifat">
                            <div class="online-status-dot"></div>
                        </div>
                        <div class="conversation-details">
                            <div class="conversation-top">
                                <span class="conversation-name">Arafatullah Rifat</span>
                                <span class="conversation-time">Now</span>
                            </div>
                            <div class="conversation-preview">Awesome, thank you! See you on Friday.</div>
                        </div>
                    </li>

                    <li class="conversation-item">
                        <div class="conversation-avatar">
                            <img src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=100&q=80" alt="Fariha Akter Prity">
                            <div class="online-status-dot"></div>
                        </div>
                        <div class="conversation-details">
                            <div class="conversation-top">
                                <span class="conversation-name">Fariha Akter Prity</span>
                                <span class="conversation-time">Yesterday</span>
                            </div>
                            <div class="conversation-preview">Thank you so much for the quick response!</div>
                        </div>
                    </li>

                    <li class="conversation-item">
                        <div class="conversation-avatar">
                            <img src="https://images.unsplash.com/photo-1531123897727-8f129e1688ce?w=100&q=80" alt="Mashfi Rahman">
                        </div>
                        <div class="conversation-details">
                            <div class="conversation-top">
                                <span class="conversation-name">Mashfi Rahman</span>
                                <span class="conversation-time">Day</span>
                            </div>
                            <div class="conversation-preview">Your digital receipt is ready on the portal.</div>
                        </div>
                    </li>

                    <li class="conversation-item">
                        <div class="conversation-avatar">
                            <img src="https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=100&q=80" alt="Farhan Nadim">
                        </div>
                        <div class="conversation-details">
                            <div class="conversation-top">
                                <span class="conversation-name">Md Farhan Nadim</span>
                                <span class="conversation-time">Aug 20</span>
                            </div>
                            <div class="conversation-preview">Everything looks great so far.</div>
                        </div>
                    </li>
                </ul>
            </div>


            <div class="chat-window">
                <div class="chat-header">
                    <div class="chat-user-info">
                        <img class="chat-user-avatar" src="https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?w=100&q=80" alt="Arafatullah Rifat">
                        <div>
                            <div class="chat-user-name">Arafatullah Rifat</div>
                            <div class="chat-user-status">Active now</div>
                        </div>
                    </div>
                    <div class="chat-actions">
                        <button class="chat-action-btn" title="Call Resident" onclick="alert('Calling tenant Arafatullah Rifat at 01708-375128...')">📞</button>
                        <button class="chat-action-btn" title="Video Inspection" onclick="alert('Starting video session...')">🎥</button>
                        <button class="chat-action-btn" title="Tenant Info" onclick="alert('Tenant Profile:\nName: Arafatullah Rifat\nLease: 12 Months (Unit 2A)\nRent: 50,000 BDT')">ⓘ</button>
                    </div>
                </div>


                <div class="chat-messages">
                    <div class="message-date-divider"><span>Today</span></div>

                    <div class="message-row received">
                        <img class="message-avatar" src="https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?w=100&q=80" alt="Avatar">
                        <div class="message-content">
                            <div class="message-bubble">Hello! Is the 3-bed flat in Gulshan still available for rent?</div>
                            <div class="message-time">10:15 AM</div>
                        </div>
                    </div>

                    <div class="message-row sent">
                        <img class="message-avatar" src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=100&q=80" alt="Landlord">
                        <div class="message-content">
                            <div class="message-bubble">Hi Rifat, yes it is currently available and ready for viewing.</div>
                            <div class="message-time">10:18 AM</div>
                        </div>
                    </div>

                    <div class="message-row received">
                        <img class="message-avatar" src="https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?w=100&q=80" alt="Avatar">
                        <div class="message-content">
                            <div class="message-bubble">Great! Can I schedule a visit this Friday afternoon?</div>
                            <div class="message-time">10:20 AM</div>
                        </div>
                    </div>

                    <div class="message-row sent">
                        <img class="message-avatar" src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=100&q=80" alt="Landlord">
                        <div class="message-content">
                            <div class="message-bubble">Sure thing. 3:00 PM Friday works well. I'll have the keys ready.</div>
                            <div class="message-time">10:22 AM</div>
                        </div>
                    </div>

                    <div class="message-row received">
                        <img class="message-avatar" src="https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?w=100&q=80" alt="Avatar">
                        <div class="message-content">
                            <div class="message-bubble">Awesome, thank you! See you on Friday.</div>
                            <div class="message-time">10:25 AM</div>
                        </div>
                    </div>
                </div>


                <div class="chat-input-bar">
                    <button type="button" title="Attach Document" onclick="alert('Attach lease contract or notice...')">📎</button>
                    <button type="button" title="Send Photo" onclick="alert('Select image to send...')">🖼️</button>
                    <div class="text-input-box">
                        <input type="text" id="messageInput" placeholder="Type a message...">
                    </div>
                    <button type="button" title="Insert Emoji" onclick="document.getElementById('messageInput').value += ' 😊'">😊</button>
                    <button type="button" class="chat-send-btn" id="sendMessageBtn" title="Send Message">➤</button>
                </div>
            </div>
        </div>
    </main>
</div>

<script src="../js/messages.js"></script>
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
