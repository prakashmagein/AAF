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

namespace Aheadworks\RewardPoints\Model\ResourceModel\SpendRule\Relation\Website;

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
 * Class ReadHandler
 */
class ReadHandler implements ExtensionInterface
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
     */
    public function execute($entity, $arguments = []): SpendRule
    {
        if ($entityId = (int)$entity->getId()) {
            $websiteData = $this->getWebsiteData($entityId);
            $this->addWebsiteDataToEntity($entity, $websiteData);
        }
        return $entity;
    }

    /**
     * Retrieve website data from corresponding table
     *
     * @param int $entityId
     * @return array
     */
    private function getWebsiteData(int $entityId): array
    {
        $websiteData = [];
        try {
            $connection = $this->getConnection();
            $tableName = $this->getTableName();
            $select = $connection->select()
                ->from($tableName, 'website_id')
                ->where('rule_id = :id');
            $websiteData = $connection->fetchCol($select, ['id' => $entityId]);
        } catch (Exception $exception) {
            $this->logger->critical($exception->getMessage());
        }
        return $websiteData;
    }

    /**
     * Add extracted website data to the corresponding entity
     *
     * @param SpendRule $entity
     * @param array $websiteData
     * @return void
     */
    private function addWebsiteDataToEntity(SpendRule $entity, array $websiteData): void
    {
        if (!empty($websiteData)) {
            $entity->setWebsiteIds($websiteData);
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
        return $this->resourceConnection->getTableName(SpendRuleResource::WEBSITE_TABLE_NAME);
    }
}
