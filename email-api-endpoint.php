<?php
/**
 * Email API Endpoint
 * https://server.layanandigitalraja.my.id/api/send_mail.php
 * 
 * Menerima POST request untuk mengirim email
 * Gunakan environment variables untuk konfigurasi
 */

// Set header untuk JSON response
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit(json_encode(['status' => 'ok']));
}

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit(json_encode([
        'success' => false,
        'message' => 'Method tidak diizinkan. Gunakan POST.'
    ]));
}

// ==========================================
// Configuration
// ==========================================

// Email API credentials (set di environment variables)
$API_KEY = getenv('EMAIL_API_KEY') ?: 'rajaxrizx';
$SMTP_HOST = getenv('SMTP_HOST') ?: 'smtp.gmail.com';
$SMTP_PORT = getenv('SMTP_PORT') ?: 587;
$SMTP_USER = getenv('SMTP_USER') ?: '';
$SMTP_PASSWORD = getenv('SMTP_PASSWORD') ?: '';
$SMTP_SECURE = getenv('SMTP_SECURE') ?: 'tls';

// Log file
$LOG_FILE = __DIR__ . '/../../logs/email_api.log';

// ==========================================
// Helper Functions
// ==========================================

/**
 * Log message ke file
 */
function logMessage($level, $message, $data = []) {
    global $LOG_FILE;
    $timestamp = date('Y-m-d H:i:s');
    $logDir = dirname($LOG_FILE);
    
    if (!is_dir($logDir)) {
        mkdir($logDir, 0755, true);
    }
    
    $logEntry = "[{$timestamp}] [{$level}] {$message}";
    if (!empty($data)) {
        $logEntry .= " " . json_encode($data);
    }
    $logEntry .= "\n";
    
    file_put_contents($LOG_FILE, $logEntry, FILE_APPEND);
}

/**
 * Validate email format
 */
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Sanitize string input
 */
function sanitize($input) {
    return trim(strip_tags($input));
}

/**
 * Send response
 */
function sendResponse($success, $message, $code = 200, $data = []) {
    http_response_code($code);
    $response = [
        'success' => $success,
        'message' => $message,
    ];
    if (!empty($data)) {
        $response = array_merge($response, $data);
    }
    echo json_encode($response);
    exit;
}

// ==========================================
// Get Input Data
// ==========================================

// Accept both JSON and form data
$input = json_decode(file_get_contents('php://input'), true);

// If not JSON, try POST data
if (!$input) {
    $input = $_POST;
}

$apiKey = sanitize($input['api_key'] ?? '');
$to = sanitize($input['to'] ?? '');
$subject = sanitize($input['subject'] ?? '');
// Support both 'message' and 'body' parameter names
$body = $input['body'] ?? $input['message'] ?? '';
$from = sanitize($input['from'] ?? '');
$fromName = sanitize($input['from_name'] ?? 'Toko Digital Raja');

// ==========================================
// Validation
// ==========================================

// Check API Key
if (empty($apiKey)) {
    logMessage('ERROR', 'API Key tidak diterima');
    sendResponse(false, 'API Key diperlukan', 400);
}

if ($apiKey !== $API_KEY) {
    logMessage('ERROR', 'API Key tidak valid', ['provided' => $apiKey]);
    sendResponse(false, 'API Key tidak valid', 401);
}

// Check required fields
if (empty($to)) {
    sendResponse(false, 'Email tujuan (to) diperlukan', 400);
}

if (!isValidEmail($to)) {
    sendResponse(false, 'Format email tujuan tidak valid', 400);
}

if (empty($subject)) {
    sendResponse(false, 'Subject email diperlukan', 400);
}

if (empty($body)) {
    sendResponse(false, 'Isi email diperlukan', 400);
}

if (empty($from)) {
    sendResponse(false, 'Email pengirim (from) diperlukan', 400);
}

if (!isValidEmail($from)) {
    sendResponse(false, 'Format email pengirim tidak valid', 400);
}

// ==========================================
// Send Email
// ==========================================

try {
    // Check if PHPMailer is available
    $usePhpMailer = false;
    if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
        require_once __DIR__ . '/../vendor/autoload.php';
        $usePhpMailer = class_exists('PHPMailer\PHPMailer\PHPMailer');
    }

    if ($usePhpMailer) {
        // Send using PHPMailer
        sendWithPhpMailer($to, $subject, $body, $from, $fromName);
    } else {
        // Send using native PHP mail()
        sendWithPhpMail($to, $subject, $body, $from, $fromName);
    }

    logMessage('INFO', 'Email terkirim berhasil', [
        'to' => $to,
        'subject' => $subject,
        'from' => $from
    ]);

    sendResponse(true, 'Email berhasil dikirim', 200, [
        'email' => $to,
        'timestamp' => date('Y-m-d H:i:s')
    ]);

} catch (Exception $e) {
    logMessage('ERROR', 'Gagal mengirim email: ' . $e->getMessage(), [
        'to' => $to,
        'subject' => $subject,
        'error' => $e->getMessage()
    ]);

    sendResponse(false, 'Gagal mengirim email: ' . $e->getMessage(), 500);
}

// ==========================================
// Email Sending Functions
// ==========================================

/**
 * Send email using PHPMailer
 */
function sendWithPhpMailer($to, $subject, $message, $from, $fromName) {
    global $SMTP_HOST, $SMTP_PORT, $SMTP_USER, $SMTP_PASSWORD, $SMTP_SECURE;

    $mail = new PHPMailer\PHPMailer\PHPMailer(true);

    // SMTP Configuration
    $mail->isSMTP();
    $mail->Host = $SMTP_HOST;
    $mail->Port = $SMTP_PORT;
    $mail->SMTPSecure = $SMTP_SECURE;
    $mail->SMTPAuth = true;
    $mail->Username = $SMTP_USER;
    $mail->Password = $SMTP_PASSWORD;
    $mail->SMTPOptions = [
        'ssl' => [
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true,
        ]
    ];

    // Set charset
    $mail->CharSet = 'UTF-8';

    // Sender
    $mail->setFrom($from, $fromName);

    // Recipient
    $mail->addAddress($to);

    // Email content
    $mail->isHTML(true);
    $mail->Subject = $subject;
    $mail->Body = $message;
    $mail->AltBody = strip_tags($message);

    // Send
    if (!$mail->send()) {
        throw new Exception($mail->ErrorInfo);
    }
}

/**
 * Send email using native PHP mail()
 */
function sendWithPhpMail($to, $subject, $message, $from, $fromName) {
    $headers = [];

    // Set headers
    $headers[] = 'MIME-Version: 1.0';
    $headers[] = 'Content-type: text/html; charset=UTF-8';
    $headers[] = 'From: ' . $fromName . ' <' . $from . '>';
    $headers[] = 'Reply-To: ' . $from;
    $headers[] = 'X-Mailer: PHP/' . phpversion();

    $headers_str = implode("\r\n", $headers);

    // Send mail
    if (!mail($to, $subject, $message, $headers_str)) {
        throw new Exception('Gagal mengirim email dengan fungsi mail()');
    }
}
