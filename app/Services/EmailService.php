<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * EmailService - Mengirim email via external API
 * Menggunakan https://server.layanandigitalraja.my.id/api/send_mail.php
 */
class EmailService
{
    protected $apiUrl;
    protected $apiKey;
    protected $fromAddress;
    protected $fromName;

    public function __construct()
    {
        $this->apiUrl = config('mail.api_url') ?? env('EMAIL_API_URL');
        $this->apiKey = config('mail.api_key') ?? env('EMAIL_API_KEY');
        $this->fromAddress = config('mail.from.address') ?? env('EMAIL_FROM_ADDRESS');
        $this->fromName = config('mail.from.name') ?? env('EMAIL_FROM_NAME');
    }

    /**
     * Send email via external API
     */
    public function send(string $to, string $subject, string $body, ?string $htmlBody = null)
    {
        try {
            $payload = [
                'to' => $to,
                'subject' => $subject,
                'message' => $htmlBody ?? $body,
                'from' => $this->fromAddress,
                'from_name' => $this->fromName,
                'api_key' => $this->apiKey,
            ];

            $response = Http::timeout(10)
                ->post($this->apiUrl, $payload);

            if ($response->successful()) {
                Log::info("Email sent successfully to {$to}", [
                    'subject' => $subject,
                    'response' => $response->json(),
                ]);
                return true;
            }

            Log::error("Failed to send email to {$to}", [
                'subject' => $subject,
                'status' => $response->status(),
                'response' => $response->body(),
            ]);

            return false;
        } catch (\Exception $e) {
            Log::error("Exception while sending email to {$to}: " . $e->getMessage(), [
                'subject' => $subject,
                'exception' => $e,
            ]);

            return false;
        }
    }

    /**
     * Send verification email
     */
    public function sendVerificationEmail(string $to, string $userName, string $verificationUrl)
    {
        $subject = 'Verifikasi Email Anda - Toko Digital Raja';

        $htmlBody = $this->getVerificationEmailTemplate($to, $userName, $verificationUrl);

        return $this->send($to, $subject, '', $htmlBody);
    }

    /**
     * Send password reset email
     */
    public function sendPasswordResetEmail(string $to, string $userName, string $resetUrl)
    {
        $subject = 'Reset Password - Toko Digital Raja';

        $htmlBody = $this->getPasswordResetEmailTemplate($to, $userName, $resetUrl);

        return $this->send($to, $subject, '', $htmlBody);
    }

    /**
     * Send order confirmation email
     */
    public function sendOrderConfirmationEmail(string $to, string $userName, array $orderData)
    {
        $subject = 'Konfirmasi Pesanan - Toko Digital Raja';

        $htmlBody = $this->getOrderConfirmationTemplate($to, $userName, $orderData);

        return $this->send($to, $subject, '', $htmlBody);
    }

