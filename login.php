<?php
session_start();
include 'db_connection.php';
include 'includes/error_handler.php';

// If user is already logged in, redirect to home page
if (isset($_SESSION['user_id'])) {
    header("Location: homelogin.php");
    exit();
}

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $email = $_POST['email'];
        $password = $_POST['password'];
        
        // Validate inputs
        if (empty($email)) {
            handle_validation_error("Email address is required", "email", "login.php");
        }
        
        if (empty($password)) {
            handle_validation_error("Password is required", "password", "login.php");
        }
        
        // Verify login
        $sql = "SELECT * FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);
        
        if (!$stmt) {
            handle_db_error("Database error occurred", $conn, "login.php");
        }
        
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            // Verify password using password_verify for bcrypt
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['first_name'] = $user['first_name'];
                $_SESSION['last_name'] = $user['last_name'];
                $_SESSION['user_type'] = $user['user_type'];
                
                // Get organization information
                $org_query = "SELECT o.organization_id, o.name as org_name 
                             FROM users u 
                             LEFT JOIN organizations o ON u.organization_id = o.organization_id 
                             WHERE u.user_id = ?";
                $org_stmt = $conn->prepare($org_query);
                
                if (!$org_stmt) {
                    log_error("Failed to prepare organization query", ERROR_DATABASE, ['error' => $conn->error]);
                    // Continue despite error - organization info is not critical
                } else {
                    $org_stmt->bind_param("i", $user['user_id']);
                    $org_stmt->execute();
                    $org_result = $org_stmt->get_result();
                    
                    if ($org_result->num_rows > 0) {
                        $org_data = $org_result->fetch_assoc();
                        $_SESSION['organization_id'] = $org_data['organization_id'];
                        $_SESSION['org_name'] = $org_data['org_name'];
                        $_SESSION['has_organization'] = true;
                    } else {
                        $_SESSION['organization_id'] = null;
                        $_SESSION['org_name'] = null;
                        $_SESSION['has_organization'] = false;
                    }
                }
                
                // Log successful login
                log_error("User logged in successfully: " . $user['email'], "auth", ['user_id' => $user['user_id']]);
                
                header("Location: homelogin.php");
                exit();
            } else {
                handle_error("Invalid email or password", ERROR_AUTH, "login.php");
            }
        } else {
            handle_error("Invalid email or password", ERROR_AUTH, "login.php");
        }
    } catch (Exception $e) {
        handle_error("An unexpected error occurred: " . $e->getMessage(), ERROR_GENERAL, "login.php", 
            ['exception' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - MDX</title>
    <link rel="stylesheet" href="assets/css/error_styles.css">
    <link rel="stylesheet" href="assets/css/login_responsive.css">
    <!-- Add Font Awesome for the menu icon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <?php include 'includes/background_styles.php'; ?>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            /* Background styles moved to background.css */
        }
        
        /* New navbar styles with mdx- prefix and !important flags */
        .mdx-navbar-new {
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            padding: 10px 5% !important;
            padding-left: 30px !important;
            background-color: #0099ff !important;
            color: #cfd9ff !important;
            border-radius: 20px !important;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2) !important;
            position: relative !important;
            margin: 10px 0 !important;
            backdrop-filter: blur(10px) !important;
            max-width: 1200px !important;
            width: 100% !important;
            margin-top: 30px !important;
            margin-left: auto !important;
            margin-right: auto !important;
            font-weight: bold !important;
            z-index: 1000 !important;
        }
        
        .mdx-logo {
            display: flex !important;
            align-items: center !important;
            flex: 0 0 auto !important;
        }
        
        .mdx-logo img {
            height: auto !important;
            width: 80px !important;
            max-width: 100% !important;
            margin-right: 15px !important;
        }
        
        .mdx-nav-links {
            display: flex !important;
            align-items: center !important;
        }
        
        .mdx-nav-links a {
            color: white !important;
            margin-left: 20px !important;
            text-decoration: none !important;
            font-size: 18px !important;
            font-weight: bold !important;
            transition: transform 0.3s ease !important;
        }
        
        .mdx-nav-links a:hover {
            transform: scale(1.2) !important;
        }
        
        /* Mobile menu toggle button */
        .mdx-menu-toggle {
            display: none !important;
            background: none !important;
            border: none !important;
            color: white !important;
            font-size: 24px !important;
            cursor: pointer !important;
            padding: 0 !important;
            margin-right: 15px !important;
            z-index: 1001 !important;
            padding-left: 18px !important;
            padding-bottom: 2px !important;
        }
        
        /* Responsive styles for the navbar */
        @media screen and (max-width: 768px) {
            .mdx-navbar-new {
                padding: 10px !important;
                border-radius: 15px !important;
                width: 90% !important;
                max-width: 90% !important;
                position: relative !important;
            }
            
            .mdx-menu-toggle {
                display: flex !important;
                position: absolute !important;
                right: 15px !important;
                top: 50% !important;
                transform: translateY(-50%) !important;
                z-index: 1002 !important;
                align-items: center !important;
                justify-content: center !important;
                width: 40px !important;
                height: 40px !important;
            }
            
            .mdx-logo img {
                width: 50px !important;
                margin-right: 12px !important;
            }
            
            .mdx-nav-links {
                position: absolute !important;
                top: 100% !important;
                left: 0 !important;
                right: 0 !important;
                flex-direction: column !important;
                background-color: #0099ff !important;
                padding: 10px 0 !important;
                border-radius: 0 0 15px 15px !important;
                box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2) !important;
                display: none !important;
                z-index: 9999 !important;
            }
            
            .mdx-nav-links.active {
                display: flex !important;
            }
            
            .mdx-nav-links a {
                width: 100% !important;
                text-align: center !important;
                padding: 10px 0 !important;
                margin: 10px 0 !important;
            }
        }

        @media screen and (max-width: 480px) {
            .mdx-navbar-new {
                padding: 8px 10px !important;
            }
            
            .mdx-logo img {
                width: 40px !important;
            }
        }
        
        .container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: calc(100vh - 100px); /* Adjust to fill remaining viewport height */
            padding-top: 0; /* Remove any top padding */
        }
        .login-container {
            margin-top: 0; /* Remove top margin */
            margin-bottom: 0; /* Remove bottom margin */
            background: white;
            padding: 30px 20px; /* Increase vertical padding slightly */
            border-radius: 15px;
            box-shadow: 5px 5px 10px rgba(0, 0, 0, 0.2);
            width: 350px;
            text-align: center;
            position: relative; /* Add position relative */
            top: -100px; /* Move up slightly to visually center better */
        }
        .logo-container img {
            background-color:#0099ff;
            border-radius: 15px;
            padding: 10px;
            width: 80px;
        }
        .input-container {
            display: flex;
            align-items: center;
            background-color: white;
            box-shadow: 5px 5px 10px rgba(0, 0, 0, 0.2);
            border-radius: 10px;
            padding: 10px;
            margin: 10px 0;
        }
        .input-container img {
            width: 20px;
            margin-right: 10px;
        }
        .input-container input {
            border: none;
            background: none;
            outline: none;
            width: 100%;
            font-size: 16px;
        }
        .forgot-password {
            text-align: left;
            margin: 20px 0 5px 10px;
            display: block;
        }
        .sign-up {
            display: block;
            margin-top: 10px;
            margin-bottom: 5px; /* Add a small bottom margin */
            font-weight: bold;
        }
        button {
            background-color: #0c1a36;
            color: white;
            padding: 10px;
            border: none;
            width: 100%;
            border-radius: 5px;
            cursor: pointer;
            font-size: 18px;
        }
        button:hover {
            background-color: #092045;
        }
        a {
            color: #0099ff;
            text-decoration: none;
            font-size: 16px;
        }
        a:hover {
            text-decoration: underline;
        }
        .login {
        display: block;
        background-color: #0099ff;
        color: white;
        padding: 14px 25px;
        text-align: center;
        text-decoration: none;
        border-radius: 5px;
        font-size: 18px;
        font-weight: bold;
        margin-top: 10px;
        }

        .login:hover {
            background-color:#092045;
        }
        #flashbang {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: white;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.5s ease;
            z-index: 9999; /* Make sure it's on top of everything */
        }
        
        /* Success message style */
        .success-message {
            background-color: #d4edda;
            color: #155724;
            padding: 12px;
            margin-bottom: 15px;
            border-radius: 5px;
            border: 1px solid #c3e6cb;
            text-align: center;
            font-weight: bold;
        }
        
        /* Error message specific styles for login page */
        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            padding: 12px;
            margin-bottom: 15px;
            border-radius: 5px;
            border: 1px solid #f5c6cb;
            text-align: center;
            font-weight: bold;
            font-size: 14px;
            width: 100%;
            box-sizing: border-box;
            position: relative;
            z-index: 10;
        }
        </style>
