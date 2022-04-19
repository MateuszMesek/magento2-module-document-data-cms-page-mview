<?php declare(strict_types=1);

namespace MateuszMesek\DocumentDataCmsPageMview;

use MateuszMesek\DocumentDataCmsPageMview\SubscriptionProvider\Generator;
use MateuszMesek\DocumentDataIndexMviewApi\SubscriptionProviderInterface;
use Traversable;

class SubscriptionProvider implements SubscriptionProviderInterface
{
    public function get(array $context): Traversable
    {
        yield '*' => [
            'cms_page' => [
                'id' => 'cms_page',
                'type' => Generator::class,
                'arguments' => []
            ]
        ];
    }
}
