<?php

namespace Trafikrak\Admin\Filament\Resources\Extension;

use Lunar\Admin\Support\Extending\ResourceExtension;
use Trafikrak\Admin\Filament\Resources\Sales\CustomerResource\SubscriptionRelationManager;

class CustomerResourceExtension extends ResourceExtension
{
    public function getRelations(array $managers): array
    {
        return [
            ...$managers,
            SubscriptionRelationManager::class,
        ];
    }
}
