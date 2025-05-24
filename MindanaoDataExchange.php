<?php
session_start();
include 'db_connection.php'; // Include your database connection file

// Query to count the number of dataset batches in the database
$sql = "SELECT COUNT(*) AS dataset_count FROM dataset_batches";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);
$dataset_count = $row['dataset_count']; // Store the dataset batch count
// Query to count the number of unique users (distinct user_id) in the dataset_batches table
$sql_sources = "SELECT COUNT(DISTINCT user_id) AS unique_sources FROM dataset_batches";
$result_sources = mysqli_query($conn, $sql_sources);
$row_sources = mysqli_fetch_assoc($result_sources);
$sources_count = $row_sources['unique_sources']; // Store the unique sources count
?>
<script src="https://kit.fontawesome.com/2c68a433da.js" crossorigin="anonymous"></script>
<!DOCTYPE html>
<html lang="en">
<head>
<link rel="stylesheet" href="assets/css/mindanaodataexchange.css">
<?php include 'includes/background_styles.php'; ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MDX</title>
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
    
    .search-dropdown {
        position: absolute;
        top: 45px;
        left: 0;
        width: 100%;
        max-width: 300px;
        background: white;
        border: 1px solid #ccc;
        border-radius: 5px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        display: none;
        z-index: 10;
    }
    .search-dropdown ul li, .trending-title {
        padding: 10px;
        cursor: pointer;
        transition: background 0.3s;
        color: black;
    }
    .search-dropdown .trending-title {
        font-weight: bold;
        padding: 8px 10px;
        border-bottom: 1px solid #ccc;
        text-align: left;
    }
    .search-dropdown ul {
        list-style: none;
        margin: 0;
        padding: 0;
    }
    .search-dropdown ul li {
        padding: 10px;
        cursor: pointer;
        transition: background 0.3s;
        text-align: left;
    }
    .search-dropdown ul li:hover {
        background: #cfd9ff;
    }
    .wrapper {
        padding: 50px 5%;
        margin-top: 50px;
        position: relative;
        z-index: 1; 
    }
    h1 {
        font-size: 90px;
        font-weight: 600;
        margin-bottom: 20px;
        color: rgba(0, 153, 255, 0.8);
        text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.2);
        transition: all 0.3s ease; /* Smooth transition for all properties */
    }
    h1:hover {
        color: rgba(0, 172, 255, 1); /* Change color on hover */
        font-size: 95px; /* Slightly increase font size */
        text-shadow: 2px 2px 5px rgba(233, 230, 230, 0.3); /* Enhance shadow */
    }

    #tagline {
        color: rgba(0, 153, 255, 0.8);
        text-align: center;
        font-size: 1.2rem;
        margin-top: 10px;
        text-shadow: 1px 1px 5px rgba(255, 253, 253, 0.67);
        transition: all 0.3s ease; /* Smooth transition for hover effects */
    }

    #tagline:hover {
        color: rgba(0, 172, 255, 1); /* Change color on hover (brighter blue) */
        font-size: 1.3rem; /* Slightly increase font size */
        text-shadow: 2px 2px 8px rgb(255, 253, 253); /* Enhance shadow */
    }

    .stats-box {
        display: flex;
        justify-content: center;
        align-items: center;
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        padding: 20px 0px 0px 0px;
        width: 30%;
        margin: 0 auto;
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        font-size: 30px;
        font-weight: bold;
        color: #ffffff;
    }
    .stat {
        flex: 1;
        text-align: center;
        color:rgba(28, 132, 227, 0.8);
    }
    .divider {
        width: 3px;
        background-color: black;
        height: 90px;
        margin-top: -20px
    }
    .upload-section {
        position: fixed;
        bottom: 20px;
        right: 20px;
        color:rgba(0, 153, 255, 0.8);
    }
    .upload-btn {
        display: inline-block;
        padding: 20px;
        background-color:rgba(0, 153, 255, 0.8);
        border-radius: 8px; /* Rounded corners */
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); /* Subtle shadow */
        transition: all 0.3s ease;
    }

    .upload-btn i {
        font-size: 40px; /* Larger icon size */
        color: #ffffff; /* White color for the icon */
    }

    .upload-btn:hover {
        background-color: #a0b6f3; /* Darker blue when hovered */
        transform: scale(1.1); /* Slightly increase size on hover */
    }
    
    .upload-btn p {
        font-size: 16px;
        color: black;
    }
    .profile-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background-color: white; 
        display: flex;
        align-items: center;
        justify-content: center;
        margin-left: 70px;
    }
    .profile-icon img {
        width: 150%;
        height: auto;
        border-radius: 50%;
        object-fit: cover;
        cursor: pointer;
    }
    .nav-links {
        display: flex;
        align-items: center;
        gap: 20px;
    }
    /* Footer Styles */
    #wrapper {
        min-height: 100vh; /* Full viewport height */
        position: relative;
        margin-bottom: 100px; /* Add space to prevent footer visibility */
    }
    
    footer {
        background-color: #0099ff;
        color: white;
        padding: 40px 0;
        width: 100%;
        box-shadow: 0 4px 10px rgba(0,0,0,0.2);
        margin-top: 200px; /* Push footer down */
    }
    
    .footer-container {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 30px;
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 30px;
    }
    
    .footer-column {
        padding: 0 15px;
        display: flex;
        flex-direction: column;
        align-items: center;
    }
    
    .footer-heading {
        font-size: 20px;
        margin-bottom: 15px;
        font-weight: bold;
        letter-spacing: 0.5px;
    }
    
    .footer-divider {
        width: 50px;
        height: 3px;
        background-color: white;
        margin: 0 auto 20px;
    }
    
    .footer-text {
        font-size: 15px;
        margin: 8px 0;
        line-height: 1.5;
        text-align: center;
    }
    
    .footer-description {
        font-size: 15px;
        line-height: 1.8;
        margin: 0;
        text-align: center;
        max-width: 400px;
    }
    
    .social-icons {
        display: flex;
        justify-content: center;
        gap: 22px;
        margin-bottom: 25px;
    }
    
    .social-link {
        color: white;
        font-size: 24px;
        transition: transform 0.3s;
    }
    
    .social-link:hover {
        transform: scale(1.2);
    }
    
    .footer-links {
        display: flex;
        flex-direction: column;
        gap: 12px;
        align-items: center;
    }
    
    .footer-link {
        color: white;
        text-decoration: none;
        font-size: 15px;
        transition: color 0.3s;
    }
    
    .footer-link:hover {
        color: #e0e0e0;
    }
    
    </style>
