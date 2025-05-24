<?php
session_start();
include 'db_connection.php';
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Handle notification sending
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $message = $_POST['message'];
    $notification_type = $_POST['notification_type'];
    $target_users = $_POST['target_users'] ?? 'all';
    
    // Insert notification into database
    $sql = "INSERT INTO notifications (title, message, type, created_by) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $title, $message, $notification_type, $_SESSION['admin_id']);
    $stmt->execute();
    $notification_id = $conn->insert_id;
    
    // Get target users
    if ($target_users == 'all') {
        $sql = "SELECT user_id, email, CONCAT(first_name, ' ', last_name) AS name FROM users WHERE is_active = 1";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
    } else {
        $sql = "SELECT user_id, email, CONCAT(first_name, ' ', last_name) AS name FROM users WHERE organization_id = ? AND is_active = 1";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $target_users);
        $stmt->execute();
    }
    $users = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    
    // Send notifications
    foreach ($users as $user) {
        // Insert user notification
        $notif_type = "notification";
        $sql = "INSERT INTO user_notifications (user_id, message, notification_type, related_id) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("issi", $user['user_id'], $message, $notif_type, $notification_id);
        $stmt->execute();
        
        // Send email if notification type is email or both
        if ($notification_type == 'email' || $notification_type == 'both') {
            $mail = new PHPMailer(true);
            try {
                // Server settings
                $mail->SMTPDebug = 0; // Set to 0 for production (no debug output)
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'wazzupbymindex@gmail.com';
                $mail->Password = 'zigg xxbk opcb qsob';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;
                
                // Recipients
                $mail->setFrom('wazzupbymindex@gmail.com', 'Mindanao Data Exchange');
                $mail->addAddress($user['email'], $user['name']);
                
                // Content
                $mail->isHTML(true);
                $mail->Subject = $title;
                $mail->Body = $message;
                $mail->AltBody = strip_tags($message);
                
                $mail->send();
                error_log("Email sent successfully to {$user['email']}");
            } catch (Exception $e) {
                // Log email error
                error_log("Email error for user {$user['user_id']}: {$mail->ErrorInfo}");
            }
        }
    }
    
    $success_message = "Notification sent successfully!";
}

// Get organizations for target selection
$organizations = $conn->query("SELECT organization_id, name FROM organizations ORDER BY name")->fetch_all(MYSQLI_ASSOC);

// Get recent notifications
$sql = "SELECT n.*, a.name as admin_name, 
        (SELECT COUNT(*) FROM user_notifications WHERE related_id = n.admin_notif_id AND notification_type = 'notification') as recipient_count
        FROM notifications n 
        JOIN administrator a ON n.created_by = a.admin_id 
        ORDER BY n.created_at DESC 
        LIMIT 10";
