
<?php

require_once 'includes/auth.php';
require_once 'includes/db.php';

if (is_logged_in()) {

    if (is_admin()) {
        header("Location: admin/dashboard.php");
    } else {
        header("Location: user/dashboard.php");
    }

    exit();
}

$error = "";


if ($_SERVER["REQUEST_METHOD"] == "POST") {

    
    $email = isset($_POST['email']) ? trim($_POST['email']) : "";
    $password = isset($_POST['password']) ? trim($_POST['password']) : "";

    
    if ($email == "" || $password == "") {

        $error = "Please fill in all fields.";

    } else {

        
        $sql = "SELECT id, full_name, email, password, role FROM users WHERE email = ?";
        $stmt = mysqli_prepare($conn, $sql);

        if ($stmt) {

            mysqli_stmt_bind_param($stmt, "s", $email);
            mysqli_stmt_execute($stmt);

            $result = mysqli_stmt_get_result($stmt);
            $user = mysqli_fetch_assoc($result);

    
            if ($user) {

                if ($password === $user['password']) {

                
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_name'] = $user['full_name'];
                    $_SESSION['role'] = $user['role'];

                    
                    if ($user['role'] == "admin") {
                        header("Location: admin/dashboard.php");
                    } else {
                        header("Location: user/dashboard.php");
                    }

                    exit();

                } else {
                    $error = "Invalid email or password.";
                }

            } else {
                $error = "No account found with that email.";
            }

            mysqli_stmt_close($stmt);

        } else {
            $error = "Something went wrong. Please try again later.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Sign In — Flood Relief Sri Lanka</title>
<link rel="stylesheet" href="style.css">
</head>

<body>

<div class="auth-wrapper">

    <div class="auth-card">

        <div class="auth-logo">
           
            <h1>Flood Relief Management</h1>
            <p>Sri Lanka Emergency Response System</p>
        </div>

        <?php if (!empty($error)) { ?>
            <div class="alert alert-error">
                ⚠️ <?php echo htmlspecialchars($error); ?>
            </div>
        <?php } ?>

        <?php if (isset($_GET['registered'])) { ?>
            <div class="alert alert-success">
                ✅ Account created successfully. Please sign in.
            </div>
        <?php } ?>

        <h2 class="auth-title">Welcome back</h2>
        <p class="auth-subtitle">Sign in to access your account</p>

        <form method="POST">

            <div class="form-group">
                <label>Email Address</label>
                <input 
                    type="email"
                    name="email"
                    placeholder="you@example.com"
                    value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                    required
                >
            </div>

            <div class="form-group">
                <label>Password</label>
                <input 
                    type="password"
                    name="password"
                    placeholder="••••••••"
                    required
                >
            </div>

            <button type="submit" class="btn btn-primary">
                Sign In →
            </button>

        </form>

        <div class="auth-link">
            Don't have an account?
            <a href="register.php">Create one</a>
        </div>

        

    </div>

</div>

</body>
</html>