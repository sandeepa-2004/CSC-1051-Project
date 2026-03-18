<?php
require_once '../includes/auth.php';
require_once '../includes/db.php';

require_login();


if (is_admin()) {
    header("Location: ../admin/dashboard.php");
    exit();
}


$user_id = $_SESSION['user_id'];
$requests = mysqli_query(
    $conn, 
    "SELECT * FROM relief_requests WHERE user_id = $user_id ORDER BY created_at DESC"
);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Requests — Flood Relief</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
<div class="app-wrapper">

    <?php include 'sidebar.php'; ?>

    <main class="main-content">

        
        <div class="page-header">
            <h1>My Relief Requests</h1>
            
        </div>

        
        <?php if (isset($_GET['submitted'])): ?>
            <div class="alert alert-success">✅ Your request has been submitted successfully!</div>
        <?php endif; ?>
        <?php if (isset($_GET['updated'])): ?>
            <div class="alert alert-success">✅ Request updated successfully!</div>
        <?php endif; ?>
        <?php if (isset($_GET['deleted'])): ?>
            <div class="alert alert-success">✅ Request deleted.</div>
        <?php endif; ?>

        
        <div class="card">
            <div class="card-header">
                <h2>All Requests</h2>
                <a href="new_request.php" class="btn btn-primary btn-sm">+ New Request</a>
            </div>

            <div class="table-wrap">
                <?php if (mysqli_num_rows($requests) > 0): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>#ID</th>
                                <th>Type</th>
                                <th>District</th>
                                <th>GN Division</th>
                                <th>Family</th>
                                <th>Severity</th>
                                <th>Status</th>
                                <th>Submitted</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = mysqli_fetch_assoc($requests)): ?>
                                <tr>
                                    <td><strong>#<?= $row['id'] ?></strong></td>
                                    <td><span class="badge badge-<?= strtolower($row['relief_type']) ?>"><?= $row['relief_type'] ?></span></td>
                                    <td><?= htmlspecialchars($row['district']) ?></td>
                                    <td><?= htmlspecialchars($row['gn_division']) ?></td>
                                    <td>👨‍👩‍👧 <?= $row['family_members'] ?></td>
                                    <td><span class="badge badge-<?= strtolower($row['severity']) ?>"><?= $row['severity'] ?></span></td>
                                    <td><span class="badge badge-<?= strtolower($row['status']) ?>"><?= $row['status'] ?></span></td>
                                    <td><?= date('d M Y', strtotime($row['created_at'])) ?></td>
                                    <td>
                                        <div class="action-btns">
                                            <a href="edit_request.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm">✏️ Edit</a>
                                            <a href="delete_request.php?id=<?= $row['id'] ?>" class="btn btn-danger btn-sm"
                                               onclick="return confirm('Are you sure you want to delete this request?')">🗑️</a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    
                    <div class="empty-state">
                        <div class="empty-icon">📭</div>
                        <h3>No requests found</h3>
                        <p>You haven't submitted any relief requests yet</p>
                        <a href="new_request.php" class="btn btn-primary btn-sm">Submit Your First Request</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>

    </main>
</div>
</body>
</html>