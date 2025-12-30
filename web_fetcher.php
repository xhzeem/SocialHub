<?php
require_once 'config.php';
require_once 'navigation.php';

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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Webhook Tester - SocialHub</title>
    <?php renderCommonStyles(); ?>
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
            border: 1px solid #dee2e6; 
        }
        .metric-value { 
            font-size: 1.5rem; 
            font-weight: bold; 
            color: #495057; 
        }
        .metric-label { 
            font-size: 0.8rem; 
            color: #6c757d; 
        }
    </style>
</head>
<body>
    <div class="container py-4">
        <div class="main-container p-4">
            <?php renderHeader('Webhook Tester', 'Test and validate webhook endpoints', 'fas fa-plug'); ?>
            
            <?php renderNavigation('web_fetcher.php'); ?>
            
            <div class="row">
                <div class="col-lg-8">
                    <div class="card shadow-sm mb-4">
                        <div class="card-body">
                            <h5 class="card-title"><i class="fas fa-network-wired me-2"></i>Webhook Configuration</h5>
                            
                            <div class="webhook-form">
                                <form method="POST">
                                    <input type="hidden" name="action" value="test_webhook">
                                    <div class="mb-3">
                                        <label for="url" class="form-label">Webhook URL</label>
                                        <input type="url" name="url" id="url" class="form-control" 
                                               value="<?php echo htmlspecialchars($url); ?>" 
                                               placeholder="https://example.com/webhook" required>
                                        <small class="text-muted">Enter the full URL of the webhook endpoint to test</small>
                                    </div>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-play me-2"></i>Test Webhook
                                    </button>
                                </form>
                            </div>
                            
                            <?php if (!empty($status_code) || $error): ?>
                                <div class="metrics">
                                    <div class="metric">
                                        <div class="metric-value"><?php echo $status_code ?: 'N/A'; ?></div>
                                        <div class="metric-label">Status Code</div>
                                    </div>
                                    <div class="metric">
                                        <div class="metric-value"><?php echo $response_time ?: 'N/A'; ?>ms</div>
                                        <div class="metric-label">Response Time</div>
                                    </div>
                                    <div class="metric">
                                        <div class="metric-value"><?php echo strlen($content); ?></div>
                                        <div class="metric-label">Response Size</div>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ($error): ?>
                                <div class="alert alert-danger">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    <?php echo htmlspecialchars($error); ?>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ($content !== ''): ?>
                                <div class="card">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0"><i class="fas fa-code me-2"></i>Response Body</h6>
                                        <span class="status-badge <?php echo $status_code >= 200 && $status_code < 300 ? 'status-success' : ($status_code >= 400 ? 'status-error' : 'status-info'); ?>">
                                            HTTP <?php echo $status_code; ?>
                                        </span>
                                    </div>
                                    <div class="card-body p-0">
                                        <div class="response-display"><?php echo htmlspecialchars($content); ?></div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <div class="card shadow-sm mb-4">
                        <div class="card-body">
                            <h6 class="card-title"><i class="fas fa-info-circle me-2"></i>Webhook Testing</h6>
                            <div class="list-group list-group-flush">
                                <div class="list-group-item">
                                    <small class="text-muted">HTTP methods</small><br>
                                    <span class="badge bg-primary">GET</span>
                                </div>
                                <div class="list-group-item">
                                    <small class="text-muted">SSL verification</small><br>
                                    <span class="badge bg-warning text-dark">Disabled</span>
                                </div>
                                <div class="list-group-item">
                                    <small class="text-muted">Timeout</small><br>
                                    <span class="badge bg-info">30 seconds</span>
                                </div>
                                <div class="list-group-item">
                                    <small class="text-muted">Redirects</small><br>
                                    <span class="badge bg-success">Enabled</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h6 class="card-title"><i class="fas fa-shield-alt me-2"></i>Usage Guidelines</h6>
                            <ul class="list-unstyled mb-0">
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Test only your own webhooks</li>
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Use HTTPS when possible</li>
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Check response codes</li>
                                <li class="mb-0"><i class="fas fa-check text-success me-2"></i>Monitor response times</li>
                                <li class="mb-0"><i class="fas fa-check text-success me-2"></i>No URL restrictions</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php renderCommonScripts(); ?>
    <script>
        function copyContent() {
            const content = document.querySelector('.content-display').textContent;
            navigator.clipboard.writeText(content).then(() => {
                alert('Content copied to clipboard!');
            });
        }
        
        function downloadContent() {
            const content = document.querySelector('.content-display').textContent;
            const blob = new Blob([content], { type: 'text/plain' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'fetched_content.txt';
            a.click();
            window.URL.revokeObjectURL(url);
        }
    </script>
</body>
</html>
