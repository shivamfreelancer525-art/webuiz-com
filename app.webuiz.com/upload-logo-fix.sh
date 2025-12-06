#!/bin/bash

# Upload logo size fix to draggify.com server
# Run: ./upload-logo-fix.sh

set -e

GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m'

SERVER_IP="72.61.146.226"
SERVER_USER="root"
DRAGGIFY_APP_DIR="/home/draggify.com/app.draggify.com"

echo -e "${YELLOW}📤 Uploading logo fix to draggify.com...${NC}"

# Upload code files
echo -e "${YELLOW}1. Uploading code files...${NC}"
scp common/resources/client/auth/ui/auth-layout/auth-layout.tsx \
    ${SERVER_USER}@${SERVER_IP}:${DRAGGIFY_APP_DIR}/common/resources/client/auth/ui/auth-layout/

scp common/resources/client/auth/ui/email-verification-page/email-verification-page.tsx \
    ${SERVER_USER}@${SERVER_IP}:${DRAGGIFY_APP_DIR}/common/resources/client/auth/ui/email-verification-page/

# Upload SVG fix
scp common/resources/client/background-selector/image-backgrounds.ts \
    ${SERVER_USER}@${SERVER_IP}:${DRAGGIFY_APP_DIR}/common/resources/client/background-selector/

# Upload navbar logo fix
scp common/resources/client/ui/navigation/navbar/logo.tsx \
    ${SERVER_USER}@${SERVER_IP}:${DRAGGIFY_APP_DIR}/common/resources/client/ui/navigation/navbar/

echo -e "${GREEN}✅ Code files uploaded${NC}\n"

# Upload build assets
echo -e "${YELLOW}2. Uploading build assets...${NC}"
rsync -avz --progress --delete \
    public/build/ \
    ${SERVER_USER}@${SERVER_IP}:${DRAGGIFY_APP_DIR}/public/build/

echo -e "${GREEN}✅ Build assets uploaded${NC}\n"

# Fix permissions and clear caches
echo -e "${YELLOW}3. Fixing permissions and clearing caches...${NC}"
ssh ${SERVER_USER}@${SERVER_IP} << 'EOF'
cd /home/draggify.com/app.draggify.com
chown -R dragg7705:dragg7705 public/build
chown -R dragg7705:dragg7705 common/resources/client/auth/ui/auth-layout/auth-layout.tsx
chown -R dragg7705:dragg7705 common/resources/client/auth/ui/email-verification-page/email-verification-page.tsx
chown -R dragg7705:dragg7705 common/resources/client/background-selector/image-backgrounds.ts
chown -R dragg7705:dragg7705 common/resources/client/ui/navigation/navbar/logo.tsx
chmod -R 755 public/build
php artisan view:clear
php artisan cache:clear
EOF

echo -e "${GREEN}✅ Permissions fixed and caches cleared${NC}\n"

echo -e "${GREEN}╔══════════════════════════════════════════════════════════════╗${NC}"
echo -e "${GREEN}║           ✅ LOGO FIX UPLOADED!                              ║${NC}"
echo -e "${GREEN}╚══════════════════════════════════════════════════════════════╝${NC}"
echo ""
echo "Visit: https://app.draggify.com/login"
echo "Logo should now be 60px (increased from 42px)"

