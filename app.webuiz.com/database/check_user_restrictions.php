<?php
/**
 * Script to check user restrictions and products
 * Run: php database/check_user_restrictions.php [email]
 */

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Common\Billing\Models\Product;
use Common\Auth\Permissions\Permission;
use Illuminate\Support\Facades\DB;

$email = $argv[1] ?? 'webuiz25@gmail.com';

echo "Checking user: $email\n";
echo str_repeat('=', 80) . "\n\n";

$user = User::where('email', $email)->first();

if (!$user) {
    echo "❌ User not found!\n";
    exit(1);
}

echo "User ID: {$user->id}\n";
echo "Email: {$user->email}\n\n";

// Check subscriptions
echo "=== SUBSCRIPTIONS ===\n";
$subscriptions = $user->subscriptions()->with('product')->get();
if ($subscriptions->isEmpty()) {
    echo "No subscriptions found\n";
} else {
    foreach ($subscriptions as $sub) {
        echo "Subscription ID: {$sub->id}\n";
        echo "  Product ID: {$sub->product_id}\n";
        echo "  Product Name: " . ($sub->product ? $sub->product->name : 'NULL') . "\n";
        echo "  Valid: " . ($sub->valid() ? 'YES' : 'NO') . "\n";
        echo "  Active: " . ($sub->active() ? 'YES' : 'NO') . "\n";
    }
}

// Get subscription product
$product = $user->getSubscriptionProduct();
echo "\n=== SUBSCRIPTION PRODUCT ===\n";
if ($product) {
    echo "Product ID: {$product->id}\n";
    echo "Product Name: {$product->name}\n";
    echo "Free: " . ($product->free ? 'YES' : 'NO') . "\n";
} else {
    echo "❌ No subscription product found!\n";
}

// Check permissions from product
if ($product) {
    echo "\n=== PRODUCT PERMISSIONS ===\n";
    $productPermissions = DB::table('permissionables')
        ->join('permissions', 'permissionables.permission_id', '=', 'permissions.id')
        ->where('permissionables.permissionable_id', $product->id)
        ->where('permissionables.permissionable_type', Product::class)
        ->select('permissions.name', 'permissionables.restrictions')
        ->get();
    
    foreach ($productPermissions as $perm) {
        echo "Permission: {$perm->name}\n";
        $restrictions = json_decode($perm->restrictions ?? '[]', true);
        foreach ($restrictions as $restriction) {
            echo "  - {$restriction['name']}: {$restriction['value']}\n";
        }
    }
}

// Check user's restriction values
echo "\n=== USER RESTRICTION VALUES ===\n";
$user->loadPermissions();
$maxWebsites = $user->getRestrictionValue('projects.create', 'count');
$maxCustomDomains = $user->getRestrictionValue('custom_domains.create', 'count');

echo "projects.create count: " . ($maxWebsites ?? 'NULL') . "\n";
echo "custom_domains.create count: " . ($maxCustomDomains ?? 'NULL') . "\n";

// Check current counts
$currentProjects = $user->projects()->count();
$currentDomains = $user->customDomains()->count();

echo "\n=== CURRENT USAGE ===\n";
echo "Projects: $currentProjects\n";
echo "Custom Domains: $currentDomains\n";

// Check if limit is reached
echo "\n=== VALIDATION CHECK ===\n";
if ($maxWebsites !== null) {
    $limitReached = $currentDomains >= $maxWebsites;
    echo "Max Websites: $maxWebsites\n";
    echo "Current Domains: $currentDomains\n";
    echo "Limit Reached: " . ($limitReached ? 'YES' : 'NO') . "\n";
    if ($limitReached) {
        echo "⚠️  User should NOT be able to add more domains\n";
    } else {
        echo "✅ User can add " . ($maxWebsites - $currentDomains) . " more domain(s)\n";
    }
} else {
    echo "⚠️  No website limit found - user might have unlimited\n";
}

// Check permissionables table
echo "\n=== PERMISSIONABLES TABLE ===\n";
$permissionables = DB::table('permissionables')
    ->join('permissions', 'permissionables.permission_id', '=', 'permissions.id')
    ->where(function($query) use ($user, $product) {
        // User permissions
        $query->where(function($q) use ($user) {
            $q->where('permissionables.permissionable_id', $user->id)
              ->where('permissionables.permissionable_type', $user->getMorphClass());
        });
        // Product permissions
        if ($product) {
            $query->orWhere(function($q) use ($product) {
                $q->where('permissionables.permissionable_id', $product->id)
                  ->where('permissionables.permissionable_type', Product::class);
            });
        }
    })
    ->whereIn('permissions.name', ['projects.create', 'custom_domains.create'])
    ->select('permissions.name', 'permissionables.permissionable_type', 'permissionables.permissionable_id', 'permissionables.restrictions')
    ->get();

foreach ($permissionables as $pa) {
    echo "Permission: {$pa->name}\n";
    echo "  Type: {$pa->permissionable_type}\n";
    echo "  ID: {$pa->permissionable_id}\n";
    echo "  Restrictions: {$pa->restrictions}\n";
}

echo "\n" . str_repeat('=', 80) . "\n";

