<?php
session_start();
include 'db_connection.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Delete comment functionality
if (isset($_POST['delete_comment'])) {
    $comment_id = $_POST['comment_id'];
    
    $delete_query = "DELETE FROM datasetcomments WHERE comment_id = ?";
    $stmt = $conn->prepare($delete_query);
    $stmt->bind_param("i", $comment_id);
    
    if ($stmt->execute()) {
        // Log the action
        $admin_id = $_SESSION['admin_id'];
        $action = "Deleted comment ID: " . $comment_id;
        $log_query = "INSERT INTO admin_logs (admin_id, action, timestamp) VALUES (?, ?, NOW())";
        
        // Check if admin_logs table exists
        $table_check = $conn->query("SHOW TABLES LIKE 'admin_logs'");
        if ($table_check->num_rows > 0) {
            $log_stmt = $conn->prepare($log_query);
            $log_stmt->bind_param("is", $admin_id, $action);
            $log_stmt->execute();
        }
        
        $_SESSION['success_message'] = "Comment deleted successfully.";
    } else {
        $_SESSION['error_message'] = "Error deleting comment.";
    }
    
    header("Location: admin_datasets.php");
    exit();
}

// Check if required tables exist
$required_tables = [
    'datasets' => false,
    'datasetcomments' => false,
    'datasetversions' => false,
    'dataset_batch_analytics' => false
];

$tables_exist = true;
foreach (array_keys($required_tables) as $table) {
    $check_query = "SHOW TABLES LIKE '$table'";
    $result = $conn->query($check_query);
    if ($result->num_rows > 0) {
        $required_tables[$table] = true;
    } else {
        $tables_exist = false;
    }
}

