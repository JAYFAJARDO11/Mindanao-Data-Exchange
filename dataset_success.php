<?php
session_start();
include 'db_connection.php';
include 'includes/security_check.php';

// Include session update to ensure organization_id is synchronized
include 'update_session.php';

// Use the comprehensive security check function
check_page_access(
    true,                      // Require login
    true,                      // Require organization
    'upload_success',          // Session flag to check
    true,                      // Expected value
    'uploadselection.php',     // Redirect URL if check fails
    "Direct access to this page is not allowed. Please upload a dataset first.",
    "dataset_success"          // Error prefix to avoid conflicts
);

// Initialize the total_count variable
$total_count = 0;

// Get count of pending access requests for this user
$user_id = $_SESSION['user_id'];
$request_count = 0;
$requestCountSql = "SELECT COUNT(*) as count FROM dataset_access_requests 
                    WHERE owner_id = $user_id AND status = 'Pending'";
$requestCountResult = mysqli_query($conn, $requestCountSql);
if ($requestCountResult) {
    $row = mysqli_fetch_assoc($requestCountResult);
    $request_count = $row['count'];
}

// Get count of unread notifications for this user
$notif_count = 0;
$notifCountSql = "SELECT COUNT(*) as count FROM user_notifications 
                  WHERE user_id = $user_id AND is_read = FALSE";
$notifCountResult = mysqli_query($conn, $notifCountSql);
if ($notifCountResult) {
    $row = mysqli_fetch_assoc($notifCountResult);
    $notif_count = $row['count'];
}

// Total count for badge display (requests + notifications)
$total_count = $request_count + $notif_count;

// Reset the success flag after checking
unset($_SESSION['upload_success']);

// Redirect to the user's dataset page after 5 seconds
header("Refresh: 5;url=mydatasets.php");

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Include user profile picture
include 'includes/user_profile_picture.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Successful</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <?php include 'includes/background_styles.php'; ?>
    <style>
        html, body {
            height: 100%;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #f4f7fc;
            margin: 0;
            padding: 0;
            color: #333;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        
        .navbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 5%;
            padding-left: 30px;
            background-color: #0099ff;
            color: #ffffff;
            border-radius: 20px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
            position: relative;
            margin: 10px 0;
            max-width: 1200px;
            width: 95%;
            margin-top: 30px;
            margin-left: auto;
            margin-right: auto;
            font-weight: bold;
            box-sizing: border-box;
            z-index: 1000;
        }
        
        .logo {
            display: flex;
            align-items: center;
        }
        
        .logo img {
            height: auto;
            width: 80px;
            max-width: 100%;
            margin-right: 15px;
        }

        .logo h2 {
            color: white;
            margin: 0;
            font-size: 22px;
            white-space: nowrap;
        }
        
        .nav-links {
            display: flex;
            align-items: center;
            gap: 20px;
            position: relative;
        }
        
        .nav-links a {
            color: white;
            margin-left: 20px;
            text-decoration: none;
            font-size: 18px;
            transition: transform 0.3s ease;
        }
        
        .nav-links a:hover {
            transform: scale(1.2);
        }

        /* Profile icon styles */
        .profile-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: white; 
            display: flex;
            align-items: center;
            justify-content: center;
            margin-left: 70px;
            position: relative;
        }
        
        .profile-icon img {
            width: 150%;
            height: auto;
            border-radius: 50%;
            object-fit: cover;
            cursor: pointer;
        }
        
        .profile-icon img:hover {
            transform: scale(1.2); /* Slightly enlarge the image on hover */
        }
        
        /* Notification badge styles */
        .navbar-notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background-color: #ff3b30;
            color: white;
            border-radius: 50%;
            width: 18px;
            height: 18px;
            font-size: 12px;
            font-weight: bold;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1001;
            padding: 0;
            line-height: 18px;
            text-align: center;
        }
        
        /* Mobile menu toggle button */
        .mobile-menu-toggle {
            display: none;
            background: none;
            border: none;
            color: white;
            font-size: 24px;
            cursor: pointer;
        }

        .container {
            background-color: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 80%;
            max-width: 500px;
            margin: 50px 0;
        }

        h1 {
            color: #0099ff;
            font-size: 36px;
            margin-bottom: 20px;
        }

        p {
            color: #333;
            font-size: 18px;
            margin-bottom: 20px;
        }

        .container a {
            background-color: #0099ff;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            font-size: 16px;
            border-radius: 5px;
            transition: background-color 0.3s ease;
            display: inline-block;
        }

        .container a:hover {
            background-color: #007acc;
        }
        
        @media (max-width: 768px) {
            .navbar {
                padding: 10px;
                border-radius: 15px;
                width: 90%;
                max-width: 90%;
                position: relative;
                z-index: 2;
            }
            
            .mobile-menu-toggle {
                display: block;
                position: absolute;
                right: 15px;
                top: 50%;
                transform: translateY(-50%);
            }
            
            .logo {
                flex-direction: row;
                align-items: center;
                text-align: center;
                max-width: 80%;
            }
            
            .logo img {
                width: 50px;
                margin-right: 12px;
            }
            
            .logo h2 {
                margin: 0;
                font-size: 22px;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }
            
            .nav-links {
                position: absolute;
                top: 100%;
                left: 0;
                right: 0;
                flex-direction: column;
                background-color: #0099ff;
                padding: 10px 0;
                border-radius: 0 0 15px 15px;
                box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
                display: none;
                z-index: 9999;
            }
            
            .nav-links.active {
                display: flex;
            }
            
            .nav-links a {
                width: 100%;
                text-align: center;
                padding: 10px 0;
                margin: 0;
            }
            
            .profile-icon {
                margin: 10px auto 0;
            }
            
            /* Ensure notification badge is visible on mobile */
            .nav-links .navbar-notification-badge {
                position: absolute;
                top: -5px;
                right: -5px;
                z-index: 1001;
            }
            
            .container {
                width: 90%;
                padding: 30px;
            }
        }
    </style>
