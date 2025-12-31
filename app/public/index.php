<?php
require_once __DIR__ . '/../config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_destroy();
    header('Location: login.php');
    exit;
}

// Get recent posts and user data
$posts = getPosts();
$users = []; // We'll create some sample users for now

$pageTitle = 'SocialHub - Home';
$headerTitle = 'Welcome to SocialHub';
$headerSubtitle = 'Connect, Share, and Collaborate';
require_once __DIR__ . '/../includes/header.php';
?>
<div class="row">
    <!-- Main Feed -->
    <div class="col-lg-8">
        <!-- Create Post -->
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title mb-3"><i class="fas fa-edit me-2"></i>Create Post</h5>
                <form method="post" action="create_post.php">
                    <div class="mb-3">
                        <textarea class="form-control" name="content" rows="3" placeholder="What's on your mind?" required></textarea>
                    </div>
                    <div class="d-flex justify-content-between">
                        <div class="btn-group btn-group-sm">
                            <button type="button" class="btn btn-outline-primary"><i class="fas fa-image"></i></button>
                            <button type="button" class="btn btn-outline-primary"><i class="fas fa-video"></i></button>
                            <button type="button" class="btn btn-outline-primary"><i class="fas fa-poll"></i></button>
                        </div>
                        <button type="submit" name="action" value="create_post" class="btn btn-primary">
                            <i class="fas fa-paper-plane me-2"></i>Post
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Posts Feed -->
        <?php if (!empty($posts)): ?>
            <?php foreach ($posts as $post): ?>
                <div class="card mb-3 fade-in">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                <?php echo strtoupper(substr($post['username'], 0, 1)); ?>
                            </div>
                            <div>
                                <h6 class="mb-0"><?php echo htmlspecialchars($post['username']); ?></h6>
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
            <div class="card">
                <div class="card-body text-center">
                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                    <h5>No posts yet</h5>
                    <p class="text-muted">Be the first to share something with the community!</p>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Sidebar -->
    <div class="col-lg-4">
        <!-- User Info -->
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-user me-2"></i>My Profile</h5>
                <div class="text-center mb-3">
                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mx-auto" style="width: 60px; height: 60px;">
                        <?php echo strtoupper(substr($_SESSION['username'], 0, 1)); ?>
                    </div>
                    <h6 class="mt-2 mb-0"><?php echo htmlspecialchars($_SESSION['username']); ?></h6>
                    <small class="text-muted"><?php echo htmlspecialchars($_SESSION['email'] ?? ''); ?></small>
                </div>
                <div class="d-grid gap-2">
                    <a href="profile.php" class="btn btn-outline-primary btn-sm">View Profile</a>
                    <a href="upload.php" class="btn btn-outline-primary btn-sm">Upload Files</a>
                    <a href="template.php" class="btn btn-outline-primary btn-sm">Templates</a>
                </div>
            </div>
        </div>

        <!-- Quick Links -->
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-bolt me-2"></i>Quick Links</h5>
                <div class="list-group list-group-flush">
                    <a href="search.php" class="list-group-item list-group-item-action">
                        <i class="fas fa-search me-2"></i>Search Users
                    </a>
                    <a href="contacts.php" class="list-group-item list-group-item-action">
                        <i class="fas fa-envelope me-2"></i>Messages
                    </a>
                    <a href="web_fetcher.php" class="list-group-item list-group-item-action">
                        <i class="fas fa-globe me-2"></i>Web Fetcher
                    </a>
                    <?php if (isset($_SESSION['username']) && $_SESSION['username'] === 'admin'): ?>
                        <a href="admin_messages.php" class="list-group-item list-group-item-action text-danger">
                            <i class="fas fa-shield-alt me-2"></i>Admin Panel
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Active Users -->
        <div class="card">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-users me-2"></i>Active Users</h5>
                <?php if (!empty($users)): ?>
                    <div class="list-group list-group-flush">
                        <?php foreach (array_slice($users, 0, 5) as $user): ?>
                            <a href="profile.php?user_id=<?php echo $user['id']; ?>" class="list-group-item list-group-item-action">
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center me-2" style="width: 30px; height: 30px; font-size: 0.8rem;">
                                        <?php echo strtoupper(substr($user['username'], 0, 1)); ?>
                                    </div>
                                    <div>
                                        <div class="fw-bold"><?php echo htmlspecialchars($user['username']); ?></div>
                                        <small class="text-muted">Online</small>
                                    </div>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="text-muted">No active users</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
