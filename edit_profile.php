<?php
require_once 'config.php';

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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile - SocialHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; }
        .main-container { background: rgba(255,255,255,0.95); border-radius: 20px; box-shadow: 0 20px 40px rgba(0,0,0,0.1); }
        .header-gradient { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; }
        .profile-avatar { 
            width: 120px; 
            height: 120px; 
            border-radius: 50%; 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            color: white; 
            font-size: 3rem; 
            font-weight: bold;
            margin: 0 auto 20px;
        }
        .nav-link { color: #667eea !important; font-weight: 500; }
        .btn-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; }
        .btn-primary:hover { background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%); }
        .form-control:focus { border-color: #667eea; box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25); }
        .preview-box { 
            background: #f8f9ff; 
            border: 2px dashed #667eea; 
            border-radius: 10px; 
            padding: 20px; 
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container py-4">
        <div class="main-container p-4">
            <div class="header-gradient rounded-top p-4 mb-4">
                <div class="text-center">
                    <h1 class="h2 mb-2"><i class="fas fa-user-edit me-2"></i>Edit Profile</h1>
                    <p class="mb-0 opacity-75">Customize your personal information</p>
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
                            <a class="nav-link" href="profile.php?user_id=<?php echo $_SESSION['user_id']; ?>"><i class="fas fa-user me-1"></i> Profile</a>
                        </li>
                    </ul>
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link text-danger" href="index.php?action=logout"><i class="fas fa-sign-out-alt me-1"></i> Logout</a>
                        </li>
                    </ul>
                </div>
            </nav>
            
            <div class="row">
                <div class="col-lg-8">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <div class="text-center mb-4">
                                <div class="profile-avatar">
                                    <?php echo strtoupper(substr($user['username'], 0, 1)); ?>
                                </div>
                                <h4><?php echo htmlspecialchars($user['username']); ?></h4>
                                <p class="text-muted">Member since <?php echo date('F Y', strtotime($user['created_at'])); ?></p>
                            </div>
                            
                            <form method="POST">
                                <input type="hidden" name="action" value="edit_profile">
                                <input type="hidden" name="user_id" value="<?php echo $userId; ?>">
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="username" class="form-label">Username</label>
                                            <input type="text" class="form-control" id="username" value="<?php echo htmlspecialchars($user['username']); ?>" readonly>
                                            <small class="text-muted">Username cannot be changed</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="email" class="form-label">Email Address</label>
                                            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" placeholder="your.email@example.com">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="bio" class="form-label">Bio</label>
                                    <textarea class="form-control" id="bio" name="bio" rows="4" placeholder="Tell us about yourself..."><?php echo htmlspecialchars($user['bio'] ?? ''); ?></textarea>
                                    <small class="text-muted">Share your interests, hobbies, or anything you'd like others to know about you.</small>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="location" class="form-label">Location</label>
                                            <input type="text" class="form-control" id="location" placeholder="City, Country">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="website" class="form-label">Website</label>
                                            <input type="url" class="form-control" id="website" placeholder="https://yourwebsite.com">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-4">
                                    <label class="form-label">Privacy Settings</label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="public_profile" checked>
                                        <label class="form-check-label" for="public_profile">
                                            Make profile public
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="show_email">
                                        <label class="form-check-label" for="show_email">
                                            Show email address publicly
                                        </label>
                                    </div>
                                </div>
                                
                                <div class="d-flex justify-content-between">
                                    <a href="profile.php?user_id=<?php echo $userId; ?>" class="btn btn-outline-secondary">
                                        <i class="fas fa-arrow-left me-2"></i>Back to Profile
                                    </a>
                                    <div>
                                        <button type="button" class="btn btn-outline-secondary me-2">
                                            <i class="fas fa-times me-2"></i>Cancel
                                        </button>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save me-2"></i>Save Changes
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <div class="card shadow-sm mb-4">
                        <div class="card-body">
                            <h6 class="card-title"><i class="fas fa-info-circle me-2"></i>Profile Tips</h6>
                            <ul class="list-unstyled mb-0">
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Add a profile photo to personalize your account</li>
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Write a compelling bio to introduce yourself</li>
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Keep your information up to date</li>
                                <li class="mb-0"><i class="fas fa-check text-success me-2"></i>Adjust privacy settings as needed</li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="card shadow-sm mb-4">
                        <div class="card-body">
                            <h6 class="card-title"><i class="fas fa-shield-alt me-2"></i>Account Security</h6>
                            <div class="d-grid gap-2">
                                <button class="btn btn-outline-primary">
                                    <i class="fas fa-key me-2"></i>Change Password
                                </button>
                                <button class="btn btn-outline-secondary">
                                    <i class="fas fa-mobile-alt me-2"></i>Two-Factor Authentication
                                </button>
                                <button class="btn btn-outline-secondary">
                                    <i class="fas fa-history me-2"></i>Login History
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h6 class="card-title"><i class="fas fa-chart-line me-2"></i>Profile Statistics</h6>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Profile Views</span>
                                <strong>127</strong>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Connections</span>
                                <strong>48</strong>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>Posts</span>
                                <strong>23</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
