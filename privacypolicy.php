<?php
session_start();
include 'db_connection.php'; // Include database connection
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Privacy Policy - MDX</title>
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
      <?php if (isset($_SESSION['user_id'])): ?>
        <!-- Logged in navigation -->
        <a href="HomeLogin.php">HOME</a>
        <a href="datasets.php">DATASETS</a>
        <a onclick="showModal()" style="cursor: pointer;">CATEGORY</a>
      <?php else: ?>
        <!-- Not logged in navigation -->
        <a href="MindanaoDataExchange.php">Home</a>
        <a href="login.php">Login</a>
        <a href="AccountSelectionPage.php">Sign Up</a>
      <?php endif; ?>
    </nav>
  </header>

  <div class="content-wrapper">
    <div class="main-content">
<h2>Privacy Policy</h2>

<p>At Mindanao Data Exchange (MDX), we are committed to protecting your privacy and ensuring transparency in how we handle your data. This Privacy Policy outlines the types of personal and non-personal data we collect, how we use and share that data, and your rights in relation to it. We value your trust and aim to maintain it through responsible data practices aligned with Philippine data privacy laws, including the Data Privacy Act of 2012 (RA 10173).</p>

<h3>1. What Information We Collect </h3>
We collect two types of information:<br> <br>

<strong>a. Personal Information</strong><br>   
This includes data that can identify you as an individual:

<li>Name, email address, organization affiliation</li>

<li>Login credentials (if you create an MDX account)</li>

<li>Communications and inquiries submitted via the website</li>

<li>Responses to comments and notifications</li> <br>

<strong>b. Non-Personal and Technical Information</strong><br>
Collected automatically when you use the platform:

<li>Date/time of visits</li>

<li>Referring URLs</li>

<li>Usage patterns and behavior (via cookies and analytics tools)</li>

<h3>2. How We Use Your Information</h3>
We use the data we collect to:

Provide access to datasets and platform features

Personalize user experience and recommend content

Communicate important updates, data releases, or policy changes

Respond to inquiries, suggestions, or complaints

Analyze usage trends and improve platform performance

Conduct research and generate reports in anonymized or aggregated form

Ensure security and prevent fraudulent or harmful behavior

<h3>3. Cookies and Tracking Technologies</h3>
MDX uses cookies and other similar technologies to enhance your experience:

Essential Cookies - For authentication and navigation

Analytics Cookies - For understanding user behavior and optimizing content

Third-party Cookies - From services like Google Analytics

You can control cookies through your browser settings, but disabling them may limit some functionalities.

<h3>4. Data Sharing and Disclosure</h3>
We may share personal or aggregated data with:

Partner organizations (for collaboration, analytics, or co-hosted events)

Government agencies (for compliance with laws or joint initiatives)

Academic institutions and researchers (in anonymized or aggregated formats)

Service providers (e.g., cloud hosting, analytics)

We will never sell your personal information.

<h3>5. Data Security</h3>
We implement physical, electronic, and organizational safeguards to protect your data:

SSL encryption

Regular security audits

Access control policies

Data backups and disaster recovery systems

However, no system is completely secure, and users share information at their own risk.

<h3>6. Your Rights Under Philippine Law</h3>
You have the right to:

Be informed of how your data is collected and used

Access your personal data held by MDX

Correct or update your data

Object to processing or request data deletion

Lodge a complaint with the National Privacy Commission (NPC)

Requests can be sent to privacy@mdx.org.ph.

<h3>7. Retention of Data</h3>
We retain personal information only for as long as necessary:

For user accounts: until deactivated

For correspondence: up to 3 years

For aggregated or statistical data: indefinitely, in anonymized form

<h3>8. Children's Privacy</h3>
MDX is not intended for use by individuals under 18 without parental or guardian consent. We do not knowingly collect data from minors.

<h3>9. Third-party Links</h3>
Our platform may contain links to external websites. MDX is not responsible for the privacy practices of such sites. Users are advised to read their privacy policies.

<h3>10. Policy Updates</h3>
We may update this policy to reflect changes in laws or our practices. We will notify users of major changes via email or platform announcements.
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

  <!-- Add this script if the user is logged in to support the category modal -->
  <?php if (isset($_SESSION['user_id'])): ?>
  <script>
    function showModal() {
      // Check if the modal exists, if not, we need to handle this differently
      if (document.getElementById("categoryModal")) {
        document.getElementById("categoryModal").style.display = "flex";
      } else {
        // Redirect to the category page instead
        window.location.href = "category.php";
      }
    }
    
    function hideModal() {
      if (document.getElementById("categoryModal")) {
        document.getElementById("categoryModal").style.display = "none";
      }
    }
  </script>
  <?php endif; ?>

  <!-- Include the category modal if user is logged in -->
  <?php if (isset($_SESSION['user_id'])): ?>
    <?php include 'category_modal.php'; ?>
  <?php endif; ?>
</body>
</html>
