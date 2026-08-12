<?php
session_start();
require_once 'db_connect.php';
$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    $sql = "SELECT * FROM user WHERE email = '$email'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);
        
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id']   = $user['user_id'];
            $_SESSION['email']     = $user['email'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['role']      = $user['role'];
            
            header("Location: dashboard.php");
            exit();
        } else {
            $message = "Invalid password.";
        }
    } else {
        $message = "Email address not found.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - Shop System</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .back-home {
            display: inline-block;
            margin-bottom: 15px;
            color: #666;
            text-decoration: none;
            font-size: 0.9rem;
            transition: color 0.2s;
        }
        .back-home:hover {
            color: #2ecc71;
        }
    </style>
</head>
<body>
    <div class="auth-card">
        <a href="landing.php" class="back-home">&larr; Back to Home</a>
        <h2>Account Login</h2>
        <?php if (isset($_GET['signup']) && $_GET['signup'] == 'success'): ?>
            <div class="alert" style="background-color: #d4edda; color: #155724;">Account created! You can now log in.</div>
        <?php endif; ?>
        <?php if (!empty($message)): ?>
            <div class="alert"><?php echo $message; ?></div>
        <?php endif; ?>
        <form method="POST" action="login.php">
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>
            <button type="submit" class="btn" style="width:100%;">Login</button>
        </form>
        <p style="margin-top:15px; font-size:0.9rem;">Need an account? <a href="signup.php">Sign Up</a></p>
    </div>
</body>
</html>