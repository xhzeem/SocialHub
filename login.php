<?php
require_once 'config.php';
require_once 'navigation.php';

if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'login') {
    $user = loginUser($_POST['username'], $_POST['password']);
    if ($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        header('Location: index.php');
        exit;
    } else {
        $error = "Invalid username or password";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SocialHub</title>
    <?php renderCommonStyles(); ?>
    <style>
        body { 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
            min-height: 100vh; 
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-container { 
            background: rgba(255,255,255,0.95); 
            border-radius: 20px; 
            box-shadow: 0 20px 40px rgba(0,0,0,0.1); 
            overflow: hidden;
            max-width: 900px;
            width: 100%;
        }
        .header-gradient { 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
            color: white; 
            padding: 3rem;
            text-align: center;
        }
        .login-form { padding: 3rem; }
        .btn-primary { 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
            border: none; 
            padding: 12px 30px;
            font-weight: 500;
        }
        .btn-primary:hover { 
            background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%); 
        }
        .form-control:focus { 
            border-color: #667eea; 
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25); 
        }
        .feature-list { list-style: none; padding: 0; }
        .feature-list li { padding: 0.5rem 0; }
        .feature-list i { color: #667eea; margin-right: 0.5rem; }
    </style>
</head>
<body>
    <div class="container py-4">
        <div class="main-container p-4">
            <?php renderHeader('Welcome Back', 'Sign in to your account', 'fas fa-sign-in-alt'); ?>
            <div class="login-container">
                <div class="row g-0">
                    <div class="col-md-6">
                        <div class="header-gradient h-100 d-flex flex-column justify-content-center">
                            <div>
                                <h1 class="display-4 fw-bold mb-3"><i class="fas fa-users me-3"></i>SocialHub</h1>
                                <p class="lead mb-4">Connect with friends and share your moments in a beautiful social experience.</p>
                                <ul class="feature-list">
                                    <li><i class="fas fa-check-circle"></i> Share photos and updates</li>
                                    <li><i class="fas fa-check-circle"></i> Connect with friends</li>
                                    <li><i class="fas fa-check-circle"></i> Join communities</li>
                                    <li><i class="fas fa-check-circle"></i> Share your story</li>
                                </ul>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="login-form">
                    <div class="text-center mb-4">
                        <h3 class="fw-bold">Welcome Back</h3>
                        <p class="text-muted">Sign in to your account</p>
                    </div>
                    
                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?php echo $error; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST">
                        <input type="hidden" name="action" value="login">
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                                <input type="text" class="form-control" id="username" name="username" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="remember">
                            <label class="form-check-label" for="remember">Remember me</label>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 mb-3">Sign In</button>
                        <div class="text-center">
                            <a href="#" class="text-decoration-none">Forgot password?</a>
                        </div>
                    </form>
                    
                    <div class="text-center mt-4">
                        <p class="text-muted">Don't have an account? <a href="register.php" class="text-decoration-none fw-bold">Sign up</a></p>
                        <p class="text-muted mb-0">Need help? <a href="contact.php" class="text-decoration-none fw-bold">Contact us</a></p>
                        <p class="text-muted mb-0">Want to try our <a href="web_fetcher.php" class="text-decoration-none fw-bold">Web Fetcher</a> tool?</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php renderCommonScripts(); ?>
</body>
</html>
