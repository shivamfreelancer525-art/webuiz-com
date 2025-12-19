#!/bin/bash

# Script to verify all changes are on the server
# Run: ssh root@72.61.146.226 "bash -s" < verify-server-changes.sh

SERVER="root@72.61.146.226"
SERVER_PATH="/home/draggify.com/app.draggify.com"

echo "=========================================="
echo "Verifying Changes on Server"
echo "=========================================="
echo ""

echo "1. Checking if PolicyPageController exists..."
ssh ${SERVER} "test -f ${SERVER_PATH}/app/Http/Controllers/PolicyPageController.php && echo '✓ PolicyPageController.php exists' || echo '✗ PolicyPageController.php NOT FOUND'"

echo ""
echo "2. Checking if routes/web.php has privacy-policy route..."
ssh ${SERVER} "grep -q 'privacy-policy.*PolicyPageController' ${SERVER_PATH}/routes/web.php && echo '✓ Privacy policy route found' || echo '✗ Privacy policy route NOT FOUND'"

echo ""
echo "3. Checking if marketing Blade templates exist..."
ssh ${SERVER} "test -f ${SERVER_PATH}/resources/views/marketing/custom-page.blade.php && echo '✓ custom-page.blade.php exists' || echo '✗ custom-page.blade.php NOT FOUND'"
ssh ${SERVER} "test -f ${SERVER_PATH}/resources/views/marketing/header.blade.php && echo '✓ header.blade.php exists' || echo '✗ header.blade.php NOT FOUND'"
ssh ${SERVER} "test -f ${SERVER_PATH}/resources/views/marketing/footer.blade.php && echo '✓ footer.blade.php exists' || echo '✗ footer.blade.php NOT FOUND'"

echo ""
echo "4. Checking if React components are updated..."
ssh ${SERVER} "grep -q 'contact-us-page' ${SERVER_PATH}/common/resources/client/custom-page/custom-page-layout.tsx && echo '✓ custom-page-layout.tsx updated' || echo '✗ custom-page-layout.tsx NOT UPDATED'"

echo ""
echo "5. Checking database footer menu links..."
ssh ${SERVER} "cd ${SERVER_PATH} && php artisan tinker --execute=\"
\\\$menus = \\\Common\\\Settings\\\Setting::where('name', 'menus')->first();
if (\\\$menus) {
    \\\$value = is_string(\\\$menus->value) ? json_decode(\\\$menus->value, true) : \\\$menus->value;
    \\\$footer = collect(\\\$value)->firstWhere('name', 'Footer');
    if (\\\$footer) {
        \\\$privacy = collect(\\\$footer['items'])->firstWhere('label', 'Privacy Policy');
        \\\$terms = collect(\\\$footer['items'])->firstWhere(function(\\\$item) { return in_array(\\\$item['label'], ['Terms & Conditions', 'Terms of Service']); });
        echo 'Privacy Policy link: ' . (\\\$privacy ? \\\$privacy['action'] : 'NOT FOUND') . PHP_EOL;
        echo 'Terms & Conditions link: ' . (\\\$terms ? \\\$terms['action'] : 'NOT FOUND') . PHP_EOL;
        if (\\\$privacy && \\\$privacy['action'] === '/privacy-policy' && \\\$terms && \\\$terms['action'] === '/terms-and-conditions') {
            echo '✓ Footer menu links are correct' . PHP_EOL;
        } else {
            echo '✗ Footer menu links need updating' . PHP_EOL;
        }
    } else {
        echo '✗ Footer menu not found' . PHP_EOL;
    }
} else {
    echo '✗ Menus setting not found' . PHP_EOL;
}
\""

echo ""
echo "6. Checking if privacy-policy page exists in database..."
ssh ${SERVER} "cd ${SERVER_PATH} && php artisan tinker --execute=\"
\\\$page = \\\Common\\\Pages\\\CustomPage::where('slug', 'privacy-policy')->first();
if (\\\$page) {
    echo '✓ Privacy Policy page exists (ID: ' . \\\$page->id . ', Title: ' . \\\$page->title . ')' . PHP_EOL;
} else {
    echo '✗ Privacy Policy page NOT FOUND in database' . PHP_EOL;
}
\""

echo ""
echo "7. Checking if terms-and-conditions page exists in database..."
ssh ${SERVER} "cd ${SERVER_PATH} && php artisan tinker --execute=\"
\\\$page = \\\Common\\\Pages\\\CustomPage::where('slug', 'terms-and-conditions')->orWhere('slug', 'terms-of-service')->first();
if (\\\$page) {
    echo '✓ Terms & Conditions page exists (ID: ' . \\\$page->id . ', Slug: ' . \\\$page->slug . ', Title: ' . \\\$page->title . ')' . PHP_EOL;
} else {
    echo '✗ Terms & Conditions page NOT FOUND in database' . PHP_EOL;
}
\""

echo ""
echo "=========================================="
echo "Verification Complete!"
echo "=========================================="

