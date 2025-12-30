<?php
require_once 'config.php';
require_once 'navigation.php';

$userId = $_GET['user_id'] ?? 1;
$user = getUser($userId);
$posts = getUserPosts($userId);
$isAdmin = isset($_SESSION['username']) && $_SESSION['username'] === 'admin';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($user['username']); ?>'s Profile - SocialHub</title>
    <?php renderCommonStyles(); ?>
</head>
<body>
    <div class="container py-4">
        <div class="main-container p-4">
            <?php renderHeader('User Profile', 'View profile information and posts', 'fas fa-user'); ?>
            
            <?php renderNavigation('profile.php'); ?>
            
            <div class="row">
                <div class="col-lg-4">
                    <div class="card shadow-sm mb-4">
                        <div class="card-body text-center">
                            <div class="profile-avatar mb-3">
                                <i class="fas fa-user-circle fa-5x text-primary"></i>
                            </div>
                            <h4 class="card-title"><?php echo htmlspecialchars($user['username']); ?></h4>
                            <p class="text-muted mb-3"><?php echo htmlspecialchars($user['email'] ?: 'No email provided'); ?></p>
                            
                            <div class="profile-stats mb-3">
                                <div class="row text-center">
                                    <div class="col-4">
                                        <div class="stat-number"><?php echo count($posts); ?></div>
                                        <div class="stat-label">Posts</div>
                                    </div>
                                    <div class="col-4">
                                        <div class="stat-number">0</div>
                                        <div class="stat-label">Friends</div>
                                    </div>
                                    <div class="col-4">
                                        <div class="stat-number">0</div>
                                        <div class="stat-label">Photos</div>
                                    </div>
                                </div>
                            </div>
                            
                            <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $user['id']): ?>
                                <a href="edit_profile.php?user_id=<?php echo $user['id']; ?>" class="btn btn-primary w-100">
                                    <i class="fas fa-edit me-2"></i>Edit Profile
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h6 class="card-title"><i class="fas fa-info-circle me-2"></i>About</h6>
                            <p class="text-muted"><?php echo htmlspecialchars($user['bio'] ?: 'No bio available'); ?></p>
                            <hr>
                            <p class="mb-1"><small class="text-muted"><i class="fas fa-calendar me-2"></i>Joined <?php echo date('F Y', strtotime($user['created_at'])); ?></small></p>
                            <p class="mb-0"><small class="text-muted"><i class="fas fa-user-tag me-2"></i>User ID: <?php echo $user['id']; ?></small></p>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-8">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title"><i class="fas fa-stream me-2"></i>Recent Posts</h5>
                            
                            <?php if (empty($posts)): ?>
                                <div class="text-center py-4">
                                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">No posts yet</p>
                                </div>
                            <?php else: ?>
                                <?php foreach ($posts as $post): ?>
                                    <div class="post-item mb-3 pb-3 border-bottom">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h6 class="mb-0"><?php echo htmlspecialchars($user['username']); ?></h6>
                                            <small class="text-muted"><?php echo date('M j, Y', strtotime($post['created_at'])); ?></small>
                                        </div>
                                        <p class="mb-2"><?php echo htmlspecialchars($post['content']); ?></p>
                                        <div class="post-actions">
                                            <button class="btn btn-sm btn-outline-primary me-2">
                                                <i class="fas fa-heart me-1"></i> Like
                                            </button>
                                            <button class="btn btn-sm btn-outline-secondary me-2">
                                                <i class="fas fa-comment me-1"></i> Comment
                                            </button>
                                            <button class="btn btn-sm btn-outline-secondary">
                                                <i class="fas fa-share me-1"></i> Share
                                            </button>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php renderCommonScripts(); ?>
</body>
</html>
