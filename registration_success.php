<?php
session_start();
include 'includes/security_check.php';

// Security check - Prevent direct access via URL
require_session_flag('registration_complete', true, 'mindanaodataexchange.php', 
                     "Direct access to this page is not allowed. Please complete registration first.",
                     "reg_success");

// Reset the flag after displaying the success page once
// This prevents refreshing the page to see it again
$_SESSION['registration_complete'] = false;

// Redirect to the login page after 5 seconds
header("Refresh: 5;url=login.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Successful - MDX</title>
    <link rel="stylesheet" href="assets/css/registration_success_responsive.css">
    <?php include 'includes/background_styles.php'; ?>
    <style>
    body {
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 0;
        text-align: center;
        overflow-x: hidden;
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
    }
    
    /* Responsive styles for the navbar */
    @media screen and (max-width: 768px) {
        .mdx-navbar-new {
            padding: 10px !important;
            border-radius: 15px !important;
            width: 90% !important;
            max-width: 90% !important;
        }
        
        .mdx-menu-toggle {
            display: block !important;
            position: absolute !important;
            right: 15px !important;
            top: 50% !important;
            transform: translateY(-50%) !important;
            z-index: 1002 !important;
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
    
    .main-container {
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 20px;
    }
    
    .success-box {
        width: 85%;
        max-width: 800px;
        background-color: white;
        border-radius: 10px;
        padding: 40px 20px;
        margin: 20px auto;
        box-sizing: border-box;
    }
    
    .progress {
        display: flex;
        justify-content: space-between;
        width: 100%;
        margin: 0 auto 30px;
    }
    
    .step {
        text-align: center;
    }
    
    .circle {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        margin: 0 auto 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: black;
        color: white;
        font-weight: bold;
    }
    
    /* All circles active on the success page */
    #circle1, #circle2, #circle3 {
        background-color: #0099ff;
        color: white;
    }
    
    h1 {
        font-size: 3.5em;
        margin: 30px 0;
        color: #0099ff;
        word-wrap: break-word;
    }
    
    small {
        display: block;
        font-size: 16px;
        color: #555;
        margin-bottom: 20px;
    }
    
    .countdown {
        margin: 20px auto;
        font-size: 36px;
        font-weight: bold;
        color: #0099ff;
    }
    
    .btn-login {
        background-color: #0099ff;
        color: white;
        border: none;
        border-radius: 5px;
        padding: 10px 0;
        width: 180px;
        font-size: 20px;
        cursor: pointer;
        text-decoration: none;
        display: inline-block;
        transition: background-color 0.3s ease;
        margin-top: 20px;
    }
    
    .btn-login:hover {
        background-color: #007acc;
    }
    
    @media (max-width: 768px) {
        .success-box {
            padding: 30px 15px;
        }
        
        h1 {
            font-size: 2.5em;
            margin: 20px 0;
        }
        
        .countdown {
            font-size: 30px;
        }
    }
    
    @media (max-width: 576px) {
        .success-box {
            padding: 20px 10px;
        }
        
        h1 {
            font-size: 2em;
            margin: 15px 0;
        }
        
        .countdown {
            font-size: 24px;
        }
        
        .circle {
            width: 35px;
            height: 35px;
        }
    }
    </style>
</head>
<body>

    <header class="mdx-navbar-new">
        <div class="mdx-logo">
            <img src="images/mdx_logo.png" alt="Mindanao Data Exchange Logo">
        </div>

        <button class="mdx-menu-toggle" id="mobile-menu-toggle">
            <i class="fas fa-bars"></i>
        </button>

        <nav class="mdx-nav-links" id="nav-links">
            <a href="mindanaodataexchange.php">Home</a>
        </nav>
    </header>

    <div class="main-container">
        <div class="success-box">
            <div class="progress">
                <div class="step active">
                    <div id="circle1" class="circle">1</div>
                    <div>Personal details</div>
                </div>
                <div class="step active">
                    <div id="circle2" class="circle">2</div>
                    <div>Verify email</div>
                </div>
                <div class="step active">
                    <div id="circle3" class="circle">3</div>
                    <div>Account created</div>
                </div>
            </div>
        
            <h1>Account Created!</h1>
            <small>You will be redirected to the login page in...</small>
            <div class="countdown" id="countdown">5</div>
            <a href="login.php" class="btn-login">Go to login</a>
        </div>
    </div>
  
    <script src="https://kit.fontawesome.com/2c68a433da.js" crossorigin="anonymous"></script>
    <script>
        // Countdown logic
        let countdown = 5;
        const countdownElement = document.getElementById('countdown');
        const redirectUrl = 'login.php'; // URL to redirect to
        
        const countdownInterval = setInterval(function() {
            countdown--;
            countdownElement.textContent = countdown;
            
            // Redirect when countdown reaches 0
            if (countdown <= 0) {
                clearInterval(countdownInterval);
                window.location.href = redirectUrl;
            }
        }, 1000); // Update every second
        
        // Mobile menu toggle functionality
        document.addEventListener("DOMContentLoaded", function() {
            // Mobile menu toggle
            const mobileMenuToggle = document.getElementById('mobile-menu-toggle');
            const navLinks = document.getElementById('nav-links');
            
            if (mobileMenuToggle && navLinks) {
                mobileMenuToggle.addEventListener('click', function(e) {
                    e.stopPropagation(); // Prevent event from bubbling up
                    navLinks.classList.toggle('active');
                });
            }
            
            // Close menu when clicking outside
            document.addEventListener('click', function(event) {
                const isClickInsideNavbar = event.target.closest('.mdx-navbar-new');
                if (!isClickInsideNavbar && navLinks && navLinks.classList.contains('active')) {
                    navLinks.classList.remove('active');
                }
            });
        });
    </script>
</body>
</html> 