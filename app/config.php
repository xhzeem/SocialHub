<?php
session_start();

$host = getenv('DB_HOST') ?: 'mysql';
$user = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASSWORD') ?: 'password';
$dbname = getenv('DB_NAME') ?: 'vulnerable_social';

// Database connection with retry logic
$maxRetries = 5;
$retryDelay = 3; // seconds
$connected = false;

for ($i = 0; $i < $maxRetries; $i++) {
    try {
        $conn = new mysqli($host, $user, $password, $dbname);
        
        if ($conn->connect_error) {
            if ($i < $maxRetries - 1) {
                sleep($retryDelay);
                continue;
            } else {
                die("Connection failed after $maxRetries attempts: " . $conn->connect_error);
            }
        }
        
        $connected = true;
        break;
    } catch (Exception $e) {
        if ($i < $maxRetries - 1) {
            sleep($retryDelay);
            continue;
        } else {
            die("Database connection failed: " . $e->getMessage());
        }
    }
}

if (!$connected) {
    die("Failed to connect to database");
}

function getPosts() {
    global $conn;
    $sql = "SELECT posts.*, users.username FROM posts JOIN users ON posts.user_id = users.id ORDER BY posts.created_at DESC";
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

function getUser($userId) {
    global $conn;
    $sql = "SELECT * FROM users WHERE id = $userId";
    $result = $conn->query($sql);
    return $result->fetch_assoc();
}

function getUserPosts($userId) {
    global $conn;
    $sql = "SELECT * FROM posts WHERE user_id = $userId ORDER BY created_at DESC";
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

function searchUsers($query) {
    global $conn;
    $sql = "SELECT * FROM users WHERE username LIKE '%$query%' OR bio LIKE '%$query%'";
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

function createPost($userId, $content) {
    global $conn;
    $sql = "INSERT INTO posts (user_id, content) VALUES ($userId, '$content')";
    return $conn->query($sql);
}

function loginUser($username, $password) {
    global $conn;
    $sql = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
    $result = $conn->query($sql);
    return $result->fetch_assoc();
}

function registerUser($username, $password, $email, $bio = '') {
    global $conn;
    $sql = "INSERT INTO users (username, password, email, bio) VALUES ('$username', '$password', '$email', '$bio')";
    return $conn->query($sql);
}

function getMessages() {
    global $conn;
    $sql = "SELECT messages.*, sender.username as sender_name, receiver.username as receiver_name FROM messages JOIN users sender ON messages.sender_id = sender.id JOIN users receiver ON messages.receiver_id = receiver.id ORDER BY messages.created_at DESC";
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

function addContactMessage($senderName, $senderEmail, $message) {
    global $conn;
    $content = "From: $senderName ($senderEmail)\n\n$message";
    $sql = "INSERT INTO messages (sender_id, receiver_id, content) VALUES (1, 1, '$content')";
    return $conn->query($sql);
}

function sendMessage($senderId, $recipientId, $content) {
    global $conn;
    $sql = "INSERT INTO messages (sender_id, receiver_id, content) VALUES ($senderId, $recipientId, '" . addslashes($content) . "')";
    return $conn->query($sql);
}

function getUserIdByUsername($username) {
    global $conn;
    $sql = "SELECT id FROM users WHERE username = '" . addslashes($username) . "'";
    $result = $conn->query($sql);
    if ($row = $result->fetch_assoc()) {
        return $row['id'];
    }
    return null;
}

function updateUserProfile($userId, $bio, $email) {
    global $conn;
    $sql = "UPDATE users SET bio = '$bio', email = '$email' WHERE id = $userId";
    return $conn->query($sql);
}

function addComment($postId, $userId, $content) {
    global $conn;
    $sql = "INSERT INTO comments (post_id, user_id, content) VALUES ($postId, $userId, '$content')";
    return $conn->query($sql);
}

function uploadFile($file) {
    $uploadDir = 'uploads/';
    
    // Debug: Log file info
    error_log("Upload attempt - File info: " . print_r($file, true));
    
    // Check if uploads directory exists, if not create it
    if (!is_dir($uploadDir)) {
        if (!mkdir($uploadDir, 0755, true)) {
            error_log("Failed to create uploads directory");
            return false;
        }
        error_log("Created uploads directory");
    }
    
    // Check directory permissions
    if (!is_writable($uploadDir)) {
        error_log("Uploads directory is not writable");
        return false;
    }
    
    $fileName = basename($file['name']);
    $targetPath = $uploadDir . $fileName;
    
    // Check if file already exists
    if (file_exists($targetPath)) {
        $fileName = time() . '_' . $fileName;
        $targetPath = $uploadDir . $fileName;
    }
    
    error_log("Moving file from " . $file['tmp_name'] . " to " . $targetPath);
    
    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        error_log("Upload successful: " . $targetPath);
        return true;
    } else {
        error_log("Upload failed - Error code: " . $file['error'] . " - Temp file exists: " . file_exists($file['tmp_name']));
        return false;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    switch ($_POST['action']) {
        case 'login':
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';
            $user = loginUser($username, $password);
            if ($user) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                header('Location: index.php');
            } else {
                $error = "Invalid credentials";
            }
            break;
            
        case 'register':
            $bio = $_POST['bio'] ?? '';
            registerUser($_POST['username'], $_POST['password'], $_POST['email'], $bio);
            header('Location: login.php');
            break;
            
        case 'post':
            if (isset($_SESSION['user_id'])) {
                $content = $_POST['content'] ?? '';
                createPost($_SESSION['user_id'], $content);
                header('Location: index.php');
            }
            break;
            
        case 'contact':
            $senderName = $_POST['sender_name'] ?? '';
            $senderEmail = $_POST['sender_email'] ?? '';
            $message = $_POST['message'] ?? '';
            addContactMessage($senderName, $senderEmail, $message);
            $success = "Message sent successfully!";
            break;
            
        case 'edit_profile':
            if (isset($_SESSION['user_id'])) {
                $bio = $_POST['bio'] ?? '';
                $email = $_POST['email'] ?? '';
                updateUserProfile($_POST['user_id'], $bio, $email);
                header("Location: profile.php?user_id={$_POST['user_id']}");
            }
            break;
            
        case 'comment':
            if (isset($_SESSION['user_id'])) {
                addComment($_POST['post_id'], $_SESSION['user_id'], $_POST['content']);
                header('Location: index.php');
            }
            break;
    }
}

if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: login.php');
}

function renderNavigation() {
    $navItems = [
        ['url' => 'index.php', 'label' => 'Home', 'icon' => 'fas fa-home'],
        ['url' => 'search.php', 'label' => 'Search', 'icon' => 'fas fa-search'],
        ['url' => 'upload.php', 'label' => 'Upload', 'icon' => 'fas fa-upload'],
        ['url' => 'template.php', 'label' => 'Templates', 'icon' => 'fas fa-code'],
        ['url' => 'web_fetcher.php', 'label' => 'Web Fetcher', 'icon' => 'fas fa-globe'],
        ['url' => 'contacts.php', 'label' => 'Contacts', 'icon' => 'fas fa-envelope']
    ];
    
    // Add admin messages for logged-in admin users
    if (isset($_SESSION['username']) && $_SESSION['username'] === 'admin') {
        $navItems[] = ['url' => 'admin_messages.php', 'label' => 'Messages', 'icon' => 'fas fa-comments'];
    }
    
    echo '<ul class="navbar-nav me-auto">';
    foreach ($navItems as $item) {
        $active = basename($_SERVER['PHP_SELF']) === basename($item['url']) ? 'active' : '';
        echo "<li class='nav-item'>";
        echo "<a class='nav-link {$active}' href='{$item['url']}'>";
        echo "<i class='{$item['icon']} me-2'></i>{$item['label']}";
        echo "</a>";
        echo "</li>";
    }
    echo '</ul>';
    
    // User menu or login
    echo '<ul class="navbar-nav">';
    if (isset($_SESSION['username'])) {
        echo '<li class="nav-item dropdown">';
        echo "<a class='nav-link dropdown-toggle' href='#' role='button' data-bs-toggle='dropdown'>";
        echo "<i class='fas fa-user me-2'></i>{$_SESSION['username']}";
        echo '</a>';
        echo '<ul class="dropdown-menu">';
        echo "<li><a class='dropdown-item' href='profile.php?user_id={$_SESSION['user_id']}'>Profile</a></li>";
        echo "<li><a class='dropdown-item' href='edit_profile.php'>Edit Profile</a></li>";
        echo '<li><hr class="dropdown-divider"></li>';
        echo "<li><a class='dropdown-item text-danger' href='index.php?logout'>Logout</a></li>";
        echo '</ul>';
        echo '</li>';
    } else {
        echo "<li class='nav-item'>";
        echo "<a class='nav-link' href='login.php'>";
        echo "<i class='fas fa-sign-in-alt me-2'></i>Login";
        echo "</a>";
        echo "</li>";
        echo "<li class='nav-item'>";
        echo "<a class='nav-link' href='register.php'>";
        echo "<i class='fas fa-user-plus me-2'></i>Register";
        echo "</a>";
        echo "</li>";
    }
    echo '</ul>';
}

function getTotalUploads() {
    global $conn;
    try {
        $sql = "SELECT COUNT(*) as total FROM uploads";
        $result = $conn->query($sql);
        $row = $result->fetch_assoc();
        return $row['total'] ?? 0;
    } catch (mysqli_sql_exception $e) {
        // Table doesn't exist, return 0
        return 0;
    }
}
?>
