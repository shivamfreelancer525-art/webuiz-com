<?php

namespace Common\Domains;

use App\Models\User;
use Common\Core\Policies\BasePolicy;
use Illuminate\Auth\Access\Response;

class CustomDomainPolicy extends BasePolicy
{
    public $permissionName = 'custom_domains';

    public function index(User $user, int $userId = null)
    {
        return $user->hasPermission("$this->permissionName.view") ||
            $user->id === $userId;
    }

    public function show(User $user, CustomDomain $customDomain)
    {
        return $user->hasPermission("$this->permissionName.view") ||
            $customDomain->user_id === $user->id;
    }

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

        // Custom domains limit should match the website limit (projects.create count)
        // This allows users to have as many custom domains as they have websites
        $maxWebsites = $user->getRestrictionValue('projects.create', 'count');
        if (!$maxWebsites) {
            // No website limit means unlimited custom domains
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

    public function update(User $user)
    {
        return $user->hasPermission("$this->permissionName.update");
    }

    public function destroy(User $user, array $domainIds)
    {
        if ($user->hasPermission("$this->permissionName.delete")) {
            return true;
        } else {
            $dbCount = app(CustomDomain::class)
                ->whereIn('id', $domainIds)
                ->where('user_id', $user->id)
                ->count();
            return $dbCount === count($domainIds);
        }
    }
}
