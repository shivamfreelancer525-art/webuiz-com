<?php namespace Common\Core\Policies;

use App\Models\User;
use Common\Pages\CustomPage;

class PagePolicy extends BasePolicy
{
    public function index(?User $user, int $userId = null)
    {
        return $user->hasPermission('custom_pages.view') || $user->id === $userId;
    }

    public function show(?User $user, CustomPage $customPage)
    {
        // Allow public access to default pages (privacy-policy, terms-and-conditions, terms-of-service, etc.)
        if ($customPage->type === 'default' && in_array($customPage->slug, ['privacy-policy', 'terms-and-conditions', 'terms-of-service'])) {
            return true;
        }
        
        // For other pages, require authentication and permission
        if (!$user) {
            return false;
        }
        
        return $user->hasPermission('custom_pages.view') || $customPage->user_id === $user->id;
    }

    public function store(User $user)
    {
        return $user->hasPermission('custom_pages.create');
    }

    public function update(User $user)
    {
        return $user->hasPermission('custom_pages.update');
    }

    public function destroy(User $user, $pageIds)
    {
        if ($user->hasPermission('custom_pages.delete')) {
            return true;
        } else {
            $dbCount = app(CustomPage::class)
                ->whereIn('id', $pageIds)
                ->where('user_id', $user->id)
                ->count();
            return $dbCount === count($pageIds);
        }
    }
}
