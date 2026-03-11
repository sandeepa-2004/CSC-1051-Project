<?php

require_once '../includes/auth.php';
require_once '../includes/db.php';


require_login();
require_admin();


$sql = "
    SELECT u.*, COUNT(r.id) AS request_count
    FROM users u
    LEFT JOIN relief_requests r ON u.id = r.user_id
    WHERE u.role = 'user'
    GROUP BY u.id
    ORDER BY u.created_at DESC
";

$users_result = mysqli_query($conn, $sql);


$view_user = null;
$user_requests = null;

if (isset($_GET['view'])) {

    $user_id = intval($_GET['view']);

    
    $stmt = mysqli_prepare(
        $conn,
        "SELECT * FROM users WHERE id = ? AND role = 'user'"
    );

    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $view_user = mysqli_fetch_assoc($result);

    
    if ($view_user) {

        $req_sql = "
            SELECT *
            FROM relief_requests
            WHERE user_id = ?
            ORDER BY created_at DESC
        ";

        $req_stmt = mysqli_prepare($conn, $req_sql);
        mysqli_stmt_bind_param($req_stmt, "i", $user_id);
        mysqli_stmt_execute($req_stmt);

        $user_requests = mysqli_stmt_get_result($req_stmt);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Users — Admin</title>
<link rel="stylesheet" href="../style.css">
</head>

<body>

<div class="app-wrapper">

<?php include 'sidebar.php'; ?>

<main class="main-content">

<div class="page-header">
    <h1>Registered Users</h1>
    
</div>

<?php if (isset($_GET['deleted'])) { ?>
    <div class="alert alert-success">
        ✅ User has been removed from the system.
    </div>
<?php } ?>

<div class="card">

<div class="card-header">
    <h2>All Users</h2>
    <span style="font-size:13px; color:var(--text-mid);">
        Total:
        <strong><?php echo mysqli_num_rows($users_result); ?></strong>
        users
    </span>
</div>

<div class="table-wrap">

<?php if (mysqli_num_rows($users_result) > 0) { ?>

<table>

<thead>
<tr>
    <th>#ID</th>
    <th>Name</th>
    <th>Email</th>
    <th>Phone</th>
    <th>Requests</th>
    <th>Registered</th>
    <th>Actions</th>
</tr>
</thead>

<tbody>

<?php while ($user = mysqli_fetch_assoc($users_result)) {

$id      = $user['id'];
$name    = htmlspecialchars($user['full_name']);
$email   = htmlspecialchars($user['email']);
$phone   = htmlspecialchars($user['phone'] ?: '—');
$requests = $user['request_count'];
$date    = date("d M Y", strtotime($user['created_at']));
$initial = strtoupper(substr($user['full_name'],0,1));

?>

<tr>

<td><strong>#<?php echo $id; ?></strong></td>

<td>
<div style="display:flex; align-items:center; gap:10px;">
    <div class="user-avatar"
        style="width:34px;height:34px;font-size:13px;background:var(--primary);">
        <?php echo $initial; ?>
    </div>

    <?php echo $name; ?>
</div>
</td>

<td><?php echo $email; ?></td>

<td><?php echo $phone; ?></td>

<td>
<span class="badge <?php echo $requests > 0 ? 'badge-user' : 'badge-pending'; ?>">
    <?php echo $requests; ?> requests
</span>
</td>

<td><?php echo $date; ?></td>

<td>
<div class="action-btns">

<a href="?view=<?php echo $id; ?>"
   class="btn btn-secondary btn-sm">
   👁️ View
</a>

<a href="delete_user.php?id=<?php echo $id; ?>"
   class="btn btn-danger btn-sm"
   onclick="return confirm('Delete this user and all their requests?')">
   🗑️ Delete
</a>

</div>
</td>

</tr>

<?php } ?>

</tbody>
</table>

<?php } else { ?>

<div class="empty-state">
    <div class="empty-icon">👥</div>
    <h3>No users registered yet</h3>
    <p>Users will appear here once they sign up</p>
</div>

<?php } ?>

</div>
</div>

</main>
</div>


<?php if ($view_user) { ?>

<div class="modal-overlay open">

<div class="modal" style="max-width:700px;">

<div class="modal-header">
<h3>
👤 User Profile —
<?php echo htmlspecialchars($view_user['full_name']); ?>
</h3>

<a href="users.php" class="modal-close">✕</a>
</div>

<div class="modal-body">

<?php
$avatar = strtoupper(substr($view_user['full_name'],0,1));
$reg_date = date("d M Y", strtotime($view_user['created_at']));
?>

<div style="display:flex;align-items:center;gap:16px;margin-bottom:24px;padding:16px;background:var(--primary-pale);border-radius:12px;">

<div class="user-avatar"
     style="width:56px;height:56px;font-size:22px;font-weight:800;flex-shrink:0;">
<?php echo $avatar; ?>
</div>

<div>
<h3 style="font-size:18px;">
<?php echo htmlspecialchars($view_user['full_name']); ?>
</h3>

<p style="color:var(--text-mid);font-size:13px;">
Registered <?php echo $reg_date; ?>
</p>
</div>

</div>

<div class="detail-grid">

<div class="detail-item">
<label>Email</label>
<p><?php echo htmlspecialchars($view_user['email']); ?></p>
</div>

<div class="detail-item">
<label>Phone</label>
<p><?php echo htmlspecialchars($view_user['phone'] ?: 'Not provided'); ?></p>
</div>

<div class="detail-item">
<label>Account Role</label>
<p><span class="badge badge-user">User</span></p>
</div>

<div class="detail-item">
<label>Total Requests</label>
<p><?php echo mysqli_num_rows($user_requests); ?></p>
</div>

</div>

<?php if (mysqli_num_rows($user_requests) > 0) { ?>

<h4 style="font-size:14px;font-weight:700;color:var(--primary);margin-bottom:12px;text-transform:uppercase;">
Relief Requests
</h4>

<div class="table-wrap"
     style="border:1px solid var(--border);border-radius:10px;overflow:hidden;">

<table>

<thead>
<tr>
<th>#</th>
<th>Type</th>
<th>District</th>
<th>Severity</th>
<th>Family</th>
<th>Status</th>
</tr>
</thead>

<tbody>

<?php while ($r = mysqli_fetch_assoc($user_requests)) { ?>

<tr>

<td><?php echo $r['id']; ?></td>

<td>
<span class="badge badge-<?php echo strtolower($r['relief_type']); ?>">
<?php echo $r['relief_type']; ?>
</span>
</td>

<td><?php echo htmlspecialchars($r['district']); ?></td>

<td>
<span class="badge badge-<?php echo strtolower($r['severity']); ?>">
<?php echo $r['severity']; ?>
</span>
</td>

<td><?php echo $r['family_members']; ?></td>

<td>
<span class="badge badge-<?php echo strtolower($r['status']); ?>">
<?php echo $r['status']; ?>
</span>
</td>

</tr>

<?php } ?>

</tbody>
</table>

</div>

<?php } else { ?>

<p style="color:var(--text-mid);font-size:14px;text-align:center;padding:20px;background:#f8fafc;border-radius:10px;">
No relief requests submitted yet.
</p>

<?php } ?>

</div>

<div class="modal-footer">

<a href="delete_user.php?id=<?php echo $view_user['id']; ?>"
   class="btn btn-danger"
   onclick="return confirm('Delete this user?')">
   🗑️ Delete User
</a>

<a href="users.php" class="btn btn-secondary">
Close
</a>

</div>

</div>
</div>

<?php } ?>

</body>
</html>