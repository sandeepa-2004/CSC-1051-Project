<?php
require_once '../includes/auth.php';
require_once '../includes/db.php';

require_login();
require_admin();

$id = intval($_GET['id'] ?? 0);

if ($id == $_SESSION['user_id']) {
    header("Location: users.php?error=self");
    exit();
}

$stmt = mysqli_prepare($conn, "DELETE FROM users WHERE id = ? AND role = 'user'");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

header("Location: users.php?deleted=1");
exit();
?>
