<?php

require_once '../includes/auth.php';
require_once '../includes/db.php';


require_login();
require_admin();


function getCount($conn, $sql) {
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);
    return $row['c'];
}




$total_users = getCount($conn,
    "SELECT COUNT(*) AS c FROM users WHERE role = 'user'"
);


$total_requests = getCount($conn,
    "SELECT COUNT(*) AS c FROM relief_requests"
);


$high_severity = getCount($conn,
    "SELECT COUNT(*) AS c FROM relief_requests WHERE severity = 'High'"
);


$pending = getCount($conn,
    "SELECT COUNT(*) AS c FROM relief_requests WHERE status = 'Pending'"
);



$food = getCount($conn,
    "SELECT COUNT(*) AS c FROM relief_requests WHERE relief_type = 'Food'"
);

$water = getCount($conn,
    "SELECT COUNT(*) AS c FROM relief_requests WHERE relief_type = 'Water'"
);

$medicine = getCount($conn,
    "SELECT COUNT(*) AS c FROM relief_requests WHERE relief_type = 'Medicine'"
);

$shelter = getCount($conn,
    "SELECT COUNT(*) AS c FROM relief_requests WHERE relief_type = 'Shelter'"
);



$low_severity = getCount($conn,
    "SELECT COUNT(*) AS c FROM relief_requests WHERE severity = 'Low'"
);

$medium_severity = getCount($conn,
    "SELECT COUNT(*) AS c FROM relief_requests WHERE severity = 'Medium'"
);



$recent_sql = "
    SELECT r.*, u.full_name
    FROM relief_requests r
    JOIN users u ON r.user_id = u.id
    ORDER BY r.created_at DESC
    LIMIT 6
";

$recent_requests = mysqli_query($conn, $recent_sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Dashboard — Flood Relief</title>
<link rel="stylesheet" href="../style.css">
</head>

<body>

<div class="app-wrapper">

<?php include 'sidebar.php'; ?>

<main class="main-content">

    <div class="page-header">
        <h1>Admin Dashboard</h1>
        <p>System overview and recent activity</p>
    </div>

    <?php if (isset($_GET['deleted'])) { ?>
        <div class="alert alert-success">
            ✅ User deleted successfully.
        </div>
    <?php } ?>

    
    <div class="stats-grid">

        <div class="stat-card green">
            <div class="stat-icon">👥</div>
            <div class="stat-value"><?php echo $total_users; ?></div>
            <div class="stat-label">Registered Users</div>
        </div>

        <div class="stat-card blue">
            <div class="stat-icon">📋</div>
            <div class="stat-value"><?php echo $total_requests; ?></div>
            <div class="stat-label">Total Requests</div>
        </div>

        <div class="stat-card red">
            <div class="stat-icon">🚨</div>
            <div class="stat-value"><?php echo $high_severity; ?></div>
            <div class="stat-label">High Severity</div>
        </div>

        <div class="stat-card amber">
            <div class="stat-icon">⏳</div>
            <div class="stat-value"><?php echo $pending; ?></div>
            <div class="stat-label">Pending Requests</div>
        </div>

    </div>

    
    <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px; margin-bottom:24px;">

        <div class="card">
            <div class="card-header">
                <h2>Relief Type Breakdown</h2>
            </div>

            <div class="card-body">

            <?php
            $types = [
                ['Food', $food, '🍚'],
                ['Water', $water, '💧'],
                ['Medicine', $medicine, '💊'],
                ['Shelter', $shelter, '🏕️']
            ];

            $max_type = max($food, $water, $medicine, $shelter, 1);

            foreach ($types as $t) {

                $label = $t[0];
                $count = $t[1];
                $emoji = $t[2];
                $width = round(($count / $max_type) * 100);
            ?>

                <div style="margin-bottom:14px;">

                    <div style="display:flex; justify-content:space-between; font-size:13px; font-weight:700;">
                        <span><?php echo $emoji . " " . $label; ?></span>
                        <span style="color:var(--text-mid);"><?php echo $count; ?></span>
                    </div>

                    <div style="background:#f1f5f9; border-radius:6px; height:10px;">
                        <div style="height:100%; width:<?php echo $width; ?>%; background:#cbd5f5;"></div>
                    </div>

                </div>

            <?php } ?>

            </div>
        </div>

        
        <div class="card">

            <div class="card-header">
                <h2>Severity Overview</h2>
            </div>

            <div class="card-body">

            <?php
            $severity_levels = [
                ['Low', $low_severity],
                ['Medium', $medium_severity],
                ['High', $high_severity]
            ];

            $max_sev = max($low_severity, $medium_severity, $high_severity, 1);

            foreach ($severity_levels as $s) {

                $label = $s[0];
                $count = $s[1];
                $width = round(($count / $max_sev) * 100);
            ?>

                <div style="margin-bottom:14px;">

                    <div style="display:flex; justify-content:space-between; font-size:13px; font-weight:700;">
                        <span><?php echo $label; ?></span>
                        <span style="color:var(--text-mid);"><?php echo $count; ?> requests</span>
                    </div>

                    <div style="background:#f1f5f9; border-radius:6px; height:10px;">
                        <div style="height:100%; width:<?php echo $width; ?>%; background:#e2e8f0;"></div>
                    </div>

                </div>

            <?php } ?>

            </div>
        </div>

    </div>

    
    <div class="card">

        <div class="card-header">
            <h2>Recent Requests</h2>
            <a href="all_requests.php" class="btn btn-secondary btn-sm">View All</a>
        </div>

        <div class="table-wrap">

        <?php if (mysqli_num_rows($recent_requests) > 0) { ?>

            <table>

                <thead>
                <tr>
                    <th>#</th>
                    <th>Requester</th>
                    <th>Type</th>
                    <th>District</th>
                    <th>Severity</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
                </thead>

                <tbody>

                <?php while ($row = mysqli_fetch_assoc($recent_requests)) { ?>

                <tr>
                    <td><strong>#<?php echo $row['id']; ?></strong></td>
                    <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                    <td><?php echo $row['relief_type']; ?></td>
                    <td><?php echo htmlspecialchars($row['district']); ?></td>
                    <td><?php echo $row['severity']; ?></td>
                    <td><?php echo $row['status']; ?></td>
                    <td><?php echo date("d M Y", strtotime($row['created_at'])); ?></td>
                </tr>

                <?php } ?>

                </tbody>

            </table>

        <?php } else { ?>

            <div class="empty-state">
                <div class="empty-icon">📭</div>
                <h3>No requests yet</h3>
                <p>Requests submitted by users will appear here</p>
            </div>

        <?php } ?>

        </div>

    </div>

</main>
</div>

</body>
</html>