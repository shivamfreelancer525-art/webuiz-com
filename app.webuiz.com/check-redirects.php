<?php

/**
 * Script to check for redirects and verify routes
 * Run: php check-redirects.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Route;
use Common\Settings\Setting;

echo "==========================================\n";
echo "Checking Routes and Redirects\n";
echo "==========================================\n\n";

// 1. Check routes
echo "1. Checking registered routes:\n";
$routes = Route::getRoutes();
$foundPrivacy = false;
$foundTerms = false;

foreach ($routes as $route) {
    $uri = $route->uri();
    $action = $route->getActionName();
    
    if (strpos($uri, 'privacy-policy') !== false) {
        echo "   ✓ Privacy Policy route: {$uri} -> {$action}\n";
        $foundPrivacy = true;
    }
    
    if (strpos($uri, 'terms-and-conditions') !== false || strpos($uri, 'terms-of-service') !== false) {
        echo "   ✓ Terms route: {$uri} -> {$action}\n";
        $foundTerms = true;
    }
}

if (!$foundPrivacy) {
    echo "   ✗ Privacy Policy route NOT FOUND\n";
}
if (!$foundTerms) {
    echo "   ✗ Terms route NOT FOUND\n";
}

echo "\n";

// 2. Check footer menu configuration
echo "2. Checking footer menu configuration:\n";
$menusSetting = Setting::where('name', 'menus')->first();

if ($menusSetting) {
    $value = is_string($menusSetting->value) ? json_decode($menusSetting->value, true) : $menusSetting->value;
    $footer = collect($value)->firstWhere('name', 'Footer');
    
    if ($footer) {
        foreach ($footer['items'] as $item) {
            if (strpos($item['label'], 'Privacy') !== false || strpos($item['label'], 'Terms') !== false) {
                echo "   Menu Item: {$item['label']}\n";
                echo "      Type: {$item['type']}\n";
                echo "      Action: {$item['action']}\n";
                
                if ($item['type'] === 'link' && strpos($item['action'], 'webuiz.com') !== false) {
                    echo "      ✗ WARNING: Using 'link' type with webuiz.com URL!\n";
                } elseif ($item['type'] === 'route' && ($item['action'] === '/privacy-policy' || $item['action'] === '/terms-and-conditions')) {
                    echo "      ✓ Correct route type and path\n";
                } else {
                    echo "      ⚠ Check if this is correct\n";
                }
            }
        }
    } else {
        echo "   ✗ Footer menu not found\n";
    }
} else {
    echo "   ✗ Menus setting not found\n";
}

echo "\n";

// 3. Check if pages exist
echo "3. Checking database pages:\n";
$privacy = \Common\Pages\CustomPage::where('slug', 'privacy-policy')->first();
$terms = \Common\Pages\CustomPage::where('slug', 'terms-and-conditions')->orWhere('slug', 'terms-of-service')->first();

if ($privacy) {
    echo "   ✓ Privacy Policy page exists (ID: {$privacy->id}, Slug: {$privacy->slug})\n";
} else {
    echo "   ✗ Privacy Policy page NOT FOUND\n";
}

if ($terms) {
    echo "   ✓ Terms page exists (ID: {$terms->id}, Slug: {$terms->slug})\n";
} else {
    echo "   ✗ Terms page NOT FOUND\n";
}

echo "\n";

// 4. Check .htaccess for redirects
echo "4. Checking .htaccess for redirects:\n";
$htaccessPath = public_path('.htaccess');
if (file_exists($htaccessPath)) {
    $htaccess = file_get_contents($htaccessPath);
    if (strpos($htaccess, 'webuiz.com') !== false || strpos($htaccess, 'privacy-policy') !== false) {
        echo "   ⚠ .htaccess contains webuiz.com or privacy-policy references\n";
        echo "   Check for redirect rules\n";
    } else {
        echo "   ✓ No webuiz.com redirects found in .htaccess\n";
    }
} else {
    echo "   ℹ .htaccess not found (this is OK)\n";
}

echo "\n==========================================\n";
echo "Check Complete!\n";
echo "==========================================\n";

