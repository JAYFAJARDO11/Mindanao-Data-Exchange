<?php
session_start();
include 'db_connection.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Check if profile picture was selected
if (isset($_POST['profile_picture'])) {
    $user_id = $_SESSION['user_id'];
    $profile_picture = $_POST['profile_picture'];
    
    // Validate the profile picture (make sure it's one of our predefined options)
    $allowed_pictures = ['avatarIconunknown.jpg', '1.jpg', '2.jpg', '3.jpg', '4.jpg', '5.jpg', '6.jpg'];
    
    if (!in_array($profile_picture, $allowed_pictures)) {
        $_SESSION['error_message'] = "Invalid profile picture selection.";
        header("Location: user_settings.php");
        exit();
    }
    
    // Update the user's profile picture in the database
    $sql = "UPDATE users SET profile_picture = ? WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $profile_picture, $user_id);
    
    if ($stmt->execute()) {
        // Update session variable too for immediate effect
        $_SESSION['profile_picture'] = $profile_picture;
        $_SESSION['success_message'] = "Profile picture updated successfully!";
    } else {
        $_SESSION['error_message'] = "Failed to update profile picture. Please try again.";
    }
}

// Redirect back to user settings page
header("Location: user_settings.php");
exit();
?> 