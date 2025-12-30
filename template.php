<?php
require_once 'config.php';
require_once 'navigation.php';

$message = '';
$template = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'render') {
    $template = $_POST['template'] ?? '';
    if (!empty($template)) {
        try {
            $loader = new \Twig\Loader\ArrayLoader(['template' => $template]);
            $twig = new \Twig\Environment($loader);
            
            $context = [
                'user' => [
                    'name' => $_SESSION['username'] ?? 'Guest',
                    'id' => $_SESSION['user_id'] ?? 0
                ],
                'posts' => getPosts(),
                'time' => date('Y-m-d H:i:s')
            ];
            
            $message = $twig->render('template', $context);
        } catch (Exception $e) {
            $message = 'Error: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Templates - SocialHub</title>
    <?php renderCommonStyles(); ?>
</head>
<body>
    <div class="container py-4">
        <div class="main-container p-4">
            <?php renderHeader('Template Engine', 'Create and render dynamic templates', 'fas fa-code'); ?>
            
            <?php renderNavigation('template.php'); ?>
            
            <div class="row">
                <div class="col-lg-8">
                    <div class="card shadow-sm mb-4">
                        <div class="card-body">
                            <h5 class="card-title"><i class="fas fa-edit me-2"></i>Template Editor</h5>
                            <form method="POST">
                                <input type="hidden" name="action" value="render">
                            <form method="GET">
                                <div class="mb-3">
                                    <label for="template" class="form-label">Template Code</label>
                                    <textarea name="template" id="template" class="form-control code-editor" rows="6" placeholder="Enter your Twig template here...">{{ user|default('Guest') }}</textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="user" class="form-label">Variable (user)</label>
                                    <input type="text" name="user" id="user" class="form-control" value="<?php echo htmlspecialchars($_GET['user'] ?? 'Guest'); ?>" placeholder="Enter variable value">
                                </div>
                                <button type="submit" class="btn btn-primary"><i class="fas fa-play me-2"></i>Render Template</button>
                            </form>
                        </div>
                    </div>
                    
                    <?php if ($_GET['template'] ?? null): ?>
                        <div class="card shadow-sm mb-4">
                            <div class="card-body">
                                <h5 class="card-title"><i class="fas fa-eye me-2"></i>Rendered Output</h5>
                                <div class="result-display">
                                    <?php echo htmlspecialchars($result); ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="col-lg-4">
                    <div class="card shadow-sm mb-4">
                        <div class="card-body">
                            <h6 class="card-title"><i class="fas fa-book me-2"></i>Twig Documentation</h6>
                            <div class="example-card">
                                <strong>Basic Variables:</strong>
                                <code>{{ variable }}</code>
                            </div>
                            <div class="example-card">
                                <strong>Filters:</strong>
                                <code>{{ variable|upper }}</code>
                            </div>
                            <div class="example-card">
                                <strong>Conditionals:</strong>
                                <code>{% if condition %} ... {% endif %}</code>
                            </div>
                            <div class="example-card">
                                <strong>Loops:</strong>
                                <code>{% for item in items %} ... {% endfor %}</code>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h6 class="card-title"><i class="fas fa-lightbulb me-2"></i>Template Examples</h6>
                            <div class="list-group list-group-flush">
                                <a href="?template=Hello {{ name }}!&user=World" class="list-group-item list-group-item-action">
                                    <small class="text-muted">Basic greeting</small><br>
                                    <code>Hello {{ name }}!</code>
                                </a>
                                <a href="?template={{ name|upper }}&user=socialhub" class="list-group-item list-group-item-action">
                                    <small class="text-muted">Upper case filter</small><br>
                                    <code>{{ name|upper }}</code>
                                </a>
                                <a href="?template={{ name|length }}&user=SocialHub" class="list-group-item list-group-item-action">
                                    <small class="text-muted">Length filter</small><br>
                                    <code>{{ name|length }}</code>
                                </a>
                                <a href="?template={{ name|reverse }}&user=hello" class="list-group-item list-group-item-action">
                                    <small class="text-muted">Reverse filter</small><br>
                                    <code>{{ name|reverse }}</code>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php renderCommonScripts(); ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/prism.min.js"></script>
</body>
</html>
