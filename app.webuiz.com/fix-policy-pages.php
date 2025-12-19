<?php

/**
 * Script to fix policy pages in database
 * - Update terms-of-service to terms-and-conditions if needed
 * - Verify pages exist with correct content
 * Run: php fix-policy-pages.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Common\Pages\CustomPage;

echo "==========================================\n";
echo "Fixing Policy Pages in Database\n";
echo "==========================================\n\n";

// 1. Check and fix Privacy Policy
echo "1. Checking Privacy Policy page...\n";
$privacy = CustomPage::where('slug', 'privacy-policy')->first();

if (!$privacy) {
    echo "   ✗ Privacy Policy page NOT FOUND - creating...\n";
    $privacy = CustomPage::create([
        'title' => 'Privacy Policy',
        'slug' => 'privacy-policy',
        'body' => '<p>Privacy Policy content will be updated.</p>',
        'type' => 'default',
    ]);
    echo "   ✓ Privacy Policy page created (ID: {$privacy->id})\n";
} else {
    echo "   ✓ Privacy Policy page exists (ID: {$privacy->id})\n";
    echo "   Title: {$privacy->title}\n";
    echo "   Type: {$privacy->type}\n";
    
    // Check if content mentions webuiz or architect
    if (strpos($privacy->body, 'webuiz.com') !== false || strpos($privacy->body, 'Architect') !== false) {
        echo "   ⚠ Content contains old references (webuiz.com or Architect)\n";
        echo "   Note: Content should be updated from webuiz privacy policy\n";
    }
}

echo "\n";

// 2. Check and fix Terms & Conditions
echo "2. Checking Terms & Conditions page...\n";
$terms = CustomPage::where('slug', 'terms-and-conditions')
    ->orWhere('slug', 'terms-of-service')
    ->first();

if (!$terms) {
    echo "   ✗ Terms & Conditions page NOT FOUND - creating...\n";
    $terms = CustomPage::create([
        'title' => 'Terms & Conditions',
        'slug' => 'terms-and-conditions',
        'body' => '<p>Terms & Conditions content will be updated.</p>',
        'type' => 'default',
    ]);
    echo "   ✓ Terms & Conditions page created (ID: {$terms->id})\n";
} else {
    echo "   ✓ Terms page exists (ID: {$terms->id}, Slug: {$terms->slug})\n";
    
    // Update slug if it's terms-of-service
    if ($terms->slug === 'terms-of-service') {
        echo "   Updating slug from 'terms-of-service' to 'terms-and-conditions'...\n";
        $terms->slug = 'terms-and-conditions';
        $terms->save();
        echo "   ✓ Slug updated\n";
    }
    
    // Update title if needed
    if ($terms->title !== 'Terms & Conditions') {
        echo "   Updating title to 'Terms & Conditions'...\n";
        $terms->title = 'Terms & Conditions';
        $terms->save();
        echo "   ✓ Title updated\n";
    }
    
    echo "   Title: {$terms->title}\n";
    echo "   Type: {$terms->type}\n";
    
    // Check if content mentions webuiz or architect
    if (strpos($terms->body, 'webuiz.com') !== false || strpos($terms->body, 'Architect') !== false) {
        echo "   ⚠ Content contains old references (webuiz.com or Architect)\n";
        echo "   Note: Content should be updated from webuiz terms and conditions\n";
    }
}

echo "\n";

// 3. Verify both pages are set as default type
echo "3. Verifying page types...\n";
if ($privacy->type !== 'default') {
    $privacy->type = 'default';
    $privacy->save();
    echo "   ✓ Privacy Policy type set to 'default'\n";
} else {
    echo "   ✓ Privacy Policy type is correct\n";
}

if ($terms->type !== 'default') {
    $terms->type = 'default';
    $terms->save();
    echo "   ✓ Terms & Conditions type set to 'default'\n";
} else {
    echo "   ✓ Terms & Conditions type is correct\n";
}

echo "\n==========================================\n";
echo "Fix Complete!\n";
echo "==========================================\n";
echo "\nNote: If content still shows old references, you may need to:\n";
echo "1. Update the page content from the webuiz privacy-policy.blade.php\n";
echo "2. Replace 'webuiz' with 'draggify' and 'Webuiz' with 'Draggify'\n";
echo "3. Replace 'Architect' with 'Draggify'\n";

