#!/bin/bash

# Deployment script for Privacy Policy & Terms & Conditions updates
# Server: root@72.61.146.226
# Path: /home/draggify.com/app.draggify.com

echo "=========================================="
echo "Deploying Privacy Policy & Terms Updates"
echo "=========================================="

# Server details
SERVER="root@72.61.146.226"
SERVER_PATH="/home/draggify.com/app.draggify.com"
LOCAL_PATH="app.webuiz.com"

echo ""
echo "Step 1: Syncing files to server..."
rsync -avz --exclude 'node_modules' --exclude 'vendor' --exclude '.git' \
  --exclude 'storage/logs/*' --exclude 'storage/framework/cache/*' \
  --exclude 'storage/framework/sessions/*' --exclude 'storage/framework/views/*' \
  ${LOCAL_PATH}/ ${SERVER}:${SERVER_PATH}/

echo ""
echo "Step 2: Running composer install on server..."
ssh ${SERVER} "cd ${SERVER_PATH} && composer install --no-dev --optimize-autoloader"

echo ""
echo "Step 3: Installing npm dependencies and building assets..."
ssh ${SERVER} "cd ${SERVER_PATH} && npm install && npm run build"

echo ""
echo "Step 4: Clearing Laravel caches..."
ssh ${SERVER} "cd ${SERVER_PATH} && php artisan config:clear && php artisan route:clear && php artisan view:clear && php artisan cache:clear"

echo ""
echo "Step 5: Verifying database pages..."
ssh ${SERVER} "cd ${SERVER_PATH} && php artisan tinker --execute=\"echo 'Privacy Policy: ' . (\\Common\\Pages\\CustomPage::where('slug', 'privacy-policy')->exists() ? 'EXISTS' : 'MISSING') . PHP_EOL; echo 'Terms & Conditions: ' . (\\Common\\Pages\\CustomPage::where('slug', 'terms-and-conditions')->orWhere('slug', 'terms-of-service')->exists() ? 'EXISTS' : 'MISSING') . PHP_EOL;\""

echo ""
echo "=========================================="
echo "Deployment Complete!"
echo "=========================================="
echo ""
echo "Please verify:"
echo "1. Privacy Policy page: https://draggify.com/privacy-policy"
echo "2. Terms & Conditions page: https://draggify.com/terms-and-conditions"
echo "3. Test as logged-in user and guest user"

