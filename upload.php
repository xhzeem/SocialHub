<?php
require_once 'config.php';
require_once 'navigation.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'upload') {
    if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        $result = uploadFile($_FILES['file']);
        if ($result) {
            $message = "File uploaded successfully!";
        } else {
            $message = "File upload failed. Please try again.";
        }
    } elseif (isset($_FILES['file']) && $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
        $errorMessages = [
            UPLOAD_ERR_INI_SIZE => 'File is too large.',
            UPLOAD_ERR_FORM_SIZE => 'File is too large.',
            UPLOAD_ERR_PARTIAL => 'File was only partially uploaded.',
            UPLOAD_ERR_NO_FILE => 'No file was uploaded.',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder.',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk.',
        ];
        $message = $errorMessages[$_FILES['file']['error']] ?? 'Unknown upload error.';
    } else {
        $message = "Please select a file to upload.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload - SocialHub</title>
    <?php renderCommonStyles(); ?>
    <style>
        body { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; }
        .main-container { background: rgba(255,255,255,0.95); border-radius: 20px; box-shadow: 0 20px 40px rgba(0,0,0,0.1); }
        .header-gradient { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; }
        .upload-area { 
            border: 3px dashed #667eea; 
            border-radius: 15px; 
            padding: 40px; 
            text-align: center; 
            background: #f8f9ff;
            transition: all 0.3s ease;
        }
        .upload-area:hover { 
            border-color: #764ba2; 
            background: #f0f2ff;
        }
        .file-icon { font-size: 4rem; color: #667eea; margin-bottom: 20px; }
        .nav-link { color: #667eea !important; font-weight: 500; }
        .btn-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; }
        .btn-primary:hover { background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%); }
        .file-type-badge { 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
            color: white; 
            padding: 4px 8px; 
            border-radius: 12px; 
            font-size: 0.8rem;
        }
    </style>
</head>
<body>
    <div class="container py-4">
        <div class="main-container p-4">
            <?php renderHeader('File Upload', 'Share your files with the community', 'fas fa-upload'); ?>
            
            <?php renderNavigation('upload.php'); ?>
            
            <?php if ($message): ?>
                <?php 
                $alertType = (strpos($message, 'successfully') !== false) ? 'alert-success' : 'alert-danger';
                $alertIcon = (strpos($message, 'successfully') !== false) ? 'fas fa-check-circle' : 'fas fa-exclamation-triangle';
                ?>
                <div class="alert <?php echo $alertType; ?> alert-dismissible fade show" role="alert">
                    <i class="<?php echo $alertIcon; ?> me-2"></i>
                    <?php echo $message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <div class="row">
                <div class="col-lg-8">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <div class="upload-area">
                                <i class="fas fa-file-upload file-icon"></i>
                                <h4>Drop your files here</h4>
                                <p class="text-muted mb-4">Support for documents, images, and web files</p>
                                
                                <form method="POST" enctype="multipart/form-data">
                                    <input type="hidden" name="action" value="upload">
                                    <div class="mb-3">
                                        <input type="file" name="file" class="form-control" required>
                                    </div>
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fas fa-upload me-2"></i>Upload File
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <div class="card shadow-sm mb-4">
                        <div class="card-body">
                            <h6 class="card-title"><i class="fas fa-info-circle me-2"></i>Supported File Types</h6>
                            <div class="d-flex flex-wrap gap-2">
                                <span class="file-type-badge">PDF</span>
                                <span class="file-type-badge">TXT</span>
                                <span class="file-type-badge">PNG</span>
                                <span class="file-type-badge">JPG</span>
                            </div>
                            <p class="text-muted mt-3 mb-0">Maximum file size: 10MB</p>
                        </div>
                    </div>
                    
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h6 class="card-title"><i class="fas fa-shield-alt me-2"></i>Upload Guidelines</h6>
                            <ul class="list-unstyled mb-0">
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Only upload content you have rights to share</li>
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Keep files appropriate for all audiences</li>
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Respect community guidelines</li>
                                <li class="mb-0"><i class="fas fa-check text-success me-2"></i>Report inappropriate content</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php renderCommonScripts(); ?>
</body>
</html>
