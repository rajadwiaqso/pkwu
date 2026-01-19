#!/bin/bash

# ==========================================
# Email API Server Setup Script
# ==========================================
# Run dengan: bash email-api-setup.sh

set -e

echo "╔════════════════════════════════════════════════════════════╗"
echo "║       Email API Server Setup Script                        ║"
echo "║  https://server.layanandigitalraja.my.id/api/send_mail.php ║"
echo "╚════════════════════════════════════════════════════════════╝"

# Check if running as root or with sudo
if [[ $EUID -ne 0 ]]; then
   echo "⚠️  Skript ini harus dijalankan dengan sudo"
   echo "Jalankan: sudo bash email-api-setup.sh"
   exit 1
fi

# ==========================================
# Configuration
# ==========================================

API_DIR="${1:-.}"
LOG_DIR="/var/log/email-api"
API_USER="${2:-www-data}"
BACKUP_DIR="${API_DIR}/.backups"

echo ""
echo "📋 Configuration:"
echo "  API Directory: $API_DIR"
echo "  Log Directory: $LOG_DIR"
echo "  API User: $API_USER"
echo ""

# ==========================================
# Create Directories
# ==========================================

echo "📁 Creating directories..."

mkdir -p "$API_DIR/api"
mkdir -p "$LOG_DIR"
mkdir -p "$BACKUP_DIR"

echo "✓ Directories created"

# ==========================================
# Copy Files
# ==========================================

echo ""
echo "📋 Copying files..."

if [ -f "email-api-endpoint.php" ]; then
    cp email-api-endpoint.php "$API_DIR/api/send_mail.php"
    echo "✓ send_mail.php copied"
else
    echo "⚠️  email-api-endpoint.php tidak ditemukan"
fi

if [ -f "email-api-config.php" ]; then
    mkdir -p "$API_DIR/config"
    cp email-api-config.php "$API_DIR/config/email-config.php"
    echo "✓ email-config.php copied"
fi

if [ -f "email-api.env.example" ]; then
    cp email-api.env.example "$API_DIR/.env.example"
    if [ ! -f "$API_DIR/.env" ]; then
        cp email-api.env.example "$API_DIR/.env"
        echo "✓ .env.example dan .env created"
    else
        echo "⚠️  .env already exists, skipped"
    fi
fi

# ==========================================
# Set Permissions
# ==========================================

echo ""
echo "🔐 Setting permissions..."

chmod 644 "$API_DIR/api/send_mail.php"
chmod 755 "$API_DIR/api"
chmod 755 "$API_DIR/config" 2>/dev/null || true
chmod 644 "$API_DIR/config/email-config.php" 2>/dev/null || true

chown -R "$API_USER:$API_USER" "$LOG_DIR" || true
chmod 755 "$LOG_DIR"
chmod 666 "$LOG_DIR" 2>/dev/null || true

touch "$LOG_DIR/email.log" 2>/dev/null || true
chmod 666 "$LOG_DIR/email.log" 2>/dev/null || true

echo "✓ Permissions set"

# ==========================================
# Install PHPMailer (Optional)
# ==========================================

echo ""
read -p "Apakah Anda ingin install PHPMailer? (y/n) " -n 1 -r
echo ""

if [[ $REPLY =~ ^[Yy]$ ]]; then
    if command -v composer &> /dev/null; then
        echo "📦 Installing PHPMailer via Composer..."
        cd "$API_DIR"
        composer require phpmailer/phpmailer 2>/dev/null || echo "⚠️  Composer install gagal"
        cd - > /dev/null
    else
        echo "⚠️  Composer tidak ditemukan, skipped PHPMailer installation"
    fi
fi

# ==========================================
# Configuration Guide
# ==========================================

echo ""
echo "╔════════════════════════════════════════════════════════════╗"
echo "║              🎉 Setup Berhasil!                            ║"
echo "╚════════════════════════════════════════════════════════════╝"

echo ""
echo "📝 Next Steps:"
echo ""
echo "1️⃣  Edit konfigurasi environment:"
echo "    nano $API_DIR/.env"
echo ""
echo "2️⃣  Setup SMTP credentials:"
echo "    - For Gmail: Get App Password from https://myaccount.google.com/apppasswords"
echo "    - For other providers: Get SMTP credentials from your provider"
echo ""
echo "3️⃣  Test connection:"
echo "    curl -X POST https://server.layanandigitalraja.my.id/api/send_mail.php \\"
echo "      -H 'Content-Type: application/json' \\"
echo "      -d '{\"api_key\":\"rajaxrizx\",\"to\":\"test@example.com\",\"subject\":\"Test\",\"message\":\"<p>Test</p>\",\"from\":\"noreply@tokodigitalraja.com\",\"from_name\":\"Test\"}'"
echo ""
echo "4️⃣  Monitor logs:"
echo "    tail -f $LOG_DIR/email.log"
echo ""
echo "📍 File Locations:"
echo "  API Endpoint: $API_DIR/api/send_mail.php"
echo "  Configuration: $API_DIR/config/email-config.php"
echo "  Environment: $API_DIR/.env"
echo "  Logs: $LOG_DIR/email.log"
echo ""
echo "📚 Documentation:"
echo "  Read EMAIL_API_SERVER.md untuk detailed setup instructions"
echo ""
echo "✅ Setup complete!"
