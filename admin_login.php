<?php
session_start();
include 'db_connection.php';
include 'includes/error_handler.php';

// Check if admin is already logged in
if (isset($_SESSION['admin_id'])) {
    header("Location: admin_dashboard.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $email = $_POST['email'];
        $password = $_POST['password'];
        
        // Validate inputs
        if (empty($email)) {
            handle_validation_error("Email address is required", "email", "admin_login.php");
        }
        
        if (empty($password)) {
            handle_validation_error("Password is required", "password", "admin_login.php");
        }
        
        // Prepare SQL statement to prevent SQL injection
        $sql = "SELECT * FROM administrator WHERE email = ?";
        $stmt = $conn->prepare($sql);
        
        if (!$stmt) {
            handle_db_error("Database error occurred", $conn, "admin_login.php");
        }
        
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $admin = $result->fetch_assoc();
            
            // Verify password using bcrypt
            if (password_verify($password, $admin['password_hash'])) {
                // Set session variables
                $_SESSION['admin_id'] = $admin['admin_id'];
                $_SESSION['admin_name'] = $admin['name'];
                
                // Update last login time
                $update_sql = "UPDATE administrator SET last_login = NOW() WHERE admin_id = ?";
                $update_stmt = $conn->prepare($update_sql);
                
                if (!$update_stmt) {
                    log_error("Failed to prepare last login update query", ERROR_DATABASE, ['error' => $conn->error]);
                    // Continue despite error - last login update is not critical
                } else {
                    $update_stmt->bind_param("i", $admin['admin_id']);
                    $update_stmt->execute();
                }
                
                // Log successful login
                log_error("Admin logged in successfully", "auth", ['admin_id' => $admin['admin_id'], 'email' => $email]);
                
                // Redirect to admin dashboard
                header("Location: admin_dashboard.php");
                exit();
            } else {
                handle_error("Invalid email or password", ERROR_AUTH, "admin_login.php");
            }
        } else {
            // If not found in administrator table, check users table for admin role
            $user_sql = "SELECT * FROM users WHERE email = ? AND role = 'admin'";
            $user_stmt = $conn->prepare($user_sql);
            
            if (!$user_stmt) {
                handle_db_error("Database error occurred", $conn, "admin_login.php");
            }
            
            $user_stmt->bind_param("s", $email);
            $user_stmt->execute();
            $user_result = $user_stmt->get_result();
            
            if ($user_result->num_rows > 0) {
                $user = $user_result->fetch_assoc();
                
                // Verify password
                if (password_verify($password, $user['password'])) {
                    // Set session variables - use user data for admin session
                    $_SESSION['admin_id'] = $user['user_id'];
                    $_SESSION['admin_name'] = $user['first_name'] . ' ' . $user['last_name'];
                    $_SESSION['admin_from_user'] = true; // Flag to indicate admin is from users table
                    
                    // Log successful login
                    log_error("User with admin role logged in as admin", "auth", ['user_id' => $user['user_id'], 'email' => $email]);
                    
                    // Redirect to admin dashboard
                    header("Location: admin_dashboard.php");
                    exit();
                } else {
                    handle_error("Invalid email or password", ERROR_AUTH, "admin_login.php");
                }
            } else {
                handle_error("Invalid email or password", ERROR_AUTH, "admin_login.php");
            }
        }
    } catch (Exception $e) {
        log_error("Admin login error", ERROR_GENERAL, [
            'exception' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        handle_error("An unexpected error occurred. Please try again.", ERROR_GENERAL, "admin_login.php");
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="assets/css/error_styles.css">
    <?php include 'includes/background_styles.php'; ?>
    <style>
        /* Reset and Base Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            background-size: cover;
            background-attachment: fixed;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #333;
            line-height: 1.6;
        }
        
        /* Login Container */
        .login-container {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
        }
        
        .login-container h2 {
            color: #0c1a36;
            margin-bottom: 30px;
            text-align: center;
            font-weight: bold;
        }
        
        /* Form Elements */
        .mb-3 {
            margin-bottom: 20px;
        }
        
        .form-control {
            display: block;
            width: 100%;
            padding: 12px;
            font-size: 16px;
            border: 1px solid #ced4da;
            border-radius: 5px;
            transition: border-color 0.3s;
        }
        
        .form-control:focus {
            border-color: #80bdff;
            outline: 0;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }
        
        /* Buttons */
        .btn {
            display: inline-block;
            font-weight: 400;
            text-align: center;
            vertical-align: middle;
            cursor: pointer;
            border: 1px solid transparent;
            padding: 12px;
            font-size: 16px;
            line-height: 1.5;
            border-radius: 5px;
            transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out, border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }
        
        .btn-login {
            background-color: #0099ff;
            color: white;
            width: 100%;
            font-weight: bold;
        }
        
        .btn-login:hover {
            background-color: #007acc;
        }
        
        /* Error Messages */
        .error-message {
            color: #dc3545;
            margin-bottom: 20px;
            text-align: center;
            padding: 10px;
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Admin Login</h2>
        <?php echo display_error_message(); ?>
        <form method="POST" action="">
            <div class="mb-3">
                <input type="email" class="form-control" name="email" placeholder="Email" required>
            </div>
            <div class="mb-3">
                <input type="password" class="form-control" name="password" placeholder="Password" required>
            </div>
            <button type="submit" class="btn btn-login">Login</button>
        </form>
    </div>
</body>
</html> 