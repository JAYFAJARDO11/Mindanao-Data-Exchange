<?php
session_start();
include 'db_connection.php'; // Include your database connection file

// Include session update to ensure organization_id is synchronized
include 'update_session.php';

if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if not authenticated
    header("Location: index.php");
    exit();
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

// Query to count the number of datasets in the database
$sql = "SELECT COUNT(*) AS dataset_count FROM datasets";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);
$dataset_count = $row['dataset_count']; // Store the dataset count
// Query to count the number of unique users (distinct user_id) in the datasets table
$sql_sources = "SELECT COUNT(DISTINCT user_id) AS unique_sources FROM datasets";
$result_sources = mysqli_query($conn, $sql_sources);
$row_sources = mysqli_fetch_assoc($result_sources);
$sources_count = $row_sources['unique_sources']; // Store the unique sources count
$upload_disabled = !isset($_SESSION['organization_id']) || $_SESSION['organization_id'] == null;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MDX</title>
    <link rel="stylesheet" href="assets/css/homelogin.css">
    <script src="https://kit.fontawesome.com/2c68a433da.js" crossorigin="anonymous"></script>
    <?php include 'includes/background_styles.php'; ?>
    <style>
    /* New navbar that avoids conflicts with homelogin.css */
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
        margin-top: 30px !important;
        margin-left: auto !important;
        margin-right: auto !important;
        max-width: 1200px !important;
        width: 100% !important;
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
    
    .mdx-search-container {
        position: relative !important;
        display: flex !important;
        align-items: center !important;
        flex: 1 !important;
        justify-content: center !important;
        max-width: 500px !important;
        width: 100% !important;
        margin: 0 20px !important;
    }
    
    .mdx-search-bar {
        display: flex !important;
        align-items: center !important;
        background: white !important;
        border-radius: 5px !important;
        padding: 8px 10px !important;
        width: 100% !important;
    }
    
    .mdx-search-bar input {
        border: none !important;
        outline: none !important;
        background: transparent !important;
        width: 100% !important;
        padding: 5px !important;
    }
    
    .mdx-search-bar button {
        background: none !important;
        border: none !important;
        cursor: pointer !important;
        padding: 5px !important;
    }
    
    .mdx-search-bar img {
        width: 20px !important;
        height: 20px !important;
    }
    
    .mdx-toggle-wrapper {
        display: flex !important;
        align-items: center !important;
        flex: 0 0 auto !important;
    }
    
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
    
    .mdx-profile-icon {
        width: 40px !important;
        height: 40px !important;
        border-radius: 50% !important;
        background-color: white !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        margin-left: 70px !important;
        position: relative !important;
        cursor: pointer !important;
    }
    
    .mdx-profile-icon img {
        width: 150% !important;
        height: auto !important;
        border-radius: 50% !important;
        object-fit: cover !important;
    }
    
    .mdx-profile-icon img:hover {
        transform: scale(1.2) !important;
    }
    
    .mdx-notification-badge {
        position: absolute !important;
        top: -5px !important;
        right: -5px !important;
        background-color: #ff3b30 !important;
        color: white !important;
        border-radius: 50% !important;
        width: 18px !important;
        height: 18px !important;
        font-size: 12px !important;
        font-weight: bold !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        z-index: 1001 !important;
        padding: 0 !important;
        line-height: 18px !important;
        text-align: center !important;
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
        
        .mdx-search-container {
            max-width: 65% !important;
            margin-left: 15px !important;
            margin-right: auto !important;
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
        
        .mdx-profile-icon {
            margin: 15px auto !important;
        }
    }

    @media screen and (max-width: 480px) {
        .mdx-navbar-new {
            padding: 8px 10px !important;
        }
        
        .mdx-logo img {
            width: 40px !important;
        }
        
        .mdx-search-container {
            max-width: 60% !important;
        }
    }
    
    /* Other non-navbar styles */
    h1 {
        font-weight: 600;
        margin-bottom: 20px;
        color: rgba(0, 153, 255, 0.8);
        text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.2);
        transition: all 0.3s ease;
    }
    h1:hover {
        color: rgba(0, 172, 255, 1);
        text-shadow: 2px 2px 5px rgba(233, 230, 230, 0.3);
    }
    #tagline {
        color: rgba(0, 153, 255, 0.8);
        text-align: center;
        margin-top: 10px;
        text-shadow: 1px 1px 5px rgba(255, 253, 253, 0.67);
        transition: all 0.3s ease;
    }
    #tagline:hover {
        color: rgba(0, 172, 255, 1);
        text-shadow: 2px 2px 8px rgb(255, 253, 253);
    }
    .stat {
        flex: 1;
        text-align: center;
        color: rgba(28, 132, 227, 0.8);
    }
    .divider {
        background-color: black;
    }
    .tooltip-text {
        position: absolute;
        top: 50%;
        right: 110%;
        transform: translateY(-50%);
        background-color: #333;
        color: #fff;
        padding: 6px 10px;
        border-radius: 5px;
        font-size: 14px;
        white-space: nowrap;
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.3s ease;
        z-index: 100;
    }
    .upload-btn:hover .tooltip-text {
        opacity: 1;
        visibility: visible;
    }
    .upload-wrapper {
        position: relative;
        display: inline-block;
    }
    .upload-wrapper.has-tooltip:hover .tooltip-text {
        opacity: 1;
        visibility: visible;
    }
    .upload-btn.disabled {
        pointer-events: none;
        opacity: 0.5;
    }
    
    .upload-section {
        position: fixed;
        bottom: 20px;
        right: 20px;
        color: rgba(0, 153, 255, 0.8);
    }
    
    .upload-btn {
        display: inline-block;
        padding: 20px;
        background-color: rgba(0, 153, 255, 0.8);
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        transition: all 0.3s ease;
    }

    .upload-btn i {
        font-size: 40px;
        color: #ffffff;
    }

    .upload-btn:hover {
        background-color: #a0b6f3;
        transform: scale(1.1);
    }
    
    .search-dropdown {
        position: absolute !important;
        top: 45px !important;
        left: 0 !important;
        width: 100% !important;
        max-width: 500px !important;
        background: white !important;
        border: 1px solid #ccc !important;
        border-radius: 5px !important;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1) !important;
        display: none !important;
        z-index: 1002 !important;
    }
    .search-dropdown ul li, .trending-title {
        padding: 10px !important;
        cursor: pointer !important;
        transition: background 0.3s !important;
        color: black !important;
    }
    .search-dropdown .trending-title {
        font-weight: bold !important;
        padding: 8px 10px !important;
        border-bottom: 1px solid #ccc !important;
        text-align: left !important;
    }
    .search-dropdown ul {
        list-style: none !important;
        margin: 0 !important;
        padding: 0 !important;
    }
    .search-dropdown ul li {
        padding: 10px !important;
        cursor: pointer !important;
        transition: background 0.3s !important;
        text-align: left !important;
    }
    .search-dropdown ul li:hover {
        background: #cfd9ff !important;
    }
    </style>
