<?php
require_once 'config.php';
require_once 'navigation.php';

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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search - SocialHub</title>
    <?php renderCommonStyles(); ?>
</head>
<body>
    <div class="container py-4">
        <div class="main-container p-4">
            <?php renderHeader('Discover People', 'Find friends and connect with our community', 'fas fa-search'); ?>
            
            <?php renderNavigation('search.php'); ?>
            
            <div class="search-box">
                <form method="POST" class="mb-4">
                    <input type="hidden" name="action" value="search">
                    <div class="row align-items-center">
                        <div class="col-md-10">
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                                <input type="text" class="form-control search-input border-start-0" name="query" placeholder="Search for people by name or username..." value="<?php echo htmlspecialchars($query); ?>">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-search w-100">
                                <i class="fas fa-search me-2"></i>Search
                            </button>
                        </div>
                    </div>
                </form>
                
                <div class="search-filters">
                    <h6 class="mb-3"><i class="fas fa-filter me-2"></i>Quick Filters</h6>
                    <div class="d-flex flex-wrap gap-2">
                        <button class="btn btn-outline-primary btn-sm">New Members</button>
                        <button class="btn btn-outline-primary btn-sm">Active Today</button>
                        <button class="btn btn-outline-primary btn-sm">Popular</button>
                        <button class="btn btn-outline-primary btn-sm">Nearby</button>
                        <button class="btn btn-outline-primary btn-sm">Same Interests</button>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-lg-8">
                    <?php if (!empty($query)): ?>
                        <h4 class="mb-4">
                            <i class="fas fa-users me-2"></i>
                            Search Results for "<?php echo htmlspecialchars($query); ?>"
                            <span class="badge bg-primary ms-2"><?php echo count($results); ?></span>
                        </h4>
                        
                        <?php if (!empty($results)): ?>
                            <?php foreach ($results as $user): ?>
                                <div class="user-card">
                                    <div class="d-flex align-items-center">
                                        <div class="user-avatar me-4">
                                            <?php echo strtoupper(substr($user['username'], 0, 1)); ?>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h5 class="mb-1"><?php echo htmlspecialchars($user['username']); ?></h5>
                                            <p class="text-muted mb-2"><?php echo htmlspecialchars($user['email'] ?? 'No email provided'); ?></p>
                                            <p class="mb-2"><?php echo htmlspecialchars($user['bio'] ?? 'No bio available'); ?></p>
                                            <div class="d-flex gap-2">
                                                <button class="btn btn-primary btn-sm">
                                                    <i class="fas fa-user-plus me-1"></i>Connect
                                                </button>
                                                <button class="btn btn-outline-secondary btn-sm">
                                                    <i class="fas fa-envelope me-1"></i>Message
                                                </button>
                                                <a href="profile.php?user_id=<?php echo $user['id']; ?>" class="btn btn-outline-primary btn-sm">
                                                    <i class="fas fa-eye me-1"></i>View Profile
                                                </a>
                                            </div>
                                        </div>
                                        <div class="text-end">
                                            <small class="text-muted">Member since <?php echo date('M Y', strtotime($user['created_at'])); ?></small>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="no-results">
                                <i class="fas fa-search fa-3x text-muted mb-3"></i>
                                <h4>No Results Found</h4>
                                <p class="text-muted">We couldn't find anyone matching "<?php echo htmlspecialchars($query); ?>"</p>
                                <p class="text-muted">Try searching with different keywords or check the spelling.</p>
                                <button class="btn btn-primary mt-3">
                                    <i class="fas fa-redo me-2"></i>Clear Search
                                </button>
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="fas fa-search fa-3x text-muted mb-3"></i>
                            <h4>Start Searching</h4>
                            <p class="text-muted">Enter a name or username above to find people on SocialHub</p>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="col-lg-4">
                    <div class="trending-tags">
                        <h6 class="mb-3"><i class="fas fa-fire me-2"></i>Trending Searches</h6>
                        <div class="mb-3">
                            <a href="#" class="tag-badge">developers</a>
                            <a href="#" class="tag-badge">designers</a>
                            <a href="#" class="tag-badge">photographers</a>
                            <a href="#" class="tag-badge">writers</a>
                            <a href="#" class="tag-badge">musicians</a>
                            <a href="#" class="tag-badge">artists</a>
                            <a href="#" class="tag-badge">entrepreneurs</a>
                            <a href="#" class="tag-badge">students</a>
                        </div>
                        <small class="text-muted">Click on any tag to search for related people</small>
                    </div>
                    
                    <div class="card shadow-sm mt-4">
                        <div class="card-body">
                            <h6 class="card-title"><i class="fas fa-lightbulb me-2"></i>Search Tips</h6>
                            <ul class="list-unstyled mb-0">
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Use partial names</li>
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Try different spellings</li>
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Search by interests</li>
                                <li class="mb-0"><i class="fas fa-check text-success me-2"></i>Browse trending tags</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php renderCommonScripts(); ?>
</body>
</html>
