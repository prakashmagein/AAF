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

namespace Aheadworks\RewardPoints\Model\ResourceModel\SpendRule\Relation\CustomerGroup;

use Aheadworks\RewardPoints\Api\Data\SpendRuleInterface;
use Aheadworks\RewardPoints\Model\ResourceModel\SpendRule as SpendRuleResource;
use Aheadworks\RewardPoints\Model\SpendRule;
use Exception;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\EntityManager\Operation\ExtensionInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Psr\Log\LoggerInterface as Logger;

/**
 * Class SaveHandler
 */
class SaveHandler implements ExtensionInterface
{
    /**
     * @param MetadataPool $metadataPool
     * @param ResourceConnection $resourceConnection
     * @param Logger $logger
     */
    public function __construct(
        private MetadataPool $metadataPool,
        private ResourceConnection $resourceConnection,
        private Logger $logger
    ) {
    }

    /**
     * Perform action on relation/extension attribute
     *
     * @param SpendRule $entity
     * @param array $arguments
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @throws Exception
     */
    public function execute($entity, $arguments = []): SpendRule
    {
        $this->deleteOldCustomerGroupData($entity->getId());
        $customerGroupDataToSave = $this->getCustomerGroupDataToSave($entity);
        $this->saveCustomerGroupData($customerGroupDataToSave);
        return $entity;
    }

    /**
     * Remove old customer group data
     *
     * @param int $id
     * @return void
     * @throws Exception
     */
    private function deleteOldCustomerGroupData(int $id): void
    {
        $this->getConnection()->delete($this->getTableName(), ['rule_id = ?' => $id]);
    }

    /**
     * Retrieve customer group data to save in the corresponding table
     *
     * @param SpendRule $entity
     * @return array
     */
    private function getCustomerGroupDataToSave(SpendRule $entity): array
    {
        $customerGroupData = [];
        $ruleId = $entity->getId();

        foreach ($entity->getCustomerGroupIds() as $customerGroupId) {
            $customerGroupData[] = [
                'rule_id' => $ruleId,
                'customer_group_id' => $customerGroupId
            ];
        }
        return $customerGroupData;
    }

    /**
     * Save customer group data in the corresponding table
     *
     * @param array $customerGroupDataToSave
     * @return void
     */
    private function saveCustomerGroupData(array $customerGroupDataToSave): void
    {
        if (!empty($customerGroupDataToSave)) {
            try {
                $connection = $this->getConnection();
                $tableName = $this->getTableName();
                $connection->insertMultiple(
                    $tableName,
                    $customerGroupDataToSave
                );
            } catch (Exception $exception) {
                $this->logger->critical($exception->getMessage());
            }
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
            $this->metadataPool->getMetadata(SpendRuleInterface::class)->getEntityConnectionName()
        );
    }

    /**
     * Get table name
     *
     * @return string
     */
    private function getTableName(): string
    {
        return $this->resourceConnection->getTableName(SpendRuleResource::CUSTOMER_GROUP_TABLE_NAME);
    }
}
