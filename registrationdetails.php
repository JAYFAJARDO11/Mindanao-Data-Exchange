<?php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registration Form</title>
    <link rel="stylesheet" href="assets/css/registration_responsive.css">
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
        .progress {
            display: flex;
            justify-content: space-between;
            width: 100%;
            margin: 30px auto;
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
            background-color: black; /* default black */
            color: white;
            font-weight: bold;
        }

        /* Specific styling for Circle 1 */
        #circle1 {
            background-color: #0099ff; /* blue */
            color: white;
        }

        h1 {
            font-size: 2.5em;
            margin-bottom: 40px;
            color: #0099ff;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
            text-align: left;
        }

        .input-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .input-group label {
            font-size: 16px;
            color: #333;
        }

        .input-group input {
            padding: 12px;
            border: 1px solid #0099ff;
            border-radius: 8px;
            font-size: 16px;
            background-color: #f8f9fa;
            width: 100%;
            box-sizing: border-box;
        }

        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            padding: 10px 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 16px;
            font-weight: bold;
            border: 1px solid #f5c6cb;
        }

        .button-group {
            display: flex;
            flex-direction: column;
            gap: 15px;
            align-items: center;
            margin-top: 20px;
            width: 100%;
        }

        .btn-next {
            background-color: #0099ff;
            color: white;
            padding: 15px 60px;
            border: none;
            border-radius: 8px;
            font-size: 18px;
            cursor: pointer;
            width: 200px;
            transition: background-color 0.3s ease;
        }
        
        .btn-next:hover {
            background-color: #007acc;
        }

        .btn-cancel {
            background: none;
            border: none;
            color: #333;
            font-size: 18px;
            cursor: pointer;
            transition: color 0.3s ease;
        }
        
        .btn-cancel:hover {
            color: #0099ff;
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
         <!--Show error message if it exists -->
        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="error-message">
                <?php echo $_SESSION['error_message']; ?>
            </div>
            <?php unset($_SESSION['error_message']); ?>
        <?php endif; ?>

        <div class="progress">
            <div class="step active">
                <div id="circle1" class="circle">1</div>
                <div>Personal details</div>
            </div>
            <div class="step active">
                <div id="circle2 " class="circle">2</div>
                <div>Verify email</div>
            </div>
            <div class="step active">
                <div id="circle3" class="circle">3</div>
                <div>Account created</div>
            </div>
        </div>
        
        <h1>Provide the following information</h1>
        
        <form action="registration_api.php" method="POST">
        <input type="hidden" name="user_type" value="individual">
            <div class="form-grid">
                <div class="input-group">
                    <label for="firstname">First Name:</label>
                    <input type="text" name="firstname" value="<?= htmlspecialchars($_SESSION['form_data']['firstname'] ?? '') ?>">
                </div>
                <div class="input-group">
                    <label for="lastname">Last Name:</label>
                    <input type="text" name="lastname" value="<?= htmlspecialchars($_SESSION['form_data']['lastname'] ?? '') ?>">
                </div>

                <div class="input-group">
                    <label for="email_address">Email Address:</label>
                    <input type="text" name="email_address" value="<?= htmlspecialchars($_SESSION['form_data']['email_address'] ?? '') ?>">
                </div>
                <div class="input-group">
                    <label for="re_enter_email">Re-enter Email Address:</label>
                    <input type="text" name="re_enter_email" value="<?= htmlspecialchars($_SESSION['form_data']['re_enter_email'] ?? '') ?>">
                    <div id="password-error" class="error-text"></div>
                </div>

                <div class="input-group">
                    <label for="password">Password:</label>
                    <input type="password" name="password" value="<?= htmlspecialchars($_SESSION['form_data']['password'] ?? '') ?>">
                </div>
                <div class="input-group">
                    <label for="re_enter_pass">Re-enter Password:</label>
                    <input type="password" name="re_enter_pass" value="<?= htmlspecialchars($_SESSION['form_data']['re_enter_pass'] ?? '') ?>">
                    <div id="password-error" class="error-text"></div>
                </div>
            </div>
            
            <div class="button-group">
                <button type="submit" class="btn-next">Next</button>
                <button type="button" class="btn-cancel" onclick="window.location.href='index.php'">Cancel</button>
            </div>
        </form>
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

<!--body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background-color: #e6f0fa;
        }
        .header {
            background-color: #0c2239;
            padding: 15px 25.65px;
        }
        .header img {
            height: 95px;
            width: 100px;
        }
        .container {
            max-width: 600px;
            margin: 50px auto;
            background-color: white;
            padding: 40px;
            text-align: center;
            border-radius: 12px;
            box-shadow: 0px 0px 10px rgba(0,0,0,0.1);
        }
        .progress { 
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        .step .circle {
            width: 30px;
            height: 30px;
            line-height: 30px;
            margin: 0 auto 10px;
            border-radius: 50%;
            background-color: #0c2239;
            color: white;
        }
        .step.active .circle {
            background-color: white;
            color: #0c2239;
            border: 2px solid #0c2239;
        }
        .step.active {
            font-weight: bold;
            color: #0c2239;
        }
        h1 {
            font-size: 2.5em;
            margin-bottom: 40px;
            color: #333;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
            text-align: left;
        }

        .input-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .input-group label {
            font-size: 16px;
            color: #333;
        }

        .input-group input {
            padding: 12px;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            font-size: 16px;
            background-color: #f8f9fa;
        }

        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            padding: 10px 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 16px;
            font-weight: bold;
            border: 1px solid #f5c6cb;
        }

        .button-group {
            display: flex;
            flex-direction: column;
            gap: 15px;
            align-items: center;
            margin-top: 20px;
        }

        .btn-next {
            background-color: #0c2239;
            color: white;
            padding: 15px 60px;
            border: none;
            border-radius: 8px;
            font-size: 18px;
            cursor: pointer;
            width: 200px;
        }

        .btn-cancel {
            background: none;
            border: none;
            color: #333;
            font-size: 18px;
            cursor: pointer;
        }
                <div class="header">
        <img src="images/mdx_logo.png" alt="Logo">
    </div>
    <div class="container">
         Show error message if it exists 
        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="error-message">
                <?php echo $_SESSION['error_message']; ?>
            </div>
            <?php unset($_SESSION['error_message']); ?>
        <?php endif; ?>

        <div class="progress">
            <div class="step active">
                <div class="circle">1</div>
                <div>Personal details</div>
            </div>
            <div class="step active">
                <div class="circle">2</div>
                <div>Verify email</div>
            </div>
            <div class="step active">
                <div class="circle">3</div>
                <div>Account created</div>
            </div>
        </div>
        
        <h1>Provide the following information</h1>
        
        <form action="registration_api.php" method="POST">
            <div class="form-grid">
                <div class="input-group">
                    <label for="firstname">First Name:</label>
                    <input type="text" name="firstname" value="<?= htmlspecialchars($_SESSION['form_data']['firstname'] ?? '') ?>">
                </div>
                <div class="input-group">
                    <label for="lastname">Last Name:</label>
                    <input type="text" name="lastname" value="<?= htmlspecialchars($_SESSION['form_data']['lastname'] ?? '') ?>">
                </div>

                <div class="input-group">
                    <label for="email_address">Email Address:</label>
                    <input type="text" name="email_address" value="<?= htmlspecialchars($_SESSION['form_data']['email_address'] ?? '') ?>">
                </div>
                <div class="input-group">
                    <label for="re_enter_email">Re-enter Email Address:</label>
                    <input type="text" name="re_enter_email" value="<?= htmlspecialchars($_SESSION['form_data']['re_enter_email'] ?? '') ?>">
                    <div id="password-error" class="error-text"></div>
                </div>

                <div class="input-group">
                    <label for="password">Password:</label>
                    <input type="password" name="password" value="<?= htmlspecialchars($_SESSION['form_data']['password'] ?? '') ?>">
                </div>
                <div class="input-group">
                    <label for="re_enter_pass">Re-enter Password:</label>
                    <input type="password" name="re_enter_pass" value="<?= htmlspecialchars($_SESSION['form_data']['re_enter_pass'] ?? '') ?>">
                    <div id="password-error" class="error-text"></div>
                </div>
            </div>
            
            <div class="button-group">
                <button type="submit" class="btn-next">Next</button>
                <button type="button" class="btn-cancel">Cancel</button>
            </div>
        </form>
    </div>

        