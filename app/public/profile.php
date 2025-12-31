<?php
require_once __DIR__ . '/../config.php';

// Require authentication for profile page
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$userId = $_GET['user_id'] ?? 1;
$user = getUser($userId);
$posts = getUserPosts($userId);
$isAdmin = isset($_SESSION['username']) && $_SESSION['username'] === 'admin';

$pageTitle = htmlspecialchars($user['username']) . "'s Profile - SocialHub";
$headerTitle = htmlspecialchars($user['username']) . "'s Profile";
$headerSubtitle = "View posts and information";
require_once __DIR__ . '/../includes/header.php';
?>

<div class="row">
    <div class="col-lg-4">
        <div class="card shadow-lg mb-4">
            <div class="card-body text-center">
                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 80px; height: 80px; font-size: 2rem;">
                    <?php echo strtoupper(substr($user['username'], 0, 1)); ?>
                </div>
                <h4 class="card-title"><?php echo htmlspecialchars($user['username']); ?></h4>
                <p class="text-muted"><?php echo htmlspecialchars($user['email']); ?></p>
                <p class="small text-muted"><?php echo htmlspecialchars($user['bio'] ?? 'No bio available'); ?></p>
                <div class="d-grid gap-2">
                    <a href="contacts.php" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-envelope me-2"></i>Send Message
                    </a>
                    <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $userId): ?>
                        <a href="edit_profile.php" class="btn btn-primary btn-sm">
                            <i class="fas fa-edit me-2"></i>Edit Profile
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="card shadow-lg">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-chart-bar me-2"></i>Statistics</h5>
                <div class="row text-center">
                    <div class="col-6">
                        <div class="border-end">
                            <h4 class="text-primary"><?php echo count($posts); ?></h4>
                            <small class="text-muted">Posts</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <h4 class="text-success">0</h4>
                        <small class="text-muted">Followers</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card shadow-lg">
            <div class="card-body">
                <h5 class="card-title mb-4">
                    <i class="fas fa-stream me-2"></i>Recent Posts
                    <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $userId): ?>
                        <a href="index.php" class="btn btn-sm btn-primary float-end">
                            <i class="fas fa-plus me-1"></i>New Post
                        </a>
                    <?php endif; ?>
                </h5>
                
                <?php if (!empty($posts)): ?>
                    <?php foreach ($posts as $post): ?>
                        <div class="card mb-3">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                        <?php echo strtoupper(substr($user['username'], 0, 1)); ?>
                                    </div>
                                    <div>
                                        <h6 class="mb-0"><?php echo htmlspecialchars($user['username']); ?></h6>
                                        <small class="text-muted"><?php echo date('M j, Y g:i A', strtotime($post['created_at'])); ?></small>
                                    </div>
                                </div>
                                <p class="mb-3"><?php echo htmlspecialchars($post['content']); ?></p>
                                <div class="d-flex gap-3">
                                    <button class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-heart me-1"></i>Like
                                    </button>
                                    <button class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-comment me-1"></i>Comment
                                    </button>
                                    <button class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-share me-1"></i>Share
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                        <h5>No posts yet</h5>
                        <p class="text-muted"><?php echo htmlspecialchars($user['username']); ?> hasn't posted anything yet.</p>
                        <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $userId): ?>
                            <a href="index.php" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Create First Post
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
