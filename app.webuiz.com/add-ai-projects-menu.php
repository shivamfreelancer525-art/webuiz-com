<?php

/**
 * Run this script to add "AI Projects" tab to the dashboard menu
 * Usage: php add-ai-projects-menu.php
 */

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Adding AI Projects menu to dashboard...\n";

// Get current menus setting
$menusSetting = DB::table('settings')->where('name', 'menus')->first();

if (!$menusSetting) {
    echo "ERROR: Menu settings not found in database\n";
    exit(1);
}

$menus = json_decode($menusSetting->value, true);

// Find the Dashboard menu
$dashboardMenuIndex = null;
foreach ($menus as $index => $menu) {
    if (isset($menu['positions']) && in_array('dashboard', $menu['positions'])) {
        $dashboardMenuIndex = $index;
        break;
    }
}

if ($dashboardMenuIndex === null) {
    echo "ERROR: Dashboard menu not found\n";
    exit(1);
}

// Check if AI Projects already exists
$aiProjectsExists = false;
foreach ($menus[$dashboardMenuIndex]['items'] as $item) {
    if (isset($item['action']) && $item['action'] === '/dashboard/ai-projects') {
        $aiProjectsExists = true;
        break;
    }
}

if ($aiProjectsExists) {
    echo "AI Projects menu already exists. No changes needed.\n";
    exit(0);
}

// Find position to insert (after Projects, before Branded domains)
$insertPosition = null;
$newItems = [];

foreach ($menus[$dashboardMenuIndex]['items'] as $index => $item) {
    $newItems[] = $item;
    
    // Insert AI Projects after Projects (which links to /dashboard)
    if (isset($item['action']) && $item['action'] === '/dashboard') {
        $newItems[] = [
            'type' => 'route',
            'position' => count($newItems),
            'label' => 'AI Projects',
            'action' => '/dashboard/ai-projects',
            'id' => 236,
        ];
    }
}

// Update positions
foreach ($newItems as $index => &$item) {
    $item['position'] = $index;
}

$menus[$dashboardMenuIndex]['items'] = $newItems;

// Save back to database
$updated = DB::table('settings')
    ->where('name', 'menus')
    ->update(['value' => json_encode($menus)]);

if ($updated) {
    echo "SUCCESS: AI Projects menu added!\n";
    echo "\nNew menu structure:\n";
    foreach ($menus[$dashboardMenuIndex]['items'] as $item) {
        if (!isset($item['permissions']) || empty($item['permissions'])) {
            echo "  - {$item['label']} => {$item['action']}\n";
        }
    }
} else {
    echo "ERROR: Failed to update database\n";
    exit(1);
}

echo "\nPlease refresh your browser to see the changes.\n";
