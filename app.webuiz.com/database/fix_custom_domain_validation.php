<?php
/**
 * Comprehensive script to check and fix custom domain validation
 * Checks database structure, user products, and restrictions
 * 
 * Run: php database/fix_custom_domain_validation.php
 */

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Common\Billing\Models\Product;
use Common\Auth\Permissions\Permission;
use Illuminate\Support\Facades\DB;

echo "=== CUSTOM DOMAIN VALIDATION DIAGNOSTIC ===\n\n";

// 1. Check all products and their permissions
echo "1. CHECKING PRODUCTS AND PERMISSIONS\n";
echo str_repeat('-', 80) . "\n";

$products = Product::all();
echo "Found {$products->count()} product(s)\n\n";

foreach ($products as $product) {
    echo "Product: {$product->name} (ID: {$product->id}, Free: " . ($product->free ? 'YES' : 'NO') . ")\n";
    
    // Get projects.create permission
    $projectsPerm = Permission::where('name', 'projects.create')->first();
    if ($projectsPerm) {
        $projectsPa = DB::table('permissionables')
            ->where('permissionable_id', $product->id)
            ->where('permissionable_type', Product::class)
            ->where('permission_id', $projectsPerm->id)
            ->first();
        
        if ($projectsPa) {
            $restrictions = json_decode($projectsPa->restrictions ?? '[]', true);
            $countRestriction = collect($restrictions)->firstWhere('name', 'count');
            $maxWebsites = $countRestriction['value'] ?? null;
            echo "  projects.create count: " . ($maxWebsites ?? 'NULL') . "\n";
        } else {
            echo "  projects.create: NOT FOUND\n";
        }
    }
    
    // Get custom_domains.create permission
    $customDomainPerm = Permission::where('name', 'custom_domains.create')->first();
    if ($customDomainPerm) {
        $customDomainPa = DB::table('permissionables')
            ->where('permissionable_id', $product->id)
            ->where('permissionable_type', Product::class)
            ->where('permission_id', $customDomainPerm->id)
            ->first();
        
        if ($customDomainPa) {
            $restrictions = json_decode($customDomainPa->restrictions ?? '[]', true);
            $countRestriction = collect($restrictions)->firstWhere('name', 'count');
            $maxDomains = $countRestriction['value'] ?? null;
            echo "  custom_domains.create count: " . ($maxDomains ?? 'NULL') . "\n";
        } else {
            echo "  custom_domains.create: NOT FOUND\n";
        }
    }
    
    echo "\n";
}

// 2. Check sample users
echo "\n2. CHECKING SAMPLE USERS\n";
echo str_repeat('-', 80) . "\n";

$testEmails = ['webuiz25@gmail.com'];
$users = User::whereIn('email', $testEmails)->get();

if ($users->isEmpty()) {
    echo "No test users found. Checking first 3 users...\n";
    $users = User::limit(3)->get();
}

foreach ($users as $user) {
    echo "\nUser: {$user->email} (ID: {$user->id})\n";
    
    // Get subscription product
    $product = $user->getSubscriptionProduct();
    if ($product) {
        echo "  Product: {$product->name} (ID: {$product->id})\n";
    } else {
        echo "  Product: NULL (billing disabled or no subscription)\n";
    }
    
    // Load permissions
    $user->loadPermissions();
    
    // Get restriction values
    $maxWebsites = $user->getRestrictionValue('projects.create', 'count');
    $maxCustomDomains = $user->getRestrictionValue('custom_domains.create', 'count');
    
    echo "  projects.create count: " . ($maxWebsites ?? 'NULL') . "\n";
    echo "  custom_domains.create count: " . ($maxCustomDomains ?? 'NULL') . "\n";
    
    // Get current counts
    $currentProjects = $user->projects()->count();
    $currentDomains = $user->customDomains()->count();
    
    echo "  Current Projects: $currentProjects\n";
    echo "  Current Domains: $currentDomains\n";
    
    // Validation check
    if ($maxWebsites !== null) {
        $canAdd = $currentDomains < $maxWebsites;
        echo "  Can Add Domain: " . ($canAdd ? 'YES' : 'NO') . "\n";
        if (!$canAdd) {
            echo "  ⚠️  LIMIT REACHED - Validation should block\n";
        }
    } else {
        echo "  Can Add Domain: UNLIMITED (no restriction)\n";
    }
}

// 3. Check permissionables table structure
echo "\n\n3. PERMISSIONABLES TABLE STRUCTURE\n";
echo str_repeat('-', 80) . "\n";

$sample = DB::table('permissionables')
    ->join('permissions', 'permissionables.permission_id', '=', 'permissions.id')
    ->whereIn('permissions.name', ['projects.create', 'custom_domains.create'])
    ->limit(5)
    ->get(['permissions.name', 'permissionables.permissionable_type', 'permissionables.restrictions']);

foreach ($sample as $row) {
    echo "Permission: {$row->name}\n";
    echo "  Type: {$row->permissionable_type}\n";
    echo "  Restrictions: {$row->restrictions}\n\n";
}

// 4. Recommendations
echo "\n4. RECOMMENDATIONS\n";
echo str_repeat('-', 80) . "\n";

$freeProduct = Product::where('free', true)->first();
if ($freeProduct) {
    $projectsPerm = Permission::where('name', 'projects.create')->first();
    if ($projectsPerm) {
        $pa = DB::table('permissionables')
            ->where('permissionable_id', $freeProduct->id)
            ->where('permissionable_type', Product::class)
            ->where('permission_id', $projectsPerm->id)
            ->first();
        
        if ($pa) {
            $restrictions = json_decode($pa->restrictions ?? '[]', true);
            $countRestriction = collect($restrictions)->firstWhere('name', 'count');
            $maxWebsites = $countRestriction['value'] ?? null;
            
            if ($maxWebsites === null) {
                echo "⚠️  Free product has no projects.create count restriction!\n";
                echo "   Users without subscriptions won't have website limits.\n";
            } else {
                echo "✅ Free product has projects.create count: $maxWebsites\n";
                echo "   Custom domain limit should match this.\n";
            }
        } else {
            echo "❌ Free product missing projects.create permission!\n";
        }
    }
}

echo "\n" . str_repeat('=', 80) . "\n";
echo "✅ Diagnostic complete!\n";

