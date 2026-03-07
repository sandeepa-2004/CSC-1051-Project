
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
            <div class="logo-icon">🌊</div>
            <h1>Flood Relief Management</h1>
            <p>Sri Lanka Emergency Response System</p>
        </div>

        

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