// Pagination settings
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$title = "Datasets";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css">
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
            padding: 20px;
            width: 250px;
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
        
        /* Header */
        .admin-header {
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        
        /* Cards */
        .card {
            background: white;
            border-radius: 5px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            overflow: hidden;
        }
        
        .card-header {
            padding: 15px 20px;
            border-bottom: 1px solid #eee;
            font-weight: bold;
        }
        
        .card-header.primary {
            background-color: #0099ff;
            color: white;
        }
        
        .card-body {
            padding: 20px;
        }
        
        .comment-card {
            border-left: 4px solid #0099ff;
        }
        
        /* Alerts */
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
        
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .alert-info {
            background-color: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }
        
        .alert-warning {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeeba;
        }
        
        .alert-dismissible {
            padding-right: 40px;
        }
        
        .close-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            color: inherit;
            font-size: 20px;
            font-weight: bold;
            cursor: pointer;
            background: none;
            border: none;
            opacity: 0.5;
        }
        
        .close-btn:hover {
            opacity: 1;
        }
        
        /* Tables */
        .table-container {
            overflow-x: auto;
            margin-bottom: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        
        th {
            background-color: #333;
            color: white;
            font-weight: bold;
        }
        
        tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        
        tr:hover {
            background-color: #f1f1f1;
        }
        
        /* Buttons */
        .btn {
            display: inline-block;
            padding: 8px 12px;
            margin-right: 5px;
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
        
        .btn-primary {
            background-color: #0099ff;
            color: white;
        }
        
        .btn-danger {
            background-color: #dc3545;
            color: white;
        }
        
        .btn-info {
            background-color: #17a2b8;
            color: white;
        }
        
        .btn-small {
            padding: 5px 10px;
            font-size: 12px;
        }
        
        /* Tabs */
        .tabs {
            margin-bottom: 20px;
            border-bottom: 1px solid #dee2e6;
        }
        
        .tabs-list {
            display: flex;
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .tab-item {
            margin-bottom: -1px;
        }
        
        .tab-button {
            padding: 10px 15px;
            background: transparent;
            border: 1px solid transparent;
            border-top-left-radius: 5px;
            border-top-right-radius: 5px;
            cursor: pointer;
            position: relative;
            display: block;
            text-decoration: none;
            color: #0099ff;
            font-weight: normal;
        }
        
        .tab-button.active {
            color: #495057;
            background-color: #fff;
            border-color: #dee2e6 #dee2e6 #fff;
            font-weight: bold;
        }
        
        .tab-button:hover:not(.active) {
            border-color: #e9ecef #e9ecef #dee2e6;
        }
        
        .tab-content {
            background: white;
        }
        
        .tab-pane {
            display: none;
            padding: 15px 0;
        }
        
        .tab-pane.active {
            display: block;
        }
        
        /* Forms */
        form {
            margin-bottom: 15px;
        }
        
        input, select, textarea {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            width: 100%;
            margin-bottom: 10px;
        }
        
        /* Utilities */
        .text-center {
            text-align: center;
        }
        
        .mt-4 {
            margin-top: 20px;
        }
        
        .mb-3 {
            margin-bottom: 15px;
        }
        
        .justify-between {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        
        /* Error container */
        .error-container {
            max-width: 600px;
            margin: 50px auto;
            text-align: center;
        }
        
        /* Badges */
        .badge {
            display: inline-block;
            padding: 3px 7px;
            font-size: 12px;
            font-weight: bold;
            line-height: 1;
            color: white;
            text-align: center;
            white-space: nowrap;
            vertical-align: baseline;
            border-radius: 10px;
        }
        
        .badge-success {
            background-color: #28a745;
        }
        
        .badge-secondary {
            background-color: #6c757d;
        }
        
        /* Pagination */
        .pagination {
            display: flex;
            list-style: none;
            padding: 0;
            margin: 20px 0;
            justify-content: center;
        }
        
        .pagination-item {
            margin: 0 5px;
        }
        
        .pagination-link {
            padding: 8px 12px;
            border: 1px solid #ddd;
            color: #0099ff;
            text-decoration: none;
            border-radius: 4px;
            display: block;
        }
        
        .pagination-link:hover {
            background-color: #f8f9fa;
        }
        
        .pagination-link.active {
            background-color: #0099ff;
            color: white;
            border-color: #0099ff;
        }
        
        .pagination-item.disabled .pagination-link {
            color: #6c757d;
            pointer-events: none;
            cursor: default;
            background-color: #fff;
        }
        
        /* Stat cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-top: 20px;
        }
        
        .stat-card {
            padding: 20px;
            text-align: center;
            border-radius: 5px;
            color: white;
        }
        
        .stat-card.info {
            background-color: #17a2b8;
        }
        
        .stat-card.success {
            background-color: #28a745;
        }
        
        .stat-card.primary {
            background-color: #0099ff;
        }
        
        .stat-number {
            font-size: 32px;
            font-weight: bold;
            margin: 10px 0;
        }
        
        /* Responsive design */
        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h3>Admin Panel</h3>
        <nav>
            <ul class="nav">
                <li><a class="nav-link" href="admin_dashboard.php"><i class="fas fa-home"></i> Dashboard</a></li>
                <li><a class="nav-link" href="admin_users.php"><i class="fas fa-users"></i> Users</a></li>
                <li><a class="nav-link active" href="#"><i class="fas fa-database"></i> Datasets</a></li>
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
        <div class="admin-header">
            <h1><i class="fas fa-database"></i> <?php echo $title; ?></h1>
            <p>Manage datasets, comments, and versions</p>
        </div>
        
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success alert-dismissible">
                <?php 
                echo $_SESSION['success_message']; 
                unset($_SESSION['success_message']);
                ?>
                <button type="button" class="close-btn" onclick="this.parentElement.style.display='none';">&times;</button>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="alert alert-danger alert-dismissible">
                <?php 
                echo $_SESSION['error_message']; 
                unset($_SESSION['error_message']);
                ?>
                <button type="button" class="close-btn" onclick="this.parentElement.style.display='none';">&times;</button>
            </div>
        <?php endif; ?>
        
        <?php if (!$tables_exist): ?>
            <div class="error-container">
                <div class="card">
                    <div class="card-body">
                        <h2 style="color: #dc3545;"><i class="fas fa-exclamation-triangle"></i> Missing Tables</h2>
                        <p>Some required database tables are missing:</p>
                        <ul style="list-style: none; margin-bottom: 15px;">
                            <?php foreach ($required_tables as $table => $exists): ?>
                                <?php if (!$exists): ?>
                                    <li style="padding: 10px; background-color: #f8d7da; color: #721c24; margin-bottom: 5px; border-radius: 4px;"><?php echo $table; ?></li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </ul>
                        <p>You need to create these tables to use this functionality.</p>
                        <a href="create_missing_tables.php" class="btn btn-primary">Create Missing Tables</a>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <!-- Tabs navigation -->
            <div class="tabs">
                <ul class="tabs-list" id="adminTabs">
                    <li class="tab-item">
                        <button class="tab-button active" data-tab="datasets">
                            <i class="fas fa-table"></i> Datasets
                        </button>
                    </li>
                    <li class="tab-item">
                        <button class="tab-button" data-tab="comments">
                            <i class="fas fa-comments"></i> Comments
                        </button>
                    </li>
                    <li class="tab-item">
                        <button class="tab-button" data-tab="versions">
                            <i class="fas fa-code-branch"></i> Versions
                        </button>
                    </li>
                    <li class="tab-item">
                        <button class="tab-button" data-tab="analytics">
                            <i class="fas fa-chart-line"></i> Analytics
                        </button>
                    </li>
                </ul>
            </div>
            
            <div class="tab-content" id="adminTabsContent">
                <!-- Datasets Tab -->
                <div class="tab-pane active" id="datasets-tab">
                    <div class="card">
                        <div class="card-header primary">
                            <h3><i class="fas fa-table"></i> All Datasets</h3>
                        </div>
                        <div class="card-body">
                            <div class="table-container">
                                <table>
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Title</th>
                                            <th>User</th>
                                            <th>Category</th>
                                            <th>Visibility</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        // Get datasets with user and category info
                                        $dataset_query = "
                                            SELECT d.dataset_id, d.title, d.visibility,
                                                   u.first_name, u.last_name, 
                                                   dc.name as category_name
                                            FROM datasets d
                                            LEFT JOIN users u ON d.user_id = u.user_id
                                            LEFT JOIN datasetcategories dc ON d.category_id = dc.category_id
                                            ORDER BY d.dataset_id DESC
                                            LIMIT ?, ?";
                                        
                                        $stmt = $conn->prepare($dataset_query);
                                        $stmt->bind_param("ii", $offset, $limit);
                                        $stmt->execute();
                                        $result = $stmt->get_result();
                                        
                                        if ($result->num_rows > 0) {
                                            while ($row = $result->fetch_assoc()) {
                                                echo "<tr>";
                                                echo "<td>" . $row['dataset_id'] . "</td>";
                                                echo "<td>" . htmlspecialchars($row['title']) . "</td>";
                                                echo "<td>" . htmlspecialchars($row['first_name'] . " " . $row['last_name']) . "</td>";
                                                echo "<td>" . htmlspecialchars($row['category_name']) . "</td>";
                                                echo "<td>" . $row['visibility'] . "</td>";
                                                echo "<td>
                                                        <a href='dataset.php?id=" . $row['dataset_id'] . "' class='btn btn-info btn-small'><i class='fas fa-eye'></i> View</a>
                                                      </td>";
                                                echo "</tr>";
                                            }
                                        } else {
                                            echo "<tr><td colspan='6' class='text-center'>No datasets found</td></tr>";
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                            
                            <?php
                            // Pagination for datasets
                            $count_query = "SELECT COUNT(*) as total FROM datasets";
                            $count_result = $conn->query($count_query);
                            $count_row = $count_result->fetch_assoc();
                            $total_datasets = $count_row['total'];
                            $total_pages = ceil($total_datasets / $limit);
                            
                            if ($total_pages > 1):
                            ?>
                            <div class="pagination-container">
                                <ul class="pagination">
                                    <li class="pagination-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
                                        <a class="pagination-link" href="?page=<?php echo $page - 1; ?>" <?php echo ($page <= 1) ? 'onclick="return false"' : ''; ?>>Previous</a>
                                    </li>
                                    
                                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                        <li class="pagination-item">
                                            <a class="pagination-link <?php echo ($page == $i) ? 'active' : ''; ?>" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                        </li>
                                    <?php endfor; ?>
                                    
                                    <li class="pagination-item <?php echo ($page >= $total_pages) ? 'disabled' : ''; ?>">
                                        <a class="pagination-link" href="?page=<?php echo $page + 1; ?>" <?php echo ($page >= $total_pages) ? 'onclick="return false"' : ''; ?>>Next</a>
                                    </li>
                                </ul>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Comments Tab -->
                <div class="tab-pane" id="comments-tab">
                    <div class="card">
                        <div class="card-header primary">
                            <h3><i class="fas fa-comments"></i> Dataset Comments</h3>
                        </div>
                        <div class="card-body">
                            <?php
                            // Get comments with dataset and user info
                            $comment_query = "
                                SELECT c.comment_id, c.dataset_id, c.comment_text, c.timestamp,
                                       d.title as dataset_title,
                                       u.first_name, u.last_name
                                FROM datasetcomments c
                                LEFT JOIN datasets d ON c.dataset_id = d.dataset_id
                                LEFT JOIN users u ON c.user_id = u.user_id
                                ORDER BY c.timestamp DESC
                                LIMIT 20";
                            
                            $comment_result = $conn->query($comment_query);
                            
                            if ($comment_result && $comment_result->num_rows > 0) {
                                while ($comment = $comment_result->fetch_assoc()) {
                            ?>
                                <div class="card comment-card mb-3">
                                    <div class="card-header justify-between">
                                        <div>
                                            <strong><?php echo htmlspecialchars($comment['first_name'] . " " . $comment['last_name']); ?></strong>
                                            <small style="color: #6c757d; margin-left: 10px;">
                                                <?php echo date("M d, Y g:i A", strtotime($comment['timestamp'])); ?>
                                            </small>
                                        </div>
                                        <form method="post" onsubmit="return confirm('Are you sure you want to delete this comment?');">
                                            <input type="hidden" name="comment_id" value="<?php echo $comment['comment_id']; ?>">
                                            <button type="submit" name="delete_comment" class="btn btn-danger btn-small">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        </form>
                                    </div>
                                    <div class="card-body">
                                        <p><?php echo htmlspecialchars($comment['comment_text']); ?></p>
                                        <div class="dataset-info" style="background-color: #f8f9fa; padding: 15px; border-radius: 5px; margin-top: 15px;">
                                            <small style="color: #6c757d;">
                                                Dataset: <a href="dataset.php?id=<?php echo $comment['dataset_id']; ?>">
                                                    <?php echo htmlspecialchars($comment['dataset_title']); ?>
                                                </a>
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            <?php
                                }
                            } else {
                                echo "<div class='alert alert-info'>No comments found.</div>";
                            }
                            ?>
                        </div>
                    </div>
                </div>
                
                <!-- Versions Tab -->
                <div class="tab-pane" id="versions-tab">
                    <div class="card">
                        <div class="card-header primary">
                            <h3><i class="fas fa-code-branch"></i> Dataset Versions</h3>
                        </div>
                        <div class="card-body">
                            <div class="table-container">
                                <table>
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Dataset</th>
                                            <th>Version</th>
                                            <th>Created By</th>
                                            <th>Date</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        // Get versions with creator info
                                        $version_query = "
                                            SELECT v.version_id, v.dataset_batch_id, v.version_number, 
                                                   v.title, v.created_at, v.is_current,
                                                   u.first_name, u.last_name
                                            FROM datasetversions v
                                            LEFT JOIN users u ON v.created_by = u.user_id
                                            ORDER BY v.created_at DESC
                                            LIMIT 20";
                                        
                                        $version_result = $conn->query($version_query);
                                        
                                        if ($version_result && $version_result->num_rows > 0) {
                                            while ($version = $version_result->fetch_assoc()) {
                                                echo "<tr>";
                                                echo "<td>" . $version['version_id'] . "</td>";
                                                echo "<td>" . htmlspecialchars($version['title']) . "</td>";
                                                echo "<td>" . $version['version_number'] . "</td>";
                                                echo "<td>" . htmlspecialchars($version['first_name'] . " " . $version['last_name']) . "</td>";
                                                echo "<td>" . date("M d, Y", strtotime($version['created_at'])) . "</td>";
                                                echo "<td>" . ($version['is_current'] ? '<span class="badge badge-success">Current</span>' : '<span class="badge badge-secondary">Previous</span>') . "</td>";
                                                echo "<td>
                                                        <a href='dataset.php?batch_id=" . $version['dataset_batch_id'] . "&version=" . $version['version_id'] . "' class='btn btn-info btn-small'>
                                                            <i class='fas fa-eye'></i> View
                                                        </a>
                                                      </td>";
                                                echo "</tr>";
                                            }
                                        } else {
                                            echo "<tr><td colspan='7' class='text-center'>No versions found</td></tr>";
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Analytics Tab -->
                <div class="tab-pane" id="analytics-tab">
                    <div class="card">
                        <div class="card-header primary">
                            <h3><i class="fas fa-chart-line"></i> Dataset Analytics</h3>
                        </div>
                        <div class="card-body">
                            <?php
                            // Check if dataset_batch_analytics table exists
                            if ($required_tables['dataset_batch_analytics']) {
                                // Top viewed datasets by batch
                                $views_query = "
                                    SELECT db.dataset_batch_id, d.dataset_id, d.title, dba.total_views
                                    FROM dataset_batch_analytics dba
                                    JOIN dataset_batches db ON dba.dataset_batch_id = db.dataset_batch_id
                                    JOIN datasets d ON db.dataset_batch_id = d.dataset_batch_id
                                    GROUP BY db.dataset_batch_id
                                    ORDER BY dba.total_views DESC
                                    LIMIT 10";
                                
                                $views_result = $conn->query($views_query);
                                
                                if ($views_result && $views_result->num_rows > 0) {
                                    echo "<h5>Top Viewed Datasets</h5>";
                                    echo "<div class='table-container'>";
                                    echo "<table>";
                                    echo "<thead><tr><th>Batch ID</th><th>Dataset</th><th>Views</th></tr></thead>";
                                    echo "<tbody>";
                                    
                                    while ($view = $views_result->fetch_assoc()) {
                                        echo "<tr>";
                                        echo "<td>" . $view['dataset_batch_id'] . "</td>";
                                        echo "<td><a href='dataset.php?id=" . $view['dataset_id'] . "'>" . htmlspecialchars($view['title']) . "</a></td>";
                                        echo "<td>" . $view['total_views'] . "</td>";
                                        echo "</tr>";
                                    }
                                    
                                    echo "</tbody></table></div>";
                                    
                                    // Show downloads report
                                    $downloads_query = "
                                        SELECT db.dataset_batch_id, d.dataset_id, d.title, dba.total_downloads
                                        FROM dataset_batch_analytics dba
                                        JOIN dataset_batches db ON dba.dataset_batch_id = db.dataset_batch_id
                                        JOIN datasets d ON db.dataset_batch_id = d.dataset_batch_id
                                        GROUP BY db.dataset_batch_id
                                        ORDER BY dba.total_downloads DESC
                                        LIMIT 10";
                                    
                                    $downloads_result = $conn->query($downloads_query);
                                    
                                    if ($downloads_result && $downloads_result->num_rows > 0) {
                                        echo "<h5 class='mt-4'>Top Downloaded Datasets</h5>";
                                        echo "<div class='table-container'>";
                                        echo "<table>";
                                        echo "<thead><tr><th>Batch ID</th><th>Dataset</th><th>Downloads</th></tr></thead>";
                                        echo "<tbody>";
                                        
                                        while ($download = $downloads_result->fetch_assoc()) {
                                            echo "<tr>";
                                            echo "<td>" . $download['dataset_batch_id'] . "</td>";
                                            echo "<td><a href='dataset.php?id=" . $download['dataset_id'] . "'>" . htmlspecialchars($download['title']) . "</a></td>";
                                            echo "<td>" . $download['total_downloads'] . "</td>";
                                            echo "</tr>";
                                        }
                                        
                                        echo "</tbody></table></div>";
                                    }
                                    
                                    // Overall statistics
                                    $stats_query = "
                                        SELECT 
                                            SUM(total_views) as total_views,
                                            SUM(total_downloads) as total_downloads,
                                            MAX(last_accessed) as last_activity
                                        FROM dataset_batch_analytics";
                                    
                                    $stats_result = $conn->query($stats_query);
                                    
                                    if ($stats_result && $stats_row = $stats_result->fetch_assoc()) {
                                        echo "<div class='stats-grid mt-4'>";
                                        echo "<div class='stat-card info'>";
                                        echo "<h5>Total Views</h5>";
                                        echo "<p class='stat-number'>" . number_format($stats_row['total_views']) . "</p>";
                                        echo "</div>";
                                        
                                        echo "<div class='stat-card success'>";
                                        echo "<h5>Total Downloads</h5>";
                                        echo "<p class='stat-number'>" . number_format($stats_row['total_downloads']) . "</p>";
                                        echo "</div>";
                                        
                                        echo "<div class='stat-card primary'>";
                                        echo "<h5>Last Activity</h5>";
                                        echo "<p style='font-size: 18px; margin-top: 10px;'>" . date('M d, Y g:i A', strtotime($stats_row['last_activity'])) . "</p>";
                                        echo "</div>";
                                        echo "</div>"; // end stats-grid
                                    }
                                } else {
                                    echo "<div class='alert alert-info'>No view data available yet.</div>";
                                }
                            } else {
                                echo "<div class='alert alert-warning'>Analytics feature requires the 'dataset_batch_analytics' table.</div>";
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
    
    <script>
        // Tab functionality
        document.addEventListener('DOMContentLoaded', function() {
            const tabButtons = document.querySelectorAll('.tab-button');
            const tabPanes = document.querySelectorAll('.tab-pane');

            // Function to activate a tab
            function activateTab(tabId) {
                // Hide all tab panes
                tabPanes.forEach(pane => {
                    pane.classList.remove('active');
                });
                
                // Deactivate all tab buttons
                tabButtons.forEach(button => {
                    button.classList.remove('active');
                });
                
                // Activate the selected tab and its content
                document.getElementById(tabId).classList.add('active');
                document.querySelector(`[data-tab="${tabId.replace('-tab', '')}"]`).classList.add('active');
            }
            
            // Add click event to tab buttons
            tabButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const tabId = this.getAttribute('data-tab') + '-tab';
                    activateTab(tabId);
                });
            });
            
            // Close alert buttons
            const closeButtons = document.querySelectorAll('.close-btn');
            closeButtons.forEach(button => {
                button.addEventListener('click', function() {
                    this.parentElement.style.display = 'none';
                });
            });
        });
    </script>
</body>
</html>