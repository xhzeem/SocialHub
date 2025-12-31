<?php
require_once __DIR__ . '/../config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$results = [];
$query = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'search') {
    $query = $_POST['query'] ?? '';
    if (!empty($query)) {
        $results = searchUsers($query);
    }
}

$pageTitle = 'Search - SocialHub';
$headerTitle = 'Discover People';
$headerSubtitle = 'Find friends and connect with our community';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="row">
    <div class="col-md-8">
        <div class="card shadow-lg mb-4">
            <div class="card-body">
                <h5 class="card-title mb-4">
                    <i class="fas fa-search me-2"></i>Search Users
                </h5>
                
                <form method="post" class="mb-4">
                    <input type="hidden" name="action" value="search">
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" class="form-control" name="query" 
                               placeholder="Search by username or bio..." 
                               value="<?php echo htmlspecialchars($query); ?>" required>
                        <button class="btn btn-primary" type="submit">
                            Search
                        </button>
                    </div>
                </form>

                <?php if (!empty($query)): ?>
                    <h6 class="mb-3">Results for "<?php echo htmlspecialchars($query); ?>"</h6>
                    
                    <?php if (!empty($results)): ?>
                        <div class="row">
                            <?php foreach ($results as $user): ?>
                                <div class="col-md-6 mb-3">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center mb-3">
                                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                                                    <?php echo strtoupper(substr($user['username'], 0, 1)); ?>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0"><?php echo htmlspecialchars($user['username']); ?></h6>
                                                    <small class="text-muted"><?php echo htmlspecialchars($user['email']); ?></small>
                                                </div>
                                            </div>
                                            <p class="mb-2 small text-muted"><?php echo htmlspecialchars($user['bio'] ?? 'No bio available'); ?></p>
                                            <div class="d-flex gap-2">
                                                <a href="profile.php?user_id=<?php echo $user['id']; ?>" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-user me-1"></i>View Profile
                                                </a>
                                                <a href="contacts.php" class="btn btn-sm btn-outline-secondary">
                                                    <i class="fas fa-envelope me-1"></i>Message
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="fas fa-search fa-3x text-muted mb-3"></i>
                            <h6>No users found</h6>
                            <p class="text-muted">Try searching with different keywords</p>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="text-center py-4">
                        <i class="fas fa-search fa-3x text-muted mb-3"></i>
                        <h6>Start Searching</h6>
                        <p class="text-muted">Enter a username or bio to find users</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card shadow-lg">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-info-circle me-2"></i>Search Tips</h5>
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <i class="fas fa-check text-success me-2"></i>
                        Search by username
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check text-success me-2"></i>
                        Search by bio content
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check text-success me-2"></i>
                        Partial matches supported
                    </li>
                </ul>
            </div>
        </div>

        <div class="card shadow-lg mt-4">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-users me-2"></i>Quick Links</h5>
                <div class="list-group list-group-flush">
                    <a href="index.php" class="list-group-item list-group-item-action">
                        <i class="fas fa-home me-2"></i>Home Feed
                    </a>
                    <a href="contacts.php" class="list-group-item list-group-item-action">
                        <i class="fas fa-envelope me-2"></i>Messages
                    </a>
                    <a href="upload.php" class="list-group-item list-group-item-action">
                        <i class="fas fa-upload me-2"></i>Upload Files
                    </a>
                    <a href="template.php" class="list-group-item list-group-item-action">
                        <i class="fas fa-code me-2"></i>Templates
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
