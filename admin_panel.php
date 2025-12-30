<?php
require_once 'config.php';

// Check if user is admin
if (!isset($_SESSION['username']) || $_SESSION['username'] !== 'admin') {
    header('Location: login.php');
    exit;
}

// Get user statistics
$users = [];
$conn = new mysqli(getenv('DB_HOST') ?: 'localhost', getenv('DB_USER') ?: 'root', getenv('DB_PASSWORD') ?: 'password', getenv('DB_NAME') ?: 'vulnerable_social');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$result = $conn->query("SELECT * FROM users ORDER BY id");
if ($result) {
    $users = $result->fetch_all(MYSQLI_ASSOC);
}

$totalUsers = count($users);
$recentUsers = $conn->query("SELECT COUNT(*) as count FROM users WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)")->fetch_assoc()['count'];
$totalPosts = $conn->query("SELECT COUNT(*) as count FROM posts")->fetch_assoc()['count'];
$recentPosts = $conn->query("SELECT COUNT(*) as count FROM posts WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)")->fetch_assoc()['count'];

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Admin Panel - SocialHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { background: #f8f9fa; min-height: 100vh; }
        .sidebar { 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
            min-height: 100vh; 
            color: white;
        }
        .sidebar .nav-link { color: rgba(255,255,255,0.8); border-radius: 8px; margin: 2px 0; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { color: white; background: rgba(255,255,255,0.1); }
        .main-content { background: white; min-height: 100vh; }
        .stats-card { 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
            color: white; 
            border-radius: 15px;
            transition: transform 0.3s ease;
        }
        .stats-card:hover { transform: translateY(-5px); }
        .admin-badge { background: #dc3545; color: white; padding: 4px 12px; border-radius: 20px; font-size: 0.8rem; }
        .chart-container { position: relative; height: 300px; }
        .user-avatar { width: 40px; height: 40px; border-radius: 50%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 p-0 sidebar">
                <div class="p-3">
                    <h4 class="mb-4"><i class="fas fa-users me-2"></i>SocialHub</h4>
                    <div class="admin-badge mb-3"><i class="fas fa-shield-alt me-1"></i>Admin</div>
                    <nav class="nav flex-column">
                        <a class="nav-link active" href="admin_panel.php">
                            <i class="fas fa-cog me-2"></i>Dashboard
                        </a>
                        <a class="nav-link" href="admin_messages.php">
                            <i class="fas fa-envelope me-2"></i>Messages
                        </a>
                        <a class="nav-link" href="admin_users.php">
                            <i class="fas fa-users me-2"></i>Users
                        </a>
                        <a class="nav-link" href="admin_content.php">
                            <i class="fas fa-file-alt me-2"></i>Content
                        </a>
                        <a class="nav-link" href="admin_analytics.php">
                            <i class="fas fa-chart-bar me-2"></i>Analytics
                        </a>
                        <a class="nav-link" href="admin_settings.php">
                            <i class="fas fa-sliders-h me-2"></i>Settings
                        </a>
                        <hr class="my-3" style="border-color: rgba(255,255,255,0.2);">
                        <a class="nav-link" href="index.php">
                            <i class="fas fa-home me-2"></i>View Site
                        </a>
                        <a class="nav-link text-warning" href="index.php?action=logout">
                            <i class="fas fa-sign-out-alt me-2"></i>Logout
                        </a>
                    </nav>
                </div>
            </div>
            
            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 main-content">
                <div class="p-4">
                    <!-- Header -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h2 class="mb-1"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</h2>
                            <p class="text-muted mb-0">Welcome back! Here's what's happening with your platform today.</p>
                        </div>
                        <div class="d-flex gap-2">
                            <button class="btn btn-outline-secondary"><i class="fas fa-download me-2"></i>Export Report</button>
                            <button class="btn btn-primary"><i class="fas fa-sync me-2"></i>Refresh</button>
                        </div>
                    </div>
                    
                    <!-- Stats Cards -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card stats-card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h5 class="card-title"><?php echo $totalUsers; ?></h5>
                                            <p class="card-text mb-0">Total Users</p>
                                        </div>
                                        <i class="fas fa-users fa-2x opacity-50"></i>
                                    </div>
                                    <small class="opacity-75">+<?php echo $recentUsers; ?> this week</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card stats-card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h5 class="card-title"><?php echo $totalPosts; ?></h5>
                                            <p class="card-text mb-0">Total Posts</p>
                                        </div>
                                        <i class="fas fa-file-alt fa-2x opacity-50"></i>
                                    </div>
                                    <small class="opacity-75">+<?php echo $recentPosts; ?> this week</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card stats-card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h5 class="card-title">1,234</h5>
                                            <p class="card-text mb-0">Active Sessions</p>
                                        </div>
                                        <i class="fas fa-signal fa-2x opacity-50"></i>
                                    </div>
                                    <small class="opacity-75">89% retention rate</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card stats-card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h5 class="card-title">98.5%</h5>
                                            <p class="card-text mb-0">Uptime</p>
                                        </div>
                                        <i class="fas fa-server fa-2x opacity-50"></i>
                                    </div>
                                    <small class="opacity-75">Last 30 days</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Charts and Recent Activity -->
                    <div class="row">
                        <div class="col-md-8">
                            <div class="card mb-4">
                                <div class="card-header bg-white">
                                    <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>User Growth</h5>
                                </div>
                                <div class="card-body">
                                    <div class="chart-container">
                                        <canvas id="userGrowthChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card mb-4">
                                <div class="card-header bg-white">
                                    <h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Traffic Sources</h5>
                                </div>
                                <div class="card-body">
                                    <div class="chart-container">
                                        <canvas id="trafficChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Recent Users -->
                    <div class="card">
                        <div class="card-header bg-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0"><i class="fas fa-users me-2"></i>Recent Users</h5>
                                <a href="admin_users.php" class="btn btn-sm btn-outline-primary">View All</a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>User</th>
                                            <th>Email</th>
                                            <th>Join Date</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach (array_slice($users, -5) as $user): ?>
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="user-avatar me-2">
                                                            <?php echo strtoupper(substr($user['username'], 0, 1)); ?>
                                                        </div>
                                                        <span><?php echo htmlspecialchars($user['username']); ?></span>
                                                    </div>
                                                </td>
                                                <td><?php echo htmlspecialchars($user['email'] ?? 'N/A'); ?></td>
                                                <td><?php echo date('M j, Y', strtotime($user['created_at'])); ?></td>
                                                <td><span class="badge bg-success">Active</span></td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <button class="btn btn-outline-primary"><i class="fas fa-eye"></i></button>
                                                        <button class="btn btn-outline-secondary"><i class="fas fa-edit"></i></button>
                                                        <button class="btn btn-outline-danger"><i class="fas fa-trash"></i></button>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // User Growth Chart
        const userGrowthCtx = document.getElementById('userGrowthChart').getContext('2d');
        new Chart(userGrowthCtx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                    label: 'New Users',
                    data: [12, 19, 23, 25, 32, 37],
                    borderColor: '#667eea',
                    backgroundColor: 'rgba(102, 126, 234, 0.1)',
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });
        
        // Traffic Sources Chart
        const trafficCtx = document.getElementById('trafficChart').getContext('2d');
        new Chart(trafficCtx, {
            type: 'doughnut',
            data: {
                labels: ['Direct', 'Social', 'Referral', 'Organic'],
                datasets: [{
                    data: [35, 25, 20, 20],
                    backgroundColor: ['#667eea', '#764ba2', '#f093fb', '#4facfe']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });
    </script>
</body>
</html>
