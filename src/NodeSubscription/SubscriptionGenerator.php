<?php declare(strict_types=1);

namespace MateuszMesek\DocumentDataCmsPageMview\NodeSubscription;

use InvalidArgumentException;
use Magento\Cms\Api\Data\PageInterface;
use Magento\Framework\DB\Ddl\Trigger;
use Magento\Framework\EntityManager\MetadataPool;
use MateuszMesek\DocumentDataIndexerMview\Data\SubscriptionFactory;
use Traversable;

class SubscriptionGenerator
{
    private MetadataPool $metadataPool;
    private SubscriptionFactory $subscriptionFactory;

    public function __construct(
        MetadataPool $metadataPool,
        SubscriptionFactory $subscriptionFactory
    )
    {
        $this->metadataPool = $metadataPool;
        $this->subscriptionFactory = $subscriptionFactory;
    }

    public function generate(): Traversable
    {
        $metadata = $this->metadataPool->getMetadata(PageInterface::class);

        foreach (Trigger::getListOfEvents() as $event) {
            switch ($event) {
                case Trigger::EVENT_INSERT:
                case Trigger::EVENT_UPDATE:
                    $prefix = 'NEW';
                    break;

                case Trigger::EVENT_DELETE:
                    $prefix = 'OLD';
                    break;

                default:
                    throw new InvalidArgumentException("Trigger event '$event' is unsupported");
            }

            yield $this->subscriptionFactory->create([
                'tableName' => $metadata->getEntityTable(),
                'triggerEvent' => $event,
                'documentId' => "$prefix.{$metadata->getIdentifierField()}",
                'dimensions' => "JSON_SET('{}', '$.scope', 0)",
            ]);

            yield $this->subscriptionFactory->create([
                'tableName' => "{$metadata->getEntityTable()}_store",
                'triggerEvent' => $event,
                'documentId' => "(SELECT {$metadata->getIdentifierField()} FROM {$metadata->getEntityTable()} WHERE {$metadata->getLinkField()} = $prefix.{$metadata->getLinkField()})",
                'dimensions' => "JSON_SET('{}', '$.scope', $prefix.store_id)",
            ]);
        }
    }
}
