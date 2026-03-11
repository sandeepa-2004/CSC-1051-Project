<?php
require_once '../includes/auth.php';
require_once '../includes/db.php';

require_login();
require_admin();

$districts = ['','Colombo','Gampaha','Kalutara','Kandy','Matale','Nuwara Eliya','Galle','Matara','Hambantota','Jaffna','Kilinochchi','Mannar','Vavuniya','Mullaitivu','Batticaloa','Ampara','Trincomalee','Kurunegala','Puttalam','Anuradhapura','Polonnaruwa','Badulla','Moneragala','Ratnapura','Kegalle'];


$filter_district = $_GET['district'] ?? '';
$filter_type     = $_GET['relief_type'] ?? '';

$where = "WHERE 1=1";
$params = [];
$types = "";

if ($filter_district) {
    $where .= " AND r.district = ?";
    $params[] = $filter_district;
    $types .= "s";
}
if ($filter_type) {
    $where .= " AND r.relief_type = ?";
    $params[] = $filter_type;
    $types .= "s";
}

function get_count($conn, $sql, $params, $types) {
    if (empty($params)) {
        $result = mysqli_query($conn, $sql . " /* no params */");
        
    }
    $stmt = mysqli_prepare($conn, $sql);
    if (!empty($params)) {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }
    mysqli_stmt_execute($stmt);
    $row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    return $row['c'];
}


$total_users = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM users WHERE role='user'"))['c'];


$base = "SELECT COUNT(*) as c FROM relief_requests r $where";

$stmt_total = mysqli_prepare($conn, $base);
if (!empty($params)) mysqli_stmt_bind_param($stmt_total, $types, ...$params);
mysqli_stmt_execute($stmt_total);
$filtered_total = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_total))['c'];


$stmt_high = mysqli_prepare($conn, $base . " AND r.severity='High'");
if (!empty($params)) mysqli_stmt_bind_param($stmt_high, $types, ...$params);
mysqli_stmt_execute($stmt_high);
$filtered_high = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_high))['c'];


$type_counts = [];
foreach (['Food','Water','Medicine','Shelter'] as $t) {
    $stmt_t = mysqli_prepare($conn, "SELECT COUNT(*) as c FROM relief_requests r $where AND r.relief_type=?");
    $new_params = array_merge($params, [$t]);
    $new_types = $types . "s";
    mysqli_stmt_bind_param($stmt_t, $new_types, ...$new_params);
    mysqli_stmt_execute($stmt_t);
    $type_counts[$t] = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_t))['c'];
}


$stmt_fam = mysqli_prepare($conn, "SELECT SUM(r.family_members) as c FROM relief_requests r $where");
if (!empty($params)) mysqli_stmt_bind_param($stmt_fam, $types, ...$params);
mysqli_stmt_execute($stmt_fam);
$families = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_fam))['c'] ?? 0;


