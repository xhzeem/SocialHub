<?php
require_once __DIR__ . '/../config.php';

// Require authentication for contacts page
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$messages = getMessages();
$isAdmin = isset($_SESSION['username']) && $_SESSION['username'] === 'admin';

// Handle message sending
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'send_message') {
    $recipient = $_POST['recipient'] ?? '';
    $content = $_POST['content'] ?? '';
    
    if (!empty($recipient) && !empty($content)) {
        $recipientId = getUserIdByUsername($recipient);
        if ($recipientId) {
            $result = sendMessage($_SESSION['user_id'], $recipientId, $content);
            if ($result) {
                // Refresh messages after sending
                $messages = getMessages();
                echo json_encode(['success' => true, 'message' => 'Message sent successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to send message']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Recipient not found']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Recipient and content are required']);
    }
    exit;
}

$pageTitle = 'Messages - SocialHub';
$headerTitle = 'Messages';
$headerSubtitle = 'View and manage user communications';
require_once __DIR__ . '/../includes/header.php';
?>
<style>
    .message-card { 
        transition: transform 0.3s ease, box-shadow 0.3s ease; 
    }
    .message-card:hover { 
        transform: translateY(-2px); 
        box-shadow: 0 8px 20px rgba(0,0,0,0.1); 
    }
    .message-meta { 
        font-size: 0.85rem; 
        color: #6c757d; 
    }
    .message-content { 
        white-space: pre-wrap; 
        line-height: 1.5; 
    }
    .admin-badge { 
        background: linear-gradient(135deg, #dc3545 0%, #c82333 100%); 
        color: white; 
        padding: 2px 8px; 
        border-radius: 12px; 
        font-size: 0.75rem; 
    }
</style>

<div class="row">
    <div class="col-md-8">
        <div class="card shadow-lg mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-envelope me-2"></i>Messages
                        <?php if ($isAdmin): ?>
                            <span class="admin-badge ms-2"><i class="fas fa-shield-alt me-1"></i>Admin</span>
                        <?php endif; ?>
                    </h5>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#composeModal">
                        <i class="fas fa-plus me-2"></i>Compose
                    </button>
                </div>

                <?php if (!empty($messages)): ?>
                    <div class="list-group">
                        <?php foreach ($messages as $message): ?>
                            <div class="list-group-item message-card">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <h6 class="mb-1">
                                            <?php echo htmlspecialchars($message['sender_name'] ?? 'Unknown'); ?>
                                            <?php if (($message['sender_name'] ?? '') === 'admin'): ?>
                                                <span class="badge bg-danger ms-2">Admin</span>
                                            <?php endif; ?>
                                        </h6>
                                        <small class="text-muted">
                                            <i class="fas fa-user me-1"></i>
                                            To: <?php echo htmlspecialchars($message['receiver_name'] ?? 'Unknown'); ?>
                                        </small>
                                    </div>
                                    <small class="text-muted message-meta">
                                        <i class="fas fa-clock me-1"></i>
                                        <?php echo date('M j, Y g:i A', strtotime($message['created_at'])); ?>
                                    </small>
                                </div>
                                <div class="message-content mb-2">
                                    <?php echo nl2br(htmlspecialchars($message['content'])); ?>
                                </div>
                                <div class="d-flex gap-2">
                                    <button class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-reply me-1"></i>Reply
                                    </button>
                                    <button class="btn btn-sm btn-outline-secondary">
                                        <i class="fas fa-share me-1"></i>Forward
                                    </button>
                                    <?php if ($isAdmin): ?>
                                        <button class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-trash me-1"></i>Delete
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                        <h5>No messages yet</h5>
                        <p class="text-muted">Start a conversation with your community members</p>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#composeModal">
                            <i class="fas fa-plus me-2"></i>Send First Message
                        </button>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card shadow-lg">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-info-circle me-2"></i>Message Center</h5>
                <div class="list-group list-group-flush">
                    <a href="#" class="list-group-item list-group-item-action active">
                        <i class="fas fa-inbox me-2"></i>Inbox
                        <span class="badge bg-primary float-end"><?php echo count($messages); ?></span>
                    </a>
                    <a href="#" class="list-group-item list-group-item-action">
                        <i class="fas fa-paper-plane me-2"></i>Sent
                        <span class="badge bg-secondary float-end">0</span>
                    </a>
                    <a href="#" class="list-group-item list-group-item-action">
                        <i class="fas fa-star me-2"></i>Starred
                        <span class="badge bg-warning text-dark float-end">0</span>
                    </a>
                    <a href="#" class="list-group-item list-group-item-action">
                        <i class="fas fa-trash me-2"></i>Trash
                        <span class="badge bg-danger float-end">0</span>
                    </a>
                </div>
            </div>
        </div>

        <?php if ($isAdmin): ?>
            <div class="card shadow-lg mt-4">
                <div class="card-body">
                    <h5 class="card-title"><i class="fas fa-user-shield me-2"></i>Admin Panel</h5>
                    <div class="d-grid gap-2">
                        <a href="admin_messages.php" class="btn btn-outline-primary">
                            <i class="fas fa-cog me-2"></i>Admin Messages
                        </a>
                        <button class="btn btn-outline-danger">
                            <i class="fas fa-trash-alt me-2"></i>Delete All Messages
                        </button>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Compose Message Modal -->
<div class="modal fade" id="composeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-pen me-2"></i>Compose Message
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="composeForm">
                    <div class="mb-3">
                        <label for="recipient" class="form-label">Recipient</label>
                        <input type="text" class="form-control" id="recipient" name="recipient" 
                               placeholder="Enter username" required>
                    </div>
                    <div class="mb-3">
                        <label for="subject" class="form-label">Subject</label>
                        <input type="text" class="form-control" id="subject" name="subject" 
                               placeholder="Enter subject">
                    </div>
                    <div class="mb-3">
                        <label for="content" class="form-label">Message</label>
                        <textarea class="form-control" id="content" name="content" rows="5" 
                                  placeholder="Type your message here..." required></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="sendMessage()">
                    <i class="fas fa-paper-plane me-2"></i>Send Message
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function sendMessage() {
    const form = document.getElementById('composeForm');
    const formData = new FormData(form);
    
    // Add action for message sending
    formData.append('action', 'send_message');
    
    fetch('contacts.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Message sent successfully!');
            bootstrap.Modal.getInstance(document.getElementById('composeModal')).hide();
            form.reset();
            // Refresh the page to show new message
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to send message. Please try again.');
    });
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
