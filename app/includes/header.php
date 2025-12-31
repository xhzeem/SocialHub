<?php
require_once __DIR__ . '/../config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? htmlspecialchars($pageTitle) : 'SocialHub'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
    <!-- Navigation -->
    <?php if (!isset($hideNavigation) || !$hideNavigation): ?>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php">
                <i class="fas fa-users me-2"></i>SocialHub
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <?php renderNavigation(); ?>
            </div>
        </div>
    </nav>
    <?php endif; ?>

    <!-- Header Section -->
    <div class="header-gradient text-white py-4">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="display-4 fw-bold mb-3">
                        <?php echo isset($headerTitle) ? htmlspecialchars($headerTitle) : 'Welcome to SocialHub'; ?>
                    </h1>
                    <p class="lead mb-0">
                        <?php echo isset($headerSubtitle) ? htmlspecialchars($headerSubtitle) : 'Connect, Share, and Collaborate'; ?>
                    </p>
                </div>
                <div class="col-md-4 text-end">
                    <?php if (isset($_SESSION['username'])): ?>
                        <div class="d-inline-block text-start">
                            <div class="text-white-50 small">Logged in as</div>
                            <div class="fw-bold"><?php echo htmlspecialchars($_SESSION['username']); ?></div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container py-4">
