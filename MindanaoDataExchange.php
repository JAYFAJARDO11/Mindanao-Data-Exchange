<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MDX</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: url('images/Mindanao.png');
            background-size: cover;
            background-attachment: fixed;
            text-align: center;
        }
        .navbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 15px 2%;
            background-color: #0c1a36;
            color: white;
        }
        .logo img {
            width: 100px;
            height: auto;
        }
        .search-bar {
            flex-grow: 1;
            margin-left: 100px;
            display: flex;
            align-items: center;
            position: relative;
        }
        .search-bar input {
            padding: 10px;
            width: 100%;
            max-width: 300px;
            border-radius: 5px;
            border: none;
        }
        .search-dropdown {
            position: absolute;
            top: 45px;
            left: 0;
            width: 100%;
            max-width: 300px;
            background: white;
            border-radius: 5px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            display: none;
            z-index: 10;
        }
        .nav-links a {
            color: white;
            margin-left: 20px;
            text-decoration: none;
            font-size: 18px;
        }
        .wrapper {
            padding: 50px 5%;
            margin-top: 50px;
        }
        h1 {
            font-size: 90px;
            font-weight: bold;
            margin-bottom: 20px;
        }
        .stats-box {
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: white;
            padding: 20px 0px;
            width: 30%;
            margin: 0 auto;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            font-size: 30px;
            font-weight: bold;
        }
        .stat {
            flex: 1;
            text-align: center;
        }
        .divider {
            width: 3px;
            background-color: black;
            height: 90px;
        }
        .upload-section {
            position: fixed;
            bottom: 20px;
            right: 20px;
        }
        .upload-btn img {
            width: 60px;
            height: auto;
        }
        @media (max-width: 1024px) {
            .stats-box {
                width: 50%;
                font-size: 24px;
            }
            h1 {
                font-size: 60px;
            }
        }
        @media (max-width: 768px) {
            .navbar {
                flex-direction: column;
                align-items: center;
            }
            .search-bar {
                margin-left: 0;
                width: 100%;
            }
            .stats-box {
                flex-direction: column;
                width: 80%;
                font-size: 20px;
            }
            .divider {
                width: 100%;
                height: 2px;
            }
            h1 {
                font-size: 50px;
            }
        }
        @media (max-width: 480px) {
            .stats-box {
                width: 90%;
                font-size: 18px;
            }
            h1 {
                font-size: 40px;
            }
        }
    </style>
</head>
<body>
    <header class="navbar">
        <div class="logo">
            <img src="images/mdx_logo.png" alt="Mangasay Data Exchange Logo">
        </div>
        <div class="search-bar">
            <input type="text" placeholder="Search datasets" onfocus="showDropdown()" onblur="hideDropdown()">
            <div class="search-dropdown" id="searchDropdown">
                <p class="trending-title">Trending</p>
                <ul>
                    <li>Smart City IoT Sensor Readings</li>
                    <li>Davao City</li>
                    <li>Space Tourism Flight Records</li>
                    <li>Extreme Weather Events Database</li>
                </ul>
            </div>
        </div>
        <nav class="nav-links">
            <a href="login.php">Login</a>
            <a href="AccountSelectionPage.php">Sign up</a>
        </nav>
    </header>
    <main class="wrapper">
        <h1>Mangasay <br> Data Exchange </h1>
        <p>Discover, Share, and Transform Data Seamlessly.</p>
        <div class="stats-box">
            <div class="stat">
                <span class="stat-number">18,000</span>
                <p>Datasets</p>
            </div>
            <div class="divider"></div>
            <div class="stat">
                <span class="stat-number">2,000</span>
                <p>Sources</p>
            </div>
        </div>
    </main>
    <div class="upload-section">
        <a href="login.php" class="upload-btn">
            <img src="images/upload_button.png" alt="Upload">
        </a>
        <p>Upload Data</p>
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
    </script>
</body>
</html>