$dist_where = $filter_type ? "WHERE relief_type = ?" : "";
$dist_stmt = mysqli_prepare($conn, "SELECT district, COUNT(*) as total FROM relief_requests $dist_where GROUP BY district ORDER BY total DESC LIMIT 10");
if ($filter_type) {
    mysqli_stmt_bind_param($dist_stmt, "s", $filter_type);
}
mysqli_stmt_execute($dist_stmt);
$dist_data = mysqli_stmt_get_result($dist_stmt);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports — Admin</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
<div class="app-wrapper">
    <?php include 'sidebar.php'; ?>

    <main class="main-content">
        <div class="page-header">
            <h1>Summary Reports</h1>
            
        </div>

        
        <div class="card" style="margin-bottom: 24px;">
            <div class="card-body" style="padding: 20px 24px;">
                <form method="GET" style="display: flex; gap: 14px; flex-wrap: wrap; align-items: flex-end;">
                    <div class="form-group" style="margin-bottom:0; flex:1; min-width:180px;">
                        <label>Filter by District</label>
                        <select name="district">
                            <option value="">All Districts</option>
                            <?php foreach (array_filter($districts) as $d): ?>
                                <option value="<?= $d ?>" <?= ($filter_district === $d) ? 'selected' : '' ?>><?= $d ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group" style="margin-bottom:0; flex:1; min-width:180px;">
                        <label>Filter by Relief Type</label>
                        <select name="relief_type">
                            <option value="">All Types</option>
                            <?php foreach (['Food','Water','Medicine','Shelter'] as $t): ?>
                                <option value="<?= $t ?>" <?= ($filter_type === $t) ? 'selected' : '' ?>><?= $t ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div style="display:flex; gap:8px;">
                        <button type="submit" class="btn btn-primary" style="max-width:none;">Apply Filter</button>
                        <a href="reports.php" class="btn btn-secondary">Reset</a>
                    </div>
                </form>

                <?php if ($filter_district || $filter_type): ?>
                <div style="margin-top:14px; padding:10px 14px; background: var(--accent-light); border-radius:8px; font-size:13px; color: var(--warning); font-weight:600;">
                    🔍 Showing results for:
                    <?= $filter_district ? "<strong>$filter_district</strong>" : '' ?>
                    <?= ($filter_district && $filter_type) ? ' + ' : '' ?>
                    <?= $filter_type ? "<strong>$filter_type requests</strong>" : '' ?>
                </div>
                <?php endif; ?>
            </div>
        </div>

        
        <div class="stats-grid" style="grid-template-columns: repeat(auto-fit, minmax(170px, 1fr)); margin-bottom: 24px;">
            <div class="stat-card green">
                <div class="stat-icon">👥</div>
                <div class="stat-value"><?= $total_users ?></div>
                <div class="stat-label">Total Registered Users</div>
            </div>
            <div class="stat-card blue">
                <div class="stat-icon">📋</div>
                <div class="stat-value"><?= $filtered_total ?></div>
                <div class="stat-label"><?= ($filter_district || $filter_type) ? 'Filtered Requests' : 'Total Requests' ?></div>
            </div>
            <div class="stat-card red">
                <div class="stat-icon">🚨</div>
                <div class="stat-value"><?= $filtered_high ?></div>
                <div class="stat-label">High Severity Households</div>
            </div>
            <div class="stat-card amber">
                <div class="stat-icon">👨‍👩‍👧</div>
                <div class="stat-value"><?= $families ?></div>
                <div class="stat-label">People Affected</div>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 24px;">
            <div class="card">
                <div class="card-header"><h2>Relief Type Summary</h2></div>
                <div class="card-body">
                    <?php
                    $icons = ['Food'=>'🍚','Water'=>'💧','Medicine'=>'💊','Shelter'=>'🏕️'];
                    $colors = ['Food'=>'badge-food','Water'=>'badge-water','Medicine'=>'badge-medicine','Shelter'=>'badge-shelter'];
                    foreach ($type_counts as $t => $c):
                    ?>
                    <div style="display:flex; align-items:center; justify-content:space-between; padding: 14px 16px; border: 1px solid var(--border); border-radius: 10px; margin-bottom: 10px;">
                        <div style="display:flex; align-items:center; gap:10px; font-size:14px; font-weight:700;">
                            <span style="font-size:20px;"><?= $icons[$t] ?></span>
                            <?= $t ?> Requests
                        </div>
                        <span class="badge <?= $colors[$t] ?>" style="font-size:15px; padding:6px 14px;"><?= $c ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h2>Top Districts by Requests</h2></div>
                <div class="card-body">
                    <?php
                    $rows = [];
                    while ($d = mysqli_fetch_assoc($dist_data)) $rows[] = $d;
                    $dmax = !empty($rows) ? max(array_column($rows, 'total')) : 1;
                    if (empty($rows)):
                    ?>
                    <div style="text-align:center; padding:30px; color:var(--text-light); font-size:14px;">No data available</div>
                    <?php else: ?>
                    <?php foreach ($rows as $d): ?>
                    <div style="margin-bottom:12px;">
                        <div style="display:flex; justify-content:space-between; margin-bottom:4px; font-size:13px; font-weight:700;">
                            <span>📍 <?= htmlspecialchars($d['district']) ?></span>
                            <span style="color:var(--text-mid);"><?= $d['total'] ?></span>
                        </div>
                        <div style="background:#f1f5f9; border-radius:6px; height:8px; overflow:hidden;">
                            <div style="height:100%; width:<?= round($d['total']/$dmax*100) ?>%; background: linear-gradient(90deg, var(--primary), var(--primary-light)); border-radius:6px;"></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    </main>
</div>
</body>
</html>
