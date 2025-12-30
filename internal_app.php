<?php
// Different application on port 8080 - Internal Admin Dashboard
// This should be different from the main SocialHub app

// Check if request is from internal network (SSRF protection bypass)
$clientIP = $_SERVER['REMOTE_ADDR'] ?? '';
$allowedSources = ['127.0.0.1', '::1', 'localhost'];

// Allow access if it's from localhost or internal container
if (!in_array($clientIP, $allowedSources) && !str_starts_with($clientIP, '172.') && !str_starts_with($clientIP, '192.168.') && !str_starts_with($clientIP, '10.')) {
    http_response_code(403);
    die('Access Denied: Internal admin panel only');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Internal Admin Panel v2.0</title>
    <style>
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            margin: 0; 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
            min-height: 100vh; 
        }
        .admin-container { 
            max-width: 1200px; 
            margin: 0 auto; 
            padding: 20px; 
        }
        .admin-header { 
            background: rgba(255,255,255,0.1); 
            backdrop-filter: blur(10px); 
            padding: 30px; 
            border-radius: 15px; 
            margin-bottom: 30px; 
            text-align: center; 
            color: white; 
        }
        .admin-card { 
            background: rgba(255,255,255,0.95); 
            border-radius: 15px; 
            padding: 25px; 
            margin-bottom: 25px; 
            box-shadow: 0 8px 32px rgba(0,0,0,0.1); 
        }
        .metric-grid { 
            display: grid; 
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); 
            gap: 20px; 
            margin: 20px 0; 
        }
        .metric-item { 
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); 
            color: white; 
            padding: 20px; 
            border-radius: 10px; 
            text-align: center; 
        }
        .metric-value { 
            font-size: 2rem; 
            font-weight: bold; 
            margin-bottom: 5px; 
        }
        .metric-label { 
            font-size: 0.9rem; 
            opacity: 0.9; 
        }
        .secret-data { 
            background: #ff6b6b; 
            color: white; 
            padding: 15px; 
            border-radius: 8px; 
            font-family: monospace; 
            margin: 10px 0; 
        }
        .nav-tabs { 
            display: flex; 
            border-bottom: 2px solid #eee; 
            margin-bottom: 20px; 
        }
        .nav-tab { 
            padding: 12px 20px; 
            background: none; 
            border: none; 
            cursor: pointer; 
            border-bottom: 3px solid transparent; 
            transition: all 0.3s; 
        }
        .nav-tab.active { 
            border-bottom-color: #667eea; 
            color: #667eea; 
            font-weight: bold; 
        }
        .tab-content { 
            display: none; 
        }
        .tab-content.active { 
            display: block; 
        }
        .service-grid { 
            display: grid; 
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); 
            gap: 20px; 
        }
        .service-card { 
            background: #f8f9fa; 
            border-left: 4px solid #667eea; 
            padding: 20px; 
            border-radius: 8px; 
        }
        .status-online { 
            color: #28a745; 
            font-weight: bold; 
        }
        .status-offline { 
            color: #dc3545; 
            font-weight: bold; 
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <div class="admin-header">
            <h1>🔐 Internal Admin Panel v2.0</h1>
            <p>System Management Dashboard - Restricted Access</p>
            <small>Server: <?php echo gethostname(); ?> | Time: <?php echo date('Y-m-d H:i:s'); ?></small>
        </div>
        
        <div class="admin-card">
            <h2>📊 System Overview</h2>
            <div class="metric-grid">
                <div class="metric-item">
                    <div class="metric-value">1,847</div>
                    <div class="metric-label">Active Users</div>
                </div>
                <div class="metric-item">
                    <div class="metric-value">99.97%</div>
                    <div class="metric-label">Uptime</div>
                </div>
                <div class="metric-item">
                    <div class="metric-value">42ms</div>
                    <div class="metric-label">Avg Response</div>
                </div>
                <div class="metric-item">
                    <div class="metric-value">8.2TB</div>
                    <div class="metric-label">Data Processed</div>
                </div>
            </div>
        </div>

        <div class="admin-card">
            <div class="nav-tabs">
                <button class="nav-tab active" onclick="showTab('secrets')">🔑 Secrets</button>
                <button class="nav-tab" onclick="showTab('services')">🌐 Services</button>
                <button class="nav-tab" onclick="showTab('database')">💾 Database</button>
                <button class="nav-tab" onclick="showTab('logs')">📋 Logs</button>
            </div>
            
            <div id="secrets" class="tab-content active">
                <h3>🔑 System Secrets & Keys</h3>
                <div class="secret-data">
                    <strong>Database Connection:</strong><br>
                    Host: internal-db.company.local:3306<br>
                    User: root<br>
                    Password: Sup3rS3cur3P@ssw0rd_2024!<br>
                    Database: production_main
                </div>
                <div class="secret-data">
                    <strong>API Keys:</strong><br>
                    Stripe: sk_live_51H2K34567890ABCDEF<br>
                    AWS: AKIAIOSFODNN7EXAMPLE<br>
                    Google: AIzaSyDdFlgW9yqJ21Z5Q5A5X5Z5Z5Z5Z5Z5Z
                </div>
                <div class="secret-data">
                    <strong>JWT Secrets:</strong><br>
                    Access Token: jwt_access_secret_key_2024_very_long_and_secure_string_for_production<br>
                    Refresh Token: jwt_refresh_secret_key_2024_different_secret_for_refresh_tokens
                </div>
                <div class="secret-data">
                    <strong>Encryption Keys:</strong><br>
                    AES-256: AES256_SECRET_KEY_FOR_INTERNAL_ENCRYPTION_2024_32BYTES<br>
                    RSA Private: -----BEGIN RSA PRIVATE KEY-----<br>
                    MIIEpAIBAAKCAQEA1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZ<br>
                    ... (truncated for security) ...<br>
                    -----END RSA PRIVATE KEY-----
                </div>
            </div>
            
            <div id="services" class="tab-content">
                <h3>🌐 Internal Services Status</h3>
                <div class="service-grid">
                    <div class="service-card">
                        <h4>Authentication Service</h4>
                        <p class="status-online">● Online</p>
                        <small>URL: http://127.0.0.1:9001</small><br>
                        <small>Version: 1.2.3</small>
                    </div>
                    <div class="service-card">
                        <h4>Payment Gateway</h4>
                        <p class="status-online">● Online</p>
                        <small>URL: http://127.0.0.1:9002</small><br>
                        <small>Version: 2.1.0</small>
                    </div>
                    <div class="service-card">
                        <h4>Notification Service</h4>
                        <p class="status-offline">● Offline</p>
                        <small>URL: http://127.0.0.1:9003</small><br>
                        <small>Last seen: 2 hours ago</small>
                    </div>
                    <div class="service-card">
                        <h4>Analytics Engine</h4>
                        <p class="status-online">● Online</p>
                        <small>URL: http://127.0.0.1:9004</small><br>
                        <small>Version: 3.0.1</small>
                    </div>
                </div>
            </div>
            
            <div id="database" class="tab-content">
                <h3>💾 Database Information</h3>
                <div class="secret-data">
                    <strong>Production Database:</strong><br>
                    Host: internal-db.company.local<br>
                    Port: 3306<br>
                    Version: MySQL 8.0.35<br>
                    Size: 245.7 GB<br>
                    Connections: 47/100
                </div>
                <div class="secret-data">
                    <strong>Backup Configuration:</strong><br>
                    Daily: 02:00 AM UTC<br>
                    Retention: 30 days<br>
                    Storage: s3://company-backups/database/<br>
                    Encryption: AES-256
                </div>
            </div>
            
            <div id="logs" class="tab-content">
                <h3>📋 Recent System Logs</h3>
                <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; font-family: monospace; font-size: 0.9rem;">
                    [2025-12-30 13:58:45] INFO: User admin logged in from 172.27.0.3<br>
                    [2025-12-30 13:57:23] WARN: Failed login attempt for user root<br>
                    [2025-12-30 13:56:12] INFO: Database backup completed successfully<br>
                    [2025-12-30 13:55:48] ERROR: Payment service timeout (5s)<br>
                    [2025-12-30 13:54:30] INFO: SSL certificate renewed (expires 2026-03-30)<br>
                    [2025-12-30 13:53:15] WARN: Unusual traffic detected from 192.168.1.100<br>
                    [2025-12-30 13:52:00] INFO: System monitoring started<br>
                </div>
            </div>
        </div>
        
        <div class="admin-card">
            <h3>🚨 Security Notice</h3>
            <p style="background: #fff3cd; padding: 15px; border-radius: 8px; border-left: 4px solid #ffc107;">
                <strong>⚠️ WARNING:</strong> This admin panel contains sensitive system information and credentials. 
                Access is restricted to internal network addresses only. All activities are monitored and logged. 
                Unauthorized access will be immediately reported to security team.
            </p>
        </div>
    </div>
    
    <script>
        function showTab(tabName) {
            // Hide all tabs
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.remove('active');
            });
            document.querySelectorAll('.nav-tab').forEach(tab => {
                tab.classList.remove('active');
            });
            
            // Show selected tab
            document.getElementById(tabName).classList.add('active');
            event.target.classList.add('active');
        }
    </script>
</body>
</html>
