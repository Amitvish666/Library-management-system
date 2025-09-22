<?php
// --- PHP LOGIN LOGIC ---
// This block should be at the very top of the file, before any HTML.

include('include/dbcon.php');

// Start the session only if it's not already active
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Variable to hold any login error messages
$error_message = '';

// Check if the form has been submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Use PREPARED STATEMENTS for security to prevent SQL Injection
    $stmt = mysqli_prepare($con, "SELECT admin_id, firstname, middlename, lastname, admin_type, password FROM admin WHERE username = ?");
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
    mysqli_stmt_close($stmt);

    // Verify the user exists and the password is correct
    // Note: This project uses plaintext passwords. In a real-world scenario, you should use password_hash() and password_verify().
    if ($row && $password === $row['password']) {
        // Login successful
        $_SESSION['id'] = $row['admin_id'];

        // Log the user's login activity
        $log_stmt = mysqli_prepare($con, "INSERT INTO user_log (firstname, middlename, lastname, admin_type, date_log) VALUES (?, ?, ?, ?, NOW())");
        mysqli_stmt_bind_param($log_stmt, "ssss", $row['firstname'], $row['middlename'], $row['lastname'], $row['admin_type']);
        mysqli_stmt_execute($log_stmt);
        mysqli_stmt_close($log_stmt);

        // Redirect to the home page
        header("location: home.php");
        exit();
    } else {
        // Login failed
        $error_message = "Invalid username or password. Please try again.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library Management System - Login</title>

    <!-- Bootstrap and Font Awesome -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="fonts/css/font-awesome.min.css" rel="stylesheet">
    
    <!-- Google Fonts for a more elegant look -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Merriweather:wght@400;700&family=Lato:wght@400;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --dark-wood: #4A3728;
            --classic-green: #5A8B5A;
            --parchment-text: #F0EAD6;
            --cream-bg: #FDFBF5;
            --dark-text: #4B4B4B;
        }

        html, body {
            height: 100%;
            margin: 0;
            font-family: 'Lato', sans-serif;
        }

        body {
            background-image: url('images/background.jpg');
            background-position: center center;
            background-repeat: no-repeat;
            background-size: cover;
            position: relative;
        }
        
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.4);
            z-index: 1;
        }

        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            z-index: 2;
            padding: 15px;
        }

        .login-card {
            width: 100%;
            max-width: 400px;
            background-color: rgba(255, 255, 255, 0.95);
            padding: 40px 30px;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            animation: fadeIn 0.8s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .login-header .icon {
            font-size: 50px;
            color: var(--classic-green);
        }

        .login-header h2 {
            font-family: 'Merriweather', serif;
            color: var(--dark-text);
            margin-top: 15px;
            font-weight: 700;
        }
        
        .form-control {
            height: 45px;
            border-radius: 25px;
            padding-left: 50px;
            border: 1px solid #ddd;
        }
        
        .input-group-addon {
            position: absolute;
            left: 1px;
            top: 1px;
            bottom: 1px;
            width: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: transparent;
            border: none;
            color: #aaa;
            font-size: 16px;
        }
        
        .input-group {
            position: relative;
        }

        .btn-login {
            background-color: var(--classic-green);
            border-color: var(--classic-green);
            color: white;
            padding: 10px;
            border-radius: 25px;
            font-weight: bold;
            letter-spacing: 0.5px;
            transition: background-color 0.3s ease;
        }

        .btn-login:hover {
            background-color: #4e7a4e;
            border-color: #4e7a4e;
            color: white;
        }
        
        .login-footer {
            text-align: center;
            margin-top: 20px;
        }
        
        .login-footer a {
            color: var(--dark-text);
            font-weight: bold;
        }
        
        .alert {
            border-radius: 5px;
        }
        
        .login-footer-text {
            position: absolute;
            bottom: 10px;
            width: 100%;
            text-align: center;
            color: rgba(255, 255, 255, 0.7);
            font-family: 'Georgia', serif;
            z-index: 2;
        }

    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <i class="fa fa-university icon"></i>
                <h2>Library Portal</h2>
            </div>
            
            <form method="post" action="index.php">
                
                <!-- Display Error Message -->
                <?php if (!empty($error_message)): ?>
                    <div class="alert alert-danger" role="alert">
                        <?php echo htmlspecialchars($error_message); ?>
                    </div>
                <?php endif; ?>

                <div class="form-group input-group">
                    <span class="input-group-addon"><i class="fa fa-user"></i></span>
                    <input type="text" class="form-control" name="username" placeholder="Username" required autofocus>
                </div>
                
                <div class="form-group input-group">
                    <span class="input-group-addon"><i class="fa fa-lock"></i></span>
                    <input type="password" class="form-control" name="password" placeholder="Password" required>
                </div>

                <div class="form-group">
                    <button class="btn btn-primary btn-block btn-login" type="submit" name="login">SIGN IN</button>
                </div>

                <div class="login-footer">
                    <a href="forgot_password.php">Forgot Password?</a>
                </div>
            </form>
        </div>
    </div>
    
    <div class="login-footer-text">
        <p>© <?php echo date('Y'); ?> Library Management System</p>
    </div>

    <!-- JavaScript files -->
    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
</body>
</html>