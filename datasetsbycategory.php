<?php
session_start();
include 'db_connection.php';

// Include session update to ensure organization_id is synchronized
include 'update_session.php';

// Check if the user is logged in (ensure 'user_id' is set in the session)
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if not authenticated
    header("Location: login.php");
    exit();
}

// Include user profile picture
include 'includes/user_profile_picture.php';

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

// Get the category ID from the URL if available
$category_id = isset($_GET['category_id']) ? $_GET['category_id'] : null;
$category_name = "";

// Get current filter (if any)
$current_filter = isset($_GET['visibility']) ? $_GET['visibility'] : '';

// Start the SQL query
$sql = "
    SELECT 
        d.dataset_id,
        d.dataset_batch_id,
        d.title,
        d.description, 
        d.file_path,
        u.first_name, 
        u.last_name,
        u.user_id,
        db.visibility,
        db.created_at,
        (SELECT COUNT(*) FROM datasetratings r JOIN datasets d2 ON r.dataset_id = d2.dataset_id WHERE d2.dataset_batch_id = d.dataset_batch_id) AS upvotes,
        (SELECT COUNT(*) FROM datasetratings r JOIN datasets d2 ON r.dataset_id = d2.dataset_id WHERE d2.dataset_batch_id = d.dataset_batch_id AND r.user_id = {$_SESSION['user_id']}) AS user_upvoted,
        c.name AS category_name
    FROM datasets d
    JOIN users u ON d.user_id = u.user_id
    JOIN datasetcategories c ON d.category_id = c.category_id
    JOIN dataset_batches db ON d.dataset_batch_id = db.dataset_batch_id
    WHERE d.dataset_id = (
        SELECT MIN(d2.dataset_id) FROM datasets d2 WHERE d2.dataset_batch_id = d.dataset_batch_id
    )
";

// Add visibility filter if specified
if (isset($_GET['visibility']) && in_array($_GET['visibility'], ['Public', 'Private'])) {
    $visibility_filter = mysqli_real_escape_string($conn, $_GET['visibility']);
    $sql .= " AND db.visibility = '$visibility_filter'";
}

// If category is selected, add the condition for category filtering
if ($category_id) {
    $category_name_sql = "SELECT name FROM datasetcategories WHERE category_id = '$category_id'";
    $category_name_result = mysqli_query($conn, $category_name_sql);
    $category_row = mysqli_fetch_assoc($category_name_result);
    $category_name = $category_row['name']; // Get the category name based on category_id
    $sql .= " AND c.category_id = '$category_id'"; // Apply the category filter
}

$result = mysqli_query($conn, $sql);

