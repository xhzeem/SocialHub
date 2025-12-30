<?php
// Internal service on port 8080 - accessible via SSRF
// This simulates an internal admin service

// Check if request is from internal network (SSRF protection bypass)
$clientIP = $_SERVER['REMOTE_ADDR'] ?? '';
$allowedSources = ['127.0.0.1', '::1', 'localhost'];

// Allow access if it's from localhost or internal container
if (!in_array($clientIP, $allowedSources) && !str_starts_with($clientIP, '172.') && !str_starts_with($clientIP, '192.168.') && !str_starts_with($clientIP, '10.')) {
    http_response_code(403);
    die('Access Denied: Internal service only');
}

header('Content-Type: application/json');

// Internal service data
$internalData = [
    'service' => 'Internal Admin API',
    'version' => '2.1.0',
    'status' => 'operational',
    'timestamp' => date('Y-m-d H:i:s'),
    'endpoints' => [
        '/admin/users' => 'User management',
        '/admin/config' => 'System configuration',
        '/admin/logs' => 'System logs',
        '/admin/backup' => 'Database backup'
    ],
    'secrets' => [
        'database_host' => 'internal-db.company.local',
        'database_user' => 'root',
        'database_pass' => 'Sup3rS3cur3P@ssw0rd!',
        'api_key' => 'sk-internal-1234567890abcdef',
        'jwt_secret' => 'internal_jwt_secret_key_2024_very_long_and_secure',
        'encryption_key' => 'AES256_SECRET_KEY_FOR_INTERNAL_ENCRYPTION_2024'
    ],
    'services' => [
        'auth_service' => 'http://127.0.0.1:9001',
        'payment_service' => 'http://127.0.0.1:9002',
        'notification_service' => 'http://127.0.0.1:9003'
    ],
    'admin_users' => [
        ['id' => 1, 'username' => 'admin', 'email' => 'admin@company.local', 'role' => 'super_admin'],
        ['id' => 2, 'username' => 'root', 'email' => 'root@company.local', 'role' => 'system_admin']
    ],
    'system_info' => [
        'server_ip' => $_SERVER['SERVER_ADDR'] ?? '127.0.0.1',
        'container_id' => substr(exec('hostname'), 0, 12),
        'docker_network' => 'app332_default'
    ]
];

echo json_encode($internalData, JSON_PRETTY_PRINT);
?>
