# 📧 Email API Server - Complete Package

## 📦 Files Included

Paket ini berisi semua yang Anda butuhkan untuk setup email API server di `https://server.layanandigitalraja.my.id/api/send_mail.php`

### Core Files

| File | Purpose |
|------|---------|
| **email-api-endpoint.php** | Main API endpoint yang menerima email requests |
| **email-api-config.php** | Configuration template |
| **email-api.env.example** | Environment variables example |
| **email-api-setup.sh** | Automated setup script |
| **EMAIL_API_SERVER.md** | Detailed documentation |

---

## 🚀 Quick Start

### Option 1: Manual Setup (Recommended)

```bash
# 1. Copy endpoint ke server
scp email-api-endpoint.php user@server.layanandigitalraja.my.id:/var/www/api/send_mail.php

# 2. SSH ke server
ssh user@server.layanandigitalraja.my.id

# 3. Setup environment
cd /var/www
mkdir -p logs
chmod 755 logs
touch logs/email.log
chmod 666 logs/email.log

# 4. Edit .env
nano .env
# Tambahkan:
# SMTP_HOST=smtp.gmail.com
# SMTP_PORT=587
# SMTP_SECURE=tls
# SMTP_USER=your-email@gmail.com
# SMTP_PASSWORD=your-app-password
# EMAIL_API_KEY=rajaxrizx

# 5. Test
curl -X POST https://server.layanandigitalraja.my.id/api/send_mail.php \
  -H "Content-Type: application/json" \
  -d '{"api_key":"rajaxrizx","to":"test@example.com","subject":"Test","message":"<p>Hello</p>","from":"noreply@tokodigitalraja.com","from_name":"Test"}'
```

### Option 2: Automated Setup (Linux/Mac)

```bash
# 1. Make script executable
chmod +x email-api-setup.sh

# 2. Run setup script
sudo bash email-api-setup.sh /var/www/api www-data

# 3. Edit .env
nano /var/www/api/.env

# 4. Test
curl -X POST https://server.layanandigitalraja.my.id/api/send_mail.php ...
```

---

## 📋 Request Format

### Example cURL Request

```bash
curl -X POST https://server.layanandigitalraja.my.id/api/send_mail.php \
  -H "Content-Type: application/json" \
  -d '{
    "api_key": "rajaxrizx",
    "to": "recipient@example.com",
    "subject": "Welcome to Toko Digital Raja",
    "message": "<h1>Hello!</h1><p>Welcome to our store</p>",
    "from": "noreply@tokodigitalraja.com",
    "from_name": "Toko Digital Raja"
  }'
```

### Example PHP Request

```php
<?php
$url = 'https://server.layanandigitalraja.my.id/api/send_mail.php';

$data = [
    'api_key' => 'rajaxrizx',
    'to' => 'user@example.com',
    'subject' => 'Test Email',
    'message' => '<h1>Hello</h1><p>This is a test</p>',
    'from' => 'noreply@tokodigitalraja.com',
    'from_name' => 'Toko Digital Raja'
];

$context = stream_context_create([
    'http' => [
        'method' => 'POST',
        'header' => 'Content-Type: application/json',
        'content' => json_encode($data),
        'timeout' => 10
    ],
    'ssl' => [
        'verify_peer' => true
    ]
]);

$response = file_get_contents($url, false, $context);
$result = json_decode($response, true);

if ($result['success']) {
    echo "Email sent to: " . $result['email'];
} else {
    echo "Error: " . $result['message'];
}
?>
```

---

## 🔧 Configuration

### SMTP Providers

**Gmail:**
```env
SMTP_HOST=smtp.gmail.com
SMTP_PORT=587
SMTP_SECURE=tls
SMTP_USER=your-email@gmail.com
SMTP_PASSWORD=your-16-char-app-password
```

**SendGrid:**
```env
SMTP_HOST=smtp.sendgrid.net
SMTP_PORT=587
SMTP_SECURE=tls
SMTP_USER=apikey
SMTP_PASSWORD=SG.xxxxxxxxxxxxx
```

**Mailgun:**
```env
SMTP_HOST=smtp.mailgun.org
SMTP_PORT=587
SMTP_SECURE=tls
SMTP_USER=postmaster@mail.yourdomain.com
SMTP_PASSWORD=your-mailgun-password
```

