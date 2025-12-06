#!/bin/bash
# Upload image and verify everything

SERVER="root@72.61.146.226"
APP_PATH="/home/draggify.com/app.draggify.com"

echo "📤 Uploading image file..."
echo ""

# 1. Upload image
scp app.webuiz.com/public/images/draggify_black.png \
    "$SERVER:$APP_PATH/public/images/draggify_black.png"

if [ $? -eq 0 ]; then
    echo "✅ Image uploaded"
else
    echo "❌ Upload failed"
    exit 1
fi

# 2. Fix permissions
echo ""
echo "🔧 Fixing permissions..."
ssh $SERVER "chown dragg7705:dragg7705 $APP_PATH/public/images/draggify_black.png && chmod 644 $APP_PATH/public/images/draggify_black.png && echo '✅ Permissions fixed'"

# 3. Verify
echo ""
echo "✅ Verification:"
ssh $SERVER "ls -lh $APP_PATH/public/images/draggify_black.png"

echo ""
echo "✅ Done! Logo should now work."

