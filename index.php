<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Registrar (SIS)</title>
    <!-- Google Fonts for modern typography -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }
        
        body {
            /* Full viewport height and width */
            height: 100vh;
            width: 100vw;
            display: flex;
            align-items: center;
            justify-content: center;
            
            /* Background image with cover sizing */
            background: url('img/log.webp') no-repeat center center fixed;
            background-size: cover;
            position: relative;
        }

        /* Dark overlay to make text more readable and increase contrast */
        .overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5); /* 50% opacity black */
            z-index: 1;
        }

        /* Glassmorphism style for the login box */
        .login-container {
            position: relative;
            z-index: 2;
            background: rgba(255, 255, 255, 0.1); /* Transparent white */
            backdrop-filter: blur(12px); /* Blur effect to background behind */
            -webkit-backdrop-filter: blur(12px); /* For Safari */
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 3rem;
            border-radius: 24px;
            width: 100%;
            max-width: 440px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.3);
            text-align: center;
            color: #ffffff;
            animation: fadeIn 0.8s ease-out;
        }

        /* Simple entrance animation */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .login-container h1 {
            font-size: 2.2rem;
            margin-bottom: 0.5rem;
            font-weight: 700;
            letter-spacing: -0.5px;
        }

        .login-container p {
            font-size: 0.95rem;
            margin-bottom: 2.5rem;
            color: rgba(255, 255, 255, 0.8);
            font-weight: 300;
        }

        .input-group {
            margin-bottom: 1.5rem;
            text-align: left;
        }

        .input-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-size: 0.85rem;
            font-weight: 500;
            color: rgba(255, 255, 255, 0.95);
            letter-spacing: 0.3px;
        }

        .input-group input {
            width: 100%;
            padding: 0.85rem 1.2rem;
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            background: rgba(0, 0, 0, 0.2); /* Slightly dark inputs */
            color: white;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .input-group input::placeholder {
            color: rgba(255, 255, 255, 0.5);
        }

        .input-group input:focus {
            outline: none;
            border-color: #6ee7b7; /* Subtle green/teal border on focus */
            background: rgba(0, 0, 0, 0.4);
            box-shadow: 0 0 0 4px rgba(110, 231, 183, 0.15);
        }

        .btn-submit {
            width: 100%;
            padding: 1rem;
            border-radius: 12px;
            border: none;
            /* Modern vibrant gradient button */
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            font-size: 1.05rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 1rem;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(16, 185, 129, 0.4);
            background: linear-gradient(135deg, #34d399 0%, #10b981 100%);
        }

        .btn-submit:active {
            transform: translateY(0);
            box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3);
        }
        
        .footer-links {
            margin-top: 2rem;
            font-size: 0.85rem;
            display: flex;
            justify-content: center;
            gap: 1.5rem;
        }

        .footer-links a {
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            transition: color 0.3s ease;
            font-weight: 500;
        }

        .footer-links a:hover {
            color: #ffffff;
        }

    </style>
</head>
<body>
    <div class="overlay"></div>
    <div class="login-container">
        <h1>Welcome</h1>
        <p>Student Information System</p>
        
        <?php if(isset($_GET['error'])): ?>
            <div style="background: rgba(239, 68, 68, 0.2); border: 1px solid #ef4444; padding: 10px; border-radius: 8px; margin-bottom: 1rem; color: #fca5a5; font-size: 0.85rem;">
                Invalid username or password. Please try again.
            </div>
        <?php endif; ?>
        
        <form action="registrar/api/auth.php" method="POST">
            <div class="input-group">
                <label for="username">Username / Student ID</label>
                <input type="text" id="username" name="username" placeholder="Enter your ID or username" required autocomplete="username">
            </div>
            
            <div class="input-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" required autocomplete="current-password">
            </div>
            
            <button type="submit" class="btn-submit">Sign In</button>
            <a href="register.php" style="display: block; margin-top: 1rem; color: rgba(255, 255, 255, 0.8); text-decoration: none; font-size: 0.9rem; font-weight: 500; transition: color 0.3s;" onmouseover="this.style.color='#ffffff'" onmouseout="this.style.color='rgba(255, 255, 255, 0.8)'">Create a New Account</a>
        </form>
        
        <div class="footer-links">
            <a href="#">Forgot Password?</a>
            <a href="#">Contact Support</a>
        </div>
    </div>
</body>
</html>
