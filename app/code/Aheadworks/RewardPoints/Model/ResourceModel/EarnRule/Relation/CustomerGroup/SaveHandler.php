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

class SaveHandler implements ExtensionInterface
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
     * @throws Exception
     */
    public function execute($entity, $arguments = []): EarnRule
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
     * @param EarnRule $entity
     * @return array
     */
    private function getCustomerGroupDataToSave(EarnRule $entity): array
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
