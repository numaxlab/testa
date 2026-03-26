<?php

namespace Testa\Storefront\Queries\Membership;

use Illuminate\Database\Eloquent\Collection;
use Testa\Models\Membership\MembershipTier;

final class GetPublishedMembershipTiers
{
    public function execute(): Collection
    {
        return MembershipTier::where('is_published', true)->get();
    }
}