</head>
<body>
    <div id="wrapper">
        <header class="mdx-navbar-new">
            <div class="mdx-logo">
                <img src="images/mdx_logo.png" alt="Mangasay Data Exchange Logo">
            </div>
            
            <div class="mdx-search-container">
                <form id="searchForm" action="search_results.php" method="GET" class="mdx-search-bar">
                    <input type="text" name="search" placeholder="Search datasets" onfocus="showDropdown()" onblur="hideDropdown()">
                    <button type="submit" aria-label="Search">
                        <img src="images/search_icon.png" alt="Search">
                    </button>
                    <div id="searchDropdown" class="search-dropdown">
                        <div class="trending-title">Trending Searches</div>
                        <ul id="trendingSearches">
                            <!-- Trending searches will be loaded here -->
                        </ul>
                    </div>
                </form>
            </div>
            
            <button class="mdx-menu-toggle" id="mobile-menu-toggle">
                <i class="fas fa-bars"></i>
            </button>
            
            <div class="mdx-nav-links" id="nav-links">
                <a href="HomeLogin.php">HOME</a>
                <a href="datasets.php">DATASETS</a>
                <a onclick="showModal()" style="cursor: pointer;">CATEGORY</a>
                <div class="mdx-profile-icon" id="navbar-profile-icon">
                    <img src="images/avatarIconunknown.jpg" alt="Profile">
                    <?php if ($total_count > 0): ?>
                        <span class="mdx-notification-badge"><?php echo $total_count; ?></span>
                    <?php endif; ?>
                </div>
            </div>
        </header>
    
        <script>
            function showDropdown() {
                var dropdown = document.getElementById("searchDropdown");
                if (dropdown) {
                    dropdown.style.display = "block";
                }
            }
            
            function hideDropdown() {
                setTimeout(() => {
                    var dropdown = document.getElementById("searchDropdown");
                    if (dropdown) {
                        dropdown.style.display = "none";
                    }
                }, 200);
            }
            
            function showModal() {
                document.getElementById("categoryModal").style.display = "flex";
            }
            
            function hideModal() {
                document.getElementById("categoryModal").style.display = "none";
            }
            
            document.addEventListener("DOMContentLoaded", function() {
                document.getElementById("categoryModal").style.display = "none";
                
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
        
        <main class="wrapper">
            <h1>Mangasay <br> Data Exchange </h1>
            <p id="tagline">Discover, Share, and Transform Data Seamlessly.</p>
            <div class="stats-box">
                <div class="stat">
                <span class="stat-number"><?= number_format($dataset_count) ?></span>
                    <p>Dataset Files</p>
                </div>
                <div class="divider"></div>
                <div class="stat">
                <span class="stat-number"><?= number_format($sources_count) ?></span> <!-- Dynamic Sources Count -->
                <p>Sources</p>
                </div>
            </div>
        </main>

        <div class="upload-section">
            <div class="upload-wrapper <?php echo $upload_disabled ? 'has-tooltip' : ''; ?>">
                <a href="uploadselection.php" class="upload-btn <?php echo $upload_disabled ? 'disabled' : ''; ?>">
                    <i class="fa-solid fa-upload"></i>
                </a>
                <?php if ($upload_disabled): ?>
                    <span class="tooltip-text">You must be part of an organization to upload datasets.</span>
                <?php endif; ?>
                <p>Upload Data</p>
            </div>
        </div>
        
        <?php include 'sidebar.php'; ?>
        <?php include 'category_modal.php'; // Include the modal?>
        
        <script src="search.js"></script>
    </div><!-- End of wrapper -->

    <!-- Footer with updated styling -->
    <footer>
        <div class="footer-container">
            <!-- Left section with copyright -->
            <div class="footer-column">
                <h3 class="footer-heading">Mangasay Data Exchange</h3>
                <div class="footer-divider"></div>
                <p class="footer-text">&copy; <?php echo date('Y'); ?> MDX</p>
                <p class="footer-text">All Rights Reserved</p>
                <p class="footer-text">Mindanao, Philippines</p>
            </div>
            
            <!-- Center section with description -->
            <div class="footer-column">
                <h3 class="footer-heading">About MDX</h3>
                <div class="footer-divider"></div>
                <p class="footer-description">
                    MDX is Mindanao's premier open data platform, connecting organizations and researchers to discover, 
                    share and transform data across the region. Our mission is to build a collaborative data ecosystem 
                    that empowers decision-makers, researchers, and communities throughout Mindanao.
                </p>
            </div>
            
            <!-- Right section with links and social -->
            <div class="footer-column">
                <h3 class="footer-heading">Connect With Us</h3>
                <div class="footer-divider"></div>
                
                <!-- Social media icons -->
                <div class="social-icons">
                    <a href="https://x.com/MindanaoE66996" target="_blank" class="social-link">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a href="https://www.facebook.com/61576255121231" target="_blank" class="social-link">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="https://www.instagram.com/mindanaodataexchange/" target="_blank" class="social-link">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="https://www.linkedin.com/in/mindanao-data-exchange-270b97366/" target="_blank" class="social-link">
                        <i class="fab fa-linkedin-in"></i>
                    </a>
                </div>
                
                <div class="footer-links">
                    <a href="aboutus.php" class="footer-link">About Us</a>
                    <a href="privacypolicy.php" class="footer-link">Privacy Policy</a>
                    <a href="termsofservices.php" class="footer-link">Terms of Service</a>
                    <a href="mailto:mindanaodataexchange@gmail.com?subject=Inquiry&body=Hello,%0D%0A%0D%0AI would like to inquire about..." class="footer-link">Contact Us</a>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>
