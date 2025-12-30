<?php
// This page should only be accessible via SSRF - no direct access allowed
// Check if request is coming from localhost only (SSRF from web container)
$clientIP = $_SERVER['REMOTE_ADDR'] ?? '';

// ONLY allow access from localhost/127.0.0.1 - this means only SSRF can access it
if ($clientIP !== '127.0.0.1' && $clientIP !== '::1') {
    http_response_code(403);
    die('Access Denied: This resource is only accessible internally.');
}

// Simulate internal admin panel with sensitive information
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Internal Admin Panel - RESTRICTED</title>
    <style>
        body { 
            font-family: 'Courier New', monospace; 
            background: #1a1a1a; 
            color: #00ff00; 
            margin: 0; 
            padding: 20px;
        }
        .container { max-width: 1200px; margin: 0 auto; }
        .header { 
            background: #ff0000; 
            color: white; 
            padding: 15px; 
            text-align: center; 
            border: 2px solid #ff0000;
            margin-bottom: 20px;
        }
        .section { 
            background: #2a2a2a; 
            border: 1px solid #00ff00; 
            padding: 20px; 
            margin: 20px 0; 
            border-radius: 5px;
        }
        .warning { 
            background: #8B0000; 
            color: white; 
            padding: 15px; 
            border: 2px solid #ff0000;
            margin: 20px 0;
            text-align: center;
            animation: blink 1s infinite;
        }
        @keyframes blink { 0%, 50% { opacity: 1; } 51%, 100% { opacity: 0.5; } }
        .data-table { 
            width: 100%; 
            border-collapse: collapse; 
            margin: 10px 0; 
        }
        .data-table th, .data-table td { 
            border: 1px solid #00ff00; 
            padding: 8px; 
            text-align: left; 
        }
        .data-table th { 
            background: #003300; 
            color: #00ff00; 
        }
        .secret { 
            background: #330000; 
            padding: 10px; 
            border: 1px dashed #ff0000; 
            margin: 10px 0;
        }
        .timestamp { color: #ffff00; }
        .ip-address { color: #00ffff; }
        .critical { color: #ff0000; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🚨 INTERNAL ADMIN PANEL - RESTRICTED ACCESS 🚨</h1>
            <p>INTERNAL SYSTEM - AUTHORIZED PERSONNEL ONLY</p>
        </div>
        
        <div class="warning">
            <strong>⚠️ WARNING: This is a restricted internal system ⚠️</strong><br>
            All access attempts are logged and monitored<br>
            Current time: <span class="timestamp"><?php echo date('Y-m-d H:i:s'); ?></span><br>
            Access from: <span class="ip-address"><?php echo $clientIP; ?></span>
        </div>
        
        <div class="section">
            <h2>🔐 Database Credentials</h2>
            <div class="secret">
                <strong>Production Database:</strong><br>
                Host: <?php echo getenv('DB_HOST') ?: 'localhost'; ?><br>
                Username: <?php echo getenv('DB_USER') ?: 'root'; ?><br>
                Password: <?php echo getenv('DB_PASSWORD') ?: 'password'; ?><br>
                Database: <?php echo getenv('DB_NAME') ?: 'vulnerable_social'; ?><br>
                Port: 3306
            </div>
            
            <div class="secret">
                <strong>API Keys:</strong><br>
                Google Maps API: AIzaSyDxZ3Y5K9X7Q2W8E6R4T1Y3U5I8O0P2L5<br>
                Stripe Secret: sk_live_51H2K3j4L5M6N7O8P9Q0R1S2T3U4V5W6X7Y8Z9A0B1C2D3E4F5G6H7I8J9K0L1M2<br>
                AWS Access: AKIAIOSFODNN7EXAMPLE<br>
                AWS Secret: wJalrXUtnFEMI/K7MDENG/bPxRfiCYEXAMPLEKEY
            </div>
        </div>
        
        <div class="section">
            <h2>👥 Admin User Accounts</h2>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Password</th>
                        <th>Role</th>
                        <th>Last Login</th>
                        <th>IP Address</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>admin</td>
                        <td>admin123</td>
                        <td class="critical">Super Admin</td>
                        <td>2025-12-30 14:32:15</td>
                        <td>192.168.1.100</td>
                    </tr>
                    <tr>
                        <td>root</td>
                        <td>P@ssw0rd!2024</td>
                        <td class="critical">System Admin</td>
                        <td>2025-12-30 13:45:22</td>
                        <td>10.0.0.50</td>
                    </tr>
                    <tr>
                        <td>superuser</td>
                        <td>letmein</td>
                        <td>Admin</td>
                        <td>2025-12-30 12:15:33</td>
                        <td>172.16.0.25</td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <div class="section">
            <h2>🔧 System Configuration</h2>
            <div class="secret">
                <strong>Server Configuration:</strong><br>
                OS: <?php echo php_uname(); ?><br>
                PHP Version: <?php echo PHP_VERSION; ?><br>
                Web Server: <?php echo $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown'; ?><br>
                Document Root: <?php echo $_SERVER['DOCUMENT_ROOT'] ?? 'Unknown'; ?><br>
                Server IP: <?php echo $_SERVER['SERVER_ADDR'] ?? 'Unknown'; ?>
            </div>
            
            <div class="secret">
                <strong>Internal Services:</strong><br>
                • Redis Cache: redis://127.0.0.1:6379<br>
                • Elasticsearch: http://127.0.0.1:9200<br>
                • RabbitMQ: amqp://guest:guest@127.0.0.1:5672<br>
                • MongoDB: mongodb://127.0.0.1:27017<br>
                • PostgreSQL: postgresql://postgres:password@127.0.0.1:5432/internal_db
            </div>
        </div>
        
        <div class="section">
            <h2>📊 Recent System Logs</h2>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Timestamp</th>
                        <th>Event</th>
                        <th>User</th>
                        <th>IP Address</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="timestamp">2025-12-30 14:32:15</td>
                        <td>Admin Login</td>
                        <td>admin</td>
                        <td class="ip-address">192.168.1.100</td>
                        <td>Success</td>
                    </tr>
                    <tr>
                        <td class="timestamp">2025-12-30 14:28:42</td>
                        <td>Database Backup</td>
                        <td>root</td>
                        <td class="ip-address">10.0.0.50</td>
                        <td>Success</td>
                    </tr>
                    <tr>
                        <td class="timestamp">2025-12-30 14:15:33</td>
                        <td>Config Change</td>
                        <td>superuser</td>
                        <td class="ip-address">172.16.0.25</td>
                        <td>Success</td>
                    </tr>
                    <tr>
                        <td class="timestamp">2025-12-30 14:10:11</td>
                        <td>Failed Login Attempt</td>
                        <td>unknown</td>
                        <td class="ip-address">203.0.113.1</td>
                        <td class="critical">Blocked</td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <div class="section">
            <h2>🔑 Private Keys & Certificates</h2>
            <div class="secret">
                <strong>SSL Certificate Private Key:</strong><br>
                -----BEGIN RSA PRIVATE KEY-----<br>
                MIIEpAIBAAKCAQEA1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz<br>
                1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890<br>
                ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZ<br>
                ... (truncated for security) ...<br>
                -----END RSA PRIVATE KEY-----
            </div>
            
            <div class="secret">
                <strong>JWT Secret Key:</strong><br>
                super_secret_jwt_key_2024_very_long_and_secure_string_for_token_signing_1234567890
            </div>
        </div>
        
        <div class="section">
            <h2>🌐 Network Information</h2>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Interface</th>
                        <th>IP Address</th>
                        <th>MAC Address</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>eth0</td>
                        <td class="ip-address">192.168.1.10</td>
                        <td>00:11:22:33:44:55</td>
                        <td>Active</td>
                    </tr>
                    <tr>
                        <td>lo</td>
                        <td class="ip-address">127.0.0.1</td>
                        <td>00:00:00:00:00:00</td>
                        <td>Active</td>
                    </tr>
                    <tr>
                        <td>docker0</td>
                        <td class="ip-address">172.17.0.1</td>
                        <td>02:42:ac:11:00:02</td>
                        <td>Active</td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <div class="warning">
            <strong>🚨 CRITICAL SECURITY NOTICE 🚨</strong><br>
            This internal panel contains highly sensitive information.<br>
            Access is strictly limited to internal network addresses only.<br>
            All activities are monitored and logged.<br>
            Unauthorized access will be prosecuted to the fullest extent of the law.<br>
            <br>
            <strong>System Status: OPERATIONAL</strong><br>
            <strong>Last Security Scan: <?php echo date('Y-m-d H:i:s', strtotime('-1 day')); ?></strong>
        </div>
    </div>
</body>
</html>
