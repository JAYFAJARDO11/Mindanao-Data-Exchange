<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Terms of Services - MDX</title>
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
      max-height: 80vh;     
      overflow-y: auto;
    }
    .main-content::-webkit-scrollbar {
      width: 8px;
    }
    .main-content::-webkit-scrollbar-thumb {
      background-color: rgba(0, 0, 0, 0.2);
      border-radius: 10px;
    }
    .main-content::-webkit-scrollbar-thumb:hover {
      background-color: rgba(0, 0, 0, 0.3);
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
        <h2>Terms of Service</h2>

        <p>Welcome to Mindanao Data Exchange (MDX), Mindanao's premier open data platform. These Terms of Service ("Terms") govern your access to and use of the MDX platform, including all datasets, APIs, tools, and services provided through it. By accessing or using MDX, you agree to be bound by these Terms.</p>

        <strong>If you do not agree with any part of these Terms, you must not use the platform.</strong>

        <h3>1. Definitions</h3>
        <li>"MDX" refers to the Mindanao Data Exchange platform.</li> <br>

        <li>"User" or "You" means any individual or entity accessing or using MDX. </li> <br>

        <li>"Content" refers to datasets, documentation, APIs, metadata, charts, articles, and other information available on MDX.</li>

        <h3>2. Eligibility and Registration</h3>
        Certain services may require registration. You agree to provide accurate, complete, and updated information when registering.

        MDX reserves the right to suspend or terminate accounts that violate these terms or provide misleading information.

        <h3>3. Use of Content and Datasets</h3>
        <strong>Permitted uses include:</strong> <br> <br>

        <li>Downloading and using data for analysis, research, innovation, journalism, policy-making, or development</li> <br>

        <li>Sharing insights or visualizations derived from datasets</li> <br>

        <strong>Prohibited uses include:</strong> <br> <br>

        <li>Using data to violate privacy, confidentiality, or applicable laws</li> <br> 

        <li>Misrepresenting, falsifying, or manipulating data</li> <br> 

        <li>Using the platform to distribute malware or spam</li> <br> 

        <li>Reselling or sublicensing data in violation of its license</li> <br> 

        <li>Always cite MDX and the data provider when using our content.</li> <br>

        <h3>4. User Conduct</h3>
        <strong>Users agree to:</strong> <br>

        <li> Use the platform responsibly and lawfully </li>

        <li> Respect intellectual property rights</li> 

        <li> Refrain from abusive, defamatory, or discriminatory language in feedback or forums </li>

        <li> Avoid activities that could harm MDX infrastructure or reputation</li>

        <h3>5. Intellectual Property Rights</h3>
        All platform content, including logos, interface, branding, and design elements, is the intellectual property of MDX or its partners and protected under Philippine intellectual property laws. You may not use MDX branding without written consent.

        <h3>6. Platform Availability and Modifications</h3>
        We strive to ensure platform availability but do not guarantee uninterrupted access. We may suspend or modify features for maintenance, upgrades, or legal compliance.

        MDX reserves the right to remove or update datasets, features, or accounts at any time without prior notice.

        <h3>7. Disclaimer of Warranties</h3>
        The platform and its content are provided "as is" and "as available." MDX makes no warranties or representations regarding:

        Accuracy, completeness, or timeliness of datasets

        Fitness for a particular purpose

        Error-free or uninterrupted use

        Users access and use the platform at their own risk.

        <h3>8. Limitation of Liability</h3>
        To the fullest extent permitted by law, MDX shall not be liable for any:

        Losses resulting from reliance on inaccurate or outdated data

        Direct or indirect damages, including data loss or system failures

        Unauthorized access or use of user data

        <h3>9. Termination</h3>
        We may suspend or terminate your access to the platform if:

        You breach these Terms

        We are required to do so by law

        The platform is discontinued

        Upon termination, your right to access data or services will immediately cease.

        <h3>10. Governing Law and Jurisdiction</h3>
        These Terms are governed by the laws of the Republic of the Philippines. Any legal disputes shall be subject to the exclusive jurisdiction of the courts in Davao City, Philippines.

        <h3>11. Changes to Terms</h3>
        We may update these Terms at any time. Continued use of the platform constitutes acceptance of the revised Terms. Users will be notified of major updates via email or platform notices.
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
