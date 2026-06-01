<?php

namespace Testa\Storefront\Queries\Media;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Testa\Models\Customer;
use Testa\Models\Media\Document;
use Testa\Models\Media\Visibility;
use Testa\Models\Membership\Benefit;
use Testa\Storefront\Queries\Membership\CustomerHasActiveBenefit;

final class GetAccessibleDocuments
{
    public function execute(?Customer $customer, int $perPage = 16): LengthAwarePaginator
    {
        $hasMemberAccess = $customer !== null
            && new CustomerHasActiveBenefit()->execute($customer, Benefit::PRIVATE_MEDIA_ACCESS);

        return Document::where('is_published', true)
            ->where(function ($query) use ($hasMemberAccess) {
                $query->where('visibility', Visibility::PUBLIC->value);

                if ($hasMemberAccess) {
                    $query->orWhere('visibility', Visibility::MEMBERS_ONLY->value);
                }
            })
            ->paginate($perPage);
    }
}
