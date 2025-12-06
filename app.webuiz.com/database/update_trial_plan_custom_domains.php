<?php
/**
 * Script to update Trial Plan to add custom_domains.create restriction (count: 1)
 * 
 * Run this with: php artisan tinker < database/update_trial_plan_custom_domains.php
 * Or: php -r "require 'vendor/autoload.php'; \$app = require_once 'bootstrap/app.php'; \$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap(); [rest of script]"
 */

use Common\Auth\Permissions\Permission;
use Common\Billing\Models\Product;
use Illuminate\Support\Facades\DB;

// Find Trial Plan (free product)
$trialPlan = Product::where('free', true)->first();

if (!$trialPlan) {
    echo "Error: Trial Plan (free product) not found!\n";
    exit(1);
}

echo "Found Trial Plan: {$trialPlan->name} (ID: {$trialPlan->id})\n";

// Find custom_domains.create permission
$permission = Permission::where('name', 'custom_domains.create')->first();

if (!$permission) {
    echo "Error: custom_domains.create permission not found!\n";
    exit(1);
}

echo "Found permission: {$permission->name} (ID: {$permission->id})\n";

// Check if permission already exists for this product
$existing = DB::table('permissionables')
    ->where('permissionable_id', $trialPlan->id)
    ->where('permissionable_type', Product::class)
    ->where('permission_id', $permission->id)
    ->first();

if ($existing) {
    // Update existing restriction
    $restrictions = json_decode($existing->restrictions ?? '[]', true);
    $countRestriction = ['name' => 'count', 'value' => 1];
    
    // Remove existing count restriction if any
    $restrictions = array_filter($restrictions, fn($r) => $r['name'] !== 'count');
    $restrictions[] = $countRestriction;
    
    DB::table('permissionables')
        ->where('permissionable_id', $trialPlan->id)
        ->where('permissionable_type', Product::class)
        ->where('permission_id', $permission->id)
        ->update(['restrictions' => json_encode(array_values($restrictions))]);
    
    echo "Updated existing permission with restriction: count = 1\n";
} else {
    // Create new permission link
    DB::table('permissionables')->insert([
        'permissionable_id' => $trialPlan->id,
        'permissionable_type' => Product::class,
        'permission_id' => $permission->id,
        'restrictions' => json_encode([['name' => 'count', 'value' => 1]]),
    ]);
    
    echo "Added new permission with restriction: count = 1\n";
}

echo "Successfully updated Trial Plan!\n";
echo "Trial Plan users can now create maximum 1 custom domain.\n";


