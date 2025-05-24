<?php
session_start();
include 'db_connection.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Handle user status updates
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    $user_id = $_POST['user_id'];
    
    if ($_POST['action'] == 'toggle_status') {
        $sql = "UPDATE users SET is_active = NOT is_active WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
    } elseif ($_POST['action'] == 'delete') {
        $sql = "DELETE FROM users WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
    }
}

// Get users with pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 10;
$offset = ($page - 1) * $per_page;

$sql = "SELECT u.*, o.name as organization_name 
        FROM users u 
        LEFT JOIN organizations o ON u.organization_id = o.organization_id 
        ORDER BY u.date_joined DESC 
        LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $per_page, $offset);
$stmt->execute();
$users = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Get total users count
$total_users = $conn->query("SELECT COUNT(*) as count FROM users")->fetch_assoc()['count'];
$total_pages = ceil($total_users / $per_page);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management - Admin Dashboard</title>
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
            margin-bottom: 10px;
        }
        
        .text-muted {
            color: #6c757d;
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
        
        /* User card */
        .user-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        
        /* Utility classes */
        .mb-4 {
            margin-bottom: 20px;
        }
        
        /* Grid system */
        .row {
            display: flex;
            flex-wrap: wrap;
            margin-right: -15px;
            margin-left: -15px;
        }
        
        .align-items-center {
            align-items: center;
        }
        
        .col-md-3 {
            flex: 0 0 25%;
            max-width: 25%;
            padding: 0 15px;
        }
        
        /* Search box */
        .search-box {
            margin-bottom: 20px;
        }
        
        .input-group {
            display: flex;
            width: 100%;
        }
        
        .form-control {
            display: block;
            width: 100%;
            padding: 8px 12px;
            font-size: 14px;
            border: 1px solid #ced4da;
            border-radius: 4px 0 0 4px;
            transition: border-color 0.3s;
        }
        
        .form-control:focus {
            border-color: #80bdff;
            outline: 0;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
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
        
        .badge-success {
            background-color: #28a745;
        }
        
        .badge-danger {
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
            border-radius: 0 4px 4px 0;
        }
        
        .btn-warning {
            background-color: #ffc107;
            color: #212529;
        }
        
        .btn-danger {
            background-color: #dc3545;
            color: white;
        }
        
        /* User actions */
        .user-actions {
            display: flex;
            gap: 10px;
        }
        
        /* Pagination */
        .pagination {
            display: flex;
            justify-content: center;
            list-style: none;
            margin: 20px 0;
        }
        
        .page-item {
            margin: 0 2px;
        }
        
        .page-link {
            display: block;
            padding: 5px 10px;
            background-color: white;
            border: 1px solid #dee2e6;
            color: #0099ff;
            text-decoration: none;
            border-radius: 4px;
        }
        
        .page-item.active .page-link {
            background-color: #0099ff;
            color: white;
            border-color: #0099ff;
        }
        
        .page-link:hover {
            background-color: #e9ecef;
        }
        
        .justify-content-center {
            justify-content: center;
        }
        
        /* Responsive Design */
        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .col-md-3 {
                flex: 0 0 100%;
                max-width: 100%;
                margin-bottom: 10px;
            }
            
            .row {
                flex-direction: column;
            }
            
            .user-actions {
                margin-top: 10px;
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
                <li><a class="nav-link active" href="#"><i class="fas fa-users"></i> Users</a></li>
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
        <h2 class="mb-4">User Management</h2>

        <div class="search-box">
            <div class="input-group">
                <input type="text" class="form-control" placeholder="Search users..." id="searchInput">
                <button class="btn btn-primary" type="button">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </div>

        <?php foreach ($users as $user): ?>
            <div class="user-card">
                <div class="row align-items-center">
                    <div class="col-md-3">
                        <h5><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></h5>
                        <p class="text-muted"><?php echo htmlspecialchars($user['email']); ?></p>
                    </div>
                    <div class="col-md-3">
                        <p><strong>Organization:</strong> <?php echo htmlspecialchars($user['organization_name'] ?? 'None'); ?></p>
                        <p><strong>Role:</strong> <?php echo ucfirst($user['user_type']); ?></p>
                    </div>
                    <div class="col-md-3">
                        <p><strong>Joined:</strong> <?php echo date('M d, Y', strtotime($user['date_joined'])); ?></p>
                        <p><strong>Status:</strong> 
                            <span class="badge <?php echo $user['is_active'] ? 'badge-success' : 'badge-danger'; ?>">
                                <?php echo $user['is_active'] ? 'Active' : 'Inactive'; ?>
                            </span>
                        </p>
                    </div>
                    <div class="col-md-3">
                        <div class="user-actions">
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>">
                                <input type="hidden" name="action" value="toggle_status">
                                <button type="submit" class="btn btn-warning btn-sm">
                                    <i class="fas fa-power-off"></i> Toggle Status
                                </button>
                            </form>
                            <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>">
                                <input type="hidden" name="action" value="delete">
                                <button type="submit" class="btn btn-danger btn-sm">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>

        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-center">
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
    </div>

    <script>
        // Search functionality
        document.getElementById('searchInput').addEventListener('keyup', function() {
            const searchText = this.value.toLowerCase();
            const userCards = document.querySelectorAll('.user-card');
            
            userCards.forEach(card => {
                const userName = card.querySelector('h5').textContent.toLowerCase();
                const userEmail = card.querySelector('.text-muted').textContent.toLowerCase();
                const userOrg = card.querySelector('p:nth-child(1)').textContent.toLowerCase();
                
                if (userName.includes(searchText) || userEmail.includes(searchText) || userOrg.includes(searchText)) {
                    card.style.display = '';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    </script>
</body>
</html> 