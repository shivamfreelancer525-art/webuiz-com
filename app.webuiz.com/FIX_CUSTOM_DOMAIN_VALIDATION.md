# Fix Custom Domain Validation

## Issue
Validation message not showing when custom domain limit is reached.

## Root Cause Analysis

### Database Structure
1. **users** table - stores user data
2. **products** table - stores plans/products
3. **subscriptions** table - links users to products
4. **permissions** table - stores permission definitions
5. **permissionables** table - links permissions to users/products/roles with restrictions

### How Restrictions Work
1. User gets product from `getSubscriptionProduct()`:
   - If billing enabled: gets product from valid subscription, or free product
   - If billing disabled: returns null (no product permissions loaded)
2. `loadPermissions()` loads permissions from:
   - User directly (permissionables with user_id)
   - User roles (permissionables with role_id)  
   - User's product (permissionables with product_id)
3. `getRestrictionValue('projects.create', 'count')` gets the count restriction from the user's permissions

### The Problem
- Frontend checks `projects.create` count restriction
- Backend also checks `projects.create` count restriction
- But if `query.data` is not loaded, `currentDomains` might be 0
- If `usage` is not loaded, `gateAllowed` defaults to true
- Message only shows if both conditions are met, which might not happen

## Fix Applied

### Frontend (`custom-domains-page.tsx`)
1. Simplified logic to show message when limit is reached
2. Show message if `limitReached && (query.data || usage)` - works with either data source
3. Also show if gate is explicitly blocking

### Backend (`CustomDomainController.php`)
Already has validation in:
- `store()` method - checks before creating
- `authorizeCrupdate()` method - checks before validation

## Database Check Script

Run this to check a user's restrictions:

```bash
php database/check_user_restrictions.php webuiz25@gmail.com
```

This will show:
- User's subscriptions
- Product permissions
- Restriction values
- Current usage
- Validation status

## Expected Database State

For a user with 1 website limit (Trial Plan):

```sql
-- Check product
SELECT * FROM products WHERE free = 1;

-- Check permissionables for that product
SELECT 
    p.name as product_name,
    perm.name as permission_name,
    pa.restrictions
FROM permissionables pa
JOIN products p ON pa.permissionable_id = p.id 
    AND pa.permissionable_type = 'Common\\Billing\\Models\\Product'
JOIN permissions perm ON pa.permission_id = perm.id
WHERE p.free = 1
AND perm.name IN ('projects.create', 'custom_domains.create');
```

Should show:
- `projects.create` with restrictions: `[{"name": "count", "value": 1}]`
- `custom_domains.create` with restrictions: `[{"name": "count", "value": 1}]` (optional, we use projects.create)

## Testing

1. Login as user with 1 website limit
2. Add 1 custom domain
3. Try to add another
4. Should see: "You have reached the maximum number of custom domains allowed for your plan..."

## If Still Not Working

Check:
1. Is `maxWebsites` null? → User might not have a product assigned
2. Is `query.data` loading? → Check network tab
3. Is `usage` loading? → Check network tab for `/api/v1/account/usage`
4. Are permissions loaded? → Check user object in browser console

