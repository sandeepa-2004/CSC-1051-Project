<?php
require_once '../includes/auth.php';
require_once '../includes/db.php';

require_login();
if (is_admin()) { header("Location: ../admin/dashboard.php"); exit(); }

$user_id = $_SESSION['user_id'];
$id = intval($_GET['id'] ?? 0);

$stmt = mysqli_prepare($conn, "DELETE FROM relief_requests WHERE id = ? AND user_id = ?");
mysqli_stmt_bind_param($stmt, "ii", $id, $user_id);
mysqli_stmt_execute($stmt);

header("Location: requests.php?deleted=1");
exit();
?>
