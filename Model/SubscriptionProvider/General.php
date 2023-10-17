<?php declare(strict_types=1);

namespace MateuszMesek\DocumentDataCmsPageMview\Model\SubscriptionProvider;

use MateuszMesek\DocumentDataCmsPageMview\Model\SubscriptionProvider\General\Generator;
use MateuszMesek\DocumentDataIndexMviewApi\Model\SubscriptionProviderInterface;
use Traversable;

class General implements SubscriptionProviderInterface
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