$recent_notifications = $conn->query($sql)->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications - Admin Dashboard</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        /* Reset and Base Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            color: #333;
            line-height: 1.6;
        }
        
        /* Layout */
        .sidebar {
            background: #0c1a36;
            color: white;
            height: 100vh;
            position: fixed;
            width: 250px;
            padding: 20px;
            overflow-y: auto;
        }
        
        .main-content {
            margin-left: 250px;
            padding: 20px;
        }
        
        /* Typography */
        h1, h2, h3, h4, h5, h6 {
            margin-bottom: 15px;
        }
        
        p {
            margin-bottom: 15px;
        }
        
        small {
            font-size: 85%;
            color: #6c757d;
        }
        
        /* Navigation */
        .sidebar h3 {
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid rgba(255,255,255,0.2);
        }
        
        .nav {
            list-style: none;
        }
        
        .nav-link {
            display: block;
            color: white;
            padding: 10px 20px;
            margin: 5px 0;
            border-radius: 5px;
            text-decoration: none;
            transition: background 0.3s;
        }
        
        .nav-link:hover {
            background: rgba(255,255,255,0.1);
        }
        
        .nav-link.active {
            background: #0099ff;
        }
        
        /* Cards */
        .notification-form {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        
        .notification-history {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        
        .notification-item {
            padding: 15px;
            border-bottom: 1px solid #eee;
        }
        
        .notification-item:last-child {
            border-bottom: none;
        }
        
        /* Form Elements */
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }
        
        .form-control {
            display: block;
            width: 100%;
            padding: 8px 12px;
            font-size: 14px;
            border: 1px solid #ced4da;
            border-radius: 4px;
            transition: border-color 0.3s;
        }
        
        .form-control:focus {
            border-color: #80bdff;
            outline: 0;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }
        
        textarea.form-control {
            min-height: 100px;
            resize: vertical;
        }
        
        select.form-control {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='%23333' viewBox='0 0 16 16'%3E%3Cpath fill-rule='evenodd' d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 12px center;
            padding-right: 30px;
        }
        
        /* Buttons */
        .btn {
            display: inline-block;
            padding: 8px 16px;
            background-color: #e9ecef;
            color: #333;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            font-size: 14px;
            line-height: 1.5;
            transition: all 0.3s;
        }
        
        .btn:hover {
            opacity: 0.9;
        }
        
        .btn-primary {
            background-color: #0099ff;
            color: white;
        }
        
        /* Alert Messages */
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            position: relative;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        /* Badges */
        .badge {
            display: inline-block;
            padding: 4px 8px;
            font-size: 12px;
            font-weight: bold;
            line-height: 1;
            color: white;
            text-align: center;
            white-space: nowrap;
            vertical-align: baseline;
            border-radius: 10px;
        }
        
        .badge-info {
            background-color: #17a2b8;
        }
        
        /* Flexbox Utilities */
        .flex-between {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }
        
        .text-end {
            text-align: right;
        }
        
        .text-muted {
            color: #6c757d;
        }
        
        /* Margins */
        .mb-4 {
            margin-bottom: 20px;
        }
        
        .mb-3 {
            margin-bottom: 15px;
        }
        
        /* Responsive Design */
        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .flex-between {
                flex-direction: column;
            }
            
            .text-end {
                text-align: left;
                margin-top: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h3>Admin Panel</h3>
        <nav>
            <ul class="nav">
                <li><a class="nav-link" href="admin_dashboard.php"><i class="fas fa-home"></i> Dashboard</a></li>
                <li><a class="nav-link" href="admin_users.php"><i class="fas fa-users"></i> Users</a></li>
                <li><a class="nav-link" href="admin_datasets.php"><i class="fas fa-database"></i> Datasets</a></li>
                <li><a class="nav-link" href="admin_organizations.php"><i class="fas fa-building"></i> Organizations</a></li>
                <li><a class="nav-link" href="admin_org_requests.php"><i class="fas fa-clipboard-list"></i> Org Requests</a></li>
                <li><a class="nav-link active" href="#"><i class="fas fa-bell"></i> Notifications</a></li>
                <li><a class="nav-link" href="admin_settings.php"><i class="fas fa-cog"></i> Settings</a></li>
                <li><a class="nav-link" href="admin_to_main.php"><i class="fas fa-globe"></i> Main Site</a></li>
                <li><a class="nav-link" href="admin_logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </nav>
    </div>

    <div class="main-content">
        <h2 class="mb-4">Send Notifications</h2>

        <?php if (isset($success_message)): ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>

        <div class="notification-form">
            <form method="POST" action="">
                <div class="form-group mb-3">
                    <label for="title" class="form-label">Notification Title</label>
                    <input type="text" class="form-control" id="title" name="title" required>
                </div>
                <div class="form-group mb-3">
                    <label for="message" class="form-label">Message</label>
                    <textarea class="form-control" id="message" name="message" rows="4" required></textarea>
                </div>
                <div class="form-group mb-3">
                    <label for="notification_type" class="form-label">Notification Type</label>
                    <select class="form-control" id="notification_type" name="notification_type" required>
                        <option value="in_app">In-App Only</option>
                        <option value="email">Email Only</option>
                        <option value="both">Both In-App and Email</option>
                    </select>
                </div>
                <div class="form-group mb-3">
                    <label for="target_users" class="form-label">Target Users</label>
                    <select class="form-control" id="target_users" name="target_users">
                        <option value="all">All Users</option>
                        <?php foreach ($organizations as $org): ?>
                            <option value="<?php echo $org['organization_id']; ?>">
                                <?php echo htmlspecialchars($org['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-paper-plane"></i> Send Notification
                </button>
            </form>
        </div>

        <div class="notification-history">
            <h3 class="mb-4">Recent Notifications</h3>
            <?php foreach ($recent_notifications as $notification): ?>
                <div class="notification-item">
                    <div class="flex-between">
                        <div>
                            <h5><?php echo htmlspecialchars($notification['title']); ?></h5>
                            <p class="text-muted"><?php echo htmlspecialchars($notification['message']); ?></p>
                            <small>
                                Sent by <?php echo htmlspecialchars($notification['admin_name']); ?> to 
                                <?php echo $notification['recipient_count']; ?> users
                            </small>
                        </div>
                        <div class="text-end">
                            <span class="badge badge-info"><?php echo ucfirst($notification['type']); ?></span>
                            <br>
                            <small class="text-muted">
                                <?php echo date('M d, Y H:i', strtotime($notification['created_at'])); ?>
                            </small>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html> 