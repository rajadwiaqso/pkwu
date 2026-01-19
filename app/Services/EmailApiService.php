<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

/**
 * EmailApiService - Mengirim email via external API
 * Menggunakan format application/x-www-form-urlencoded dengan Guzzle HTTP Client
 * Endpoint: https://server.layanandigitalraja.my.id/api/send_mail.php
 */
class EmailApiService
{
    protected $httpClient;
    protected $apiKey;
    protected $apiUrl;
    protected $fromAddress;
    protected $fromName;

    public function __construct()
    {
        $this->httpClient = new Client();
        // Ambil API key dan URL dari konfigurasi .env
        $this->apiKey = config('mail.api_key') ?? env('EMAIL_API_KEY');
        $this->apiUrl = config('mail.api_url') ?? env('EMAIL_API_URL');
        $this->fromAddress = config('mail.from.address') ?? env('EMAIL_FROM_ADDRESS', 'noreply@tokodigitalraja.com');
        $this->fromName = config('mail.from.name') ?? env('EMAIL_FROM_NAME', 'Toko Digital Raja');
    }

    /**
     * Send email to recipient
     *
     * @param string $to Recipient email address
     * @param string $subject Email subject
     * @param string $body Email body (HTML or plain text)
     * @param string|null $from Sender email (optional, uses default if not provided)
     * @param string|null $fromName Sender name (optional, uses default if not provided)
     * @return bool True if email sent successfully, false otherwise
     */
    public function sendEmail(
        string $to,
        string $subject,
        string $body,
        ?string $from = null,
        ?string $fromName = null
    ): bool {
        try {
            $from = $from ?? $this->fromAddress;
            $fromName = $fromName ?? $this->fromName;

            // Send request dengan form_params (URL-encoded)
            $response = $this->httpClient->post($this->apiUrl, [
                'headers' => [
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ],
                'form_params' => [
                    'api_key' => $this->apiKey,
                    'to' => $to,
                    'subject' => $subject,
                    'body' => $body,
                    'from' => $from,
                    'from_name' => $fromName,
                ],
                'timeout' => 10,
            ]);

            $statusCode = $response->getStatusCode();
            $responseBody = json_decode($response->getBody(), true);

            // Check if response indicates success
            if ($statusCode >= 200 && $statusCode < 300) {
                if (isset($responseBody['success']) && $responseBody['success'] === true) {
                    Log::info('Email sent successfully via API', [
                        'to' => $to,
                        'subject' => $subject,
                        'message' => $responseBody['message'] ?? 'Success',
                    ]);
                    return true;
                } else {
                    $message = $responseBody['message'] ?? 'Unknown error';
                    Log::error('API returned success status but success flag is false', [
                        'to' => $to,
                        'status_code' => $statusCode,
                        'message' => $message,
                        'response' => $responseBody,
                    ]);
                    return false;
                }
            } else {
                Log::error('Failed to send email via API: HTTP ' . $statusCode, [
                    'to' => $to,
                    'subject' => $subject,
                    'response' => $responseBody,
                ]);
                return false;
            }
        } catch (\Exception $e) {
            Log::error('Exception sending email via API', [
                'to' => $to,
                'subject' => $subject,
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
            ]);
            return false;
        }
    }

    /**
     * Send verification email
     *
     * @param string $to User email
     * @param string $userName User name
     * @param string $verificationUrl Verification URL with token
     * @return bool
     */
    public function sendVerificationEmail(string $to, string $userName, string $verificationUrl): bool
    {
        $subject = 'Verifikasi Email - Toko Digital Raja';
        $body = $this->getVerificationEmailTemplate($to, $userName, $verificationUrl);

        return $this->sendEmail($to, $subject, $body);
    }

    /**
     * Send password reset email
     *
     * @param string $to User email
     * @param string $userName User name
     * @param string $resetUrl Password reset URL with token
     * @return bool
     */
    public function sendPasswordResetEmail(string $to, string $userName, string $resetUrl): bool
    {
        $subject = 'Reset Kata Sandi - Toko Digital Raja';
        $body = $this->getPasswordResetEmailTemplate($to, $userName, $resetUrl);

        return $this->sendEmail($to, $subject, $body);
    }

    /**
     * Send order confirmation email
     *
     * @param string $to Customer email
     * @param string $customerName Customer name
     * @param array $orderData Order information
     * @return bool
     */
    public function sendOrderConfirmationEmail(string $to, string $customerName, array $orderData): bool
    {
        $subject = 'Konfirmasi Pesanan - Toko Digital Raja';
        $body = $this->getOrderConfirmationEmailTemplate($to, $customerName, $orderData);

        return $this->sendEmail($to, $subject, $body);
    }