    /**
     * Get verification email HTML template
     */
    private function getVerificationEmailTemplate(string $to, string $userName, string $verificationUrl): string
    {
        return <<<HTML
        <!DOCTYPE html>
        <html lang="id">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Verifikasi Email</title>
            <style>
                body { font-family: Arial, sans-serif; background-color: #f5f5f5; }
                .container { max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
                .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; border-radius: 8px 8px 0 0; }
                .content { padding: 30px; }
                .button { display: inline-block; background-color: #667eea; color: white; padding: 12px 30px; text-decoration: none; border-radius: 4px; margin: 20px 0; }
                .footer { background-color: #f5f5f5; padding: 20px; text-align: center; color: #666; font-size: 12px; border-top: 1px solid #ddd; }
                .warning { background-color: #fff3cd; padding: 10px; border-left: 4px solid #ffc107; margin: 20px 0; color: #856404; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>Selamat Datang di Toko Digital Raja!</h1>
                </div>
                <div class="content">
                    <p>Halo <strong>{$userName}</strong>,</p>
                    <p>Terima kasih telah mendaftar di Toko Digital Raja. Untuk melengkapi proses registrasi, silakan verifikasi email Anda dengan mengklik tombol di bawah ini:</p>
                    
                    <center>
                        <a href="{$verificationUrl}" class="button">Verifikasi Email</a>
                    </center>
                    
                    <p>Atau salin dan tempel link ini ke browser Anda:</p>
                    <p style="background-color: #f5f5f5; padding: 10px; word-break: break-all; font-size: 12px;">{$verificationUrl}</p>
                    
                    <div class="warning">
                        <strong>⚠️ Perhatian:</strong> Link verifikasi ini hanya berlaku selama 24 jam. Jika link sudah kadaluarsa, Anda dapat meminta email verifikasi baru.
                    </div>
                    
                    <p style="margin-top: 30px; color: #666; font-size: 14px;">
                        Jika Anda tidak melakukan pendaftaran ini, abaikan email ini. Akun Anda tidak akan dibuat sampai email diverifikasi.
                    </p>
                </div>
                <div class="footer">
                    <p>&copy; 2026 Toko Digital Raja. Semua hak dilindungi.</p>
                    <p>Email ini dikirim ke {$to} dari sistem otomatis kami.</p>
                </div>
            </div>
        </body>
        </html>
        HTML;
    }

    /**
     * Get password reset email HTML template
     */
    private function getPasswordResetEmailTemplate(string $to, string $userName, string $resetUrl): string
    {
        return <<<HTML
        <!DOCTYPE html>
        <html lang="id">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Reset Password</title>
            <style>
                body { font-family: Arial, sans-serif; background-color: #f5f5f5; }
                .container { max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
                .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; border-radius: 8px 8px 0 0; }
                .content { padding: 30px; }
                .button { display: inline-block; background-color: #667eea; color: white; padding: 12px 30px; text-decoration: none; border-radius: 4px; margin: 20px 0; }
                .footer { background-color: #f5f5f5; padding: 20px; text-align: center; color: #666; font-size: 12px; border-top: 1px solid #ddd; }
                .warning { background-color: #f8d7da; padding: 10px; border-left: 4px solid #dc3545; margin: 20px 0; color: #721c24; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>Reset Password</h1>
                </div>
                <div class="content">
                    <p>Halo <strong>{$userName}</strong>,</p>
                    <p>Kami menerima permintaan untuk mereset password akun Anda. Klik tombol di bawah untuk melanjutkan:</p>
                    
                    <center>
                        <a href="{$resetUrl}" class="button">Reset Password</a>
                    </center>
                    
                    <p>Atau salin dan tempel link ini ke browser Anda:</p>
                    <p style="background-color: #f5f5f5; padding: 10px; word-break: break-all; font-size: 12px;">{$resetUrl}</p>
                    
                    <div class="warning">
                        <strong>⚠️ Keamanan:</strong> Link ini hanya berlaku selama 1 jam. Jika Anda tidak meminta reset password, abaikan email ini.
                    </div>
                </div>
                <div class="footer">
                    <p>&copy; 2026 Toko Digital Raja. Semua hak dilindungi.</p>
                </div>
            </div>
        </body>
        </html>
        HTML;
    }

    /**
     * Get order confirmation email HTML template
     */
    private function getOrderConfirmationTemplate(string $to, string $userName, array $orderData): string
    {
        $orderNumber = $orderData['order_number'] ?? 'N/A';
        $total = $orderData['total'] ?? 0;
        $items = $orderData['items'] ?? [];

        $itemsHtml = '';
        foreach ($items as $item) {
            $itemsHtml .= <<<HTML
            <tr>
                <td style="padding: 10px; border-bottom: 1px solid #ddd;">{$item['product_name']}</td>
                <td style="padding: 10px; border-bottom: 1px solid #ddd; text-align: right;">{$item['quantity']}</td>
                <td style="padding: 10px; border-bottom: 1px solid #ddd; text-align: right;">Rp {$item['price']}</td>
            </tr>
            HTML;
        }

        return <<<HTML
        <!DOCTYPE html>
        <html lang="id">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Konfirmasi Pesanan</title>
            <style>
                body { font-family: Arial, sans-serif; background-color: #f5f5f5; }
                .container { max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
                .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; border-radius: 8px 8px 0 0; }
                .content { padding: 30px; }
                .order-table { width: 100%; border-collapse: collapse; margin: 20px 0; }
                .footer { background-color: #f5f5f5; padding: 20px; text-align: center; color: #666; font-size: 12px; border-top: 1px solid #ddd; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>Pesanan Diterima</h1>
                </div>
                <div class="content">
                    <p>Halo <strong>{$userName}</strong>,</p>
                    <p>Terima kasih atas pesanan Anda! Pesanan telah berhasil dibuat dan sedang diproses.</p>
                    
                    <p><strong>Nomor Pesanan:</strong> {$orderNumber}</p>
                    
                    <table class="order-table">
                        <thead>
                            <tr style="background-color: #f5f5f5;">
                                <th style="padding: 10px; text-align: left;">Produk</th>
                                <th style="padding: 10px; text-align: right;">Qty</th>
                                <th style="padding: 10px; text-align: right;">Harga</th>
                            </tr>
                        </thead>
                        <tbody>
                            {$itemsHtml}
                        </tbody>
                    </table>
                    
                    <p style="text-align: right; font-size: 16px;"><strong>Total: Rp {$total}</strong></p>
                    
                    <p>Kami akan mengirimkan notifikasi ketika pesanan Anda dikirim.</p>
                </div>
                <div class="footer">
                    <p>&copy; 2026 Toko Digital Raja. Semua hak dilindungi.</p>
                </div>
            </div>
        </body>
        </html>
        HTML;
    }
}
