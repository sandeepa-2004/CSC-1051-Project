<?php
require_once '../includes/auth.php';
require_once '../includes/db.php';


require_login();


if (is_admin()) {
    header("Location: ../admin/dashboard.php");
    exit();
}

$user_id = $_SESSION['user_id'];

function getUserCount($conn, $user_id, $extra_condition = "")
{
    $sql = "SELECT COUNT(*) AS c 
            FROM relief_requests 
            WHERE user_id = $user_id $extra_condition";

    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);

    return $row['c'] ?? 0;
}


$total   = getUserCount($conn, $user_id);
$pending = getUserCount($conn, $user_id, "AND status = 'Pending'");
$high    = getUserCount($conn, $user_id, "AND severity = 'High'");


$recent_sql = "
    SELECT *
    FROM relief_requests
    WHERE user_id = $user_id
    ORDER BY created_at DESC
    LIMIT 5
";

$recent = mysqli_query($conn, $recent_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard — Flood Relief</title>
<link rel="stylesheet" href="../style.css">
</head>

<body>

<div class="app-wrapper">

    <?php include 'sidebar.php'; ?>

    <main class="main-content">

       
        <div class="page-header">
            <h1>
                Hello, <?= htmlspecialchars(explode(' ', $_SESSION['user_name'])[0]) ?> 👋
            </h1>
            <p>Here's an overview of your relief requests</p>
        </div>

        
        <?php if (isset($_GET['deleted'])): ?>
            <div class="alert alert-success">
                ✅ Request deleted successfully.
            </div>
        <?php endif; ?>

        
        <div class="stats-grid">

            <div class="stat-card green">
                <div class="stat-icon">📋</div>
                <div class="stat-value"><?= $total ?></div>
                <div class="stat-label">Total Requests</div>
            </div>

            <div class="stat-card amber">
                <div class="stat-icon">⏳</div>
                <div class="stat-value"><?= $pending ?></div>
                <div class="stat-label">Pending</div>
            </div>

            <div class="stat-card red">
                <div class="stat-icon">🚨</div>
                <div class="stat-value"><?= $high ?></div>
                <div class="stat-label">High Severity</div>
            </div>

        </div>


        
        <div class="card">

            <div class="card-header">
                <h2>Recent Requests</h2>
                <a href="new_request.php" class="btn btn-primary btn-sm">
                    + New Request
                </a>
            </div>

            <div class="table-wrap">

                <?php if (mysqli_num_rows($recent) > 0): ?>

                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Type</th>
                            <th>District</th>
                            <th>Severity</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>

                    <tbody>

                    <?php while ($row = mysqli_fetch_assoc($recent)): ?>

                        <tr>

                            <td><?= $row['id'] ?></td>

                            <td>
                                <span class="badge badge-<?= strtolower($row['relief_type']) ?>">
                                    <?= $row['relief_type'] ?>
                                </span>
                            </td>

                            <td><?= htmlspecialchars($row['district']) ?></td>

                            <td>
                                <span class="badge badge-<?= strtolower($row['severity']) ?>">
                                    <?= $row['severity'] ?>
                                </span>
                            </td>

                            <td>
                                <span class="badge badge-<?= strtolower($row['status']) ?>">
                                    <?= $row['status'] ?>
                                </span>
                            </td>

                            <td>
                                <?= date('d M Y', strtotime($row['created_at'])) ?>
                            </td>

                            <td>

                                <div class="action-btns">

                                    <a href="edit_request.php?id=<?= $row['id'] ?>" 
                                       class="btn btn-warning btn-sm">
                                       ✏️ Edit
                                    </a>

                                    <a href="delete_request.php?id=<?= $row['id'] ?>" 
                                       class="btn btn-danger btn-sm"
                                       onclick="return confirm('Delete this request?')">
                                       🗑️ Delete
                                    </a>

                                </div>

                            </td>

                        </tr>

                    <?php endwhile; ?>

                    </tbody>
                </table>

                <?php else: ?>

                
                <div class="empty-state">

                    <div class="empty-icon">📭</div>

                    <h3>No requests yet</h3>

                    <p>Submit your first relief request to get started</p>

                    <a href="new_request.php" class="btn btn-primary btn-sm">
                        Submit Request
                    </a>

                </div>

                <?php endif; ?>

            </div>

        
            <?php if ($total > 5): ?>

            <div style="padding:16px 24px;border-top:1px solid var(--border);">

                <a href="requests.php"
                   style="color:var(--primary);
                          font-weight:700;
                          font-size:14px;
                          text-decoration:none;">

                    View all requests →

                </a>

            </div>

            <?php endif; ?>

        </div>

    </main>

</div>

</body>
</html>