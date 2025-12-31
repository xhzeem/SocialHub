<?php
require_once __DIR__ . '/../config.php';

// Require authentication
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create_post') {
    $content = $_POST['content'] ?? '';
    if (!empty($content)) {
        $result = createPost($_SESSION['user_id'], $content);
        if ($result) {
            header('Location: index.php');
            exit;
        } else {
            $message = "Failed to create post. Please try again.";
        }
    } else {
        $message = "Post content cannot be empty.";
    }
}

$pageTitle = 'Create Post - SocialHub';
$headerTitle = 'Create Post';
$headerSubtitle = 'Share your thoughts with the community';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-lg">
            <div class="card-body p-4">
                <div class="text-center mb-4">
                    <i class="fas fa-edit fa-3x text-primary mb-3"></i>
                    <h4 class="card-title">Create New Post</h4>
                    <p class="text-muted">Share your thoughts with the community</p>
                </div>

                <?php if ($message): ?>
                    <div class="alert alert-danger" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i><?php echo htmlspecialchars($message); ?>
                    </div>
                <?php endif; ?>

                <form method="post">
                    <input type="hidden" name="action" value="create_post">
                    
                    <div class="mb-4">
                        <label for="content" class="form-label">
                            <i class="fas fa-pen me-2"></i>Post Content
                        </label>
                        <textarea class="form-control" id="content" name="content" rows="6" 
                                  placeholder="What's on your mind?" required></textarea>
                        <small class="text-muted">Share your thoughts, ideas, or updates with the community</small>
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane me-2"></i>Publish Post
                        </button>
                        <a href="index.php" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-2"></i>Cancel
                        </a>
                    </div>
                </form>

                <div class="mt-4">
                    <h5><i class="fas fa-lightbulb me-2"></i>Posting Tips</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            Be respectful and constructive
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            Share relevant and interesting content
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            Engage with others through comments
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
