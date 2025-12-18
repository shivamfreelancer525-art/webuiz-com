<?php
/**
 * Script to update reCAPTCHA keys in database
 * 
 * Usage: php update-recaptcha-keys.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Common\Settings\Setting;
use Illuminate\Support\Facades\Crypt;

// Your reCAPTCHA keys
$siteKey = '6LffXQIsAAAAANYBiKUdrhMBJU9O9aK_t3RJ8BqN';
$secretKey = '6LffXQIsAAAAABvdx_hxO_gd_GLPHs8elrnAf5Yd';

echo "Updating reCAPTCHA keys in database...\n\n";

// Update site key
$siteKeySetting = Setting::firstOrNew(['name' => 'recaptcha.site_key']);
$siteKeySetting->value = $siteKey;
$siteKeySetting->save();
echo "✓ Site Key updated: {$siteKey}\n";

// Update secret key (will be encrypted automatically)
$secretKeySetting = Setting::firstOrNew(['name' => 'recaptcha.secret_key']);
$secretKeySetting->value = $secretKey;
$secretKeySetting->save();
echo "✓ Secret Key updated (encrypted): {$secretKey}\n";

// Enable registration page reCAPTCHA
$enableRegister = Setting::firstOrNew(['name' => 'client.recaptcha.enable.register']);
$enableRegister->value = 'true';
$enableRegister->save();
echo "✓ Registration page reCAPTCHA enabled\n";

echo "\n✅ All keys updated successfully!\n";
echo "\nNext steps:\n";
echo "1. Clear cache: php artisan config:clear && php artisan cache:clear\n";
echo "2. Test registration at /register\n";

