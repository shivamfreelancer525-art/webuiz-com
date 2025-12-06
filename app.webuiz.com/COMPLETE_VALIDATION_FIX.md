# Complete Custom Domain Validation Fix

## Summary
Fixed the validation logic to properly show error messages when custom domain limit is reached based on user's website limit.

## Changes Made

### 1. Frontend (`custom-domains-page.tsx`)
- Simplified `limitReached` check to be more explicit
- Changed `shouldShowMessage` to show when limit is reached if ANY data source is available
- Ensures message shows even if only `query.data` OR `usage` is loaded

### 2. Backend (`CustomDomainController.php`)
- Already has validation in `store()` and `authorizeCrupdate()`
- Checks `projects.create` count restriction
- Returns 403 error with proper message

## Database Structure

### Tables Involved:
1. **products** - Plans/products
2. **subscriptions** - Links users to products
3. **permissions** - Permission definitions
4. **permissionables** - Links permissions to products/users with restrictions

### Expected Data:
For a user with 1 website limit:
- Product should have `projects.create` permission with restriction: `[{"name": "count", "value": 1}]`
- User gets this restriction through their subscription product

## Diagnostic Commands

### Check User Restrictions:
```bash
php database/check_user_restrictions.php webuiz25@gmail.com
```

### Check All Products:
```bash
php database/fix_custom_domain_validation.php
```

### Check Database Directly:
```sql
-- Check products and their permissions
SELECT 
    p.id,
    p.name,
    p.free,
    perm.name as permission_name,
    pa.restrictions
FROM products p
LEFT JOIN permissionables pa ON pa.permissionable_id = p.id 
    AND pa.permissionable_type = 'Common\\Billing\\Models\\Product'
LEFT JOIN permissions perm ON pa.permission_id = perm.id
WHERE perm.name IN ('projects.create', 'custom_domains.create')
ORDER BY p.id, perm.name;

-- Check user's product
SELECT 
    u.email,
    p.name as product_name,
    p.free,
    s.valid,
    s.active
FROM users u
LEFT JOIN subscriptions s ON s.user_id = u.id
LEFT JOIN products p ON s.product_id = p.id
WHERE u.email = 'webuiz25@gmail.com';

-- Check user's current usage
SELECT 
    u.email,
    COUNT(DISTINCT pr.id) as project_count,
    COUNT(DISTINCT cd.id) as domain_count
FROM users u
LEFT JOIN projects pr ON pr.user_id = u.id
LEFT JOIN custom_domains cd ON cd.user_id = u.id
WHERE u.email = 'webuiz25@gmail.com'
GROUP BY u.id, u.email;
```

## Testing Steps

1. **Login as test user** (with 1 website limit)
2. **Check current state:**
   - Open browser console
   - Check `user.permissions` - should have `projects.create` with restrictions
   - Check `maxWebsites` value
3. **Add 1 custom domain**
4. **Verify:**
   - Button should be disabled
   - Error message should show
   - Message: "You have reached the maximum number of custom domains allowed for your plan. Your plan allows 1 website(s), so you can add up to 1 custom domain(s)."
5. **Try to add another domain:**
   - Backend should return 403 error
   - Frontend should show error message

## Common Issues

### Issue 1: `maxWebsites` is null
**Cause:** User doesn't have a product with `projects.create` count restriction
**Fix:** Ensure free product has `projects.create` permission with count restriction

### Issue 2: Message not showing
**Cause:** `query.data` and `usage` both not loaded
**Fix:** Check network tab - ensure API calls are successful

### Issue 3: Button not disabled
**Cause:** `canCreateDomain` is true when it shouldn't be
**Fix:** Check `maxWebsites`, `currentDomains`, and `withinWebsiteLimit` values

## Files Changed
- `resources/client/dashboard/custom-domains-page.tsx` - Fixed validation logic
- `common/Domains/CustomDomainController.php` - Already has validation (no changes needed)

