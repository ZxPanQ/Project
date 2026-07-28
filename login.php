<?php
session_start();
require_once 'db_connect.php';
$message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];
    $sql = "SELECT * FROM user WHERE username = '$username'";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);
        
        // Verify hashed password
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['full_name'] = $user['full_name'];
            
            header("Location: dashboard.php");
            exit();
        } else {
            $message = "Invalid password.";
        }
    } else {
        $message = "Username not found.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - Shop System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="auth-card">
        <h2>Account Login</h2>
        <?php if (isset($_GET['signup']) && $_GET['signup'] == 'success'): ?>
            <div class="alert" style="background-color: #d4edda; color: #155724;">Account created! You can now log in.</div>
        <?php endif; ?>
        <?php if (!empty($message)): ?>
            <div class="alert"><?php echo $message; ?></div>
        <?php endif; ?>
        <form method="POST" action="login.php">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" required>
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
