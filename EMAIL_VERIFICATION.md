# 📧 Email Verification Feature

## Overview
Fitur email verification yang lengkap dengan pengiriman email melalui external API.

## Teknologi yang Digunakan

### Backend
- **EmailService** (`app/Services/EmailService.php`) - Service untuk mengirim email via external API
- **VerificationController** (`app/Http/Controllers/Auth/VerificationController.php`) - Controller untuk handle verification flow
- **EmailVerificationToken** Model - Menyimpan token verifikasi

### Frontend
- **VerifyEmail.vue** (`resources/js/Pages/Auth/VerifyEmail.vue`) - Halaman verifikasi email dengan resend functionality
- **Register.vue** - Updated untuk redirect ke halaman verifikasi setelah registrasi

## Configuration

### Environment Variables (.env)
```env
EMAIL_API_URL=https://server.layanandigitalraja.my.id/api/send_mail.php
EMAIL_API_KEY=rajaxrizx
EMAIL_FROM_ADDRESS=noreply@tokodigitalraja.com
EMAIL_FROM_NAME="Toko Digital Raja"
```

### Mail Config
Edit `config/mail.php` untuk menggunakan konfigurasi ini.

## Alur Verifikasi Email

### 1. Registration Flow
```
User Register → Create User → Generate Token → Send Email → Redirect to Verify Page
```

### 2. Email Verification Flow
```
User Klik Link Email → Verify Token → Mark as Verified → Auto Login → Redirect Home
```

### 3. Resend Verification
```
User Request Resend → Check Rate Limit → Generate New Token → Send Email → Show Success
```

## Routes

### Public Routes
- `GET /verify-email/{token}` - Verify email dengan token
- `GET /email-verification` - Halaman verifikasi email

### Authenticated Routes
- `POST /resend-verification` - Resend email verifikasi
- `GET /verify-email-page` - Show verification page

## File Structure

```
app/
├── Services/
│   └── EmailService.php              # Service untuk email
├── Http/Controllers/Auth/
│   ├── AuthController.php            # Updated untuk register flow
│   └── VerificationController.php    # Verification logic
└── Models/
    └── EmailVerificationToken.php    # Token model (sudah ada)

resources/js/Pages/Auth/
└── VerifyEmail.vue                   # Verification page

config/
└── mail.php                          # Updated dengan email API config

routes/
└── web.php                           # Updated routes
```

## Email Templates

### Verification Email
- Judul yang menarik
- Clear call-to-action button
- Direct link jika button tidak bekerja
- Warning tentang expiration (24 jam)
- Instruksi untuk spam folder

### Password Reset Email
- Similar structure dengan verification email
- Warning tentang security

### Order Confirmation Email
- Order number dan items
- Total price
- Shipping info

## Features

### ✅ Implemented
- [x] External API integration
- [x] Email template dengan HTML
- [x] Token generation & expiration (24 hours)
- [x] Rate limiting untuk resend (1 menit)
- [x] Auto-login setelah verifikasi
- [x] Resend verification functionality
- [x] Mobile-friendly verification page
- [x] Error handling & logging

### 📝 Usage Examples

#### Send Verification Email
```php
use App\Services\EmailService;

$emailService = new EmailService();
$emailService->sendVerificationEmail(
    'user@example.com',
    'User Name',
    'https://example.com/verify-email/token123'
);
```

#### Send Password Reset Email
```php
$emailService->sendPasswordResetEmail(
    'user@example.com',
    'User Name',
    'https://example.com/reset-password/token123'
);
```

#### Send Order Confirmation
```php
$emailService->sendOrderConfirmationEmail(
    'user@example.com',
    'User Name',
    [
        'order_number' => 'ORD-001',
        'total' => 150000,
        'items' => [
            [
                'product_name' => 'Product 1',
                'quantity' => 2,
                'price' => '75000',
            ]
        ]
    ]
);
```

## Testing

### Manual Testing Steps

1. **Register new user**
   - Go to `/register`
   - Fill form dengan data valid
   - Submit registration
   - Should redirect ke `/email-verification`

2. **Check Email**
   - Cek email yang digunakan untuk registrasi
   - Klik link verifikasi atau gunakan token

3. **Verify Email**
   - Link akan verify email dan auto-login
   - Redirect ke home dengan success message

4. **Resend Email**
   - Di halaman verification, click "Kirim Email Verifikasi Ulang"
   - Should get cooldown of 60 seconds

5. **Test Expiration**
   - Generate token, tunggu 24+ jam
   - Token akan invalid dan show error message

## Security Considerations

- ✅ Tokens di-generate dengan `Str::random(64)` - cryptographically secure
- ✅ Tokens expire setelah 24 jam
- ✅ Old tokens di-delete ketika generate new token
- ✅ Rate limiting pada resend (1 menit cooldown)
- ✅ Email tidak di-log dalam plain text
- ✅ API key secure di environment variable

## Error Handling

| Error | Handling |
|-------|----------|
| Token tidak valid | Show error, ask to resend |
| Token expired | Show error, offer to resend |
| Already verified | Show info message |
| Email tidak terkirim | Log error, show friendly message |
| Rate limit exceeded | Show countdown timer |

## Future Enhancements

- [ ] Email preview di development
- [ ] Webhook untuk delivery confirmation
- [ ] SMS verification sebagai backup
- [ ] Social login integration
- [ ] Two-factor authentication
- [ ] Email change verification
- [ ] Bulk verification email retry
