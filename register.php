<?php
require_once 'config.php';
require_once 'navigation.php';

if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'register') {
    $result = registerUser($_POST['username'], $_POST['password'], $_POST['email']);
    if ($result) {
        header('Location: login.php');
    } else {
        $error = "Registration failed. Username may already exist.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - SocialHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
            min-height: 100vh; 
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .register-container { 
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
        .register-form { padding: 3rem; }
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
        .password-strength { height: 5px; border-radius: 3px; margin-top: 5px; }
        .strength-weak { background: #dc3545; }
        .strength-medium { background: #ffc107; }
        .strength-strong { background: #28a745; }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="row g-0">
            <div class="col-md-6">
                <div class="header-gradient h-100 d-flex flex-column justify-content-center">
                    <div>
                        <h1 class="display-4 fw-bold mb-3"><i class="fas fa-users me-3"></i>SocialHub</h1>
                        <p class="lead mb-4">Join our community and connect with friends around the world.</p>
                        <ul class="feature-list">
                            <li><i class="fas fa-check-circle"></i> Share photos and updates</li>
                            <li><i class="fas fa-check-circle"></i> Connect with friends</li>
                            <li><i class="fas fa-check-circle"></i> Join communities</li>
                            <li><i class="fas fa-check-circle"></i> Share your story</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="register-form">
                    <div class="text-center mb-4">
                        <h3 class="fw-bold">Create Account</h3>
                        <p class="text-muted">Join SocialHub today</p>
                    </div>
                    
                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?php echo $error; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST">
                        <input type="hidden" name="action" value="register">
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                                <input type="text" class="form-control" id="username" name="username" required>
                            </div>
                            <small class="text-muted">Choose a unique username</small>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <small class="text-muted">We'll never share your email</small>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <div class="password-strength" id="passwordStrength"></div>
                            <small class="text-muted">Use at least 8 characters</small>
                        </div>
                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Confirm Password</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                            </div>
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="terms" required>
                            <label class="form-check-label" for="terms">
                                I agree to the <a href="#" class="text-decoration-none">Terms of Service</a> and <a href="#" class="text-decoration-none">Privacy Policy</a>
                            </label>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 mb-3">Create Account</button>
                        <div class="text-center">
                            <p class="text-muted mb-0">Already have an account? <a href="login.php" class="text-decoration-none fw-bold">Sign in</a></p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Password strength checker
        document.getElementById('password').addEventListener('input', function() {
            const password = this.value;
            const strengthBar = document.getElementById('passwordStrength');
            
            if (password.length < 4) {
                strengthBar.className = 'password-strength strength-weak';
            } else if (password.length < 8) {
                strengthBar.className = 'password-strength strength-medium';
            } else {
                strengthBar.className = 'password-strength strength-strong';
            }
        });
    </script>
</body>
</html>
