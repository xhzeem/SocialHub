<?php
require_once 'config.php';
require_once 'navigation.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_destroy();
    header('Location: login.php');
    exit;
}

$posts = getPosts();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SocialHub - Connect with Friends</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; }
        .main-container { background: rgba(255,255,255,0.95); border-radius: 20px; box-shadow: 0 20px 40px rgba(0,0,0,0.1); }
        .header-gradient { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; }
        .post-card { transition: transform 0.3s ease, box-shadow 0.3s ease; }
        .post-card:hover { transform: translateY(-5px); box-shadow: 0 10px 25px rgba(0,0,0,0.15); }
        .nav-link { color: #667eea !important; font-weight: 500; }
        .btn-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; }
        .btn-primary:hover { background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%); }
        .user-avatar { width: 40px; height: 40px; border-radius: 50%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; }
        .post-content { line-height: 1.6; }
        .comment-section { background: #f8f9fa; border-radius: 10px; padding: 15px; margin-top: 15px; }
    </style>
</head>
<body>
    <div class="container py-4">
        <div class="main-container p-4">
            <div class="header-gradient rounded-top p-4 mb-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h2 mb-1"><i class="fas fa-users me-2"></i>SocialHub</h1>
                        <p class="mb-0 opacity-75">Connect with friends and share your moments</p>
                    </div>
                    <div class="d-flex align-items-center">
                        <div class="user-avatar me-3">
                            <?php echo strtoupper(substr($_SESSION['username'], 0, 1)); ?>
                        </div>
                        <div>
                            <div class="fw-bold"><?php echo htmlspecialchars($_SESSION['username']); ?></div>
                            <small class="opacity-75">Online</small>
                        </div>
                    </div>
                </div>
            </div>
            
            <?php renderNavigation('index.php'); ?>
            
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo $error; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($success)): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php echo $success; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <div class="row">
                <div class="col-lg-8">
                    <div class="card shadow-sm mb-4">
                        <div class="card-body">
                            <h5 class="card-title"><i class="fas fa-pen me-2"></i>Share your thoughts</h5>
                            <form method="POST">
                                <input type="hidden" name="action" value="post">
                                <div class="mb-3">
                                    <textarea name="content" class="form-control" placeholder="What's on your mind?" rows="3" required></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane me-2"></i>Post</button>
                            </form>
                        </div>
                    </div>
                    
                    <?php foreach ($posts as $post): ?>
                        <div class="card post-card shadow-sm mb-4">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="user-avatar me-3">
                                        <?php echo strtoupper(substr($post['username'], 0, 1)); ?>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 fw-bold"><?php echo htmlspecialchars($post['username']); ?></h6>
                                        <small class="text-muted"><?php echo date('M j, Y g:i A', strtotime($post['created_at'])); ?></small>
                                    </div>
                                </div>
                                <div class="post-content mb-3">
                                    <?php echo $post['content']; ?>
                                </div>
                                
                                <div class="comment-section">
                                    <form method="POST" class="mb-3">
                                        <input type="hidden" name="action" value="comment">
                                        <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                                        <div class="input-group">
                                            <input type="text" name="content" class="form-control" placeholder="Add a comment..." required>
                                            <button type="submit" class="btn btn-outline-primary"><i class="fas fa-comment me-1"></i> Comment</button>
                                        </div>
                                    </form>
                                </div>
                                
                                <div class="d-flex justify-content-between align-items-center">
                                    <a href="profile.php?user_id=<?php echo $post['user_id']; ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-user me-1"></i> View Profile
                                    </a>
                                    <div>
                                        <button class="btn btn-sm btn-outline-danger me-2"><i class="fas fa-heart me-1"></i> Like</button>
                                        <button class="btn btn-sm btn-outline-secondary"><i class="fas fa-share me-1"></i> Share</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="col-lg-4">
                    <div class="card shadow-sm mb-4">
                        <div class="card-body">
                            <h6 class="card-title"><i class="fas fa-bolt me-2"></i>Trending Topics</h6>
                            <div class="d-flex flex-wrap gap-2">
                                <span class="badge bg-primary">#socialhub</span>
                                <span class="badge bg-secondary">#friends</span>
                                <span class="badge bg-success">#memories</span>
                                <span class="badge bg-danger">#trending</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h6 class="card-title"><i class="fas fa-user-friends me-2"></i>Suggested Friends</h6>
                            <div class="list-group list-group-flush">
                                <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <div>
                                        <div class="fw-bold">Alice Johnson</div>
                                        <small class="text-muted">3 mutual friends</small>
                                    </div>
                                    <button class="btn btn-sm btn-outline-primary">Add</button>
                                </div>
                                <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <div>
                                        <div class="fw-bold">Bob Smith</div>
                                        <small class="text-muted">5 mutual friends</small>
                                    </div>
                                    <button class="btn btn-sm btn-outline-primary">Add</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php renderCommonScripts(); ?>
</body>
</html>
