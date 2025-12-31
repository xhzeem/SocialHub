<?php
require_once __DIR__ . '/../config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$userId = $_GET['user_id'] ?? $_SESSION['user_id'];
$user = getUser($userId);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit_profile') {
    updateUserProfile($_POST['user_id'], $_POST['bio'], $_POST['email']);
    header("Location: profile.php?user_id={$_POST['user_id']}");
    exit;
}

$pageTitle = 'Edit Profile - SocialHub';
$headerTitle = 'Edit Profile';
$headerSubtitle = 'Update your profile information';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-lg">
            <div class="card-body p-4">
                <div class="text-center mb-4">
                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 80px; height: 80px; font-size: 2rem;">
                        <?php echo strtoupper(substr($user['username'], 0, 1)); ?>
                    </div>
                    <h4 class="card-title">Edit Profile</h4>
                    <p class="text-muted">Update your profile information</p>
                </div>

                <form method="post">
                    <input type="hidden" name="action" value="edit_profile">
                    <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="username" class="form-label">
                                    <i class="fas fa-user me-2"></i>Username
                                </label>
                                <input type="text" class="form-control" id="username" name="username" 
                                       value="<?php echo htmlspecialchars($user['username']); ?>" readonly>
                                <small class="text-muted">Username cannot be changed</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="email" class="form-label">
                                    <i class="fas fa-envelope me-2"></i>Email
                                </label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?php echo htmlspecialchars($user['email']); ?>" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label for="bio" class="form-label">
                            <i class="fas fa-info-circle me-2"></i>Bio
                        </label>
                        <textarea class="form-control" id="bio" name="bio" rows="4" 
                                  placeholder="Tell us about yourself..."><?php echo htmlspecialchars($user['bio'] ?? ''); ?></textarea>
                        <small class="text-muted">Share a little about yourself with the community</small>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Save Changes
                        </button>
                        <a href="profile.php?user_id=<?php echo $user['id']; ?>" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-2"></i>Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <div class="card shadow-lg mt-4">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-shield-alt me-2"></i>Security Information</h5>
                <div class="row">
                    <div class="col-md-6">
                        <p class="mb-2">
                            <strong>User ID:</strong> <?php echo $user['id']; ?>
                        </p>
                        <p class="mb-2">
                            <strong>Account Created:</strong> <?php echo date('M j, Y', strtotime($user['created_at'])); ?>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-2">
                            <strong>Last Login:</strong> <?php echo date('M j, Y g:i A'); ?>
                        </p>
                        <p class="mb-2">
                            <strong>Account Status:</strong> 
                            <span class="badge bg-success">Active</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