</head>
<body>
<div id="flashbang"></div>

    <header class="mdx-navbar-new">
        <div class="mdx-logo">
            <img src="images/mdx_logo.png" alt="Mangasay Data Exchange Logo">
        </div>

        <button class="mdx-menu-toggle" id="mobile-menu-toggle">
            <i class="fas fa-bars"></i>
        </button>

        <nav class="mdx-nav-links" id="nav-links">
            <a href="MindanaoDataExchange.php">Home</a>
            <a href="AccountSelectionPage.php">Sign up</a>
        </nav>
    </header>

    <div class="container">
        <div class="login-container">
            <div class="logo-container">
                <img src="images/mdx_logo.png" alt="MDX Logo">
            </div>
            
            <div class="message-container">
                <?php echo display_error_message(); ?>
                <?php echo display_success_message(); ?>
            </div>
            
            <form action="login.php" method="POST">
                <div class="input-container">
                    <img src="images/user_icon.png" alt="User Icon">
                    <input type="text" name="email" placeholder="Email address" required>
                </div>
                <div class="input-container">
                    <img src="images/password_icon.png" alt="Password Icon">
                    <input type="password" name="password" placeholder="Password" required>
                </div>
                <a href="forgot-password.php" class="forgot-password">Forgot password?</a>
                <button type="submit" class="login">LOGIN</button>
            </form>
            <a href="AccountSelectionPage.php" class="sign-up">Sign up</a>
        </div>
    </div>
    <script>
        document.querySelector('.login').addEventListener('click', function(e) {
            e.preventDefault(); // Stop form from submitting immediately

            const flashbang = document.getElementById('flashbang');
            flashbang.style.opacity = 1; // Show white screen

            // Wait 500ms for the flash, then submit the form
            setTimeout(() => {
                this.closest('form').submit(); // Now submit
            }, 500); // match the CSS transition time
        });
        
        // Mobile menu toggle functionality
        document.addEventListener("DOMContentLoaded", function() {
            // Mobile menu toggle
            const mobileMenuToggle = document.getElementById('mobile-menu-toggle');
            const navLinks = document.getElementById('nav-links');
            
            if (mobileMenuToggle && navLinks) {
                mobileMenuToggle.addEventListener('click', function(e) {
                    e.stopPropagation(); // Prevent event from bubbling up
                    e.preventDefault(); // Prevent default behavior
                    navLinks.classList.toggle('active');
                });
            }
            
            // Close menu when clicking outside
            document.addEventListener('click', function(event) {
                // Only handle clicks outside the toggle button itself
                if (!event.target.closest('#mobile-menu-toggle')) {
                    const isClickInsideNavbar = event.target.closest('.mdx-navbar-new');
                    if (!isClickInsideNavbar && navLinks && navLinks.classList.contains('active')) {
                        navLinks.classList.remove('active');
                    }
                }
            });
        });
    </script>

</body>
</html>
