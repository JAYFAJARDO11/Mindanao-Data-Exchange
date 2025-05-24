<?php
session_start();
include('db_connection.php');
include('includes/error_handler.php');

if (!isset($_SESSION['verification_code']) || !isset($_SESSION['pending_change'])) {
    handle_error("Verification session expired. Please try again.", ERROR_AUTH, "user_settings.php");
}

// Initialize the total_count variable
$total_count = 0;

// Get count of pending access requests for this user
$request_count = 0;
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $requestCountSql = "SELECT COUNT(*) as count FROM dataset_access_requests 
                        WHERE owner_id = $user_id AND status = 'Pending'";
    $requestCountResult = mysqli_query($conn, $requestCountSql);
    if ($requestCountResult) {
        $row = mysqli_fetch_assoc($requestCountResult);
        $request_count = $row['count'];
    }
}

// Get count of unread notifications for this user
$notif_count = 0;
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $notifCountSql = "SELECT COUNT(*) as count FROM user_notifications 
                      WHERE user_id = $user_id AND is_read = FALSE";
    $notifCountResult = mysqli_query($conn, $notifCountSql);
    if ($notifCountResult) {
        $row = mysqli_fetch_assoc($notifCountResult);
        $notif_count = $row['count'];
    }
}

// Total count for badge display (requests + notifications)
$total_count = $request_count + $notif_count;

// Include user profile picture
include 'includes/user_profile_picture.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $enteredCode = $_POST['verification_code'];
        $correctCode = $_SESSION['verification_code'];
        $pendingChange = $_SESSION['pending_change'];
    
        if ($enteredCode == $correctCode) {
            $userId = $_SESSION['user_id'];
            
            if ($pendingChange['type'] == 'email') {
                // Update email
                $newEmail = $pendingChange['value'];
                $sql = "UPDATE users SET email = ? WHERE user_id = ?";
                $stmt = $conn->prepare($sql);
                
                if (!$stmt) {
                    handle_db_error("Database error occurred", $conn, "user_settings.php");
                }
                
                $stmt->bind_param("si", $newEmail, $userId);
                
                if ($stmt->execute()) {
                    $_SESSION['email'] = $newEmail;
                    set_success_message("Email updated successfully!");
                    
                    // Log successful email change
                    log_error("Email updated successfully", "auth", [
                        'user_id' => $userId,
                        'new_email' => $newEmail
                    ]);
                } else {
                    handle_db_error("Failed to update email", $conn, "user_settings.php");
                }
            } else if ($pendingChange['type'] == 'password') {
                // Update password using bcrypt
                $newPassword = password_hash($pendingChange['value'], PASSWORD_BCRYPT);
                $sql = "UPDATE users SET password = ? WHERE user_id = ?";
                $stmt = $conn->prepare($sql);
                
                if (!$stmt) {
                    handle_db_error("Database error occurred", $conn, "user_settings.php");
                }
                
                $stmt->bind_param("si", $newPassword, $userId);
                
                if ($stmt->execute()) {
                    // Use password-specific success message
                    $_SESSION['password_change_success'] = "Password updated successfully!";
                    
                    // Log successful password change
                    log_error("Password updated successfully", "auth", [
                        'user_id' => $userId
                    ]);
                } else {
                    handle_db_error("Failed to update password", $conn, "user_settings.php");
                }
            }
    
            // Clear verification data
            unset($_SESSION['verification_code']);
            unset($_SESSION['pending_change']);
            
            header("Location: user_settings.php");
            exit();
        } else {
            handle_validation_error("Invalid verification code. Please try again.", "verification_code");
        }
    } catch (Exception $e) {
        log_error("Verification error", ERROR_GENERAL, [
            'exception' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        handle_error("An unexpected error occurred. Please try again.", ERROR_GENERAL);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Changes</title>
    <link rel="stylesheet" href="assets/css/error_styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <?php include 'includes/background_styles.php'; ?>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .navbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 5%;
            padding-left: 30px;
            background-color: #0099ff;
            color: #cfd9ff;
            border-radius: 20px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            position: relative;
            margin: 10px 0;
            backdrop-filter: blur(10px);
            max-width: 1200px;
            width: 100%;
            margin-top: 30px;
            margin-left: auto;
            margin-right: auto;
            font-weight: bold;
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
            padding: 0;
            z-index: 1001;
        }
        
        .mobile-menu-toggle i {
            display: block;
        }

        /* Responsive styles for the navbar */
        @media screen and (max-width: 768px) {
            .navbar {
                padding: 10px;
                border-radius: 15px;
                width: 90%;
                max-width: 90%;
                position: relative;
                z-index: 1002;
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
                font-size: 18px;
                text-align: center;
                overflow: hidden;
                text-overflow: ellipsis;
                max-width: 300px;
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
        }

        @media screen and (max-width: 480px) {
            .navbar {
                padding: 8px 10px;
            }
            
            .logo img {
                width: 45px;
                margin-right: 10px;
            }
            
            .logo h2 {
                font-size: 18px;
                text-align: center;
            }
        }
        .container {
            width: 30%;
            margin: 50px auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            position: relative;
        }
        .verification-form {
            max-width: 400px;
            margin: 0 auto;
            padding: 20px;
        }
        .verification-form h2 {
            color: #0099ff;
            margin-bottom: 20px;
        }
        .verification-form input[type="text"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .verification-form button {
            background-color: #0099ff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            width: 100%;
            margin-top: 10px;
        }
        .verification-form button:hover {
            background-color: #007acc;
        }
        .error-message {
            color: #dc3545;
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            padding: 10px;
            margin: 10px 0;
            border-radius: 4px;
            font-size: 14px;
        }
        
        /* Responsive styles for the container */
        @media screen and (max-width: 768px) {
            .container {
                width: 80%;
                margin: 30px auto;
            }
        }
        
        @media screen and (max-width: 480px) {
            .container {
                width: 90%;
                margin: 20px auto;
                padding: 15px;
            }
            
            .verification-form {
                padding: 10px;
            }
        }
    </style>
</head>
<body>

    <header class="navbar">
        <div class="logo">
            <img src="images/mdx_logo.png" alt="Mangasay Data Exchange Logo">
            <h2>Verify Changes</h2>
        </div>
        <button class="mobile-menu-toggle" id="mobile-menu-toggle">
            <i class="fas fa-bars"></i>
        </button>
        <nav class="nav-links" id="nav-links">
            <a href="HomeLogin.php">HOME</a>
            <a href="datasets.php">ALL DATASETS</a>
            <a href="mydatasets.php">MY DATASETS</a>
            <div class="profile-icon" id="navbar-profile-icon" style="position: relative;">
                <img src="<?php echo htmlspecialchars($profile_picture); ?>" alt="Profile">
                <?php if ($total_count > 0): ?>
                    <span class="navbar-notification-badge" style="position: absolute; top: -5px; right: -5px;"><?php echo $total_count; ?></span>
                <?php endif; ?>
            </div>
        </nav>
    </header>

    <div class="container">
        <div class="verification-form">
            <h2>Verify Changes</h2>
            <?php echo display_error_message(); ?>
            <?php echo display_success_message(); ?>
            
            <form action="verify_changes_code.php" method="POST">
                <input type="text" name="verification_code" placeholder="Enter verification code" required>
                <button type="submit">Verify Changes</button>
            </form>
        </div>
    </div>

    <?php include 'sidebar.php'; ?>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Mobile menu toggle functionality
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
</body>
</html> 