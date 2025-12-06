#!/bin/bash
# Check server files and database status

SERVER="root@72.61.146.226"
APP_PATH="/home/draggify.com/app.draggify.com"

echo "🔍 Checking Server Status..."
echo ""

# 1. Check auth-layout.tsx file on server
echo "1. Checking auth-layout.tsx on server:"
echo "   File path: $APP_PATH/common/resources/client/auth/ui/auth-layout/auth-layout.tsx"
ssh $SERVER "cat $APP_PATH/common/resources/client/auth/ui/auth-layout/auth-layout.tsx | grep -A 3 'img' | head -5"
echo ""

# 2. Check if image file exists
echo "2. Checking if image file exists:"
ssh $SERVER "ls -lh $APP_PATH/public/images/draggify_black.png 2>&1"
echo ""

# 3. Check database settings
echo "3. Checking database settings:"
ssh $SERVER "cd $APP_PATH && php -r \"
require 'vendor/autoload.php';
\$app = require_once 'bootstrap/app.php';
\$app->make('Illuminate\\\\Contracts\\\\Console\\\\Kernel')->bootstrap();
use Common\\\\Settings\\\\Setting;
\$logoDark = Setting::where('name', 'branding.logo_dark')->first();
\$logoLight = Setting::where('name', 'branding.logo_light')->first();
echo 'logo_dark: ' . (\$logoDark ? \$logoDark->value : 'NOT FOUND') . PHP_EOL;
echo 'logo_light: ' . (\$logoLight ? \$logoLight->value : 'NOT FOUND') . PHP_EOL;
\""
echo ""

# 4. Check bootstrap data (what frontend receives)
echo "4. Checking bootstrap data (what frontend receives):"
ssh $SERVER "cd $APP_PATH && php -r \"
require 'vendor/autoload.php';
\$app = require_once 'bootstrap/app.php';
\$app->make('Illuminate\\\\Contracts\\\\Console\\\\Kernel')->bootstrap();
\$bootstrap = new \\Common\\Core\\Bootstrap\\BootstrapData(
    app(\\Common\\Settings\\Settings::class),
    app(\\Illuminate\\Http\\Request::class),
    app(\\Common\\Auth\\Roles\\Role::class),
    app(\\Common\\Localizations\\LocalizationsRepository::class)
);
\$data = \$bootstrap->init();
echo 'Bootstrap logo_dark: ' . (\$data['settings']['branding']['logo_dark'] ?? 'null') . PHP_EOL;
echo 'Bootstrap logo_light: ' . (\$data['settings']['branding']['logo_light'] ?? 'null') . PHP_EOL;
echo 'Bootstrap base_url: ' . (\$data['settings']['base_url'] ?? 'null') . PHP_EOL;
echo 'Bootstrap asset_url: ' . (\$data['settings']['asset_url'] ?? 'null') . PHP_EOL;
\""
echo ""

# 5. Check local file for comparison
echo "5. Local file (for comparison):"
cat app.webuiz.com/common/resources/client/auth/ui/auth-layout/auth-layout.tsx | grep -A 3 'img' | head -5
echo ""

# 6. Check if image accessible via URL
echo "6. Testing image URL accessibility:"
ssh $SERVER "curl -I http://localhost/images/draggify_black.png 2>&1 | head -3"
echo ""

echo "✅ Check complete!"

