# Verify Server Files and Database

## Quick Check Commands

### 1. Check Auth Layout File on Server
```bash
ssh root@72.61.146.226
cat /home/draggify.com/app.draggify.com/common/resources/client/auth/ui/auth-layout/auth-layout.tsx | grep -A 5 "img"
```

**Expected:** Should match your local file (without `getAssetUrl()`)

---

### 2. Check Database Settings
```bash
ssh root@72.61.146.226
cd /home/draggify.com/app.draggify.com

# Method 1: Using PHP directly
php -r "
require 'vendor/autoload.php';
\$app = require_once 'bootstrap/app.php';
\$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
use Common\Settings\Setting;
\$logoDark = Setting::where('name', 'branding.logo_dark')->first();
\$logoLight = Setting::where('name', 'branding.logo_light')->first();
echo 'logo_dark: ' . (\$logoDark ? \$logoDark->value : 'NOT FOUND') . PHP_EOL;
echo 'logo_light: ' . (\$logoLight ? \$logoLight->value : 'NOT FOUND') . PHP_EOL;
"

# Method 2: Using SQL directly
DB_USER=$(grep DB_USERNAME .env | cut -d '=' -f2 | xargs)
DB_PASS=$(grep DB_PASSWORD .env | cut -d '=' -f2 | xargs)
DB_NAME=$(grep DB_DATABASE .env | cut -d '=' -f2 | xargs)
mysql -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" -e "SELECT name, value FROM settings WHERE name LIKE 'branding.%';"
```

**Expected:**
- `branding.logo_dark` = `images/draggify_black.png`
- `branding.logo_light` = `images/draggify_black.png`

---

### 3. Check if Image File Exists
```bash
ssh root@72.61.146.226
ls -lh /home/draggify.com/app.draggify.com/public/images/draggify_black.png
```

**Expected:** File should exist with proper permissions

---

### 4. Check Bootstrap Data (What Frontend Receives)
```bash
ssh root@72.61.146.226
cd /home/draggify.com/app.draggify.com
php -r "
require 'vendor/autoload.php';
\$app = require_once 'bootstrap/app.php';
\$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
\$bootstrap = new \Common\Core\Bootstrap\BootstrapData(
    app(\Common\Settings\Settings::class),
    app(\Illuminate\Http\Request::class),
    app(\Common\Auth\Roles\Role::class),
    app(\Common\Localizations\LocalizationsRepository::class)
);
\$data = \$bootstrap->init();
echo 'logo_dark: ' . (\$data['settings']['branding']['logo_dark'] ?? 'null') . PHP_EOL;
echo 'logo_light: ' . (\$data['settings']['branding']['logo_light'] ?? 'null') . PHP_EOL;
echo 'base_url: ' . (\$data['settings']['base_url'] ?? 'null') . PHP_EOL;
"
```

**Expected:** Should show `images/draggify_black.png` for both logos

---

### 5. Compare Local vs Server File
```bash
# Local
cat app.webuiz.com/common/resources/client/auth/ui/auth-layout/auth-layout.tsx | grep -A 3 "img"

# Server
ssh root@72.61.146.226 "cat /home/draggify.com/app.draggify.com/common/resources/client/auth/ui/auth-layout/auth-layout.tsx | grep -A 3 'img'"
```

---

### 6. Check Image URL Accessibility
```bash
ssh root@72.61.146.226
curl -I http://localhost/images/draggify_black.png
# Or test from browser: https://app.draggify.com/images/draggify_black.png
```

---

## Automated Check Script

Run the script:
```bash
./app.webuiz.com/check_server_status.sh
```

---

## Common Issues & Fixes

### Issue 1: Database has wrong path
**Fix:**
```bash
ssh root@72.61.146.226
cd /home/draggify.com/app.draggify.com
DB_USER=$(grep DB_USERNAME .env | cut -d '=' -f2 | xargs)
DB_PASS=$(grep DB_PASSWORD .env | cut -d '=' -f2 | xargs)
DB_NAME=$(grep DB_DATABASE .env | cut -d '=' -f2 | xargs)
mysql -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" -e "UPDATE settings SET value = 'images/draggify_black.png' WHERE name = 'branding.logo_dark'; UPDATE settings SET value = 'images/draggify_black.png' WHERE name = 'branding.logo_light';"
php artisan config:clear && php artisan cache:clear
```

### Issue 2: Image file missing
**Fix:**
```bash
# From local machine
scp app.webuiz.com/public/images/draggify_black.png root@72.61.146.226:/home/draggify.com/app.draggify.com/public/images/
ssh root@72.61.146.226 "chown dragg7705:dragg7705 /home/draggify.com/app.draggify.com/public/images/draggify_black.png"
```

### Issue 3: Files not uploaded
**Fix:**
```bash
# Upload auth layout file
scp app.webuiz.com/common/resources/client/auth/ui/auth-layout/auth-layout.tsx \
    root@72.61.146.226:/home/draggify.com/app.draggify.com/common/resources/client/auth/ui/auth-layout/
```

### Issue 4: Cache not cleared
**Fix:**
```bash
ssh root@72.61.146.226
cd /home/draggify.com/app.draggify.com
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
```