</head>
<body>
    <header class="navbar">
        <div class="logo">
            <img src="images/mdx_logo.png" alt="Mindanao Data Exchange Logo">
            <h2>Upload Success</h2>
        </div>
        <button class="mobile-menu-toggle" id="mobile-menu-toggle">
            <i class="fas fa-bars"></i>
        </button>
        <nav class="nav-links" id="nav-links">
            <a href="HomeLogin.php">HOME</a>
            <a href="datasets.php">ALL DATASETS</a>
            <a href="mydatasets.php">MY DATASETS</a>
            <div class="profile-icon" id="navbar-profile-icon">
                <img src="<?php echo htmlspecialchars($profile_picture); ?>" alt="Profile">
                <?php if ($total_count > 0): ?>
                    <span class="navbar-notification-badge"><?php echo $total_count; ?></span>
                <?php endif; ?>
            </div>
        </nav>
    </header>

    <div class="container">
        <h1>Upload Successful!</h1>
        <p>Your dataset has been successfully uploaded.</p>
        <p>Thank you for contributing to the community!</p>
        <a href="datasets.php">Go to Datasets</a>
    </div>

    <script>
        // Mobile menu toggle and sidebar functionality
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuToggle = document.getElementById('mobile-menu-toggle');
            const navLinks = document.getElementById('nav-links');
            
            if (mobileMenuToggle && navLinks) {
                mobileMenuToggle.addEventListener('click', function() {
                    navLinks.classList.toggle('active');
                });
                
                // Close menu when clicking outside
                document.addEventListener('click', function(event) {
                    const isClickInsideNavbar = event.target.closest('.navbar');
                    if (!isClickInsideNavbar && navLinks.classList.contains('active')) {
                        navLinks.classList.remove('active');
                    }
                });
            }
            
            // Profile icon click handler
            const profileIcon = document.getElementById('navbar-profile-icon');
            if (profileIcon) {
                profileIcon.addEventListener('click', function() {
                    const sidebar = document.querySelector('.sidebar');
                    const sidebarOverlay = document.querySelector('.sidebar-overlay');
                    if (sidebar && sidebarOverlay) {
                        sidebar.classList.add('active');
                        sidebarOverlay.classList.add('active');
                    }
                });
            }
        });
    </script>

    <?php include 'sidebar.php'; // Include the sidebar ?>
</body>
</html>