**AWS SES:**
```env
SMTP_HOST=email-smtp.us-east-1.amazonaws.com
SMTP_PORT=587
SMTP_SECURE=tls
SMTP_USER=your-ses-user
SMTP_PASSWORD=your-ses-password
```

---

## 🔐 Security Checklist

- [ ] Change `EMAIL_API_KEY` dari default value
- [ ] Use strong SMTP passwords (16+ characters)
- [ ] Enable HTTPS only
- [ ] Configure firewall untuk limit access
- [ ] Setup rate limiting
- [ ] Monitor logs untuk suspicious activity
- [ ] Use environment variables, jangan hardcode
- [ ] Backup configuration regularly
- [ ] Keep logs untuk audit trail

---

## 📊 API Responses

### Success (200 OK)

```json
{
  "success": true,
  "message": "Email berhasil dikirim",
  "email": "recipient@example.com",
  "timestamp": "2026-01-19 12:00:00"
}
```

### Validation Error (400 Bad Request)

```json
{
  "success": false,
  "message": "Email tujuan (to) diperlukan"
}
```

### Authentication Error (401 Unauthorized)

```json
{
  "success": false,
  "message": "API Key tidak valid"
}
```

### Server Error (500 Internal Server Error)

```json
{
  "success": false,
  "message": "Gagal mengirim email: SMTP connection failed"
}
```

---

## 🧪 Testing

### Test with Postman

1. Set method ke POST
2. URL: `https://server.layanandigitalraja.my.id/api/send_mail.php`
3. Headers: `Content-Type: application/json`
4. Body (raw JSON):
```json
{
  "api_key": "rajaxrizx",
  "to": "your-email@example.com",
  "subject": "Test Email",
  "message": "<h1>Hello</h1><p>This is a test email</p>",
  "from": "noreply@tokodigitalraja.com",
  "from_name": "Toko Digital Raja"
}
```

### Test HTML Email

```bash
curl -X POST https://server.layanandigitalraja.my.id/api/send_mail.php \
  -H "Content-Type: application/json" \
  -d '{
    "api_key": "rajaxrizx",
    "to": "test@example.com",
    "subject": "HTML Email Test",
    "message": "<style>body{font-family:Arial}.header{background:#667eea;color:white;padding:20px}</style><div class=\"header\"><h1>Hello!</h1></div><p>This is an HTML email</p>",
    "from": "noreply@tokodigitalraja.com",
    "from_name": "Toko Digital Raja"
  }'
```

---

## 🐛 Troubleshooting

### Email tidak terkirim

**Check:**
1. Log file: `tail -f /var/log/email-api/email.log`
2. SMTP credentials di .env
3. Firewall allow port 587/465
4. Test SMTP: `telnet smtp.gmail.com 587`

### "API Key tidak valid"

**Solution:**
1. Verify API_KEY di .env
2. Ensure environment variables loaded
3. Test: `echo $EMAIL_API_KEY`

### CORS Error

**Solution:**
Add header ke nginx config:
```nginx
add_header 'Access-Control-Allow-Origin' '*' always;
add_header 'Access-Control-Allow-Methods' 'POST, OPTIONS' always;
```

---

## 📈 Performance Tips

1. **Use queue processing** untuk email yang tidak urgent
2. **Setup connection pooling** untuk high volume
3. **Enable caching** untuk DNS lookups
4. **Monitor rate limits** untuk avoid blacklisting
5. **Setup backup SMTP** untuk redundancy

---

## 🚀 Integration with Laravel

Laravel application Anda sudah configured untuk menggunakan endpoint ini.

**Config location**: `config/mail.php`

```php
'api_url' => env('EMAIL_API_URL'),
'api_key' => env('EMAIL_API_KEY'),
```

**Usage dalam application**:

```php
use App\Services\EmailService;

$emailService = new EmailService();
$emailService->sendVerificationEmail(
    'user@example.com',
    'John Doe',
    'https://app.com/verify/token123'
);
```

---

## 📞 Support

- 📖 Read [EMAIL_API_SERVER.md](EMAIL_API_SERVER.md) untuk detailed documentation
- 📋 Check logs: `/var/log/email-api/email.log`
- 🔗 Reference Laravel integration: `app/Services/EmailService.php`

---

## 📝 Version

- **Version**: 1.0
- **Status**: ✅ Production Ready
- **Last Updated**: January 19, 2026

---

**Ready to deploy! 🚀**
