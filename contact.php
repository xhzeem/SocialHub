<?php
require_once 'config.php';

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'contact') {
    $success = addContactMessage($_POST['sender_name'], $_POST['sender_email'], $_POST['content']);
    if ($success) {
        $message = "Your message has been sent successfully!";
    } else {
        $message = "Failed to send message. Please try again.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - SocialHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; }
        .main-container { background: rgba(255,255,255,0.95); border-radius: 20px; box-shadow: 0 20px 40px rgba(0,0,0,0.1); }
        .header-gradient { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; }
        .contact-form { 
            background: white; 
            border-radius: 15px; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            padding: 40px;
        }
        .form-control:focus { border-color: #667eea; box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25); }
        .btn-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; }
        .btn-primary:hover { background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%); }
        .info-card { 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
            color: white; 
            border-radius: 15px; 
            padding: 30px;
            margin-bottom: 20px;
        }
        .contact-icon { font-size: 2.5rem; margin-bottom: 15px; }
        .nav-link { color: #667eea !important; font-weight: 500; }
    </style>
</head>
<body>
    <div class="container py-4">
        <div class="main-container p-4">
            <div class="header-gradient rounded-top p-4 mb-4">
                <div class="text-center">
                    <h1 class="h2 mb-2"><i class="fas fa-envelope me-2"></i>Contact Us</h1>
                    <p class="mb-0 opacity-75">Get in touch with our team</p>
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
                            <a class="nav-link active" href="contact.php"><i class="fas fa-envelope me-1"></i> Contact</a>
                        </li>
                    </ul>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <ul class="navbar-nav">
                            <li class="nav-item">
                                <a class="nav-link text-danger" href="index.php?action=logout"><i class="fas fa-sign-out-alt me-1"></i> Logout</a>
                            </li>
                        </ul>
                    <?php else: ?>
                        <ul class="navbar-nav">
                            <li class="nav-item">
                                <a class="nav-link" href="login.php"><i class="fas fa-sign-in-alt me-1"></i> Login</a>
                            </li>
                        </ul>
                    <?php endif; ?>
                </div>
            </nav>
            
            <?php if ($message): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php echo $message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <div class="row">
                <div class="col-lg-8">
                    <div class="contact-form">
                        <h3 class="mb-4"><i class="fas fa-paper-plane me-2"></i>Send us a Message</h3>
                        <p class="text-muted mb-4">We'd love to hear from you. Fill out the form below and we'll get back to you as soon as possible.</p>
                        
                        <form method="POST">
                            <input type="hidden" name="action" value="contact">
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="sender_name" class="form-label">Your Name *</label>
                                        <input type="text" class="form-control" id="sender_name" name="sender_name" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="sender_email" class="form-label">Your Email *</label>
                                        <input type="email" class="form-control" id="sender_email" name="sender_email" required>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="receiver_name" class="form-label">Recipient</label>
                                <select class="form-control" id="receiver_name" name="receiver_name">
                                    <option value="admin">Admin Team</option>
                                    <option value="support">Support Team</option>
                                    <option value="sales">Sales Team</option>
                                    <option value="technical">Technical Team</option>
                                </select>
                                <small class="text-muted">Choose the department you'd like to contact</small>
                            </div>
                            
                            <div class="mb-3">
                                <label for="subject" class="form-label">Subject</label>
                                <input type="text" class="form-control" id="subject" name="subject" placeholder="How can we help you?">
                            </div>
                            
                            <div class="mb-4">
                                <label for="content" class="form-label">Message *</label>
                                <textarea class="form-control" id="content" name="content" rows="6" required placeholder="Tell us more about your inquiry..."></textarea>
                            </div>
                            
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="newsletter" name="newsletter">
                                    <label class="form-check-label" for="newsletter">
                                        I'd like to receive updates and newsletters from SocialHub
                                    </label>
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-paper-plane me-2"></i>Send Message
                            </button>
                        </form>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <div class="info-card">
                        <div class="text-center">
                            <i class="fas fa-headset contact-icon"></i>
                            <h5>Get in Touch</h5>
                            <p class="mb-0">Our friendly team is here to help</p>
                        </div>
                    </div>
                    
                    <div class="card shadow-sm mb-4">
                        <div class="card-body">
                            <h6 class="card-title"><i class="fas fa-clock me-2"></i>Business Hours</h6>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Monday - Friday</span>
                                <strong>9:00 AM - 6:00 PM</strong>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>Saturday - Sunday</span>
                                <strong>10:00 AM - 4:00 PM</strong>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card shadow-sm mb-4">
                        <div class="card-body">
                            <h6 class="card-title"><i class="fas fa-map-marker-alt me-2"></i>Office Location</h6>
                            <p class="mb-0">
                                123 Social Street<br>
                                Tech City, TC 12345<br>
                                United States
                            </p>
                        </div>
                    </div>
                    
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h6 class="card-title"><i class="fas fa-phone me-2"></i>Contact Information</h6>
                            <div class="mb-2">
                                <strong>Phone:</strong> +1 (555) 123-4567
                            </div>
                            <div class="mb-2">
                                <strong>Email:</strong> support@socialhub.com
                            </div>
                            <div>
                                <strong>Live Chat:</strong> Available 24/7
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
