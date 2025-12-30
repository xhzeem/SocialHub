<?php
require_once 'config.php';
require_once 'navigation.php';

// Include Composer autoloader for Twig
require_once 'vendor/autoload.php';

$message = '';
$email_content = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'render') {
    $email_content = $_POST['template'] ?? '';
    if (!empty($email_content)) {
        try {
            $loader = new \Twig\Loader\ArrayLoader(['email' => $email_content]);
            $twig = new \Twig\Environment($loader);
            
            $context = [
                'user' => [
                    'name' => $_SESSION['username'] ?? 'Guest',
                    'email' => $_SESSION['email'] ?? 'guest@example.com',
                    'id' => $_SESSION['user_id'] ?? 0
                ],
                'posts' => getPosts(),
                'time' => date('Y-m-d H:i:s')
            ];
            
            $message = $twig->render('email', $context);
        } catch (Exception $e) {
            $message = "Error rendering message: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Message Composer - SocialHub</title>
    <?php renderCommonStyles(); ?>
    <style>
        .code-editor { 
            font-family: 'Courier New', monospace; 
            background: #f8f9fa; 
            border: 1px solid #dee2e6; 
        }
        .result-box { 
            background: #f8f9fa; 
            border: 1px solid #dee2e6; 
            border-radius: 4px; 
            padding: 15px; 
            max-height: 300px; 
            overflow-y: auto; 
        }
        .content-display { 
            background: white; 
            padding: 10px; 
            border-radius: 4px; 
            margin: 0; 
            white-space: pre-wrap; 
        }
    </style>
</head>
<body>
    <div class="container py-4">
        <div class="main-container p-4">
            <?php renderHeader('Message Composer', 'Create and preview personalized messages', 'fas fa-pen'); ?>
            
            <?php renderNavigation('template.php'); ?>
            
            <div class="row">
                <div class="col-lg-8">
                    <div class="card shadow-sm mb-4">
                        <div class="card-body">
                            <h5 class="card-title"><i class="fas fa-edit me-2"></i>Compose Message</h5>
                            <form method="POST">
                                <input type="hidden" name="action" value="render">
                                <div class="mb-3">
                                    <label for="template" class="form-label">Message Content</label>
                                    <textarea name="template" id="template" class="form-control code-editor" rows="6" placeholder="Type your message here...">Hi {{ user.name }},

Thanks for being part of our community!

Best,
The Team</textarea>
                                </div>
                                <button type="submit" class="btn btn-primary"><i class="fas fa-eye me-2"></i>Preview Message</button>
                            </form>
                        </div>
                    </div>
                    
                    <?php if ($message): ?>
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title"><i class="fas fa-envelope-open-text me-2"></i>Message Preview</h5>
                                <div class="result-box">
                                    <pre class="content-display"><?php echo htmlspecialchars($message); ?></pre>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="col-lg-4">
                    <div class="card shadow-sm mb-4">
                        <div class="card-body">
                            <h6 class="card-title"><i class="fas fa-info-circle me-2"></i>Message Features</h6>
                            <div class="list-group list-group-flush">
                                <div class="list-group-item">
                                    <small class="text-muted">Personalization</small><br>
                                    <span class="badge bg-primary">Active</span>
                                </div>
                                <div class="list-group-item">
                                    <small class="text-muted">Dynamic content</small><br>
                                    <span class="badge bg-success">Enabled</span>
                                </div>
                                <div class="list-group-item">
                                    <small class="text-muted">User variables</small><br>
                                    <span class="badge bg-info">Available</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h6 class="card-title"><i class="fas fa-shield-alt me-2"></i>Writing Tips</h6>
                            <ul class="list-unstyled mb-0">
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Be friendly and welcoming</li>
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Include personal touches</li>
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Keep messages concise</li>
                                <li class="mb-0"><i class="fas fa-check text-success me-2"></i>Test before sending</li>
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