</head>
<body>
    
    <div id="wrapper">
        <header class="mdx-navbar-new">
            <div class="mdx-logo">
                <img src="images/mdx_logo.png" alt="Mangasay Data Exchange Logo">
            </div>

            <button class="mdx-menu-toggle" id="mobile-menu-toggle">
                <i class="fas fa-bars"></i>
            </button>

            <nav class="mdx-nav-links" id="nav-links">
                <a href="login.php">Login</a>
                <a href="AccountSelectionPage.php">Sign up</a>
            </nav>
        </header>
        <main class="wrapper">
            <h1>Mangasay <br> Data Exchange </h1>
            <p id="tagline">Discover, Share, and Transform Data Seamlessly.</p>
            <div class="stats-box">
                <div class="stat">
                <span class="stat-number"><?= number_format($dataset_count) ?></span>
                    <p>Datasets</p>
                </div>
                <div class="divider"></div>
                <div class="stat">
                <span class="stat-number"><?= number_format($sources_count) ?></span>
                    <p>Sources</p>
                </div>
            </div>
        </main>
        
        <div class="upload-section">
            <a href="login.php" class="upload-btn">
                <i class="fa-solid fa-upload"></i>
            </a>
            <p>Upload Data</p>
        </div>
    </div>
    <script>
        function showDropdown() {
            document.getElementById("searchDropdown").style.display = "block";
        }
        function hideDropdown() {
            setTimeout(() => {
                document.getElementById("searchDropdown").style.display = "none";
            }, 200);
        }
        
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

    <!-- Footer with internal CSS styling -->
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
                
                <!-- Important links -->
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
