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

namespace Aheadworks\RewardPoints\Model\ResourceModel\SpendRule\Indexer;

use Aheadworks\RewardPoints\Model\ResourceModel\SpendRule as SpendRuleResourceModel;
use Aheadworks\RewardPoints\Model\Indexer\SpendRule\ProductInterface as SpendRuleProductInterface;
use Aheadworks\RewardPoints\Model\ResourceModel\SpendRule\Indexer\Product\DataCollector;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Event\ManagerInterface as EventManagerInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Indexer\Table\StrategyInterface;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Catalog\Model\Product as ProductModel;
use Magento\Indexer\Model\ResourceModel\AbstractResource;

/**
 * Class Product
 */
class Product extends AbstractResource implements IdentityInterface
{
    /**
     * @var int
     */
    const INSERT_PER_QUERY = 500;

    /**
     * @var array
     */
    private $entities = [];

    /**
     * @param Context $context
     * @param StrategyInterface $tableStrategy
     * @param EventManagerInterface $eventManager
     * @param DataCollector $dataCollector
     * @param null $connectionName
     */
    public function __construct(
        Context $context,
        StrategyInterface $tableStrategy,
        private EventManagerInterface $eventManager,
        private DataCollector $dataCollector,
        $connectionName = null
    ) {
        parent::__construct($context, $tableStrategy, $connectionName);
    }

    /**
     * Define main rule product index table
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            SpendRuleResourceModel::PRODUCT_TABLE_NAME,
            SpendRuleProductInterface::ID
        );
    }

    /**
     * Reindex all data
     *
     * @return $this
     * @throws \Exception
     */
    public function reindexAll()
    {
        $this->tableStrategy->setUseIdxTable(true);
        $this->clearTemporaryIndexTable();
        $this->beginTransaction();
        try {
            $oldData = $this->getDataFromIndex();
            $newData = $this->dataCollector->getAllData();
            $this->saveDataToIndex($newData);
            $this->commit();
        } catch (\Exception $e) {
            $this->rollBack();
            throw $e;
        }
        $this->syncData();
        $this->dispatchCleanCacheByTags(array_merge($oldData, $newData));
        return $this;
    }

    /**
     * Reindex rule product data for specified ids
     *
     * @param array|int $ids
     * @return $this
     * @throws \Exception
     */
    public function reindexRows($ids)
    {
        if (!is_array($ids)) {
            $ids = [$ids];
        }

        $newData = $this->dataCollector->getDataToUpdate($ids);
        $this->beginTransaction();
        try {
            $condition = $this->getConnection()
                ->prepareSqlCondition(SpendRuleProductInterface::PRODUCT_ID, ['in' => $ids]);
            $oldData = $this->getDataFromIndex($condition);

            $this->removeRowsFromTable(SpendRuleProductInterface::PRODUCT_ID, $ids);
            $this->saveDataToIndex($newData, false);
            $this->commit();
            $this->dispatchCleanCacheByTags(array_merge($oldData, $newData));
        } catch (\Exception $e) {
            $this->rollBack();
            throw $e;
        }
        return $this;
    }

    /**
     * Clean up temporary index table
     *
     * @return void
     */
    public function clearTemporaryIndexTable()
    {
        $this->getConnection()->truncateTable($this->getIdxTable());
    }

    /**
     * Get data from main index table
     *
     * @param string|null $condition
     * @return array
     * @throws LocalizedException
     */
    private function getDataFromIndex($condition = null): array
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from(
                $this->getMainTable(),
                [SpendRuleProductInterface::PRODUCT_ID]
            )
            ->group(SpendRuleProductInterface::PRODUCT_ID);

        if ($condition) {
            $select->where($condition);
        }

        return $connection->fetchAll($select);
    }

    /**
     * Save data to index table
     *
     * @param array $data
     * @param bool $useTemporaryIndexTable
     * @return $this
     * @throws LocalizedException
     */
    private function saveDataToIndex(array $data, bool $useTemporaryIndexTable = true)
    {
        $counter = 0;
        $toInsert = [];
        foreach ($data as $row) {
            $counter++;
            $toInsert[] = $row;
            if ($counter % self::INSERT_PER_QUERY == 0) {
                $this->insertRowsToTable($toInsert, $useTemporaryIndexTable);
                $toInsert = [];
            }
        }
        $this->insertRowsToTable($toInsert, $useTemporaryIndexTable);

        return $this;
    }

    /**
     * Insert rows to index table
     *
     * @param array $rowsData
     * @param bool $useTemporaryIndexTable
     * @return $this
     * @throws LocalizedException
     */
    private function insertRowsToTable(array $rowsData, bool $useTemporaryIndexTable = true)
    {
        $table = $useTemporaryIndexTable
            ? $this->getTable($this->getIdxTable())
            : $this->getMainTable();

        if (count($rowsData)) {
            $this->getConnection()->insertMultiple(
                $table,
                $rowsData
            );
        }
        return $this;
    }

    /**
     * Remove rows from index table
     *
     * @param string $field
     * @param array $data
     * @throws LocalizedException
     */
    private function removeRowsFromTable(string $field, array $data): void
    {
        $connection = $this->getConnection();
        $connection->delete(
            $this->getMainTable(),
            [$connection->prepareSqlCondition($field, ['in' => $data])]
        );
    }

    /**
     * Synchronize data between index storage and original storage
     *
     * @return $this
     * @throws LocalizedException
     */
    public function syncData(): Product
    {
        try {
            $this->getConnection()->truncateTable($this->getMainTable());
            $this->insertFromTable($this->getIdxTable(), $this->getMainTable(), false);
        } catch (\Exception $e) {
            throw $e;
        }
        return $this;
    }

    /**
     * Dispatch clean_cache_by_tags event
     *
     * @param array $entities
     * @return void
     */
    private function dispatchCleanCacheByTags($entities = []): void
    {
        $this->entities = $entities;
        $this->eventManager->dispatch('clean_cache_by_tags', ['object' => $this]);
    }

    /**
     * Get affected cache tags
     *
     * @return array
     */
    public function getIdentities()
    {
        $identities = [];
        foreach ($this->entities as $entity) {
            $identities[] = ProductModel::CACHE_TAG . '_' . $entity[SpendRuleProductInterface::PRODUCT_ID];
        }
        return array_unique($identities);
    }
}
