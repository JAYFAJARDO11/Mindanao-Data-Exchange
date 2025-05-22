<?php
session_start();
// Get custom error message if available
$error_message = isset($_SESSION['error_message']) ? $_SESSION['error_message'] : "You do not have the necessary permissions to access this page.";

// Get error type if available
$error_type = isset($_SESSION['error_type']) ? $_SESSION['error_type'] : "permission";

// Determine appropriate action links based on error type
$primary_link = "login.php";
$primary_text = "Log In";
$secondary_link = isset($_SESSION['user_id']) ? 'HomeLogin.php' : 'mindanaodataexchange.php';
$secondary_text = "Back to Home";

// Customize links based on error type
if ($error_type === "organization") {
    $primary_link = "organization.php";
    $primary_text = "Join Organization";
} elseif ($error_type === "validation") {
    $primary_link = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : $secondary_link;
    $primary_text = "Try Again";
}

// Clear the error message after displaying it
unset($_SESSION['error_message']);
unset($_SESSION['error_type']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unauthorized Access</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <?php include 'includes/background_styles.php'; ?>
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
            color: #333;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .container {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            padding: 40px;
            text-align: center;
            max-width: 500px;
            width: 90%;
        }

        h1 {
            font-size: 28px;
            color: #0099ff;
            margin-bottom: 20px;
        }

        .icon {
            font-size: 60px;
            color: #0099ff;
            margin-bottom: 20px;
        }

        p {
            font-size: 16px;
            line-height: 1.6;
            margin-bottom: 20px;
            color: #555;
        }

        .btn {
            display: inline-block;
            padding: 12px 24px;
            background-color: #0099ff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            text-decoration: none;
            transition: background-color 0.3s ease;
            margin: 5px;
        }

        .btn:hover {
            background-color: #0077cc;
        }

        .btn-outline {
            background-color: transparent;
            border: 1px solid #0099ff;
            color: #0099ff;
        }

        .btn-outline:hover {
            background-color: #f0f7ff;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="icon">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        <h1>Unauthorized Access</h1>
        <p><?php echo htmlspecialchars($error_message); ?></p>
        <p>Please log in with the appropriate credentials or contact the administrator if you believe this is an error.</p>
        <div>
            <a href="<?php echo $primary_link; ?>" class="btn"><?php echo $primary_text; ?></a>
            <a href="<?php echo $secondary_link; ?>" class="btn btn-outline"><?php echo $secondary_text; ?></a>
        </div>
    </div>
</body>
</html>
