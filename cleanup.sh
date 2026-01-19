#!/bin/bash

echo "🧹 Cleaning up Laravel project for School Website..."
echo ""

# Remove vendor directory
echo "📦 Removing old vendor directory..."
rm -rf vendor/

# Remove node_modules
echo "📦 Removing old node_modules..."
rm -rf node_modules/

# Remove lock files
echo "🔒 Removing lock files..."
rm -f composer.lock package-lock.json

# Remove compiled assets
echo "🎨 Removing compiled assets..."
rm -rf public/build/
rm -rf public/hot

echo ""
echo "✅ Cleanup complete!"
echo ""
echo "Next steps:"
echo "1. composer install"
echo "2. npm install"
echo "3. cp .env.example .env"
echo "4. php artisan key:generate"
echo "5. php artisan migrate"
echo "6. npm run build"
echo ""
