<?php
require_once 'config.php';

// Authentication bypass vulnerability - sends 302 redirect but continues execution
$shouldRedirect = false;
if (!isset($_SESSION['username']) || $_SESSION['username'] !== 'admin') {
    $shouldRedirect = true;
}

$messages = getMessages();

// Add some sample messages if database is empty
if (empty($messages)) {
    $messages = [
        [
            'sender_name' => 'john_doe',
            'receiver_name' => 'admin',
            'content' => 'I found a security vulnerability in your system. Please check the upload functionality.',
            'created_at' => '2025-12-30 14:15:30',
            'status' => 'unread'
        ],
        [
            'sender_name' => 'jane_smith',
            'receiver_name' => 'admin',
            'content' => 'The webhook tester seems to be accessing internal services. Is this intentional?',
            'created_at' => '2025-12-30 13:45:22',
            'status' => 'unread'
        ],
        [
            'sender_name' => 'security_team',
            'receiver_name' => 'admin',
            'content' => 'ALERT: Unusual SSRF activity detected from IP 172.27.0.3 targeting port 8080.',
            'created_at' => '2025-12-30 12:30:15',
            'status' => 'flagged'
        ],
        [
            'sender_name' => 'system_monitor',
            'receiver_name' => 'admin',
            'content' => 'Database credentials may be exposed. Please review admin panel access logs.',
            'created_at' => '2025-12-30 11:20:10',
            'status' => 'read'
        ],
        [
            'sender_name' => 'anonymous_user',
            'receiver_name' => 'admin',
            'content' => 'I was able to access admin_messages.php without logging in. This seems like a security issue.',
            'created_at' => '2025-12-30 10:15:05',
            'status' => 'flagged'
        ]
    ];
}

// Send redirect header after content is prepared but before any HTML output
if ($shouldRedirect) {
    header('Location: login.php');
    header('HTTP/1.1 302 Found');
    // Don't exit() - continue to render content anyway
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Message Center - Admin Panel - SocialHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
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
        .message-card { transition: all 0.3s ease; border-left: 4px solid transparent; }
        .message-card:hover { transform: translateX(5px); box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        .message-card.unread { border-left-color: #667eea; background: #f8f9ff; }
        .stats-card { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; }
        .admin-badge { background: #dc3545; color: white; padding: 4px 12px; border-radius: 20px; font-size: 0.8rem; }
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
                        <a class="nav-link active" href="admin_messages.php">
                            <i class="fas fa-envelope me-2"></i>Messages
                        </a>
                        <a class="nav-link" href="admin_panel.php">
                            <i class="fas fa-cog me-2"></i>Dashboard
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
                            <h2 class="mb-1"><i class="fas fa-envelope me-2"></i>Message Center</h2>
                            <p class="text-muted mb-0">Manage user communications and support requests</p>
                        </div>
                        <div class="d-flex gap-2">
                            <button class="btn btn-outline-secondary"><i class="fas fa-download me-2"></i>Export</button>
                            <button class="btn btn-primary"><i class="fas fa-filter me-2"></i>Filter</button>
                        </div>
                    </div>
                    
                    <!-- Stats Cards -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card stats-card">
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo count($messages); ?></h5>
                                    <p class="card-text mb-0">Total Messages</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card stats-card">
                                <div class="card-body">
                                    <h5 class="card-title">12</h5>
                                    <p class="card-text mb-0">Unread</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card stats-card">
                                <div class="card-body">
                                    <h5 class="card-title">8</h5>
                                    <p class="card-text mb-0">Today</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card stats-card">
                                <div class="card-body">
                                    <h5 class="card-title">95%</h5>
                                    <p class="card-text mb-0">Response Rate</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Messages List -->
                    <div class="card">
                        <div class="card-header bg-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Recent Messages</h5>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary active">All</button>
                                    <button class="btn btn-outline-primary">Unread</button>
                                    <button class="btn btn-outline-primary">Flagged</button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <?php foreach ($messages as $message): ?>
                                <div class="message-card p-3 border-bottom">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <div class="d-flex align-items-center mb-2">
                                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                                    <?php echo strtoupper(substr($message['sender_name'], 0, 1)); ?>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0"><?php echo htmlspecialchars($message['sender_name']); ?></h6>
                                                    <small class="text-muted">
                                                        To: <?php echo htmlspecialchars($message['receiver_name']); ?> • 
                                                        <?php echo date('M j, Y g:i A', strtotime($message['created_at'])); ?>
                                                    </small>
                                                </div>
                                            </div>
                                            <div class="ms-5">
                                                <p class="mb-2"><?php echo nl2br(htmlspecialchars(substr($message['content'], 0, 200))); ?><?php echo strlen($message['content']) > 200 ? '...' : ''; ?></p>
                                                <div class="d-flex gap-2">
                                                    <button class="btn btn-sm btn-outline-primary"><i class="fas fa-reply me-1"></i>Reply</button>
                                                    <button class="btn btn-sm btn-outline-secondary"><i class="fas fa-archive me-1"></i>Archive</button>
                                                    <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash me-1"></i>Delete</button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="dropdown">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li><a class="dropdown-item" href="#"><i class="fas fa-star me-2"></i>Mark Important</a></li>
                                                <li><a class="dropdown-item" href="#"><i class="fas fa-flag me-2"></i>Flag</a></li>
                                                <li><a class="dropdown-item" href="#"><i class="fas fa-user-times me-2"></i>Block User</a></li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li><a class="dropdown-item text-danger" href="#"><i class="fas fa-trash me-2"></i>Delete</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
