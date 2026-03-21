<?php
session_start();
require_once 'registrar/api/db_connection.php';

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        // 1. Check if username or email already exists in student_accounts
        $stmt = $conn->prepare("SELECT id FROM student_accounts WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            $error = "An account with this Username or Email already exists.";
        } else {
            // 2. Try to find student_id by email in student_contacts
            $student_id = null;
            $stmt = $conn->prepare("SELECT student_id FROM student_contacts WHERE email = ? LIMIT 1");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $link_res = $stmt->get_result();
            if ($row = $link_res->fetch_assoc()) {
                $student_id = $row['student_id'];
            }

            // 3. Create account
            $hashed_pw = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO student_accounts (username, email, password, student_id) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $username, $email, $hashed_pw, $student_id);
            if ($stmt->execute()) {
                $success = "Account created successfully! You can now sign in.";
            } else {
                $error = "Error creating account. Please try again later.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Student Information System</title>
    <!-- Google Fonts for modern typography -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', sans-serif; }
        body { height: 100vh; width: 100vw; display: flex; align-items: center; justify-content: center; background: url('img/log.webp') no-repeat center center fixed; background-size: cover; position: relative; }
        .overlay { position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0, 0, 0, 0.5); z-index: 1; }
        .login-container { position: relative; z-index: 2; background: rgba(255, 255, 255, 0.1); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.2); padding: 3rem; border-radius: 24px; width: 100%; max-width: 480px; box-shadow: 0 15px 35px rgba(0, 0, 0, 0.3); text-align: center; color: #ffffff; animation: fadeIn 0.8s ease-out; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        .login-container h1 { font-size: 2rem; margin-bottom: 0.5rem; font-weight: 700; letter-spacing: -0.5px; }
        .login-container p { font-size: 0.95rem; margin-bottom: 2rem; color: rgba(255, 255, 255, 0.8); font-weight: 300; }
        .input-group { margin-bottom: 1.2rem; text-align: left; }
        .input-group label { display: block; margin-bottom: 0.4rem; font-size: 0.85rem; font-weight: 500; color: rgba(255, 255, 255, 0.95); }
        .input-group input { width: 100%; padding: 0.8rem 1.1rem; border-radius: 12px; border: 1px solid rgba(255, 255, 255, 0.3); background: rgba(0, 0, 0, 0.2); color: white; transition: all 0.3s ease; }
        .input-group input:focus { outline: none; border-color: #6ee7b7; background: rgba(0, 0, 0, 0.4); box-shadow: 0 0 0 4px rgba(110, 231, 183, 0.15); }
        .btn-submit { width: 100%; padding: 0.9rem; border-radius: 12px; border: none; background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; font-size: 1.05rem; font-weight: 600; cursor: pointer; transition: all 0.3s ease; margin-top: 1rem; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3); }
        .btn-submit:hover { transform: translateY(-2px); box-shadow: 0 6px 15px rgba(16, 185, 129, 0.4); }
        .alert { padding: 10px; border-radius: 8px; margin-bottom: 1rem; font-size: 0.85rem; border: 1px solid; }
        .alert-error { background: rgba(239, 68, 68, 0.2); border-color: #ef4444; color: #fca5a5; }
        .alert-success { background: rgba(16, 185, 129, 0.2); border-color: #10b981; color: #6ee7b7; }
        .back-link { display: block; margin-top: 1.5rem; color: rgba(255, 255, 255, 0.7); text-decoration: none; font-size: 0.85rem; transition: color 0.3s; }
        .back-link:hover { color: white; }
    </style>
</head>
<body>
    <div class="overlay"></div>
    <div class="login-container">
        <h1>Create Account</h1>
        <p>Join the Student Information System</p>
        
        <?php if($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <form action="" method="POST" <?php if($success) echo 'style="display:none;"'; ?>>
            <div class="input-group">
                <label for="username">Choose Username</label>
                <input type="text" id="username" name="username" placeholder="e.g. johndoe123" required>
            </div>

            <div class="input-group">
                <label for="email">Student Email</label>
                <input type="email" id="email" name="email" placeholder="e.g. john@email.com" required>
            </div>
            
            <div class="input-group">
                <label for="password">Set Password</label>
                <input type="password" id="password" name="password" placeholder="Choose a password" required>
            </div>
            
            <div class="input-group">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" placeholder="Repeat your password" required>
            </div>
            
            <button type="submit" class="btn-submit">Create Account</button>
        </form>
        
        <a href="index.php" class="back-link">← Already have an account? Sign In</a>
    </div>
</body>
</html>
