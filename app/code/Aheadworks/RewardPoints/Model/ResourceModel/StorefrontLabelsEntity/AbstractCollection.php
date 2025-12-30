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

namespace Aheadworks\RewardPoints\Model\ResourceModel\StorefrontLabelsEntity;

use Aheadworks\RewardPoints\Model\ResourceModel\AbstractCollection as BaseAbstractCollection;
use Aheadworks\RewardPoints\Model\StorefrontLabelsResolver;
use Aheadworks\RewardPoints\Api\Data\StorefrontLabelsInterface;
use Aheadworks\RewardPoints\Api\Data\StorefrontLabelsEntityInterface;
use Aheadworks\RewardPoints\Model\ResourceModel\StorefrontLabels\Repository as StorefrontLabelsRepository;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Psr\Log\LoggerInterface;

abstract class AbstractCollection extends BaseAbstractCollection
{
    /**
     * @var int
     */
    protected $storeId;

    /**
     * @var StorefrontLabelsResolver
     */
    protected $storefrontLabelsResolver;

    /**
     * @param EntityFactoryInterface $entityFactory
     * @param LoggerInterface $logger
     * @param FetchStrategyInterface $fetchStrategy
     * @param ManagerInterface $eventManager
     * @param StorefrontLabelsResolver $storefrontLabelsResolver
     * @param AdapterInterface|null $connection
     * @param AbstractDb|null $resource
     */
    public function __construct(
        EntityFactoryInterface $entityFactory,
        LoggerInterface $logger,
        FetchStrategyInterface $fetchStrategy,
        ManagerInterface $eventManager,
        StorefrontLabelsResolver $storefrontLabelsResolver,
        AdapterInterface $connection = null,
        AbstractDb $resource = null
    ) {
        $this->storefrontLabelsResolver = $storefrontLabelsResolver;
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
    }

    /**
     * Set store id for entity labels retrieving
     *
     * @param int $storeId
     * @return $this
     */
    public function setStoreId($storeId)
    {
        $this->storeId = $storeId;
        return $this;
    }

    /**
     * Get store id for entity labels retrieving
     *
     * @return int
     */
    public function getStoreId()
    {
        return $this->storeId;
    }

    /**
     * Redeclare after load method for specifying collection items original data
     *
     * @return $this
     */
    protected function _afterLoad(): self
    {
        $this->attachLabels();
        $this->addCurrentLabels();
        return parent::_afterLoad();
    }

    /**
     * Attach labels on storefront per store view
     *
     * @return void
     */
    protected function attachLabels(): void
    {
        $this->attachRelationTable(
            StorefrontLabelsRepository::MAIN_TABLE_NAME,
            $this->getIdFieldName(),
            'entity_id',
            [
                StorefrontLabelsInterface::PRODUCT_PROMO_TEXT,
                StorefrontLabelsInterface::CATEGORY_PROMO_TEXT,
                StorefrontLabelsInterface::STORE_ID
            ],
            StorefrontLabelsEntityInterface::LABELS,
            [
                [
                    'field' => 'entity_type',
                    'condition' => '=',
                    'value' => $this->getStorefrontLabelsEntityType()
                ]
            ],
            [
                'field' => StorefrontLabelsInterface::STORE_ID,
                'direction' => SortOrder::SORT_ASC
            ],
            true
        );
    }

    /**
     * Retrieve type of entity with storefront labels
     *
     * @return string
     */
    abstract protected function getStorefrontLabelsEntityType(): string;

    /**
     * Add labels on storefront for specific store view
     *
     * @return $this
     */
    protected function addCurrentLabels(): self
    {
        $currentStoreId = $this->getStoreId();
        if (isset($currentStoreId)) {
            foreach ($this as $item) {
                $labelsData = $item->getData(StorefrontLabelsEntityInterface::LABELS);
                if (is_array($labelsData)) {
                    $currentLabelsRecord = $this->storefrontLabelsResolver
                        ->getLabelsForStoreAsArray($labelsData, $currentStoreId);
                    $item->setData(StorefrontLabelsEntityInterface::CURRENT_LABELS, $currentLabelsRecord);
                }
            }
        }
        return $this;
    }
}