    /**
     * Get verification email HTML template
     */
    protected function getVerificationEmailTemplate(string $to, string $userName, string $verificationUrl): string
    {
        return <<<HTML
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; border-radius: 5px 5px 0 0; text-align: center; }
                .content { background: #f9f9f9; padding: 30px; }
                .button { display: inline-block; background: #667eea; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; margin: 20px 0; }
                .footer { background: #333; color: white; padding: 20px; text-align: center; font-size: 12px; border-radius: 0 0 5px 5px; }
                .info { background: #e3f2fd; border-left: 4px solid #2196f3; padding: 15px; margin: 20px 0; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>Verifikasi Email Anda</h1>
                </div>
                <div class="content">
                    <p>Halo <strong>{$userName}</strong>,</p>
                    <p>Terima kasih telah mendaftar di <strong>Toko Digital Raja</strong>. Untuk menyelesaikan registrasi, silakan verifikasi email Anda dengan mengklik tombol di bawah:</p>
                    
                    <center>
                        <a href="{$verificationUrl}" class="button">Verifikasi Email</a>
                    </center>
                    
                    <p>Atau salin dan paste URL ini ke browser Anda:</p>
                    <p style="word-break: break-all;"><code>{$verificationUrl}</code></p>
                    
                    <div class="info">
                        <strong>⏰ Catatan:</strong> Link verifikasi ini berlaku selama 24 jam. Setelah itu, Anda perlu meminta link verifikasi baru.
                    </div>
                    
                    <p style="color: #999; font-size: 12px;">Jika Anda tidak mendaftar di Toko Digital Raja, abaikan email ini.</p>
                </div>
                <div class="footer">
                    <p>&copy; 2026 Toko Digital Raja. All rights reserved.</p>
                    <p>Email: noreply@tokodigitalraja.com</p>
                </div>
            </div>
        </body>
        </html>
        HTML;
    }

    /**
     * Get password reset email HTML template
     */
    protected function getPasswordResetEmailTemplate(string $to, string $userName, string $resetUrl): string
    {
        return <<<HTML
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; padding: 30px; border-radius: 5px 5px 0 0; text-align: center; }
                .content { background: #f9f9f9; padding: 30px; }
                .button { display: inline-block; background: #f5576c; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; margin: 20px 0; }
                .footer { background: #333; color: white; padding: 20px; text-align: center; font-size: 12px; border-radius: 0 0 5px 5px; }
                .warning { background: #ffebee; border-left: 4px solid #f44336; padding: 15px; margin: 20px 0; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>Reset Kata Sandi</h1>
                </div>
                <div class="content">
                    <p>Halo <strong>{$userName}</strong>,</p>
                    <p>Kami menerima permintaan untuk me-reset kata sandi akun Anda. Klik tombol di bawah untuk membuat kata sandi baru:</p>
                    
                    <center>
                        <a href="{$resetUrl}" class="button">Reset Kata Sandi</a>
                    </center>
                    
                    <p>Atau salin dan paste URL ini ke browser Anda:</p>
                    <p style="word-break: break-all;"><code>{$resetUrl}</code></p>
                    
                    <div class="warning">
                        <strong>⚠️ Penting:</strong> Jika Anda tidak meminta reset kata sandi, abaikan email ini. Link ini hanya berlaku 1 jam.
                    </div>
                    
                    <p style="color: #999; font-size: 12px;">Untuk alasan keamanan, jangan bagikan link ini kepada siapa pun.</p>
                </div>
                <div class="footer">
                    <p>&copy; 2026 Toko Digital Raja. All rights reserved.</p>
                    <p>Email: noreply@tokodigitalraja.com</p>
                </div>
            </div>
        </body>
        </html>
        HTML;
    }

    /**
     * Get order confirmation email HTML template
     */
    protected function getOrderConfirmationEmailTemplate(string $to, string $customerName, array $orderData): string
    {
        $orderId = $orderData['id'] ?? 'N/A';
        $totalAmount = $orderData['total_amount'] ?? '0';
        $itemsHtml = '';

        if (isset($orderData['items']) && is_array($orderData['items'])) {
            foreach ($orderData['items'] as $item) {
                $name = $item['product_name'] ?? 'Product';
                $qty = $item['quantity'] ?? 0;
                $price = $item['price'] ?? 0;

                $itemsHtml .= "<tr style=\"border-bottom: 1px solid #ddd;\">"
                    . "<td style=\"padding: 10px; text-align: left;\">" . htmlspecialchars((string)$name) . "</td>"
                    . "<td style=\"padding: 10px; text-align: center;\">" . (int)$qty . "</td>"
                    . "<td style=\"padding: 10px; text-align: right;\">Rp " . htmlspecialchars((string)$price) . "</td>"
                    . "</tr>";
            }
        }

        return <<<HTML
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; border-radius: 5px 5px 0 0; text-align: center; }
                .content { background: #f9f9f9; padding: 30px; }
                table { width: 100%; border-collapse: collapse; margin: 20px 0; }
                th { background: #667eea; color: white; padding: 10px; text-align: left; }
                .total-row { background: #667eea; color: white; font-weight: bold; padding: 15px; text-align: right; border-radius: 5px; margin: 20px 0; font-size: 18px; }
                .button { display: inline-block; background: #667eea; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; margin: 20px 0; }
                .footer { background: #333; color: white; padding: 20px; text-align: center; font-size: 12px; border-radius: 0 0 5px 5px; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>Konfirmasi Pesanan</h1>
                    <p>Nomor Pesanan: <strong>{$orderId}</strong></p>
                </div>
                <div class="content">
                    <p>Halo <strong>{$customerName}</strong>,</p>
                    <p>Terima kasih telah berbelanja di <strong>Toko Digital Raja</strong>. Pesanan Anda telah diterima dan sedang diproses.</p>
                    
                    <table>
                        <thead>
                            <tr>
                                <th>Produk</th>
                                <th>Jumlah</th>
                                <th>Harga</th>
                            </tr>
                        </thead>
                        <tbody>
                            {$itemsHtml}
                        </tbody>
                    </table>
                    
                    <div class="total-row">
                        Total: Rp {$totalAmount}
                    </div>
                    
                    <p style="text-align: center;">
                        <a href="https://app.tokodigitalraja.com/orders/{$orderId}" class="button">Lihat Detail Pesanan</a>
                    </p>
                    
                    <p style="color: #666; margin-top: 30px;">Anda akan menerima notifikasi ketika pesanan dikirim.</p>
                </div>
                <div class="footer">
                    <p>&copy; 2026 Toko Digital Raja. All rights reserved.</p>
                    <p>Email: noreply@tokodigitalraja.com | WhatsApp: 0812-3575-4281</p>
                </div>
            </div>
        </body>
        </html>
        HTML;
    }
}
