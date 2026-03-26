<?php

namespace Testa\Storefront\Queries\Membership;

use Illuminate\Database\Eloquent\Collection;
use Testa\Models\Membership\MembershipPlan;

final class GetMembershipPlansByTier
{
    public function execute(string $tierId): Collection
    {
        return MembershipPlan::where('membership_tier_id', $tierId)
            ->with(['variant'])
            ->where('is_published', true)
            ->get();
    }
}
