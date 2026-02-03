<?php

namespace App\Models;

use App\Workspaces\WorkspaceRelationships;
use Common\Auth\BaseUser;
use Common\Domains\CustomDomain;
use Common\Workspaces\Workspace;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Sanctum\HasApiTokens;

class User extends BaseUser
{
    use WorkspaceRelationships, HasApiTokens;

    public function workspaces(): HasMany
    {
        return $this->hasMany(Workspace::class, 'owner_id');
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    public function customDomains(): HasMany
    {
        return $this->hasMany(CustomDomain::class);
    }

    public function aiHistory(): HasMany
    {
        return $this->hasMany(AiHistoryItem::class);
    }

    public function getAiTextTokenUsage()
    {
        return [
            'total' => $this->getRestrictionValue('ai.text', 'tokens'),
            'used' => (int) $this->aiHistory()
                ->text()
                ->whereBetween('created_at', [
                    now()->startOfMonth(),
                    now()->endOfMonth(),
                ])
                ->sum('tokens_used'),
        ];
    }

    public function getAiImageTokenUsage()
    {
        return [
            'total' => $this->getRestrictionValue('ai.images', 'tokens'),
            'used' => (int) $this->aiHistory()
                ->images()
                ->whereBetween('created_at', [
                    now()->startOfMonth(),
                    now()->endOfMonth(),
                ])
                ->sum('tokens_used'),
        ];
    }

    /**
     * Get AI projects relationship (only AI-generated)
     */
    public function aiProjects(): HasMany
    {
        return $this->hasMany(Project::class)->where('is_ai_generated', true);
    }

    /**
     * Get AI project usage for current month
     * Monthly limit - deleting projects doesn't restore quota
     */
    /**
     * Get AI project usage for current billing month
     * Resets based on subscription renewal date
     */
    public function getAiProjectUsage(): array
    {
        $startDate = now()->startOfMonth();
        $endDate = now()->endOfMonth();

        // Check for active subscription to determine billing cycle
        $subscription = $this->subscriptions->first();

        // If user has a subscription with a renewal/creation date
        if ($subscription) {
            // Use renews_at if available, otherwise created_at
            $anchorDate = $subscription->renews_at ?? $subscription->created_at;

            if ($anchorDate) {
                // Ensure anchor is a Carbon instance
                $anchorDate = \Carbon\Carbon::parse($anchorDate);
                
                // Calculate the start of the current monthly period relative to usage
                // Example: Renews on 15th. Today is 20th. Start = 15th of this month.
                // Example: Renews on 15th. Today is 10th. Start = 15th of last month.
                
                $dayOfMonth = $anchorDate->day;
                $currentMonthDate = now()->day($dayOfMonth);
                
                // Handle edge cases like 31st where current month might not have it (Carbon handles this by overflowing, so we check)
                if ($currentMonthDate->month != now()->month) {
                     // If overflowed (e.g. Feb 30 -> Mar 2), set to last day of current month
                     $currentMonthDate = now()->endOfMonth();
                }

                if ($currentMonthDate->isPast() || $currentMonthDate->isToday()) {
                    $startDate = $currentMonthDate->startOfDay();
                } else {
                    $startDate = $currentMonthDate->subMonth()->startOfDay();
                }
                
                // End date is start date + 1 month (approx) or just now() since we only care about used so far
                $endDate = $startDate->copy()->addMonth();
            }
        }

        // Count AI projects created in the current billing period
        $used = $this->projects()
            ->where('is_ai_generated', true)
            ->whereBetween('created_at', [
                $startDate,
                now(), // Count up to right now
            ])
            ->count();

        // AI project limit is same as regular project limit
        $total = $this->getRestrictionValue('projects.create', 'count');

        return [
            'used' => $used,
            'total' => $total,
            'period_start' => $startDate->toDateTimeString(), // Useful for debugging/UI
            'period_end' => $endDate->toDateTimeString(),
        ];
    }
}
