<?php
$current = basename($_SERVER['PHP_SELF']);
?>
<aside class="sidebar">
    <div class="sidebar-header">
        <a href="dashboard.php" class="sidebar-logo">
           
            
        </a>
    </div>

    <div class="sidebar-user">
        <div class="user-avatar" style="background: #7c3aed;"><?= strtoupper(substr($_SESSION['user_name'], 0, 1)) ?></div>
        <div class="user-info">
            <h4><?= htmlspecialchars($_SESSION['user_name']) ?></h4>
            <p>Administrator</p>
        </div>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-label">Overview</div>
        <a href="dashboard.php" class="nav-item <?= $current === 'dashboard.php' ? 'active' : '' ?>">
            <span class="nav-icon">🏠</span> Dashboard
        </a>

        <div class="nav-label">Management</div>
        <a href="users.php" class="nav-item <?= $current === 'users.php' ? 'active' : '' ?>">
            <span class="nav-icon">👥</span> Registered Users
        </a>
        <a href="all_requests.php" class="nav-item <?= $current === 'all_requests.php' ? 'active' : '' ?>">
            <span class="nav-icon">📋</span> All Requests
        </a>

        <div class="nav-label">Reports</div>
        <a href="reports.php" class="nav-item <?= $current === 'reports.php' ? 'active' : '' ?>">
            <span class="nav-icon">📊</span> Summary Reports
        </a>
    </nav>

    <div class="sidebar-footer">
        <a href="../includes/logout.php" class="nav-item">
            <span class="nav-icon">🚪</span> Sign Out
        </a>
    </div>
</aside>
