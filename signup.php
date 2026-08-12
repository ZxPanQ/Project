<?php
require_once 'db_connect.php';
$message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $confirm_password = mysqli_real_escape_string($conn, $_POST['confirm_password']);
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    
    if ($password !== $confirm_password) {
        $message = "Passwords do not match!";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        $sql = "INSERT INTO user (email, password, full_name) 
                VALUES ('$email', '$hashed_password', '$full_name')";
                
        if (mysqli_query($conn, $sql)) {
            header("Location: login.php?signup=success");
            exit();
        } else {
            $message = "Error creating account: " . mysqli_error($conn);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sign Up - Shop System</title>
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
        <a href="index.php" class="back-home">&larr; Back to Home</a>
        <h2>Create Staff Account</h2>
        <?php if (!empty($message)): ?>
            <div class="alert"><?php echo $message; ?></div>
        <?php endif; ?>
        <form method="POST" action="signup.php">
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="full_name" required>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>
            <div class="form-group">
                <label>Confirm Password</label>
                <input type="password" name="confirm_password" required>
            </div>
            <button type="submit" class="btn" style="width:100%;">Sign Up</button>
        </form>
        <p style="margin-top:15px; font-size:0.9rem;">Already have an account? <a href="login.php">Login here</a></p>
    </div>
</body>
</html>