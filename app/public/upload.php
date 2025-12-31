<?php
require_once __DIR__ . '/../config.php';


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

$pageTitle = 'Upload - SocialHub';
$headerTitle = 'File Upload';
$headerSubtitle = 'Share your files with the community';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="row">
    <div class="col-md-8">
        <div class="card shadow-lg mb-4">
            <div class="card-body">
                <h5 class="card-title mb-4">
                    <i class="fas fa-cloud-upload-alt me-2"></i>Upload Files
                </h5>

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

                <div class="upload-area">
                    <i class="fas fa-file-upload file-icon"></i>
                    <h4>Drop files here or click to browse</h4>
                    <p class="text-muted">Maximum file size: 10MB</p>
                    
                    <form method="post" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="upload">
                        <div class="mb-3">
                            <input type="file" class="form-control" id="file" name="file" required>
                        </div>
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-upload me-2"></i>Upload File
                        </button>
                    </form>
                </div>
            </div>
        </div>

    </div>

    <div class="col-md-4">
        <div class="card shadow-lg">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-info-circle me-2"></i>Upload Guidelines</h5>
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <i class="fas fa-check text-success me-2"></i>
                        Maximum file size: 10MB
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check text-success me-2"></i>
                        Supported formats: All
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check text-success me-2"></i>
                        Files stored securely
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check text-success me-2"></i>
                        Download links generated
                    </li>
                </ul>
            </div>
        </div>

        <div class="card shadow-lg mt-4">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-chart-pie me-2"></i>Storage Stats</h5>
                <div class="text-center">
                    <h4 class="text-primary"><?php echo getTotalUploads(); ?></h4>
                    <small class="text-muted">Total Files</small>
                </div>
                <div class="progress mt-3">
                    <div class="progress-bar" role="progressbar" style="width: 25%">
                        25% Used
                    </div>
                </div>
                <small class="text-muted">2.5GB of 10GB used</small>
            </div>
        </div>
    </div>
</div>

<script>
function deleteFile(fileId) {
    if (confirm('Are you sure you want to delete this file?')) {
        // In a real app, this would make an AJAX call
        window.location.reload();
    }
}

// File upload preview
document.getElementById('file').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const fileInfo = document.querySelector('.upload-area h4');
        fileInfo.textContent = `Selected: ${file.name} (${formatFileSize(file.size)})`;
    }
});

function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
