<?php

require_once 'includes/auth.php';
require_once 'includes/db.php';


if (is_logged_in()) {
    header("Location: index.php");
    exit();
}

$error = "";


if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $full_name = isset($_POST['full_name']) ? trim($_POST['full_name']) : "";
    $email     = isset($_POST['email']) ? trim($_POST['email']) : "";
    $phone     = isset($_POST['phone']) ? trim($_POST['phone']) : "";
    $password  = isset($_POST['password']) ? trim($_POST['password']) : "";
    $confirm   = isset($_POST['confirm_password']) ? trim($_POST['confirm_password']) : "";


    if ($full_name == "" || $email == "" || $password == "") {

        $error = "Please fill in all required fields.";

    } elseif ($password != $confirm) {

        $error = "Passwords do not match.";

    } elseif (strlen($password) < 6) {

        $error = "Password must be at least 6 characters.";

    } else {

        
        $sql = "SELECT id FROM users WHERE email = ?";
        $check = mysqli_prepare($conn, $sql);

        if ($check) {

            mysqli_stmt_bind_param($check, "s", $email);
            mysqli_stmt_execute($check);
            mysqli_stmt_store_result($check);

            if (mysqli_stmt_num_rows($check) > 0) {

                $error = "This email is already registered.";

            } else {

                
                $insert_sql = "INSERT INTO users (full_name, email, phone, password, role)
                               VALUES (?, ?, ?, ?, 'user')";

                $stmt = mysqli_prepare($conn, $insert_sql);

                if ($stmt) {

                    mysqli_stmt_bind_param($stmt, "ssss", $full_name, $email, $phone, $password);

                    if (mysqli_stmt_execute($stmt)) {

                        
                        header("Location: index.php?registered=1");
                        exit();

                    } else {
                        $error = "Unable to create account. Please try again.";
                    }

                    mysqli_stmt_close($stmt);

                } else {
                    $error = "Database error. Please try later.";
                }
            }

            mysqli_stmt_close($check);

        } else {
            $error = "Database error. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account — Flood Relief Sri Lanka</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="auth-wrapper">
    <div class="auth-card">
        <div class="auth-logo">
    
            <h1>Flood Relief Management</h1>
            <p>Sri Lanka Emergency Response System</p>
        </div>
 <?php if ($error): ?>
            <div class="alert alert-error">⚠️ <?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        

        <h2 class="auth-title">Create your account</h2>
        <p class="auth-subtitle">Register to submit flood relief requests</p>

        <form method="POST">
            <div class="form-group">
                <label>Full Name *</label>
                <input type="text" name="full_name" placeholder="Saman Perera" value="<?= htmlspecialchars($_POST['full_name'] ?? '') ?>" required>
            </div>
            <div class="form-group">
                <label>Email Address *</label>
                <input type="email" name="email" placeholder="you@example.com" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
            </div>
            <div class="form-group">
                <label>Phone Number</label>
                <input type="tel" name="phone" placeholder="07X XXX XXXX" value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Password *</label>
                    <input type="password" name="password" placeholder="Min. 6 characters" required>
                </div>
                <div class="form-group">
                    <label>Confirm Password *</label>
                    <input type="password" name="confirm_password" placeholder="Repeat password" required>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Create Account →</button>
        </form>

        <div class="auth-link">
            Already have an account? <a href="index.php">Sign in</a>
        </div>
    </div>
</div>
</body>
</html>
