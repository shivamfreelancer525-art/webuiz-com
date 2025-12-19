#!/bin/bash

# Deploy Project Link Fix to Server
# This script uploads the updated files that make project links open in new tabs

SERVER="root@72.61.146.226"
SERVER_PATH="/home/draggify.com/app.draggify.com"

echo "=========================================="
echo "Deploying Project Link Fix to Server"
echo "=========================================="
echo ""

# 1. Upload dashboard-page.tsx
echo "1. Uploading dashboard-page.tsx..."
scp app.webuiz.com/resources/client/dashboard/dashboard-page.tsx $SERVER:$SERVER_PATH/resources/client/dashboard/
if [ $? -eq 0 ]; then
    echo "   ✓ dashboard-page.tsx uploaded"
else
    echo "   ✗ Failed to upload dashboard-page.tsx"
    exit 1
fi

echo ""

# 2. Upload project-link.tsx
echo "2. Uploading project-link.tsx..."
scp app.webuiz.com/resources/client/projects/project-link.tsx $SERVER:$SERVER_PATH/resources/client/projects/
if [ $? -eq 0 ]; then
    echo "   ✓ project-link.tsx uploaded"
else
    echo "   ✗ Failed to upload project-link.tsx"
    exit 1
fi

echo ""
echo "=========================================="
echo "Files uploaded successfully!"
echo "=========================================="
echo ""
echo "Next steps on server:"
echo "1. SSH to server: ssh $SERVER"
echo "2. Navigate to: cd $SERVER_PATH"
echo "3. Rebuild React assets: npm run build"
echo "4. Clear caches: php artisan view:clear && php artisan config:clear"
echo ""

