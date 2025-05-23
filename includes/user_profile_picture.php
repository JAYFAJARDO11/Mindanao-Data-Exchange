<?php
// This file retrieves the user's profile picture for use in navigation bars

// Get user's profile picture if not already in session
if (!isset($_SESSION['profile_picture']) && isset($_SESSION['user_id'])) {
    // Include database connection if not already included
    if (!isset($conn)) {
        include_once 'db_connection.php';
    }
    
    $user_id = $_SESSION['user_id'];
    $sql = "SELECT profile_picture FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($user = $result->fetch_assoc()) {
        $_SESSION['profile_picture'] = $user['profile_picture'];
    }
}

// Get user's profile picture
$profile_picture = isset($_SESSION['profile_picture']) ? $_SESSION['profile_picture'] : 'avatarIconunknown.jpg';

// If profile_picture doesn't include the path, add it
if (strpos($profile_picture, 'images/') === false) {
    // Check if it's one of the numbered profile pictures (1.png through 6.png)
    if (in_array($profile_picture, ['1.png', '2.png', '3.png', '4.png', '5.png', '6.png'])) {
        $profile_picture = 'images/profile-pics/' . $profile_picture;
    } else {
        $profile_picture = 'images/' . $profile_picture;
    }
}
?> 