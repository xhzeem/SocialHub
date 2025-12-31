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
?>
