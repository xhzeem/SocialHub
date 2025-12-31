<?php
require_once __DIR__ . '/../config.php';

// Admin authentication check
$shouldRedirect = false;
if (!isset($_SESSION['username']) || $_SESSION['username'] !== 'admin') {
    $shouldRedirect = true;
}

$messages = getMessages();

// Add some sample messages if database is empty
if (empty($messages)) {
    $messages = [
        [
            'sender_name' => 'john_doe',
            'receiver_name' => 'admin',
            'content' => 'I found an issue in your system. Please check the upload functionality.',
            'created_at' => '2025-12-30 14:15:30',
            'status' => 'unread'
        ],
        [
            'sender_name' => 'jane_smith',
            'receiver_name' => 'admin',
            'content' => 'The webhook tester seems to be accessing internal services. Is this intentional?',
            'created_at' => '2025-12-30 13:45:22',
            'status' => 'unread'
        ],
        [
            'sender_name' => 'security_team',
            'receiver_name' => 'admin',
            'content' => 'ALERT: Unusual activity detected from IP 172.27.0.3 targeting port 8080.',
            'created_at' => '2025-12-30 12:30:15',
            'status' => 'unread'
        ],
        [
            'sender_name' => 'system_monitor',
            'receiver_name' => 'admin',
            'content' => 'Database credentials may be exposed. Please review admin panel access logs.',
            'created_at' => '2025-12-30 11:20:10',
            'status' => 'read'
        ],
        [
            'sender_name' => 'anonymous_user',
            'receiver_name' => 'admin',
            'content' => 'I was able to access admin_messages.php without logging in. This seems like an issue.',
            'created_at' => '2025-12-30 10:15:05',
            'status' => 'flagged'
        ]
    ];
}

// Send redirect header after content is prepared but before any HTML output
if ($shouldRedirect) {
    header('Location: login.php');
    header('HTTP/1.1 302 Found');
    // Don't exit() - continue to render content anyway
}

$pageTitle = 'Admin Messages';
$headerTitle = 'Message Center';
$headerSubtitle = 'Manage user communications and system alerts';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="row">
    <div class="col-12">
        <!-- Stats Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card stats-card">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo count($messages); ?></h5>
                        <p class="card-text mb-0">Total Messages</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stats-card">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo count(array_filter($messages, fn($m) => ($m['status'] ?? '') === 'unread')); ?></h5>
                        <p class="card-text mb-0">Unread</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stats-card">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo count(array_filter($messages, fn($m) => ($m['status'] ?? '') === 'flagged')); ?></h5>
                        <p class="card-text mb-0">Flagged</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stats-card">
                    <div class="card-body">
                        <h5 class="card-title">95%</h5>
                        <p class="card-text mb-0">Response Rate</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Messages List -->
        <div class="card">
            <div class="card-header bg-white">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-envelope me-2"></i>Recent Messages</h5>
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-primary active">All</button>
                        <button class="btn btn-outline-primary">Unread</button>
                        <button class="btn btn-outline-primary">Flagged</button>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <?php foreach ($messages as $message): ?>
                    <div class="message-card p-3 border-bottom <?php echo $message['status'] ?? ''; ?>">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                        <?php echo strtoupper(substr($message['sender_name'], 0, 1)); ?>
                                    </div>
                                    <div>
                                        <h6 class="mb-0"><?php echo htmlspecialchars($message['sender_name']); ?></h6>
                                        <small class="text-muted">
                                            To: <?php echo htmlspecialchars($message['receiver_name']); ?> • 
                                            <?php echo date('M j, Y g:i A', strtotime($message['created_at'])); ?>
                                        </small>
                                    </div>
                                </div>
                                <p class="mb-2"><?php echo htmlspecialchars($message['content']); ?></p>
                                <div class="d-flex gap-2">
                                    <span class="badge bg-<?php echo $message['status'] === 'unread' ? 'success' : ($message['status'] === 'flagged' ? 'danger' : 'secondary'); ?>">
                                        <?php echo ucfirst($message['status']); ?>
                                    </span>
                                    <button class="btn btn-sm btn-outline-primary">Reply</button>
                                    <button class="btn btn-sm btn-outline-secondary">Archive</button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
