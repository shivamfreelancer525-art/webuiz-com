#!/bin/bash

# Upload auth logo responsive fix to draggify.com server
# Run: ./upload-auth-logo-fix.sh

set -e

GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m'

SERVER_IP="72.61.146.226"
SERVER_USER="root"
DRAGGIFY_APP_DIR="/home/draggify.com/app.draggify.com"
PUBLIC_HTML_DIR="/home/draggify.com/public_html"

echo -e "${YELLOW}📤 Uploading auth logo responsive fix to draggify.com...${NC}"

# 1. Build frontend assets first
echo -e "${YELLOW}1. Building frontend assets...${NC}"
cd "$(dirname "$0")"
npm run build

echo -e "${GREEN}✅ Frontend built${NC}\n"

# 2. Upload Laravel view file (framework.blade.php)
echo -e "${YELLOW}2. Uploading framework.blade.php...${NC}"
scp common/resources/views/framework.blade.php \
    ${SERVER_USER}@${SERVER_IP}:${DRAGGIFY_APP_DIR}/common/resources/views/

echo -e "${GREEN}✅ framework.blade.php uploaded${NC}\n"

# 3. Upload React component files
echo -e "${YELLOW}3. Uploading React component files...${NC}"
scp common/resources/client/auth/ui/auth-layout/auth-layout.tsx \
    ${SERVER_USER}@${SERVER_IP}:${DRAGGIFY_APP_DIR}/common/resources/client/auth/ui/auth-layout/

scp common/resources/client/auth/ui/email-verification-page/email-verification-page.tsx \
    ${SERVER_USER}@${SERVER_IP}:${DRAGGIFY_APP_DIR}/common/resources/client/auth/ui/email-verification-page/

scp common/resources/client/ui/navigation/navbar/logo.tsx \
    ${SERVER_USER}@${SERVER_IP}:${DRAGGIFY_APP_DIR}/common/resources/client/ui/navigation/navbar/

echo -e "${GREEN}✅ React components uploaded${NC}\n"

# 4. Upload header.blade.php (landing page)
echo -e "${YELLOW}4. Uploading header.blade.php (landing page)...${NC}"
scp ../public_html/resources/views/header.blade.php \
    ${SERVER_USER}@${SERVER_IP}:${PUBLIC_HTML_DIR}/resources/views/

echo -e "${GREEN}✅ header.blade.php uploaded${NC}\n"

# 5. Upload build assets
echo -e "${YELLOW}5. Uploading build assets...${NC}"
rsync -avz --progress --delete \
    public/build/ \
    ${SERVER_USER}@${SERVER_IP}:${DRAGGIFY_APP_DIR}/public/build/

echo -e "${GREEN}✅ Build assets uploaded${NC}\n"

# 6. Fix permissions and clear caches
echo -e "${YELLOW}6. Fixing permissions and clearing caches...${NC}"
ssh ${SERVER_USER}@${SERVER_IP} << 'EOF'
cd /home/draggify.com/app.draggify.com
chown -R dragg7705:dragg7705 public/build
chown -R dragg7705:dragg7705 common/resources/views/framework.blade.php
chown -R dragg7705:dragg7705 common/resources/client/auth/ui/auth-layout/auth-layout.tsx
chown -R dragg7705:dragg7705 common/resources/client/auth/ui/email-verification-page/email-verification-page.tsx
chown -R dragg7705:dragg7705 common/resources/client/ui/navigation/navbar/logo.tsx
chmod -R 755 public/build
chmod 644 common/resources/views/framework.blade.php
chmod 644 common/resources/client/auth/ui/auth-layout/auth-layout.tsx
chmod 644 common/resources/client/auth/ui/email-verification-page/email-verification-page.tsx
chmod 644 common/resources/client/ui/navigation/navbar/logo.tsx
php artisan view:clear
php artisan cache:clear
php artisan config:clear

# Fix permissions for public_html
cd /home/draggify.com/public_html
chown -R dragg7705:dragg7705 resources/views/header.blade.php
chmod 644 resources/views/header.blade.php
EOF

echo -e "${GREEN}✅ Permissions fixed and caches cleared${NC}\n"

echo -e "${GREEN}╔══════════════════════════════════════════════════════════════╗${NC}"
echo -e "${GREEN}║           ✅ AUTH LOGO RESPONSIVE FIX UPLOADED!              ║${NC}"
echo -e "${GREEN}╚══════════════════════════════════════════════════════════════╝${NC}"
echo ""
echo "Test on:"
echo "  - Login: https://app.draggify.com/login"
echo "  - Register: https://app.draggify.com/register"
echo "  - Landing: https://draggify.com"
echo ""
echo "Logo sizes:"
echo "  - Auth pages (Mobile < 768px): 150px"
echo "  - Auth pages (Tablet/Desktop ≥ 768px): 180px"
echo "  - Navbar (Mobile < 768px): 150px"
echo "  - Navbar (Tablet 768px-991px): 150px"
echo "  - Navbar (Desktop ≥ 992px): 180px"

