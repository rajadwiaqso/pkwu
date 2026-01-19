# 📧 Email API Service - Update Format Request

## ✅ Perbaikan yang Dilakukan

### 1. **Email API Service Baru** ✨
File baru: `app/Services/EmailApiService.php`

Service ini menggunakan **Guzzle HTTP Client** dengan format request yang benar:
- ✅ Format: `application/x-www-form-urlencoded`
- ✅ Parameter: Menggunakan `form_params` bukan JSON body
- ✅ Kompatibel dengan endpoint: `email-api-endpoint.php`

### 2. **Update Controllers**
Semua controllers sudah diupdate untuk menggunakan `EmailApiService`:
- `app/Http/Controllers/Auth/VerificationController.php`
- `app/Http/Controllers/Auth/AuthController.php`

### 3. **Update Email API Endpoint**
File: `email-api-endpoint.php`

**Perubahan:**
- ✅ Support kedua parameter: `body` dan `message` (backwards compatible)
- ✅ Lebih fleksibel menerima format request
- ✅ Fixed validation messages

---

## 🔧 Format Request Baru

### Menggunakan Guzzle (dari Laravel)

```php
use App\Services\EmailApiService;

$emailService = new EmailApiService();
$emailService->sendEmail(
    to: 'user@example.com',
    subject: 'Welcome',
    body: '<h1>Hello</h1>',
    from: 'noreply@tokodigitalraja.com',
    fromName: 'Toko Digital Raja'
);
```

### Menggunakan cURL

```bash
curl -X POST https://server.layanandigitalraja.my.id/api/send_mail.php \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -d "api_key=rajaxrizx&to=user@example.com&subject=Test&body=Hello&from=noreply@tokodigitalraja.com&from_name=Test"
```

### Menggunakan PHP (Native)

```php
$data = http_build_query([
    'api_key' => 'rajaxrizx',
    'to' => 'user@example.com',
    'subject' => 'Test Email',
    'body' => '<h1>Hello</h1><p>Welcome</p>',
    'from' => 'noreply@tokodigitalraja.com',
    'from_name' => 'Toko Digital Raja'
]);

$context = stream_context_create([
    'http' => [
        'method' => 'POST',
        'header' => 'Content-Type: application/x-www-form-urlencoded',
        'content' => $data,
        'timeout' => 10
    ]
]);

$response = file_get_contents('https://server.layanandigitalraja.my.id/api/send_mail.php', false, $context);
$result = json_decode($response, true);
```

---

## 📋 Perbedaan Request Format

### ❌ Format Lama (JSON) - Tidak Direkomendasikan
```json
{
  "api_key": "rajaxrizx",
  "to": "user@example.com",
  "subject": "Test",
  "message": "Hello",
  "from": "noreply@tokodigitalraja.com",
  "from_name": "Test"
}
```

### ✅ Format Baru (Form-Encoded) - Recommended
```
api_key=rajaxrizx&to=user@example.com&subject=Test&body=Hello&from=noreply@tokodigitalraja.com&from_name=Test
```

---

## 🚀 Testing

### Test dengan Postman

1. **Method**: POST
2. **URL**: `https://server.layanandigitalraja.my.id/api/send_mail.php`
3. **Headers**: Otomatis (Postman set ke `application/x-www-form-urlencoded`)
4. **Body Type**: Form-data (bukan JSON!)
5. **Form Parameters**:
   - `api_key`: rajaxrizx
   - `to`: your-email@example.com
   - `subject`: Test Email
   - `body`: <h1>Hello</h1>
   - `from`: noreply@tokodigitalraja.com
   - `from_name`: Test

### Test dengan cURL

```bash
# Test verification email
curl -X POST https://server.layanandigitalraja.my.id/api/send_mail.php \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -d "api_key=rajaxrizx&to=raja.aqso55@gmail.com&subject=Verifikasi Email&body=%3Ch1%3EHello%21%3C%2Fh1%3E&from=noreply@tokodigitalraja.com&from_name=Toko%20Digital%20Raja"
```

### Test dari Laravel

```php
// Di route atau controller
Route::get('/test-email', function () {
    $emailService = new \App\Services\EmailApiService();
    
    $result = $emailService->sendEmail(
        to: 'raja.aqso55@gmail.com',
        subject: 'Test dari Laravel',
        body: '<h1>Ini test email</h1><p>Format baru dengan Guzzle</p>',
        from: 'noreply@tokodigitalraja.com',
        fromName: 'Toko Digital Raja'
    );
    
    return $result ? 'Email terkirim!' : 'Email gagal dikirim!';
});
```

