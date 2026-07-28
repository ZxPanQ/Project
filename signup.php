<?php
require_once 'db_connect.php';
$message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $role = mysqli_real_escape_string($conn, $_POST['role']);
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $sql = "INSERT INTO user (username, password, full_name, role) 
            VALUES ('$username', '$hashed_password', '$full_name', '$role')";
    if (mysqli_query($conn, $sql)) {
        header("Location: login.php?signup=success");
        exit();
    } else {
        $message = "Error creating account: " . mysqli_error($conn);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sign Up - Shop System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="auth-card">
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
                <label>Username</label>
                <input type="text" name="username" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>
            <div class="form-group">
                <label>Role</label>
                <select name="role">
                    <option value="staff">Staff</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
            <button type="submit" class="btn" style="width:100%;">Sign Up</button>
        </form>
        <p style="margin-top:15px; font-size:0.9rem;">Already have an account? <a href="login.php">Login here</a></p>
    </div>
</body>
</html>
