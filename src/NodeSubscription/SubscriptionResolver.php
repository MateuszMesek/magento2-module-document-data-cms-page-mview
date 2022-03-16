<?php declare(strict_types=1);

namespace MateuszMesek\DocumentDataCmsPageMview\NodeSubscription;

use Generator;
use MateuszMesek\DocumentDataIndexerMviewApi\NodeSubscriptionsResolverInterface;

class SubscriptionResolver implements NodeSubscriptionsResolverInterface
{
    public function resolve(): Generator
    {
        yield '*' => [
            'cms_page' => [
                'id' => 'cms_page',
                'type' => SubscriptionGenerator::class,
                'arguments' => []
            ]
        ];
    }
}
