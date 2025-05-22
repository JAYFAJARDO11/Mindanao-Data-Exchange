<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>About Us - MDX</title>
  <link rel="stylesheet" href="assets/css/mindanaodataexchange.css" />
  <?php include 'includes/background_styles.php'; ?>
  <style>

    body {
      font-family: 'Segoe UI', sans-serif;
      background-color: #f7f9fc;
      margin: 0;
      padding: 0 0 60px 0;
      color: #333;
      position: relative;
      min-height: 100vh;
      overflow-x: hidden;
    }

    /* Modern navbar styles with mdx- prefix */
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

    .content-wrapper {
      max-width: 900px;
      margin: 0 auto 0 auto;
      padding: 0 30px;
      box-sizing: border-box;
      position: relative;
      z-index: 10;
    }

    .main-content {
      background-color: rgba(255, 255, 255, 0.85);
      border-radius: 12px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
      padding: 30px;
      text-align: justify;
      margin-top: 20px;
    }

    .main-content h2 {
      margin-top: 0;
    }

    .info-panels {
      margin-top: 30px;
    }

    .panel {
      background-color: white;
      padding: 20px;
      border-radius: 12px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .panel .btn {
      display: inline-block;
      margin-top: 10px;
      padding: 10px 16px;
      background-color: #0099ff;
      color: white;
      text-decoration: none;
      border-radius: 8px;
      font-weight: bold;
      transition: background-color 0.3s ease;
    }

    .panel .btn:hover {
      background-color: #007acc;
    }
  </style>
</head>
<body>

  <header class="mdx-navbar-new">
    <div class="mdx-logo">
      <img src="images/mdx_logo.png" alt="Mindanao Data Exchange Logo">
    </div>
    
    <button class="mdx-menu-toggle" id="mobile-menu-toggle">
      <i class="fas fa-bars"></i>
    </button>
    
    <nav class="mdx-nav-links" id="nav-links">
      <a href="mindanaodataexchange.php">Home</a>
      <a href="login.php">Login</a>
      <a href="AccountSelectionPage.php">Sign Up</a>
    </nav>
  </header>

  <div class="content-wrapper">
    <div class="main-content">
      <h2>About Us</h2>
      <p>
        MDX (Mindanao Data Exchange) is Mindanao's premier open data platform, an innovative hub that connects organizations, researchers, policymakers, and communities to discover, share, and transform data into actionable insights. We believe that access to reliable and transparent data is essential to empowering sustainable development, informed decision-making, and inclusive progress across the region.
      </p>
      <p>
        Founded on the principles of collaboration, transparency, and accessibility, MDX aims to bridge data gaps by serving as a central repository for datasets spanning key sectors such as agriculture, environment, health, education, infrastructure, economy, and governance. Our platform is designed to facilitate data-driven research, promote policy innovation, and foster partnerships among public institutions, civil society, academia, and the private sector.
      </p>
      <p>
        At MDX, we don't just collect data, we build a dynamic ecosystem where data is a shared resource for social good. Through capacity-building initiatives, open data literacy programs, and strategic collaborations, we empower local communities and decision-makers to harness data for real-world impact.
      </p>
      <p>
        Whether you are a researcher looking for granular datasets, a local government unit aiming to improve service delivery, or a citizen seeking transparency and accountability, MDX is your trusted partner in shaping a data-informed future for Mindanao.
      </p>
    </div>

    <div class="info-panels">
      <div class="panel">
        <h3>Platform Information</h3>
        <p><strong>Operator:</strong> Mindanao Data Exchange (MDX)</p>
        <p><strong>Location:</strong> Davao City, Mindanao, Philippines</p>
        <p><strong>Launched:</strong> March 2025</p>
        <p><strong>Contact:</strong> 09553318341</p>
        <a href="https://www.facebook.com/profile.php?id=61576255121231" class="btn">Contact Us</a>
      </div>
    </div>
  </div>
  
  <script src="https://kit.fontawesome.com/2c68a433da.js" crossorigin="anonymous"></script>
  <script>
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
</body>
</html>
