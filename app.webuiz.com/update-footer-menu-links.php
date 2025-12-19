<?php

/**
 * Script to update footer menu links in database
 * Run: php update-footer-menu-links.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Common\Settings\Setting;

$menusSetting = Setting::where('name', 'menus')->first();

if (!$menusSetting) {
    echo "Menus setting not found!\n";
    exit(1);
}

// Handle both JSON string and array formats
if (is_string($menusSetting->value)) {
    $menus = json_decode($menusSetting->value, true);
    if (!$menus) {
        echo "Could not decode menus JSON!\n";
        exit(1);
    }
} elseif (is_array($menusSetting->value)) {
    $menus = $menusSetting->value;
} else {
    echo "Menus value is in unexpected format!\n";
    exit(1);
}

$updated = false;

foreach ($menus as &$menu) {
    if ($menu['name'] === 'Footer' && isset($menu['items'])) {
        foreach ($menu['items'] as &$item) {
            // Update Privacy Policy link
            if ($item['label'] === 'Privacy Policy') {
                if ($item['action'] === '/pages/privacy-policy' || 
                    strpos($item['action'], 'webuiz.com') !== false ||
                    strpos($item['action'], 'privacy-policy') !== false) {
                    $item['action'] = '/privacy-policy';
                    $item['type'] = 'route'; // Ensure it's a route, not a link
                    $updated = true;
                    echo "Updated Privacy Policy link to: /privacy-policy (type: route)\n";
                }
            }
            
            // Update Terms & Conditions link
            if ($item['label'] === 'Terms of Service' || $item['label'] === 'Terms & Conditions') {
                if ($item['action'] === '/pages/terms-of-service' || 
                    $item['action'] === '/pages/terms-and-conditions' ||
                    strpos($item['action'], 'webuiz.com') !== false ||
                    strpos($item['action'], 'terms') !== false) {
                    $item['action'] = '/terms-and-conditions';
                    $item['label'] = 'Terms & Conditions';
                    $item['type'] = 'route'; // Ensure it's a route, not a link
                    $updated = true;
                    echo "Updated Terms & Conditions link to: /terms-and-conditions (type: route)\n";
                }
            }
        }
    }
}

if ($updated) {
    $menusSetting->value = json_encode($menus);
    $menusSetting->save();
    echo "\nFooter menu links updated successfully!\n";
} else {
    echo "\nNo updates needed. Links are already correct.\n";
}