---

## ✨ Fitur EmailApiService

### Methods Tersedia

```php
$emailService = new EmailApiService();

// 1. Generic send email
$emailService->sendEmail(
    to: 'user@example.com',
    subject: 'Subject',
    body: '<h1>Content</h1>',
    from: 'sender@example.com',      // Optional
    fromName: 'Sender Name'           // Optional
);

// 2. Verification email (dengan template)
$emailService->sendVerificationEmail(
    to: 'user@example.com',
    userName: 'John Doe',
    verificationUrl: 'https://app.com/verify/token123'
);

// 3. Password reset email (dengan template)
$emailService->sendPasswordResetEmail(
    to: 'user@example.com',
    userName: 'John Doe',
    resetUrl: 'https://app.com/reset/token123'
);

// 4. Order confirmation email (dengan template)
$emailService->sendOrderConfirmationEmail(
    to: 'customer@example.com',
    customerName: 'Jane Doe',
    orderData: [
        'id' => '12345',
        'total_amount' => '150000',
        'items' => [
            [
                'product_name' => 'Laptop',
                'quantity' => 1,
                'price' => '150000'
            ]
        ]
    ]
);
```

---

## 🔐 Environment Variables

Pastikan `.env` di server sudah configured:

```env
EMAIL_API_KEY=rajaxrizx
EMAIL_API_URL=https://server.layanandigitalraja.my.id/api/send_mail.php
EMAIL_FROM_ADDRESS=noreply@tokodigitalraja.com
EMAIL_FROM_NAME=Toko Digital Raja

# SMTP Configuration di server
SMTP_HOST=smtp.gmail.com
SMTP_PORT=587
SMTP_SECURE=tls
SMTP_USER=your-email@gmail.com
SMTP_PASSWORD=your-app-password
API_KEY=rajaxrizx
```

---

## 📊 Response Format

### Success Response
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
  "message": "Email tujuan (to) diperlukan"
}
```

---

## 🐛 Troubleshooting

### Email tidak terkirim

**Guzzle Client Issue:**
1. Pastikan Guzzle HTTP client sudah installed: `composer require guzzlehttp/guzzle`
2. Check Laravel logs: `storage/logs/laravel.log`
3. Verify .env variables: `php artisan tinker` → `dd(env('EMAIL_API_KEY'))`

**Endpoint Issue:**
1. Check endpoint logs: `/var/log/email-api/email.log`
2. Test endpoint dengan curl
3. Verify SMTP credentials di server

### "API Key tidak valid"

**Solusi:**
1. Check .env di Laravel: `EMAIL_API_KEY=rajaxrizx`
2. Check .env di server: `API_KEY=rajaxrizx`
3. Must match exactly!
4. Restart PHP-FPM: `sudo systemctl restart php-fpm`

### "Content-Type header" error

**Solusi:**
- Guzzle otomatis set header ke `application/x-www-form-urlencoded`
- Jika manual, pastikan: `'Content-Type: application/x-www-form-urlencoded'`

---

## 📈 Production Deployment

### 1. Update Laravel

```bash
cd /home/vscode/pkwu
composer require guzzlehttp/guzzle
```

### 2. Deploy ke Server

```bash
# Build assets
npm run build

# Deploy code
git push origin main
```

### 3. Test Integration

```bash
# SSH ke server
ssh user@server.com

# Test email dari command line
php artisan tinker
>>> $service = new App\Services\EmailApiService()
>>> $service->sendEmail('test@example.com', 'Test', '<p>Test</p>', 'from@example.com', 'Test')
=> true
```

---

## ✅ Verification Checklist

- [x] EmailApiService dibuat dengan Guzzle
- [x] Controllers updated untuk menggunakan EmailApiService
- [x] Email endpoint support `body` parameter
- [x] Format request: `application/x-www-form-urlencoded`
- [x] HTML templates untuk verification, password reset, order confirmation
- [x] Error handling & logging implemented
- [x] Environment variables supported
- [x] Backwards compatible dengan endpoint

**Status**: ✨ SIAP DEPLOY!

---

## 📞 Quick Links

- [EmailApiService](app/Services/EmailApiService.php)
- [Email Endpoint](email-api-endpoint.php)
- [Environment Template](email-api.env.example)
- [Deployment Guide](EMAIL_API_SERVER.md)

