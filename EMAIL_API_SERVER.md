# 📧 Email API Server Setup Guide

## Overview
Email API endpoint yang akan menerima request dari Laravel application dan mengirimkan email melalui SMTP.

**Endpoint**: `https://server.layanandigitalraja.my.id/api/send_mail.php`

---

## 📋 File yang Disediakan

1. **email-api-endpoint.php** - Main API endpoint
2. **email-api-config.php** - Configuration file
3. **email-api-setup.sh** - Setup script (optional)
4. **EMAIL_API_SERVER.md** - Dokumentasi ini

---

## 🔧 Setup Instructions

### 1. Prerequisites

Pastikan server memiliki:
- PHP 7.4+ atau lebih baru
- curl extension enabled
- mail() function enabled atau SMTP access
- (Optional) PHPMailer library untuk SMTP support

### 2. Copy Files ke Server

```bash
# SSH ke server
ssh user@server.layanandigitalraja.my.id

# Navigate ke API directory
cd /var/www/server.layanandigitalraja.my.id/api/

# Copy email endpoint
cp email-api-endpoint.php ./send_mail.php

# Copy config
mkdir -p config
cp email-api-config.php config/
```

### 3. Setup Environment Variables

Edit `.env` atau create `.env.local` di server:

```bash
# SMTP Configuration
SMTP_HOST=smtp.gmail.com
SMTP_PORT=587
SMTP_SECURE=tls
SMTP_USER=your-email@gmail.com
SMTP_PASSWORD=your-app-password

# Email API
EMAIL_API_KEY=rajaxrizx
MAIL_FROM_ADDRESS=noreply@tokodigitalraja.com
MAIL_FROM_NAME="Toko Digital Raja"
```

**Untuk Gmail**:
- Enable 2-Factor Authentication
- Generate "App Password" di: https://myaccount.google.com/apppasswords
- Gunakan App Password sebagai `SMTP_PASSWORD`

### 4. Setup Logging Directory

```bash
# Create logs directory
mkdir -p /var/log/email-api
chmod 755 /var/log/email-api

# Create log file
touch /var/log/email-api/email.log
chmod 666 /var/log/email-api/email.log
```

### 5. (Optional) Install PHPMailer

Untuk SMTP support yang lebih robust:

```bash
cd /var/www/server.layanandigitalraja.my.id

# Install via Composer
composer require phpmailer/phpmailer

# Or manual download
wget https://github.com/PHPMailer/PHPMailer/archive/master.zip
unzip master.zip -d vendor/
```

### 6. Setup Permissions

```bash
chmod 644 api/send_mail.php
chmod 755 api/
chmod 755 /var/log/email-api/
```

### 7. Test Connection

```bash
curl -X POST https://server.layanandigitalraja.my.id/api/send_mail.php \
  -H "Content-Type: application/json" \
  -d '{
    "api_key": "rajaxrizx",
    "to": "test@example.com",
    "subject": "Test Email",
    "message": "<h1>Hello</h1><p>This is a test email</p>",
    "from": "noreply@tokodigitalraja.com",
    "from_name": "Toko Digital Raja"
  }'
```

---

## 📨 API Request Format

### Endpoint
```
POST https://server.layanandigitalraja.my.id/api/send_mail.php
```

### Headers
```
Content-Type: application/json
```

### Request Body (JSON)

```json
{
  "api_key": "rajaxrizx",
  "to": "user@example.com",
  "subject": "Email Subject",
  "message": "<html>...</html>",
  "from": "noreply@tokodigitalraja.com",
  "from_name": "Toko Digital Raja"
}
```

### Required Fields

| Field | Type | Description |
|-------|------|-------------|
| api_key | string | API authentication key |
| to | string | Recipient email address |
| subject | string | Email subject |
| message | string | Email body (HTML supported) |
| from | string | Sender email address |
| from_name | string | Sender display name |

---

## 📤 Response Format

### Success Response (200 OK)

```json
{
  "success": true,
  "message": "Email berhasil dikirim",
  "email": "user@example.com",
  "timestamp": "2026-01-19 12:00:00"
}
```

### Error Response

```json
{
  "success": false,
  "message": "Error description here"
}
```

### Error Codes

| Code | Meaning |
|------|---------|
| 200 | Success |
| 400 | Bad Request (validation failed) |
| 401 | Unauthorized (invalid API key) |
| 405 | Method Not Allowed |
| 500 | Server Error |

---

## 🔐 Security Considerations

- ✅ API Key validation required
- ✅ HTTPS only (enforce in nginx/apache)
- ✅ CORS headers configured
- ✅ Input validation dan sanitization
- ✅ Logging untuk audit trail
- ✅ Environment variables untuk credentials
- ✅ Rate limiting (configurable)

### Additional Security Steps

**1. Nginx Configuration**

```nginx
server {
    listen 443 ssl http2;
    server_name server.layanandigitalraja.my.id;

    ssl_certificate /etc/ssl/certs/your-cert.crt;
    ssl_certificate_key /etc/ssl/private/your-key.key;

    location /api/send_mail.php {
        # Only allow POST
        limit_except POST {
            deny all;
        }

        # Rate limiting
        limit_req zone=api burst=10 nodelay;

        # PHP handler
        fastcgi_pass unix:/var/run/php/php7.4-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }

    # Redirect HTTP to HTTPS
    error_page 497 https://$server_name$request_uri;
}

# Rate limiting zone
limit_req_zone $binary_remote_addr zone=api:10m rate=10r/m;
```

