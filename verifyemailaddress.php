<?php
session_start();
include 'includes/security_check.php';

// Security check - Prevent direct access via URL
check_page_access(
    false,                     // Don't require login
    false,                     // Don't require organization
    'pending_user',            // Session flag to check
    true,                      // Expected value (just check if it exists)
    'mindanaodataexchange.php', // Redirect URL if check fails
    "Direct access to this page is not allowed. Please complete registration first.",
    "verify_email"             // Error prefix to avoid conflicts
);

// Also verify that verification code exists
if (!isset($_SESSION['verification_code'])) {
    $_SESSION['error_message'] = "Missing verification code. Please register again.";
    $_SESSION['error_type'] = "verify_email_missing_code";
    header("Location: mindanaodataexchange.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Email</title>
    <link rel="stylesheet" href="assets/css/verifyemail_responsive.css">
    <?php include 'includes/background_styles.php'; ?>
    <style>
    body {
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 0;
        text-align: center;
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

    .container {
      max-width: 800px;
      width: 85%;
      margin: 20px auto;
      background-color: white;
      padding: 30px;
      text-align: center;
      border-radius: 12px;
      box-shadow: 0px 0px 10px rgba(0,0,0,0.1);
      box-sizing: border-box;
    }
    
    .content {
      width: 100%;
    }
    
    h2 {
        font-size: 2.5em;
        color: #0099ff;
        margin-bottom: 20px;
    }

    p {
        font-size: 16px;
        color: #555;
        margin-bottom: 20px;
    }

    .verification-form {
        margin-top: 30px;
        background-color: #f8f8f8;
        padding: 20px;
        border-radius: 6px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
        width: 100%;
        box-sizing: border-box;
    }

    .verification-form label {
        display: block;
        font-size: 18px;
        color: #333;
        margin-bottom: 10px;
    }

    .verification-form input[type="text"] {
        width: 100%;
        padding: 12px;
        font-size: 16px;
        border-radius: 4px;
        border: 1px solid #ccc;
        margin-bottom: 20px;
        box-sizing: border-box;
    }

    .verification-form button[type="submit"] {
        background-color: #0099ff;
        color: white;
        border: none;
        padding: 12px 30px;
        font-size: 16px;
        border-radius: 4px;
        cursor: pointer;
        width: 100%;
        transition: background-color 0.3s ease;
    }

    .verification-form button[type="submit"]:hover {
        background-color: #007acc;
    }

    .message {
        padding: 15px;
        margin-bottom: 20px;
        border-radius: 5px;
        width: 100%;
        box-sizing: border-box;
    }

    .success {
        background-color: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    .error {
        background-color: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }
    
    .progress {
        display: flex;
        justify-content: space-between;
        width: 100%;
        margin: 20px auto;
        height: auto;
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

    /* Specific styling for Circle 1 and 2 */
    #circle1, #circle2 {
        background-color: #0099ff;
        color: white;
    }
    </style>
</head>
<body>
<header class="mdx-navbar-new">
    <div class="mdx-logo">
        <img src="images/mdx_logo.png" alt="Mangasay Data Exchange Logo">
    </div>

    <button class="mdx-menu-toggle" id="mobile-menu-toggle">
        <i class="fas fa-bars"></i>
    </button>

    <nav class="mdx-nav-links" id="nav-links">
        <a href="mindanaodataexchange.php">Home</a>
    </nav>
</header>
    
<div class="container">
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
        
    <div class="content">
        <h2>VERIFY YOUR EMAIL ADDRESS</h2>
        <p>We have sent an email to <strong><?php echo $_SESSION['pending_user']['email']; ?></strong> so that you can verify your email address. If you don't see the email, please check your spam or junk folder.</p>
        <?php if (isset($_SESSION['message'])): ?>
            <div class="message <?php echo ($_SESSION['verified']) ? 'success' : 'error'; ?>">
                <?php echo $_SESSION['message']; ?>
            </div>
            <?php unset($_SESSION['message']); ?>
        <?php endif; ?>

        <div class="verification-form">
            <form action="verify_code.php" method="POST">
                <label for="verification_code">Enter the verification code sent to your email:</label>
                <input type="text" id="verification_code" name="verification_code" required>
                <button type="submit">Verify</button>
            </form>
        </div>  
    </div>
</div>

<script src="https://kit.fontawesome.com/2c68a433da.js" crossorigin="anonymous"></script>
<script>
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
