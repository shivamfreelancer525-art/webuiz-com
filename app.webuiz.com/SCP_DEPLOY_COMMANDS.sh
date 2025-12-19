#!/bin/bash

# SCP Commands to deploy Privacy Policy & Terms & Conditions updates
# Server: root@72.61.146.226
# Server Path: /home/draggify.com/app.draggify.com

SERVER="root@72.61.146.226"
SERVER_BASE="/home/draggify.com/app.draggify.com"
LOCAL_BASE="app.webuiz.com"

echo "=========================================="
echo "Uploading files to server via SCP"
echo "=========================================="

# 1. Upload PolicyPageController (NEW)
echo "1. Uploading PolicyPageController..."
scp ${LOCAL_BASE}/app/Http/Controllers/PolicyPageController.php ${SERVER}:${SERVER_BASE}/app/Http/Controllers/

# 2. Upload PagePolicy (MODIFIED)
echo "2. Uploading PagePolicy..."
scp ${LOCAL_BASE}/common/Core/Policies/PagePolicy.php ${SERVER}:${SERVER_BASE}/common/Core/Policies/

# 3. Upload routes/web.php (MODIFIED)
echo "3. Uploading routes..."
scp ${LOCAL_BASE}/routes/web.php ${SERVER}:${SERVER_BASE}/routes/

# 4. Upload React components
echo "4. Uploading React components..."
scp ${LOCAL_BASE}/common/resources/client/custom-page/custom-page-body.tsx ${SERVER}:${SERVER_BASE}/common/resources/client/custom-page/
scp ${LOCAL_BASE}/common/resources/client/custom-page/custom-page-body.css ${SERVER}:${SERVER_BASE}/common/resources/client/custom-page/
scp ${LOCAL_BASE}/common/resources/client/custom-page/custom-page-layout.tsx ${SERVER}:${SERVER_BASE}/common/resources/client/custom-page/

# 5. Upload app-routes.tsx
echo "5. Uploading app-routes..."
scp ${LOCAL_BASE}/resources/client/app-routes.tsx ${SERVER}:${SERVER_BASE}/resources/client/

# 6. Upload Blade templates (NEW)
echo "6. Uploading Blade templates..."
scp ${LOCAL_BASE}/resources/views/marketing/custom-page.blade.php ${SERVER}:${SERVER_BASE}/resources/views/marketing/
scp ${LOCAL_BASE}/resources/views/marketing/header.blade.php ${SERVER}:${SERVER_BASE}/resources/views/marketing/
scp ${LOCAL_BASE}/resources/views/marketing/footer.blade.php ${SERVER}:${SERVER_BASE}/resources/views/marketing/

echo ""
echo "=========================================="
echo "Files uploaded successfully!"
echo "=========================================="
echo ""
echo "Now run these commands on the server:"
echo "cd /home/draggify.com/app.draggify.com"
echo "npm install && npm run build"
echo "php artisan config:clear && php artisan route:clear && php artisan view:clear && php artisan cache:clear"

