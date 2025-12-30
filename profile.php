<?php
require_once 'config.php';

$userId = $_GET['user_id'] ?? 1;
$user = getUser($userId);
$posts = getUserPosts($userId);
$isAdmin = isset($_SESSION['username']) && $_SESSION['username'] === 'admin';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($user['username']); ?>'s Profile</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; background: #f0f2f5; }
        .container { max-width: 800px; margin: 0 auto; padding: 20px; }
        .header { background: #4267B2; color: white; padding: 20px; text-align: center; }
        .post { background: white; margin: 20px 0; padding: 20px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .post h3 { margin: 0 0 10px 0; color: #4267B2; }
        .nav { background: white; padding: 10px; margin-bottom: 20px; border-radius: 8px; }
        .nav a { margin-right: 15px; text-decoration: none; color: #4267B2; }
        .btn { background: #4267B2; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; display: inline-block; }
        .btn:hover { background: #365899; }
    </style>
</head>
<body>
    <div class="header">
        <h1>🔓 <?php echo htmlspecialchars($user['username']); ?>'s Profile</h1>
    </div>
    
    <div class="container">
        <div class="nav">
            <a href="index.php">Home</a>
            <a href="search.php">Search</a>
            <a href="upload.php">Upload</a>
            <a href="template.php">Template Test</a>
            <a href="contacts.php">Contacts</a>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="index.php?action=logout">Logout (<?php echo $_SESSION['username']; ?>)</a>
            <?php else: ?>
                <a href="login.php">Login</a>
                <a href="register.php">Register</a>
            <?php endif; ?>
        </div>
        
        <div class="post">
            <p><strong>Email:</strong> <?php echo $user['email'] ?: 'Not provided'; ?></p>
            <p><strong>Bio:</strong> <?php echo $user['bio'] ?: 'No bio'; ?></p>
            <p><strong>Member since:</strong> <?php echo $user['created_at']; ?></p>
            
            <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $user['id']): ?>
                <a href="edit_profile.php?user_id=<?php echo $user['id']; ?>" class="btn">Edit Profile</a>
            <?php endif; ?>
        </div>
        
        <h3>Posts by <?php echo htmlspecialchars($user['username']); ?></h3>
        <?php foreach ($posts as $post): ?>
            <div class="post">
                <p><?php echo $post['content']; ?></p>
                <small><?php echo $post['created_at']; ?></small>
            </div>
        <?php endforeach; ?>
    </div>
</body>
</html>
