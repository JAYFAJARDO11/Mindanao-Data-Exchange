<?php
session_start();
include 'db_connection.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Handle force logout
if (isset($_POST['force_logout']) && isset($_POST['user_id'])) {
    $user_id = $_POST['user_id'];
    $sql = "DELETE FROM user_sessions WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
}

// Get total counts for dashboard stats
$total_users = $conn->query("SELECT COUNT(*) as count FROM users")->fetch_assoc()['count'];
$total_active_users = $conn->query("SELECT COUNT(*) as count FROM users WHERE is_active = 1")->fetch_assoc()['count'];
$total_datasets = $conn->query("SELECT COUNT(*) as count FROM dataset_batches")->fetch_assoc()['count'];
$total_organizations = $conn->query("SELECT COUNT(*) as count FROM organizations")->fetch_assoc()['count'];
$total_categories = $conn->query("SELECT COUNT(*) as count FROM datasetcategories")->fetch_assoc()['count'];

// Get total views and downloads
$total_views = $conn->query("SELECT SUM(total_views) as count FROM dataset_batch_analytics")->fetch_assoc()['count'] ?? 0;
$total_downloads = $conn->query("SELECT SUM(total_downloads) as count FROM dataset_batch_analytics")->fetch_assoc()['count'] ?? 0;

// Get recent datasets (last 5)
$sql = "SELECT 
            db.dataset_batch_id,
            d.dataset_id,
            d.title,
            db.visibility,
            db.created_at,
            u.first_name,
            u.last_name,
            c.name AS category_name
        FROM dataset_batches db
        JOIN datasets d ON d.dataset_batch_id = db.dataset_batch_id
        JOIN users u ON db.user_id = u.user_id
        LEFT JOIN datasetcategories c ON d.category_id = c.category_id
        WHERE d.dataset_id = (
            SELECT MIN(dataset_id) FROM datasets WHERE dataset_batch_id = db.dataset_batch_id
        )
        ORDER BY db.created_at DESC
        LIMIT 5";
$recent_datasets = $conn->query($sql);

// Get recent users (last 5)
$sql = "SELECT u.*, o.name AS organization_name 
        FROM users u 
        LEFT JOIN organizations o ON u.organization_id = o.organization_id 
        ORDER BY u.date_joined DESC 
        LIMIT 5";
$recent_users = $conn->query($sql);

// Get recent organizations (last 5)
$sql = "SELECT o.*, COUNT(u.user_id) as member_count 
        FROM organizations o
        LEFT JOIN users u ON o.organization_id = u.organization_id
        GROUP BY o.organization_id
        ORDER BY o.created_at DESC
        LIMIT 5";
$recent_organizations = $conn->query($sql);

// Get public vs private datasets ratio
$public_datasets = $conn->query("SELECT COUNT(*) as count FROM dataset_batches WHERE visibility = 'Public'")->fetch_assoc()['count'];
$private_datasets = $conn->query("SELECT COUNT(*) as count FROM dataset_batches WHERE visibility = 'Private'")->fetch_assoc()['count'];

// Get top categories by dataset count
$sql = "SELECT c.name, COUNT(d.dataset_id) as dataset_count
        FROM datasetcategories c
        JOIN datasets d ON c.category_id = d.category_id
        GROUP BY c.category_id
        ORDER BY dataset_count DESC
        LIMIT 5";
$top_categories = $conn->query($sql);

// Get organizations with most members
$sql = "SELECT o.name, COUNT(u.user_id) as member_count
        FROM organizations o
        JOIN users u ON o.organization_id = u.organization_id
        GROUP BY o.organization_id
        ORDER BY member_count DESC
        LIMIT 5";
