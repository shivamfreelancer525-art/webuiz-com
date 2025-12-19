<?php

/**
 * Script to update privacy policy and terms & conditions content from webuiz
 * Run: php update-policy-content-from-webuiz.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Common\Pages\CustomPage;

echo "==========================================\n";
echo "Updating Policy Pages Content from Webuiz\n";
echo "==========================================\n\n";

// Path to webuiz blade files
$webuizBasePath = __DIR__ . '/../public_html/resources/views';
$privacyBladePath = $webuizBasePath . '/privacy-policy.blade.php';
$termsBladePath = $webuizBasePath . '/terms-and-conditions.blade.php';

// Function to extract content from blade file
function extractContentFromBlade($filePath) {
    if (!file_exists($filePath)) {
        echo "   ✗ File not found: $filePath\n";
        return null;
    }
    
    $content = file_get_contents($filePath);
    
    // Extract content between <section class="pt-130..."> and </section>
    // This is the main content section
    if (preg_match('/<section class="pt-130[^"]*"[^>]*>\s*<div class="container">\s*(.*?)\s*<\/div>\s*<\/section>/s', $content, $matches)) {
        $htmlContent = $matches[1];
        
        // Replace webuiz with draggify (case-insensitive)
        $htmlContent = preg_replace('/webuiz\.com/i', 'draggify.com', $htmlContent);
        $htmlContent = preg_replace('/Webuiz/i', 'Draggify', $htmlContent);
        $htmlContent = preg_replace('/webuiz/i', 'draggify', $htmlContent);
        $htmlContent = preg_replace('/Architect/i', 'Draggify', $htmlContent);
        $htmlContent = preg_replace('/https:\/\/app\.webuiz\.com/i', 'https://app.draggify.com', $htmlContent);
        
        return trim($htmlContent);
    }
    
    echo "   ✗ Could not extract content from blade file\n";
    return null;
}

// 1. Update Privacy Policy
echo "1. Updating Privacy Policy content...\n";
$privacy = CustomPage::where('slug', 'privacy-policy')->first();

if (!$privacy) {
    echo "   ✗ Privacy Policy page not found in database!\n";
    exit(1);
}

$privacyContent = extractContentFromBlade($privacyBladePath);
if ($privacyContent) {
    $privacy->body = $privacyContent;
    $privacy->title = 'Privacy Policy'; // Ensure title is correct
    $privacy->type = 'default';
    $privacy->save();
    echo "   ✓ Privacy Policy content updated\n";
    echo "   Content length: " . strlen($privacyContent) . " characters\n";
} else {
    echo "   ✗ Failed to extract Privacy Policy content\n";
}

echo "\n";

// 2. Update Terms & Conditions
echo "2. Updating Terms & Conditions content...\n";
$terms = CustomPage::where('slug', 'terms-and-conditions')
    ->orWhere('slug', 'terms-of-service')
    ->first();

if (!$terms) {
    echo "   ✗ Terms & Conditions page not found in database!\n";
    exit(1);
}

$termsContent = extractContentFromBlade($termsBladePath);
if ($termsContent) {
    $terms->body = $termsContent;
    $terms->title = 'Terms & Conditions';
    $terms->slug = 'terms-and-conditions'; // Ensure slug is correct
    $terms->type = 'default';
    $terms->save();
    echo "   ✓ Terms & Conditions content updated\n";
    echo "   Content length: " . strlen($termsContent) . " characters\n";
} else {
    echo "   ✗ Failed to extract Terms & Conditions content\n";
}

echo "\n==========================================\n";
echo "Content Update Complete!\n";
echo "==========================================\n";

