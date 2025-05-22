<?php
session_start();
if (isset($_GET['error'])) {
    $error_message = htmlspecialchars($_GET['error']); // Sanitize the error message
}
if (isset($_SESSION['user_id'])) {
    // Redirect to login page if already logged in
    header("Location: homelogin.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Selection - MDX</title>
    <link rel="stylesheet" href="assets/css/accountselection_responsive.css">
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
    
    .account-selection-box {
        width: 85%;
        max-width: 1100px;
        background-color: white;
        border-radius: 10px;
        padding: 40px;
        margin: 20px auto;
        box-sizing: border-box;
    }
    
    .account-options {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 35px;
    }
    
    .account-options th {
        padding: 20px;
        text-align: center;
        font-weight: normal;
    }
    
    .account-options td {
        padding: 22px 20px;
        vertical-align: middle;
        border-bottom: 1px solid #000;
    }
    
    .account-options tr:last-child td {
        border-bottom: none;
    }
    
    .account-options td:first-child {
        width: 50%;
        text-align: left;
    }
    
    .account-options td:not(:first-child) {
        width: 25%;
        text-align: center;
    }
    
    .feature-name {
        color: #0099ff;
        font-weight: bold;
        font-size: 16px;
        margin-bottom: 8px;
    }
    
    .feature-desc {
        font-size: 14px;
        line-height: 1.4;
        margin: 0;
    }
    
    .account-type {
        font-size: 18px;
        color: #0099ff;
        margin-top: 5px;
    }
    
    .account-icon {
        width: 40px;
        height: auto;
    }
    
    .checkmark {
        width: 30px;
        height: 30px;
    }
    
    .action-buttons {
        text-align: center;
        margin-top: 20px;
    }
    
    .btn-next {
        background-color: #0099ff;
        color: white;
        border: none;
        border-radius: 5px;
        padding: 10px 0;
        width: 180px;
        font-size: 24px;
        cursor: pointer;
        margin-bottom: 12px;
        text-decoration: none;
        display: inline-block;
        transition: background-color 0.3s ease;
    }
    
    .btn-next:hover {
        background-color: #007acc;
    }
    
    .btn-cancel {
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
    }
    
    .btn-cancel:hover {
        background-color: #007acc;
    }
    
    .login-link {
        margin-top: 20px;
        font-size: 15px;
    }
    
    .login-link a {
        color: #0099ff;
        text-decoration: none;
        transition: text-decoration 0.3s ease;
    }
    
    .login-link a:hover {
        text-decoration: underline;
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
            <a href="login.php">Login</a>
        </nav>
    </header>

    <div class="main-container">
        <div class="account-selection-box">
            <table class="account-options">
                <thead>
                    <tr>
                        <th></th>
                        <th>
                            <img src="images/user_icon.png" alt="Normal User" class="account-icon">
                            <div class="account-type">Normal MDX Account</div>
                        </th>
                        <th>
                            <img src="images/user_icon.png" alt="Organization" class="account-icon">
                            <div class="account-type">With Organization</div>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <div class="feature-name">SEARCH AND DOWNLOAD DATA</div>
                            <p class="feature-desc">Explore a vast collection of datasets from various sources. Filter, search, and download the data you need for analysis and research.</p>
                        </td>
                        <td>
                            <img src="images/check.png" alt="Available" class="checkmark">
                        </td>
                        <td>
                            <img src="images/check.png" alt="Available" class="checkmark">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="feature-name">CONTACT THE CONTRIBUTOR</div>
                            <p class="feature-desc">Connect with dataset contributors for inquiries, collaboration, or further details about their shared data.</p>
                        </td>
                        <td>
                            <img src="images/check.png" alt="Available" class="checkmark">
                        </td>
                        <td>
                            <img src="images/check.png" alt="Available" class="checkmark">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="feature-name">ADD DATA</div>
                            <p class="feature-desc">Contribute datasets to the platform, making them available for others to discover and use. Share valuable insights, research, or real-world data.</p>
                        </td>
                        <td>
                            <!-- No checkmark for normal account -->
                        </td>
                        <td>
                            <img src="images/check.png" alt="Available" class="checkmark">
                        </td>
                    </tr>
                </tbody>
            </table>
            
            <div class="action-buttons">
                <a href="registrationdetails.php" class="btn-next">Next</a><br>
                <a href="MindanaoDataExchange.php" class="btn-cancel">Cancel</a>
                <p class="login-link">Already have an account? <a href="login.php">Log in</a></p>
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
