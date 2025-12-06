#!/bin/bash
# Complete script to upload logo and fix everything

SERVER="root@72.61.146.226"
APP_PATH="/home/draggify.com/app.draggify.com"
IMAGE_PATH="$APP_PATH/public/images"

echo "📤 Uploading logo image..."
echo ""

# Check if local file exists
if [ ! -f "app.webuiz.com/public/images/draggify_black.png" ]; then
    echo "❌ Error: Local image file not found!"
    echo "   Expected: app.webuiz.com/public/images/draggify_black.png"
    exit 1
fi

# 1. Create images directory on server if it doesn't exist
echo "1. Creating images directory on server..."
ssh $SERVER "mkdir -p $IMAGE_PATH && echo '✅ Directory created/verified'"

# 2. Upload the image
echo ""
echo "2. Uploading image file..."
scp app.webuiz.com/public/images/draggify_black.png "$SERVER:$IMAGE_PATH/draggify_black.png"

if [ $? -eq 0 ]; then
    echo "✅ Image uploaded successfully"
else
    echo "❌ Upload failed!"
    exit 1
fi

# 3. Fix permissions
echo ""
echo "3. Fixing permissions..."
ssh $SERVER "chown dragg7705:dragg7705 $IMAGE_PATH/draggify_black.png && chmod 644 $IMAGE_PATH/draggify_black.png && echo '✅ Permissions fixed'"

# 4. Verify
echo ""
echo "4. Verification:"
ssh $SERVER "ls -lh $IMAGE_PATH/draggify_black.png"

# 5. Test URL accessibility
echo ""
echo "5. Testing image URL:"
ssh $SERVER "curl -I http://localhost/images/draggify_black.png 2>&1 | head -3"

echo ""
echo "✅ Complete! Logo should now work on https://app.draggify.com"

