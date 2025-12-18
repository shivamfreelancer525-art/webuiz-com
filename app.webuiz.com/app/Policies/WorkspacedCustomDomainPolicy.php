<?php

namespace App\Policies;

use App\Models\User;
use Common\Domains\CustomDomain;
use Common\Core\Exceptions\AccessResponseWithAction;
use Common\Workspaces\Policies\WorkspacedResourcePolicy;
use Illuminate\Auth\Access\Response;

class WorkspacedCustomDomainPolicy extends WorkspacedResourcePolicy
{
    protected string $resource = CustomDomain::class;

    protected string $permissionName = 'custom_domains';

    public function store(User $user)
    {
        // Check if user has permission to create custom domains
        if (!$this->hasPermission($user, 'custom_domains.create')) {
            return Response::deny();
        }

        // Admin can ignore count restriction
        if ($user->hasPermission('admin')) {
            return Response::allow();
        }

        // Check if user is on trial plan (free product)
        // Trial plan users cannot add any custom domains
        $subscriptionProduct = $user->getSubscriptionProduct();
        if ($subscriptionProduct && $subscriptionProduct->free) {
            $message = __('Trial plan does not allow custom domains. Please upgrade your plan to add custom domains.');
            return $this->denyWithAction($message, $this->upgradeAction());
        }

        // Custom domains limit should match the website limit (projects.create count)
        // This allows users to have as many custom domains as they have websites
        $maxWebsites = $user->getRestrictionValue('projects.create', 'count');
        if (!$maxWebsites) {
            // No website limit means unlimited custom domains (for paid plans only)
            // But we already checked for trial plan above, so this is safe
            return Response::allow();
        }

        // Check if user did not go over their max quota (based on website limit)
        if ($user->customDomains()->count() >= $maxWebsites) {
            $message = __('policies.quota_exceeded', [
                'resources' => 'custom domains',
                'resource' => 'custom domain',
            ]);
            return $this->denyWithAction($message, $this->upgradeAction());
        }

        return Response::allow();
    }
}
