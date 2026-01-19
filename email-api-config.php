<?php
/**
 * Email API Configuration
 * Tempat ini untuk menyimpan konfigurasi email API
 * 
 * Simpan file ini di server: /var/www/server.layanandigitalraja.my.id/config/email-config.php
 */

return [
    // API Security
    'api_key' => getenv('EMAIL_API_KEY') ?: 'rajaxrizx',
    'allowed_origins' => [
        'localhost:8000',
        'localhost:3000',
        '127.0.0.1',
        // Add production domains here
    ],

    // SMTP Configuration
    'smtp' => [
        'host' => getenv('SMTP_HOST') ?: 'smtp.gmail.com',
        'port' => getenv('SMTP_PORT') ?: 587,
        'secure' => getenv('SMTP_SECURE') ?: 'tls', // tls or ssl
        'user' => getenv('SMTP_USER') ?: '',
        'password' => getenv('SMTP_PASSWORD') ?: '',
    ],

    // Default sender
    'from' => [
        'address' => getenv('MAIL_FROM_ADDRESS') ?: 'noreply@tokodigitalraja.com',
        'name' => getenv('MAIL_FROM_NAME') ?: 'Toko Digital Raja',
    ],

    // Rate limiting
    'rate_limit' => [
        'enabled' => true,
        'per_minute' => 10,
        'per_hour' => 100,
    ],

    // Logging
    'logging' => [
        'enabled' => true,
        'file' => '/var/log/email-api.log',
        'level' => 'debug', // debug, info, warning, error
    ],

    // Email validation
    'validation' => [
        'check_mx_records' => false,
        'max_recipients_per_request' => 1,
    ],

    // Retry configuration
    'retry' => [
        'enabled' => true,
        'max_attempts' => 3,
        'delay_seconds' => 5,
    ],
];
