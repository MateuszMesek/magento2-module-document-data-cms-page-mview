<?php declare(strict_types=1);

namespace MateuszMesek\DocumentDataCmsPageMview\Model;

use MateuszMesek\DocumentDataCmsPageMview\Model\SubscriptionProvider\Generator;
use MateuszMesek\DocumentDataIndexMviewApi\Model\SubscriptionProviderInterface;
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