**2. Apache Configuration (.htaccess)**

```apache
RewriteEngine On

# Require HTTPS
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

# Only allow POST method
<Limit GET HEAD PUT DELETE>
    Deny from all
</Limit>

# Disable directory listing
Options -Indexes

# Add security headers
Header set X-Content-Type-Options "nosniff"
Header set X-Frame-Options "DENY"
Header set X-XSS-Protection "1; mode=block"
```

---

## 📊 Monitoring & Logging

### View Logs

```bash
# Real-time log monitoring
tail -f /var/log/email-api/email.log

# Search for errors
grep ERROR /var/log/email-api/email.log

# Count sent emails
grep "Email terkirim berhasil" /var/log/email-api/email.log | wc -l
```

### Log Format

```
[2026-01-19 12:00:00] [INFO] Email terkirim berhasil {"to":"user@example.com","subject":"Test","from":"noreply@tokodigitalraja.com"}
[2026-01-19 12:01:00] [ERROR] Gagal mengirim email: SMTP connection failed {"to":"user@example.com","error":"Connection refused"}
```

---

## 🧪 Testing

### Test with cURL

```bash
# Test successful send
curl -X POST https://server.layanandigitalraja.my.id/api/send_mail.php \
  -H "Content-Type: application/json" \
  -d '{
    "api_key": "rajaxrizx",
    "to": "recipient@example.com",
    "subject": "Test Subject",
    "message": "<p>Test message</p>",
    "from": "noreply@tokodigitalraja.com",
    "from_name": "Test"
  }'

# Test with invalid API key
curl -X POST https://server.layanandigitalraja.my.id/api/send_mail.php \
  -H "Content-Type: application/json" \
  -d '{
    "api_key": "invalid",
    "to": "recipient@example.com",
    "subject": "Test",
    "message": "Test",
    "from": "noreply@tokodigitalraja.com",
    "from_name": "Test"
  }'
```

### Test with PHP

```php
<?php
$url = 'https://server.layanandigitalraja.my.id/api/send_mail.php';

$data = [
    'api_key' => 'rajaxrizx',
    'to' => 'test@example.com',
    'subject' => 'Test Email',
    'message' => '<h1>Hello</h1><p>This is a test</p>',
    'from' => 'noreply@tokodigitalraja.com',
    'from_name' => 'Toko Digital Raja'
];

$response = file_get_contents($url, false, stream_context_create([
    'http' => [
        'method' => 'POST',
        'header' => 'Content-Type: application/json',
        'content' => json_encode($data)
    ]
]));

echo $response;
?>
```

---

## 🐛 Troubleshooting

### Issue: "Failed to send email"

**Solution**:
1. Check SMTP credentials in environment variables
2. Verify SMTP server is accessible
3. Check firewall rules for port 587 or 465
4. Enable "Less secure app access" if using Gmail

### Issue: "Connection refused"

**Solution**:
1. Check SMTP host and port
2. Verify network connectivity: `telnet smtp.gmail.com 587`
3. Check firewall rules

### Issue: "500 Internal Server Error"

**Solution**:
1. Check PHP error logs: `tail -f /var/log/php-errors.log`
2. Verify PHP permissions
3. Check logs directory permissions

### Issue: "Invalid API Key"

**Solution**:
1. Verify API_KEY in .env matches request
2. Check environment variables are loaded: `php -r "echo getenv('EMAIL_API_KEY');"`

---

## 📈 Performance Optimization

### 1. Use Queue/Job Processing

Para menghindari blocking requests, implementasikan queue:

```php
// Send email async dengan queue
Job::dispatch([
    'to' => $to,
    'subject' => $subject,
    'message' => $message,
]);
```

### 2. Enable Caching

```php
// Cache DNS lookups
apc_store('smtp_host', 'smtp.gmail.com', 3600);
```

### 3. Connection Pooling

Untuk high-traffic, gunakan connection pooling di SMTP.

---

## 🚀 Deployment Checklist

- [ ] Copy files ke server
- [ ] Setup environment variables (.env)
- [ ] Create logging directory dengan permissions
- [ ] Test SMTP connection
- [ ] Test API endpoint dengan cURL
- [ ] Configure firewall (allow 587/465)
- [ ] Setup SSL certificate
- [ ] Configure nginx/apache
- [ ] Monitor logs untuk errors
- [ ] Setup email alerts untuk critical errors
- [ ] Document API credentials (secure)
- [ ] Setup backup email provider

---

## 📞 Integration with Laravel

Laravel application sudah dikonfigurasi untuk menggunakan endpoint ini:

```php
// config/mail.php - Email API Configuration
'api_url' => env('EMAIL_API_URL', 'https://server.layanandigitalraja.my.id/api/send_mail.php'),
'api_key' => env('EMAIL_API_KEY', 'rajaxrizx'),
```

---

## 📝 Version History

- **v1.0** - Initial release
  - Basic email sending
  - SMTP support
  - Error logging
  - Rate limiting ready

---

**Last Updated**: January 19, 2026
**Status**: ✅ Production Ready
