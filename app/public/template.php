<?php
require_once __DIR__ . '/../config.php';

// Security Headers Middleware
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: DENY");
header("X-XSS-Protection: 1; mode=block");
header("Referrer-Policy: strict-origin-when-cross-origin");

// Authentication Middleware (Required for template page)
$requireAuth = true; // Set to true to require authentication
if ($requireAuth && !isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Rate Limiting Middleware
$rateLimitKey = 'template_render_' . ($_SESSION['user_id'] ?? session_id());
$rateLimitWindow = 60; // 1 minute
$rateLimitMax = 10; // 10 requests per minute

if (!isset($_SESSION[$rateLimitKey])) {
    $_SESSION[$rateLimitKey] = ['count' => 0, 'reset_time' => time() + $rateLimitWindow];
}

if (time() > $_SESSION[$rateLimitKey]['reset_time']) {
    $_SESSION[$rateLimitKey] = ['count' => 0, 'reset_time' => time() + $rateLimitWindow];
}

if ($_SESSION[$rateLimitKey]['count'] >= $rateLimitMax) {
    http_response_code(429);
    die('Rate limit exceeded. Please try again later.');
}

// Input Validation Middleware
function validateTemplateInput($input) {
    // Check for dangerous patterns
    $dangerousPatterns = [
        '/eval\s*\(/i',
        '/system\s*\(/i',
        '/exec\s*\(/i',
        '/shell_exec\s*\(/i',
        '/passthru\s*\(/i',
        '/file_get_contents\s*\(/i',
        '/file_put_contents\s*\(/i',
        '/fopen\s*\(/i',
        '/unlink\s*\(/i',
        '/`.*`/U', // Backticks
    ];
    
    foreach ($dangerousPatterns as $pattern) {
        if (preg_match($pattern, $input)) {
            return false; // Dangerous pattern found
        }
    }
    
    return true; // Input is safe (for SSTI demo, we allow most things)
}

// Logging Middleware
function logTemplateRender($user, $content) {
    $logEntry = [
        'timestamp' => date('Y-m-d H:i:s'),
        'user' => $user,
        'content_preview' => substr($content, 0, 100),
        'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
    ];
    
    // In a real app, this would go to a file or database
    error_log('Template Render: ' . json_encode($logEntry));
}

$message = '';
$email_content = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'render') {
    // Update rate limit counter
    $_SESSION[$rateLimitKey]['count']++;
    
    $template_name = $_POST['template_name'] ?? 'Default Template';
    $email_content = $_POST['email_content'] ?? '';
    
    // Input Validation
    if (!validateTemplateInput($email_content)) {
        $message = "Dangerous code detected. Template rendering blocked for security.";
    } elseif (!empty($email_content)) {
        // Log the template render attempt
        logTemplateRender($_SESSION['username'] ?? 'Guest', $email_content);
        
        // Simple template rendering without Twig (SSTI vulnerability)
        try {
            // Replace basic template variables
            $context = [
                'user' => [
                    'name' => $_SESSION['username'] ?? 'Guest',
                    'email' => $_SESSION['email'] ?? 'guest@example.com',
                    'id' => $_SESSION['user_id'] ?? 0
                ],
                'posts' => getPosts(),
                'time' => date('Y-m-d H:i:s'),
                'server' => [
                    'php_version' => PHP_VERSION,
                    'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
                    'document_root' => $_SERVER['DOCUMENT_ROOT'] ?? 'Unknown'
                ]
            ];
            
            // Simple variable replacement (vulnerable to SSTI)
            $message = $email_content;
            foreach ($context as $key => $value) {
                if (is_array($value)) {
                    foreach ($value as $sub_key => $sub_value) {
                        // Only replace if sub_value is a string or can be converted to string
                        if (is_string($sub_value) || is_numeric($sub_value) || is_bool($sub_value)) {
                            $replaceValue = is_bool($sub_value) ? ($sub_value ? 'true' : 'false') : (string)$sub_value;
                            $message = str_replace('{{ ' . $key . '.' . $sub_key . ' }}', $replaceValue, $message);
                            $message = str_replace('{{ ' . $key . '[' . $sub_key . '] }}', $replaceValue, $message);
                        }
                    }
                } else {
                    // Only replace if value is a string or can be converted to string
                    if (is_string($value) || is_numeric($value) || is_bool($value)) {
                        $replaceValue = is_bool($value) ? ($value ? 'true' : 'false') : (string)$value;
                        $message = str_replace('{{ ' . $key . ' }}', $replaceValue, $message);
                    }
                }
            }
            
            // Evaluate remaining {{ }} expressions as PHP (SSTI vulnerability)
            $message = preg_replace_callback('/\{\{(.*?)\}\}/', function($matches) {
                $expression = trim($matches[1]);
                try {
                    // Evaluate the expression as PHP
                    ob_start();
                    $result = eval('return ' . $expression . ';');
                    $output = ob_get_clean();
                    return $output !== '' ? $output : (string)$result;
                } catch (Throwable $e) {
                    return '{{' . $expression . '}}'; // Return original if evaluation fails
                }
            }, $message);
            
            // Also allow PHP evaluation for SSTI vulnerability
            if (strpos($email_content, '<?php') !== false) {
                ob_start();
                eval('?>' . $email_content);
                $message = ob_get_clean();
            }
            
        } catch (Exception $e) {
            $message = "Error rendering template: " . $e->getMessage();
        }
    } else {
        $message = "Template content cannot be empty.";
    }
}

$pageTitle = 'Template Composer - SocialHub';
$headerTitle = 'Template Composer';
$headerSubtitle = 'Create and render custom templates';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card shadow-lg">
            <div class="card-body p-4">
                <div class="text-center mb-4">
                    <i class="fas fa-code fa-3x text-primary mb-3"></i>
                    <h4 class="card-title">Template Composer</h4>
                    <p class="text-muted">Create and render custom email templates</p>
                </div>

                <?php if ($message): ?>
                    <div class="card mb-4">
                        <div class="card-header bg-success text-white">
                            <i class="fas fa-check-circle me-2"></i>Rendered Template
                        </div>
                        <div class="card-body">
                            <pre class="bg-light p-3 rounded"><?php echo htmlspecialchars($message); ?></pre>
                        </div>
                    </div>
                <?php endif; ?>

                <form method="post">
                    <input type="hidden" name="action" value="render">
                    
                    <div class="mb-3">
                        <label for="template_name" class="form-label">
                            <i class="fas fa-tag me-2"></i>Template Name
                        </label>
                        <input type="text" class="form-control" id="template_name" name="template_name" 
                               value="Welcome Template" required>
                    </div>
                    
                    <div class="mb-4">
                        <label for="email_content" class="form-label">
                            <i class="fas fa-file-code me-2"></i>Template Content
                        </label>
                        <textarea class="form-control" id="email_content" name="email_content" rows="8" 
                                  placeholder="Enter your template content here..." required><?php echo htmlspecialchars($email_content ?? ''); ?></textarea>
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-play me-2"></i>Render Template
                        </button>
                        <button type="button" class="btn btn-outline-secondary" onclick="loadExample()">
                            <i class="fas fa-lightbulb me-2"></i>Load Example
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function loadExample() {
    const example = `Hello there,

Welcome to our platform! Here are your details:
- Email address: your.email@example.com
- Account number: 12345
- Current date: 2025-12-31
- System version: 8.1.34

Thank you for joining us!`;
    
    document.getElementById('email_content').value = example;
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