$top_organizations = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        /* Reset and Base Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            color: #333;
            line-height: 1.6;
        }
        
        /* Layout */
        .sidebar {
            background: #0c1a36;
            color: white;
            height: 100vh;
            position: fixed;
            width: 250px;
            padding: 20px;
            overflow-y: auto;
        }
        
        .main-content {
            margin-left: 250px;
            padding: 20px;
        }
        
        /* Typography */
        h1, h2, h3, h4, h5, h6 {
            margin-bottom: 15px;
        }
        
        p {
            margin-bottom: 15px;
        }
        
        /* Navigation */
        .sidebar h3 {
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid rgba(255,255,255,0.2);
        }
        
        .nav {
            list-style: none;
        }
        
        .nav-link {
            display: block;
            color: white;
            padding: 10px 20px;
            margin: 5px 0;
            border-radius: 5px;
            text-decoration: none;
            transition: background 0.3s;
        }
        
        .nav-link:hover {
            background: rgba(255,255,255,0.1);
        }
        
        .nav-link.active {
            background: #0099ff;
        }
        
        /* Cards */
        .card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            overflow: hidden;
        }
        
        .card-body {
            padding: 20px;
        }
        
        /* Stat Cards */
        .stat-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        
        .stat-card i {
            font-size: 2em;
            color: #0099ff;
            margin-bottom: 10px;
        }
        
        .stat-card h3 {
            font-size: 1.8rem;
            margin: 5px 0;
        }
        
        .stat-card p {
            color: #6c757d;
        }
        
        /* Widget Components */
        .widget {
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            overflow: hidden;
        }
        
        .widget-title {
            background: #f8f9fa;
            padding: 15px 20px;
            border-bottom: 1px solid #eee;
            font-weight: bold;
            display: flex;
            align-items: center;
        }
        
        .widget-title i {
            margin-right: 10px;
            color: #0099ff;
        }
        
        .widget-body {
            padding: 15px;
        }
        
        /* Data Items */
        .recent-item {
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        
        .recent-item:last-child {
            border-bottom: none;
        }
        
        /* Utility Classes */
        .d-flex {
            display: flex;
        }
        
        .justify-content-between {
            justify-content: space-between;
        }
        
        .align-items-center {
            align-items: center;
        }
        
        .mb-1 {
            margin-bottom: 5px;
        }
        
        .mb-3 {
            margin-bottom: 15px;
        }
        
        .mb-4 {
            margin-bottom: 20px;
        }
        
        .mt-3 {
            margin-top: 15px;
        }
        
        .text-center {
            text-align: center;
        }
        
        .text-muted {
            color: #6c757d;
            font-size: 0.85rem;
        }
        
        h6 {
            margin: 0 0 5px 0;
            font-size: 1rem;
        }
        
        /* Badges */
        .badge {
            display: inline-block;
            padding: 4px 8px;
            font-size: 0.75rem;
            font-weight: bold;
            border-radius: 20px;
            color: white;
        }
        
        .badge-public, .badge-success {
            background-color: #28a745;
        }
        
        .badge-private, .badge-danger {
            background-color: #dc3545;
        }
        
        /* Buttons */
        .btn {
            display: inline-block;
            padding: 8px 12px;
            background-color: #e9ecef;
            color: #333;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            font-size: 14px;
            line-height: 1.5;
            transition: all 0.3s;
        }
        
        .btn:hover {
            opacity: 0.9;
        }
        
        .btn-sm {
            padding: 5px 10px;
            font-size: 12px;
        }
        
        .btn-primary {
            background-color: #0099ff;
            color: white;
        }
        
        /* Alert Messages */
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            position: relative;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        /* Progress Bars */
        .progress {
            display: flex;
            height: 10px;
            overflow: hidden;
            background-color: #e9ecef;
            border-radius: 4px;
            margin-bottom: 10px;
        }
        
        .progress-bar {
            display: flex;
            flex-direction: column;
            justify-content: center;
            color: #fff;
            text-align: center;
            white-space: nowrap;
            background-color: #0099ff;
            transition: width 0.6s ease;
        }
        
        .bg-info {
            background-color: #17a2b8 !important;
        }
        
        .bg-success {
            background-color: #28a745 !important;
        }
        
        /* Layout Grid */
        .row {
            display: flex;
            flex-wrap: wrap;
            margin: 0 -15px 20px -15px;
        }
        
        .col-md-3 {
            flex: 0 0 25%;
            max-width: 25%;
            padding: 0 15px;
        }
        
        .col-md-4 {
            flex: 0 0 33.333333%;
            max-width: 33.333333%;
            padding: 0 15px;
        }
        
        .col-md-6 {
            flex: 0 0 50%;
            max-width: 50%;
            padding: 0 15px;
        }
        
        .col-md-12 {
            flex: 0 0 100%;
            max-width: 100%;
            padding: 0 15px;
        }
        
        /* Responsive Design */
        @media (max-width: 992px) {
            .col-md-3 {
                flex: 0 0 50%;
                max-width: 50%;
            }
        }
        
        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .col-md-3, .col-md-4, .col-md-6, .col-md-12 {
                flex: 0 0 100%;
                max-width: 100%;
            }
            
            .row {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h3>Admin Panel</h3>
        <nav>
            <ul class="nav">
                <li><a class="nav-link active" href="#"><i class="fas fa-home"></i> Dashboard</a></li>
                <li><a class="nav-link" href="admin_users.php"><i class="fas fa-users"></i> Users</a></li>
                <li><a class="nav-link" href="admin_datasets.php"><i class="fas fa-database"></i> Datasets</a></li>
                <li><a class="nav-link" href="admin_organizations.php"><i class="fas fa-building"></i> Organizations</a></li>
                <li><a class="nav-link" href="admin_org_requests.php"><i class="fas fa-clipboard-list"></i> Org Requests</a></li>
                <li><a class="nav-link" href="admin_notifications.php"><i class="fas fa-bell"></i> Notifications</a></li>
                <li><a class="nav-link" href="admin_settings.php"><i class="fas fa-cog"></i> Settings</a></li>
                <li><a class="nav-link" href="admin_to_main.php"><i class="fas fa-globe"></i> Main Site</a></li>
                <li><a class="nav-link" href="admin_logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </nav>
    </div>

    <div class="main-content">
        <h2 class="mb-4">Dashboard</h2>

        <div class="row">
            <div class="col-md-3">
                <div class="stat-card">
                    <i class="fas fa-users"></i>
                    <h3><?php echo $total_users; ?></h3>
                    <p>Total Users</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <i class="fas fa-database"></i>
                    <h3><?php echo $total_datasets; ?></h3>
                    <p>Total Datasets</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <i class="fas fa-eye"></i>
                    <h3><?php echo $total_views; ?></h3>
                    <p>Total Views</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <i class="fas fa-download"></i>
                    <h3><?php echo $total_downloads; ?></h3>
                    <p>Total Downloads</p>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-3">
                <div class="stat-card">
                    <i class="fas fa-building"></i>
                    <h3><?php echo $total_organizations; ?></h3>
                    <p>Organizations</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <i class="fas fa-tags"></i>
                    <h3><?php echo $total_categories; ?></h3>
                    <p>Categories</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <i class="fas fa-user-check"></i>
                    <h3><?php echo $total_active_users; ?></h3>
                    <p>Active Users</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <i class="fas fa-lock"></i>
                    <h3><?php echo $public_datasets; ?> / <?php echo $private_datasets; ?></h3>
                    <p>Public / Private</p>
                </div>
            </div>
        </div>

        <?php if (isset($_GET['message'])): ?>
            <div class="alert alert-success">
                <?php echo htmlspecialchars($_GET['message']); ?>
            </div>
        <?php endif; ?>

        <div class="row">
            <!-- Recent Datasets -->
            <div class="col-md-6">
                <div class="widget">
                    <div class="widget-title">
                        <i class="fas fa-database"></i> Recent Datasets
                    </div>
                    <div class="widget-body">
                        <?php if ($recent_datasets->num_rows === 0): ?>
                            <p class="text-muted">No datasets found</p>
                        <?php else: ?>
                            <?php while ($dataset = $recent_datasets->fetch_assoc()): ?>
                                <div class="recent-item">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1"><?php echo htmlspecialchars($dataset['title']); ?></h6>
                                            <small class="text-muted">
                                                <i class="fas fa-user"></i> <?php echo htmlspecialchars($dataset['first_name'] . ' ' . $dataset['last_name']); ?> |
                                                <i class="fas fa-calendar"></i> <?php echo date('M d, Y', strtotime($dataset['created_at'])); ?>
                                            </small>
                                        </div>
                                        <div>
                                            <span class="badge <?php echo $dataset['visibility'] == 'Public' ? 'badge-public' : 'badge-private'; ?>">
                                                <?php echo $dataset['visibility']; ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                            <div class="text-center mt-3">
                                <a href="admin_datasets.php" class="btn btn-primary btn-sm">View All Datasets</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Recent Users -->
            <div class="col-md-6">
                <div class="widget">
                    <div class="widget-title">
                        <i class="fas fa-users"></i> Recent Users
                    </div>
                    <div class="widget-body">
                        <?php if ($recent_users->num_rows === 0): ?>
                            <p class="text-muted">No users found</p>
                        <?php else: ?>
                            <?php while ($user = $recent_users->fetch_assoc()): ?>
                                <div class="recent-item">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1"><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></h6>
                                            <small class="text-muted">
                                                <i class="fas fa-envelope"></i> <?php echo htmlspecialchars($user['email']); ?> | 
                                                <i class="fas fa-calendar"></i> <?php echo date('M d, Y', strtotime($user['date_joined'])); ?>
                                            </small>
                                        </div>
                                        <div>
                                            <span class="badge <?php echo $user['is_active'] ? 'badge-success' : 'badge-danger'; ?>">
                                                <?php echo $user['is_active'] ? 'Active' : 'Inactive'; ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                            <div class="text-center mt-3">
                                <a href="admin_users.php" class="btn btn-primary btn-sm">View All Users</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Analytics Widgets Row -->
        <div class="row">
            <!-- Top Categories -->
            <div class="col-md-6">
                <div class="widget">
                    <div class="widget-title">
                        <i class="fas fa-tags"></i> Top Categories
                    </div>
                    <div class="widget-body">
                        <?php if ($top_categories->num_rows === 0): ?>
                            <p class="text-muted">No categories found</p>
                        <?php else: ?>
                            <?php 
                            $max_count = 0;
                            $categories = [];
                            while ($category = $top_categories->fetch_assoc()) {
                                $categories[] = $category;
                                if ($category['dataset_count'] > $max_count) {
                                    $max_count = $category['dataset_count'];
                                }
                            }
                            ?>
                            
                            <?php foreach ($categories as $category): ?>
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span><?php echo htmlspecialchars($category['name']); ?></span>
                                        <span><?php echo $category['dataset_count']; ?> datasets</span>
                                    </div>
                                    <div class="progress">
                                        <div class="progress-bar bg-info" 
                                             style="width: <?php echo ($category['dataset_count'] / $max_count) * 100; ?>%">
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Top Organizations -->
            <div class="col-md-6">
                <div class="widget">
                    <div class="widget-title">
                        <i class="fas fa-building"></i> Top Organizations
                    </div>
                    <div class="widget-body">
                        <?php if ($top_organizations->num_rows === 0): ?>
                            <p class="text-muted">No organizations found</p>
                        <?php else: ?>
                            <?php 
                            $max_count = 0;
                            $orgs = [];
                            while ($org = $top_organizations->fetch_assoc()) {
                                $orgs[] = $org;
                                if ($org['member_count'] > $max_count) {
                                    $max_count = $org['member_count'];
                                }
                            }
                            ?>
                            
                            <?php foreach ($orgs as $org): ?>
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span><?php echo htmlspecialchars($org['name']); ?></span>
                                        <span><?php echo $org['member_count']; ?> members</span>
                                    </div>
                                    <div class="progress">
                                        <div class="progress-bar bg-success" 
                                             style="width: <?php echo ($org['member_count'] / $max_count) * 100; ?>%">
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            <div class="text-center mt-3">
                                <a href="admin_organizations.php" class="btn btn-primary btn-sm">View All Organizations</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Recent Organizations Widget -->
        <div class="row">
            <div class="col-md-12">
                <div class="widget">
                    <div class="widget-title">
                        <i class="fas fa-building"></i> Recent Organizations
                    </div>
                    <div class="widget-body">
                        <div class="row">
                            <?php if ($recent_organizations->num_rows === 0): ?>
                                <div class="col-md-12">
                                    <p class="text-muted">No organizations found</p>
                                </div>
                            <?php else: ?>
                                <?php while ($org = $recent_organizations->fetch_assoc()): ?>
                                    <div class="col-md-4">
                                        <div class="card">
                                            <div class="card-body">
                                                <h5 class="card-title"><?php echo htmlspecialchars($org['name']); ?></h5>
                                                <p>
                                                    <i class="fas fa-users"></i> Members: <?php echo $org['member_count']; ?><br>
                                                    <i class="fas fa-calendar"></i> Created: <?php echo date('M d, Y', strtotime($org['created_at'])); ?>
                                                </p>
                                                <a href="admin_organizations.php?org_id=<?php echo $org['organization_id']; ?>" class="btn btn-primary btn-sm">
                                                    View Members
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>