#!/bin/bash
# Check database on server for custom domain validation

SERVER="root@72.61.146.226"
APP_PATH="/home/draggify.com/app.draggify.com"

echo "🔍 Checking Database on Server..."
echo ""

# 1. Check products and permissions
echo "1. Checking products and permissions:"
ssh $SERVER "cd $APP_PATH && php -r \"
require 'vendor/autoload.php';
\$app = require_once 'bootstrap/app.php';
\$app->make('Illuminate\\\\Contracts\\\\Console\\\\Kernel')->bootstrap();
use Common\\\\Billing\\\\Models\\\\Product;
use Common\\\\Auth\\\\Permissions\\\\Permission;
use Illuminate\\\\Support\\\\Facades\\\\DB;

\$products = Product::all();
echo 'Found ' . \$products->count() . ' product(s)' . PHP_EOL . PHP_EOL;

foreach (\$products as \$product) {
    echo 'Product: ' . \$product->name . ' (ID: ' . \$product->id . ', Free: ' . (\$product->free ? 'YES' : 'NO') . ')' . PHP_EOL;
    
    \$projectsPerm = Permission::where('name', 'projects.create')->first();
    if (\$projectsPerm) {
        \$pa = DB::table('permissionables')
            ->where('permissionable_id', \$product->id)
            ->where('permissionable_type', 'Common\\\\\\\\Billing\\\\\\\\Models\\\\\\\\Product')
            ->where('permission_id', \$projectsPerm->id)
            ->first();
        
        if (\$pa) {
            \$restrictions = json_decode(\$pa->restrictions ?? '[]', true);
            \$countRestriction = collect(\$restrictions)->firstWhere('name', 'count');
            \$maxWebsites = \$countRestriction['value'] ?? null;
            echo '  projects.create count: ' . (\$maxWebsites ?? 'NULL') . PHP_EOL;
        } else {
            echo '  projects.create: NOT FOUND' . PHP_EOL;
        }
    }
    echo PHP_EOL;
}
\""

# 2. Check test user
echo ""
echo "2. Checking test user (webuiz25@gmail.com):"
ssh $SERVER "cd $APP_PATH && php -r \"
require 'vendor/autoload.php';
\$app = require_once 'bootstrap/app.php';
\$app->make('Illuminate\\\\Contracts\\\\Console\\\\Kernel')->bootstrap();
use App\\\\Models\\\\User;

\$user = User::where('email', 'webuiz25@gmail.com')->first();
if (!\$user) {
    echo 'User not found!' . PHP_EOL;
    exit(1);
}

\$user->loadPermissions();
\$maxWebsites = \$user->getRestrictionValue('projects.create', 'count');
\$currentDomains = \$user->customDomains()->count();

echo 'User: ' . \$user->email . ' (ID: ' . \$user->id . ')' . PHP_EOL;
echo 'projects.create count: ' . (\$maxWebsites ?? 'NULL') . PHP_EOL;
echo 'Current Domains: ' . \$currentDomains . PHP_EOL;

if (\$maxWebsites !== null) {
    \$limitReached = \$currentDomains >= \$maxWebsites;
    echo 'Limit Reached: ' . (\$limitReached ? 'YES' : 'NO') . PHP_EOL;
} else {
    echo 'No website limit found' . PHP_EOL;
}
\""

echo ""
echo "✅ Check complete!"

