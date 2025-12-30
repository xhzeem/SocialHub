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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'fetch_url') {
    $url = $_POST['url'] ?? '';
    
    if (!empty($url)) {
        try {
            // SSRF vulnerability - no validation on URL
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            
            $content = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            
            if ($content === false) {
                $error = 'Failed to fetch URL: ' . curl_error($ch);
            } elseif ($httpCode >= 400) {
                $error = "HTTP Error: $httpCode";
            }
            
            curl_close($ch);
        } catch (Exception $e) {
            $error = 'Error: ' . $e->getMessage();
        }
    } else {
        $error = 'Please enter a URL';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Web Fetcher - SocialHub</title>
    <?php renderCommonStyles(); ?>
</head>
<body>
    <div class="container py-4">
        <div class="main-container p-4">
            <?php renderHeader('Web Fetcher', 'Fetch and display content from any URL', 'fas fa-globe'); ?>
            
            <?php renderNavigation('web_fetcher.php'); ?>
            
            <div class="fetch-form">
                <div class="text-center mb-4">
                    <h3 class="fw-bold">URL Content Fetcher</h3>
                    <p class="text-muted">Enter any URL to fetch and display its content</p>
                </div>
                
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <span class="status-indicator status-error"></span>
                        <strong>Error:</strong> <?php echo htmlspecialchars($error); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <?php if ($content !== ''): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <span class="status-indicator status-success"></span>
                        <strong>Success:</strong> Content fetched successfully from <?php echo htmlspecialchars($url); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <form method="POST">
                    <input type="hidden" name="action" value="fetch_url">
                    <div class="row align-items-center mb-4">
                        <div class="col-md-10">
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i class="fas fa-link text-muted"></i></span>
                                <input type="url" class="form-control url-input border-start-0" name="url" placeholder="Enter URL (e.g., https://example.com)" value="<?php echo htmlspecialchars($url); ?>" required>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-fetch w-100">
                                <i class="fas fa-download me-2"></i>Fetch
                            </button>
                        </div>
                    </div>
                </form>
                
                <div class="url-preview">
                    <h6 class="mb-3"><i class="fas fa-info-circle me-2"></i>Supported URL Types</h6>
                    <div class="row">
                        <div class="col-md-4">
                            <ul class="feature-list">
                                <li><i class="fas fa-check-circle"></i>HTTP/HTTPS</li>
                                <li><i class="fas fa-check-circle"></i>FTP</li>
                                <li><i class="fas fa-check-circle"></i>File URLs</li>
                            </ul>
                        </div>
                        <div class="col-md-4">
                            <ul class="feature-list">
                                <li><i class="fas fa-check-circle"></i>Local URLs</li>
                                <li><i class="fas fa-check-circle"></i>Internal IPs</li>
                                <li><i class="fas fa-check-circle"></i>Custom Ports</li>
                            </ul>
                        </div>
                        <div class="col-md-4">
                            <ul class="feature-list">
                                <li><i class="fas fa-check-circle"></i>API Endpoints</li>
                                <li><i class="fas fa-check-circle"></i>JSON/XML</li>
                                <li><i class="fas fa-check-circle"></i>Text Content</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            
            <?php if ($content !== ''): ?>
                <div class="result-box">
                    <h5 class="mb-3">
                        <i class="fas fa-code me-2"></i>Fetched Content
                        <small class="text-muted ms-2">From: <?php echo htmlspecialchars($url); ?></small>
                    </h5>
                    <div class="content-display">
                        <?php echo htmlspecialchars(substr($content, 0, 10000)); ?>
                        <?php if (strlen($content) > 10000): ?>
                            <div class="text-muted mt-3">
                                <i class="fas fa-ellipsis-h me-1"></i>
                                Content truncated (showing first 10,000 characters)
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="mt-3">
                        <button class="btn btn-outline-primary btn-sm" onclick="copyContent()">
                            <i class="fas fa-copy me-1"></i>Copy Content
                        </button>
                        <button class="btn btn-outline-secondary btn-sm" onclick="downloadContent()">
                            <i class="fas fa-download me-1"></i>Download
                        </button>
                    </div>
                </div>
            <?php endif; ?>
            
            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h6 class="card-title"><i class="fas fa-lightbulb me-2"></i>Use Cases</h6>
                            <ul class="list-unstyled mb-0">
                                <li class="mb-2"><i class="fas fa-arrow-right text-primary me-2"></i>Check website status</li>
                                <li class="mb-2"><i class="fas fa-arrow-right text-primary me-2"></i>Fetch API responses</li>
                                <li class="mb-2"><i class="fas fa-arrow-right text-primary me-2"></i>View page source</li>
                                <li class="mb-0"><i class="fas fa-arrow-right text-primary me-2"></i>Access internal services</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h6 class="card-title"><i class="fas fa-shield-alt me-2"></i>Features</h6>
                            <ul class="list-unstyled mb-0">
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>SSL verification bypass</li>
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Follow redirects</li>
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>30 second timeout</li>
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
