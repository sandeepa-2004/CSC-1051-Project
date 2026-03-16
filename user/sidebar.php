<?php

$current = basename($_SERVER['PHP_SELF']);
?>
<aside class="sidebar">

    
    <div class="sidebar-header">
        
    
    </div>


    <div class="sidebar-user">
        <div class="user-avatar">
            <?= strtoupper(substr($_SESSION['user_name'], 0, 1)) ?>
        </div>
        <div class="user-info">
            <h4><?= htmlspecialchars($_SESSION['user_name']) ?></h4>
            <p>Affected Person</p>
        </div>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-label">Main</div>

        <a href="dashboard.php" class="nav-item <?= $current === 'dashboard.php' ? 'active' : '' ?>">
            <span class="nav-icon">🏠</span> Dashboard
        </a>

        <a href="requests.php" class="nav-item <?= $current === 'requests.php' ? 'active' : '' ?>">
            <span class="nav-icon">📋</span> My Requests
        </a>

        <a href="new_request.php" class="nav-item <?= $current === 'new_request.php' ? 'active' : '' ?>">
            <span class="nav-icon">➕</span> New Request
        </a>
    </nav>

    
    <div class="sidebar-footer">
        <a href="../includes/logout.php" class="nav-item">
            <span class="nav-icon">🚪</span> Sign Out
        </a>
    </div>

</aside>