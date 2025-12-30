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

namespace Aheadworks\RewardPoints\Model\ResourceModel\EarnRule\Relation\CustomerGroup;

use Aheadworks\RewardPoints\Api\Data\EarnRuleInterface;
use Aheadworks\RewardPoints\Model\ResourceModel\EarnRule as EarnRuleResource;
use Aheadworks\RewardPoints\Model\EarnRule;
use Exception;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\EntityManager\Operation\ExtensionInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Psr\Log\LoggerInterface as Logger;

class ReadHandler implements ExtensionInterface
{
    /**
     * @param MetadataPool $metadataPool
     * @param ResourceConnection $resourceConnection
     * @param Logger $logger
     */
    public function __construct(
        private readonly MetadataPool $metadataPool,
        private readonly ResourceConnection $resourceConnection,
        private readonly Logger $logger
    ) {
    }

    /**
     * Perform action on relation/extension attribute
     *
     * @param EarnRule $entity
     * @param array $arguments
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute($entity, $arguments = []): EarnRule
    {
        if ($entityId = (int)$entity->getId()) {
            $customerGroupData = $this->getCustomerGroupData($entityId);
            $this->addCustomerGroupDataToEntity($entity, $customerGroupData);
        }

        return $entity;
    }

    /**
     * Retrieve customer group data from corresponding table
     *
     * @param int $entityId
     * @return array
     */
    private function getCustomerGroupData(int $entityId): array
    {
        $customerGroupData = [];
        try {
            $connection = $this->getConnection();
            $tableName = $this->getTableName();
            $select = $connection->select()
                ->from($tableName, 'customer_group_id')
                ->where('rule_id = :id');
            $customerGroupData = $connection->fetchCol($select, ['id' => $entityId]);
        } catch (Exception $exception) {
            $this->logger->critical($exception->getMessage());
        }
        return $customerGroupData;
    }

    /**
     * Add extracted customer group data to the corresponding entity
     *
     * @param EarnRule $entity
     * @param array $customerGroupData
     * @return void
     */
    private function addCustomerGroupDataToEntity(EarnRule $entity, array $customerGroupData): void
    {
        if (!empty($customerGroupData)) {
            $entity->setCustomerGroupIds($customerGroupData);
        }
    }

    /**
     * Get connection
     *
     * @return AdapterInterface
     * @throws Exception
     */
    private function getConnection(): AdapterInterface
    {
        return $this->resourceConnection->getConnectionByName(
            $this->metadataPool->getMetadata(EarnRuleInterface::class)->getEntityConnectionName()
        );
    }

    /**
     * Get table name
     *
     * @return string
     */
    private function getTableName(): string
    {
        return $this->resourceConnection->getTableName(EarnRuleResource::CUSTOMER_GROUP_TABLE_NAME);
    }
}
