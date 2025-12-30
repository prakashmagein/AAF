<?php
/**
 * Aheadworks Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://aheadworks.com/end-user-license-agreement/
 *
 * @package    RewardPoints
 * @version    2.4.0
 * @copyright  Copyright (c) 2024 Aheadworks Inc. (https://aheadworks.com/)
 * @license    https://aheadworks.com/end-user-license-agreement/
 */
declare(strict_types=1);

namespace Aheadworks\RewardPoints\Model\ResourceModel\Transaction\Relation\Entity;

use Exception;
use Magento\Framework\App\ResourceConnection;
use Aheadworks\RewardPoints\Api\Data\TransactionInterface;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\EntityManager\Operation\ExtensionInterface;

class ReadHandler implements ExtensionInterface
{
    /**
     * @param MetadataPool $metadataPool
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        private readonly MetadataPool $metadataPool,
        private readonly ResourceConnection $resourceConnection
    ) {
    }

    /**
     * Perform action on relation/extension attribute
     *
     * @param TransactionInterface $entity
     * @param array $arguments
     * @return TransactionInterface
     * @throws Exception
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute($entity, $arguments = []): TransactionInterface
    {
        if ($entityId = (int)$entity->getId()) {
            $connection = $this->resourceConnection->getConnectionByName(
                $this->metadataPool->getMetadata(TransactionInterface::class)->getEntityConnectionName()
            );
            $select = $connection->select()
                ->from($this->resourceConnection->getTableName('aw_rp_transaction_entity'))
                ->where('transaction_id = :id');
            $entitiesData = $connection->fetchAssoc($select, ['id' => $entityId]);
            $resultIds = [];
            foreach ($entitiesData as $entityData) {
                $resultIds[$entityData['entity_type']] = isset($resultIds[$entityData['entity_type']])
                    ? $this->addEntityDataToResult($resultIds, $entityData)
                    : $this->prepareEntityType($entityData);
            }
            $entity->setEntities($resultIds);
        }

        return $entity;
    }

    /**
     * Prepare entity type
     *
     * @param array $entityData
     * @return array
     */
    private function prepareEntityType(array $entityData): array
    {
        return [
            'entity_id'    => $entityData['entity_id'],
            'entity_label' => $entityData['entity_label']
        ];
    }

    /**
     * Add entity data to result
     *
     * @param array $resultIds
     * @param array $entityData
     * @return array
     */
    private function addEntityDataToResult(array $resultIds, array $entityData): array
    {
        return array_merge(
            [$resultIds[$entityData['entity_type']]],
            [
                $this->prepareEntityType($entityData)
            ]
        );
    }
}