// Set this AFTER the session is updated
$upload_disabled = !isset($_SESSION['organization_id']) || $_SESSION['organization_id'] == null;
include 'batch_analytics.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>All Datasets</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="assets/css/datasets_styles.css">
    <?php include 'includes/background_styles.php'; ?>
   <style>
        html, body {
            height: 100%;
        }

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            text-align: center;
        }
        .filter-sidebar {
            display: none; /* Hide the sidebar version */
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
            white-space: normal; /* Allow text to wrap */
            word-wrap: break-word; /* Break long words */
            word-break: keep-all; /* Keep words together when possible */
            hyphens: auto; /* Add hyphens for better word breaks */
            max-width: 300px; /* Slightly increase max-width */
            text-align: left; /* Align text to the left */
            line-height: 1.2; /* Add some line spacing */
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
                white-space: normal;
                overflow: hidden;
                text-overflow: ellipsis;
                max-width: 200px;
                line-height: 1.2;
                text-align: left;
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

        /* Add this to fix z-index issues */
        .search-bar, #category-btn, .add-data-btn, #wrapper {
            position: relative;
            z-index: 1; /* Lower z-index than navbar */
        }

        @media screen and (max-width: 480px) {
            .navbar {
                padding: 8px 10px;
            }
            
            .logo img {
                width: 50px;
            }
        }
        
        /* Restore the removed styles */
        h2 {
            text-align: center;
            color: #0c1a36;
            margin-bottom: 15px;
        }
        
        /* All dataset card, grid, and wrapper styles now come from datasets_styles.css */
        .search-bar {
            display: flex;
            position: relative;
            justify-content: center;
        }
        .search-bar input {
            padding: 20px;
            width: 100%;
            max-width: 1000px;
            border: solid black 1px;
            border-radius:10px;
            margin-top: 20px;
        }
        .search-bar button {
            background: none;
            border: none;
            cursor: pointer;
        }
        .search-bar img {
            width: 20px;
            height: 20px;
            margin-left: -50px;
            margin-top: 20px;
        }
        .add-data-btn {
            margin-top: 20px;
            padding: 8px 5px;
            padding-top: 20px;
            padding-bottom: 20px;
            background-color: #0099ff;
            color: white;
            border: none;
            width: 100px;
            border-radius: 5px;
            text-decoration: none; /* Remove underline */
            display: inline-block;
            text-align: center;
            box-shadow: 0 5px 10px rgba(0,0,0,0.2), inset 0 2px 4px rgba(255,255,255,0.4);
            font-weight: bold;
            cursor: pointer;
        }
        .add-data-btn:hover {
            background-color: #007acc;
        }
        #category-btn {
            margin-top: 20px;
            padding: 8px 5px;
            padding-top: 20px;
            background-color: #4CAF50; /* Green color */
            color: white;
            border: none;
            width: 120px; /* Make it slightly wider than the add button */
            border-radius: 5px;
            text-decoration: none; /* Remove underline */
            display: inline-block;
            text-align: center;
            box-shadow: 0 5px 10px rgba(0,0,0,0.2), inset 0 2px 4px rgba(255,255,255,0.4);
            font-weight: bold;
            cursor: pointer;
            margin-right: 15px; /* Add some space between buttons */
        }

        #category-btn:hover {
            background-color: #45a049; /* Darker green on hover */
        }
        .no-datasets {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 500px; /* Full height of the viewport */
            text-align: center;
            color: #0c1a36;
        }
        .no-datasets p{
            margin-left: 38px;
        }

        .no-found-img {
            width: 250px;
            max-width: 90%;
            margin-bottom: 20px;
        }

        .no-datasets p {
            font-size: 28px;
            font-weight: bold;
        }
        .download-btn {
            display: inline-block;
            margin-top: 10px;
            padding: 6px 12px;
            background-color: #0099ff;
            color: white;
            text-decoration: none;
            font-weight: bold;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }
        .download-btn:hover {
            background-color: #e65c00;
        }
        .tooltip-wrapper {
            position: relative;
            display: inline-block;
        }

        .tooltip-text {
            visibility: hidden;
            width: 260px;
            background-color: #333;
            color: #fff;
            text-align: center;
            border-radius: 6px;
            padding: 8px 0;
            position: absolute;
            z-index: 1;
            bottom: 80%; /* Position above the button */
            left: 50%;
            margin-left: -130px;
            opacity: 0;
            transition: opacity 0.3s;
            font-size: 14px;
            pointer-events: none;
        }

        .tooltip-wrapper:hover .tooltip-text {
            visibility: visible;
            opacity: 1;
        }

        .add-data-btn.disabled-link {
            background-color: #e0e0e0;
            color: #b0b0b0;
            cursor: not-allowed;
            pointer-events: none;
            margin-top: 21px;
            padding-bottom:19.5px;
        }
        
        .controls-wrapper {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            margin: 10px 0;
            padding-right: 20px;
        }
        
        .filter-group {
            display: flex;
            align-items: left;
            gap: 10px;
            margin-right: 20px;
        }

        .filter-label {
            font-weight: bold;
            color: #333;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        </style>
</head>
<body>
<div class="container">
<header class="navbar">
        <div class="logo">
            <img src="images/mdx_logo.png" alt="Mangasay Data Exchange Logo">
            <h2>
                 <?php if ($category_name): ?>
                    <span style="display: block; font-size: 20px;">Category:</span>
                    <span style="display: block;"><?php echo htmlspecialchars($category_name); ?></span>
                 <?php else: ?>
                    All Datasets
                 <?php endif; ?>
            </h2>
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
    <form id="searchForm" action="search_results.php" method="GET">
        <div class="search-bar">
        <input type="text" name="search" placeholder="Search datasets" onfocus="showDropdown()" onblur="hideDropdown()">
        <?php if ($category_id): ?>
            <input type="hidden" name="category" value="<?php echo htmlspecialchars($category_name); ?>">
        <?php endif; ?>
        <button>
            <img src="images/search_icon.png" alt="Search">
        </button>
    </form>
    <a id="category-btn" onclick="showModal()" style="cursor: pointer;">CATEGORY</a>

    <span class="tooltip-wrapper">
        <a href="<?= $upload_disabled ? '#' : 'uploadselection.php' ?>" 
        id="add-data-btn" 
        class="add-data-btn<?= $upload_disabled ? ' disabled-link' : '' ?>">
            ADD DATA
        </a>
        <?php if ($upload_disabled): ?>
            <span class="tooltip-text">You must be part of an organization to upload datasets.</span>
        <?php endif; ?>
    </span>
</div>

<!-- Visibility filter sidebar -->
<!-- This is now handled by the controls-wrapper -->

<div id="wrapper">
    <?php
    // Set variables required by dataset_layout.php
    $page_title = $category_name ? 'Category: ' . htmlspecialchars($category_name) : 'All Datasets';
    $filter_base_url = "datasetsbycategory.php" . ($category_id ? "?category_id=$category_id" : "");
    
    // Store the current category in session for back navigation
    if ($category_id) {
        $_SESSION['last_category_id'] = $category_id;
        $_SESSION['last_category_name'] = $category_name;
    }
    
    // Include the common layout
    include 'dataset_layout.php';
    ?>
</div>
<?php include 'category_modal.php'; // Include the modal?>
<?php include 'sidebar.php'; // Include the sidebar ?>
<script>
        function showModal() {
            document.getElementById("categoryModal").style.display = "flex";
        }
        function hideModal() {
            document.getElementById("categoryModal").style.display = "none";
        }
        
        document.addEventListener("DOMContentLoaded", function () {
            document.getElementById("categoryModal").style.display = "none";
            
            // Initialize view toggle
            const savedView = localStorage.getItem('datasetViewPreference');
            if (savedView) {
                toggleView(savedView);
            }
            
            // Mobile menu toggle functionality
            const mobileMenuToggle = document.getElementById('mobile-menu-toggle');
            const navLinks = document.getElementById('nav-links');
            
            mobileMenuToggle.addEventListener('click', function() {
                navLinks.classList.toggle('active');
            });
            
            // Profile icon click handler
            document.getElementById('navbar-profile-icon').addEventListener('click', function() {
                document.querySelector('.sidebar').classList.add('active');
                document.querySelector('.sidebar-overlay').classList.add('active');
            });
        });

    function upvoteDataset(datasetId) {
        const upvoteButton = document.querySelector(`[data-id="${datasetId}"] button`);
        const countSpan = document.getElementById(`upvote-count-${datasetId}`);
        
        // Check if the button is already upvoted (has 'upvoted' class)
        const isUpvoted = upvoteButton.classList.contains('upvoted');

        // Toggle the class based on the upvote state
        if (isUpvoted) {
            upvoteButton.classList.remove('upvoted');
            upvoteButton.textContent = '⬆ Upvote'; // Change text to 'Upvote' when unvoted
        } else {
            upvoteButton.classList.add('upvoted');
            upvoteButton.textContent = '⬆ Upvoted'; // Change text to 'Upvoted' when clicked
        }

        fetch('upvote.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `dataset_id=${datasetId}`
        })
        .then(response => response.json())
        .then(data => {
            let current = parseInt(countSpan.textContent);

            if (data.status === 'voted' && !isUpvoted) {
                countSpan.textContent = current + 1;
            } else if (data.status === 'unvoted' && isUpvoted) {
                countSpan.textContent = current - 1;
            } else if (data.status === 'unauthorized') {
                alert('You must be logged in to upvote.');
            } else {
                alert('Something went wrong.');
            }
        });
    }
</script>
</body>
</html>