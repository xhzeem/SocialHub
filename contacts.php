<?php
require_once 'config.php';

$messages = getMessages();
$isAdmin = isset($_SESSION['username']) && $_SESSION['username'] === 'admin';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages - SocialHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; }
        .main-container { background: rgba(255,255,255,0.95); border-radius: 20px; box-shadow: 0 20px 40px rgba(0,0,0,0.1); }
        .header-gradient { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; }
        .message-card { transition: transform 0.3s ease, box-shadow 0.3s ease; }
        .message-card:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(0,0,0,0.1); }
        .nav-link { color: #667eea !important; font-weight: 500; }
        .btn-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; }
        .btn-primary:hover { background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%); }
        .message-meta { font-size: 0.85rem; color: #6c757d; }
        .message-content { white-space: pre-wrap; line-height: 1.5; }
        .admin-badge { background: linear-gradient(135deg, #dc3545 0%, #c82333 100%); color: white; padding: 2px 8px; border-radius: 12px; font-size: 0.75rem; }
    </style>
</head>
<body>
    <div class="container py-4">
        <div class="main-container p-4">
            <div class="header-gradient rounded-top p-4 mb-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h2 mb-1"><i class="fas fa-envelope me-2"></i>Messages</h1>
                        <p class="mb-0 opacity-75">Connect with your community</p>
                    </div>
                    <?php if ($isAdmin): ?>
                        <span class="admin-badge"><i class="fas fa-shield-alt me-1"></i>Admin</span>
                    <?php endif; ?>
                </div>
            </div>
            
            <nav class="navbar navbar-expand-lg navbar-light bg-white rounded mb-4 shadow-sm">
                <div class="container-fluid">
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="index.php"><i class="fas fa-home me-1"></i> Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="search.php"><i class="fas fa-search me-1"></i> Search</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="upload.php"><i class="fas fa-upload me-1"></i> Upload</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="template.php"><i class="fas fa-code me-1"></i> Templates</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="contacts.php"><i class="fas fa-envelope me-1"></i> Contacts</a>
                        </li>
                    </ul>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <ul class="navbar-nav">
                            <li class="nav-item">
                                <a class="nav-link text-danger" href="index.php?action=logout"><i class="fas fa-sign-out-alt me-1"></i> Logout</a>
                            </li>
                        </ul>
                    <?php else: ?>
                        <ul class="navbar-nav">
                            <li class="nav-item">
                                <a class="nav-link" href="login.php"><i class="fas fa-sign-in-alt me-1"></i> Login</a>
                            </li>
                        </ul>
                    <?php endif; ?>
                </div>
            </nav>
            
            <div class="row mb-4">
                <div class="col-md-8">
                    <h3><i class="fas fa-comments me-2"></i>Community Messages</h3>
                    <p class="text-muted">Latest messages from our community members</p>
                </div>
                <div class="col-md-4 text-end">
                    <a href="contact.php" class="btn btn-primary">
                        <i class="fas fa-pen me-2"></i>Send Message
                    </a>
                </div>
            </div>
            
            <div class="row">
                <?php foreach ($messages as $message): ?>
                    <div class="col-lg-6 mb-4">
                        <div class="card message-card shadow-sm h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div>
                                        <h6 class="card-title mb-1">
                                            <i class="fas fa-user-circle me-2"></i>
                                            <?php echo htmlspecialchars($message['sender_name']); ?>
                                        </h6>
                                        <div class="message-meta">
                                            To: <?php echo htmlspecialchars($message['receiver_name']); ?> • 
                                            <?php echo date('M j, Y g:i A', strtotime($message['created_at'])); ?>
                                        </div>
                                    </div>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="dropdown">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="#"><i class="fas fa-reply me-2"></i>Reply</a></li>
                                            <li><a class="dropdown-item" href="#"><i class="fas fa-share me-2"></i>Forward</a></li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li><a class="dropdown-item text-danger" href="#"><i class="fas fa-trash me-2"></i>Delete</a></li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="message-content">
                                    <?php echo htmlspecialchars($message['content']); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <?php if (empty($messages)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <h4 class="text-muted">No messages yet</h4>
                    <p class="text-muted">Be the first to start a conversation!</p>
                    <a href="contact.php" class="btn btn-primary">
                        <i class="fas fa-pen me-2"></i>Send First Message
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
