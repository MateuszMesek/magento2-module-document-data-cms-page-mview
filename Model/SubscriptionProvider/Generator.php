<?php declare(strict_types=1);

namespace MateuszMesek\DocumentDataCmsPageMview\Model\SubscriptionProvider;

use InvalidArgumentException;
use Magento\Cms\Api\Data\PageInterface;
use Magento\Framework\DB\Ddl\Trigger;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Store\Model\ResourceModel\Store as StoreResource;
use Magento\Store\Model\StoreDimensionProvider;
use MateuszMesek\DocumentDataIndexMview\Model\Data\SubscriptionFactory;
use Traversable;

class Generator
{
    public function __construct(
        private readonly MetadataPool        $metadataPool,
        private readonly StoreResource       $storeResource,
        private readonly SubscriptionFactory $subscriptionFactory
    )
    {
    }

    public function generate(): Traversable
    {
        $metadata = $this->metadataPool->getMetadata(PageInterface::class);

        $storeTable = $this->storeResource->getMainTable();
        $storeDimensionName = StoreDimensionProvider::DIMENSION_NAME;

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
                'rows' => <<<SQL
                    SELECT $prefix.{$metadata->getIdentifierField()} AS document_id,
                           NULL AS node_path,
                           JSON_SET('{}', '$.$storeDimensionName', store.store_id) AS dimensions
                    FROM $storeTable AS store
                    WHERE store.store_id != 0
                SQL
            ]);

            yield $this->subscriptionFactory->create([
                'tableName' => "{$metadata->getEntityTable()}_store",
                'triggerEvent' => $event,
                'rows' => <<<SQL
                    SELECT {$metadata->getIdentifierField()}  AS document_id,
                           NULL AS node_path,
                           JSON_SET('{}', '$.$storeDimensionName', store.store_id) AS dimensions
                    FROM {$metadata->getEntityTable()}
                    CROSS JOIN $storeTable AS store
                        ON store.store_id != 0
                    WHERE {$metadata->getLinkField()} = $prefix.{$metadata->getLinkField()}
                SQL
            ]);
        }
    }
}
