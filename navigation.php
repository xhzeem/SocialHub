<?php
// Navigation component for SocialHub
function renderNavigation($currentPage = '') {
    $isLoggedIn = isset($_SESSION['user_id']);
    $userId = $_SESSION['user_id'] ?? '';
    
    // Navigation items configuration
    $navItems = [
        'index.php' => ['icon' => 'fas fa-home', 'label' => 'Home'],
        'upload.php' => ['icon' => 'fas fa-upload', 'label' => 'Upload'],
        'template.php' => ['icon' => 'fas fa-code', 'label' => 'Templates'],
        'web_fetcher.php' => ['icon' => 'fas fa-globe', 'label' => 'Web Fetcher'],
        'search.php' => ['icon' => 'fas fa-search', 'label' => 'Search'],
    ];
    
    // Add profile link if logged in
    if ($isLoggedIn) {
        $navItems["profile.php?user_id=$userId"] = ['icon' => 'fas fa-user', 'label' => 'Profile'];
        $navItems['contacts.php'] = ['icon' => 'fas fa-envelope', 'label' => 'Contacts'];
    }
    
    ?>
    <nav class="navbar navbar-expand-lg navbar-light bg-white rounded mb-4 shadow-sm">
        <div class="container-fluid">
            <ul class="navbar-nav me-auto">
                <?php foreach ($navItems as $url => $item): ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($currentPage === $url) ? 'active' : ''; ?>" 
                           href="<?php echo $url; ?>">
                            <i class="<?php echo $item['icon']; ?> me-1"></i>
                            <?php echo $item['label']; ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
            <?php if ($isLoggedIn): ?>
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link text-danger" href="index.php?action=logout">
                            <i class="fas fa-sign-out-alt me-1"></i> Logout
                        </a>
                    </li>
                </ul>
            <?php else: ?>
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="login.php">
                            <i class="fas fa-sign-in-alt me-1"></i> Login
                        </a>
                    </li>
                </ul>
            <?php endif; ?>
        </div>
    </nav>
    <?php
}

// Header component
function renderHeader($title, $subtitle, $icon = 'fas fa-users') {
    ?>
    <div class="header-gradient rounded-top p-4 mb-4">
        <div class="text-center">
            <h1 class="h2 mb-2"><i class="<?php echo $icon; ?> me-2"></i><?php echo $title; ?></h1>
            <p class="mb-0 opacity-75"><?php echo $subtitle; ?></p>
        </div>
    </div>
    <?php
}

// Footer component
function renderFooter() {
    ?>
    <div class="text-center mt-4 py-3 border-top">
        <p class="text-muted mb-0">
            &copy; 2025 SocialHub. All rights reserved. | 
            <a href="contact.php" class="text-decoration-none">Contact Us</a> | 
            <a href="#" class="text-decoration-none">Privacy Policy</a> | 
            <a href="#" class="text-decoration-none">Terms of Service</a>
        </p>
    </div>
    <?php
}

// Common styles
function renderCommonStyles() {
    ?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
            min-height: 100vh; 
        }
        .main-container { 
            background: rgba(255,255,255,0.95); 
            border-radius: 20px; 
            box-shadow: 0 20px 40px rgba(0,0,0,0.1); 
        }
        .header-gradient { 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
            color: white; 
        }
        .nav-link { 
            color: #667eea !important; 
            font-weight: 500; 
            transition: all 0.3s ease;
        }
        .nav-link:hover { 
            color: #764ba2 !important; 
            transform: translateY(-1px);
        }
        .nav-link.active { 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important; 
            color: white !important; 
            border-radius: 8px;
        }
        .btn-primary { 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
            border: none; 
        }
        .btn-primary:hover { 
            background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%); 
        }
        .form-control:focus { 
            border-color: #667eea; 
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25); 
        }
    </style>
    <?php
}

// Common scripts
function renderCommonScripts() {
    ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <?php
}
?>
