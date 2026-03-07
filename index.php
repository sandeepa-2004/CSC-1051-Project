

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
            <div class="logo-icon">🌊</div>
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