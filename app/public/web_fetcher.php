<?php
require_once __DIR__ . '/../config.php';


if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$content = '';
$error = '';
$url = '';
$status_code = '';
$response_time = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'test_webhook') {
    $url = $_POST['url'] ?? '';
    
    if (!empty($url)) {
        try {
            $start_time = microtime(true);
            
            // Webhook testing functionality
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Webhook-Tester/1.0');
            
            $content = curl_exec($ch);
            $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $response_time = round((microtime(true) - $start_time) * 1000, 2);
            
            if ($content === false) {
                $error = 'Failed to connect: ' . curl_error($ch);
            }
            
            curl_close($ch);
        } catch (Exception $e) {
            $error = 'Connection error: ' . $e->getMessage();
        }
    } else {
        $error = 'Please enter a webhook URL';
    }
}

$pageTitle = 'Webhook Tester - SocialHub';
$headerTitle = 'Webhook Tester';
$headerSubtitle = 'Test and validate webhook endpoints';
require_once __DIR__ . '/../includes/header.php';
?>
<style>
    .webhook-form { 
        background: #f8f9fa; 
        padding: 20px; 
        border-radius: 8px; 
        margin-bottom: 20px; 
    }
    .response-display { 
        background: #1e1e1e; 
        color: #d4d4d4; 
        padding: 20px; 
        border-radius: 8px; 
        font-family: 'Courier New', monospace; 
        white-space: pre-wrap; 
        max-height: 400px; 
        overflow-y: auto; 
    }
    .status-badge { 
        padding: 4px 8px; 
        border-radius: 4px; 
        font-size: 0.8rem; 
        font-weight: bold; 
    }
    .status-success { 
        background: #d4edda; 
        color: #155724; 
    }
    .status-error { 
        background: #f8d7da; 
        color: #721c24; 
    }
    .status-info { 
        background: #d1ecf1; 
        color: #0c5460; 
    }
    .metrics { 
        display: flex; 
        gap: 15px; 
        margin-bottom: 20px; 
    }
    .metric { 
        background: white; 
        padding: 15px; 
        border-radius: 8px; 
        text-align: center; 
        flex: 1; 
    }
</style>

<div class="row">
    <div class="col-md-8">
        <div class="card shadow-lg mb-4">
            <div class="card-body">
                <h5 class="card-title mb-4">
                    <i class="fas fa-plug me-2"></i>Webhook Tester
                </h5>
                
                <div class="webhook-form">
                    <form method="post">
                        <input type="hidden" name="action" value="test_webhook">
                        <div class="mb-3">
                            <label for="url" class="form-label">
                                <i class="fas fa-link me-2"></i>Webhook URL
                            </label>
                            <input type="url" class="form-control" id="url" name="url" 
                                   placeholder="https://example.com/webhook" 
                                   value="<?php echo htmlspecialchars($url); ?>" required>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-play me-2"></i>Test Webhook
                            </button>
                            <button type="button" class="btn btn-outline-secondary" onclick="loadExample()">
                                <i class="fas fa-lightbulb me-2"></i>Load Example
                            </button>
                        </div>
                    </form>
                </div>

                <?php if ($error): ?>
                    <div class="alert alert-danger" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i><?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <?php if ($content || $status_code): ?>
                    <div class="metrics">
                        <div class="metric">
                            <h6 class="text-primary"><?php echo htmlspecialchars($status_code ?: 'N/A'); ?></h6>
                            <small class="text-muted">Status Code</small>
                        </div>
                        <div class="metric">
                            <h6 class="text-success"><?php echo htmlspecialchars($response_time ?: 'N/A'); ?></h6>
                            <small class="text-muted">Response Time</small>
                        </div>
                        <div class="metric">
                            <h6 class="text-info"><?php echo strlen($content); ?> bytes</h6>
                            <small class="text-muted">Content Size</small>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0">
                                <i class="fas fa-code me-2"></i>Response Content
                            </h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="response-display"><?php echo htmlspecialchars($content); ?></div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card shadow-lg">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-info-circle me-2"></i>Features</h5>
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <i class="fas fa-check text-success me-2"></i>
                        HTTP/HTTPS support
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check text-success me-2"></i>
                        Follow redirects
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check text-success me-2"></i>
                        SSL verification disabled
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check text-success me-2"></i>
                        30 second timeout
                    </li>
                </ul>
            </div>
        </div>

        <div class="card shadow-lg mt-4">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-link me-2"></i>Test URLs</h5>
                <div class="list-group list-group-flush">
                    <a href="#" class="list-group-item list-group-item-action" onclick="setUrl('https://httpbin.org/get')">
                        <small>https://httpbin.org/get</small>
                    </a>
                    <a href="#" class="list-group-item list-group-item-action" onclick="setUrl('https://jsonplaceholder.typicode.com/posts/1')">
                        <small>https://jsonplaceholder.typicode.com/posts/1</small>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function loadExample() {
    document.getElementById('url').value = 'https://httpbin.org/get';
}

function setUrl(url) {
    document.getElementById('url').value = url;
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
