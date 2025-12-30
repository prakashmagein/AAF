<?php
/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * http://landofcoder.com/license
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Landofcoder
 * @package    Lof_ProductShipping
 * @copyright  Copyright (c) 2022 Landofcoder (https://landofcoder.com/)
 * @license    https://landofcoder.com/LICENSE-1.0.html
 */

namespace Lof\ProductShipping\Model\ResourceModel\Shipping;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var string
     */
    protected $_idFieldName = 'lofshipping_id';

    /**
     * @param \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\DB\Adapter\AdapterInterface|null $connection
     * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb|null $resource
     */
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    )
    {
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $connection,
            $resource
        );
        $this->_storeManager = $storeManager;
    }

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Lof\ProductShipping\Model\Shipping', 'Lof\ProductShipping\Model\ResourceModel\Shipping');
        $this->_map['fields']['lofshipping_id'] = 'main_table.lofshipping_id';
    }

    /**
     * @inheritdoc
     */
    protected function _afterLoad()
    {
        // $shippingId = "lofshipping_id";
        // $foreignKey = "lofshipping_id";
        // $childTable = "lof_ps_rate_product";

        $connection = $this->getConnection();
        foreach ($this as $item) {
            $id = $item->getId();

            if ($id) {
                $select = $connection->select()
                    ->from($this->getTable('lof_ps_rate_product'))
                    ->where(
                        'lofshipping_id = ' . (int)$id
                    );
                $products = $connection->fetchAll($select);
                $item->setData('products', $products);

                if ($shippingMethodId = $item->getShippingMethodId()) {
                    $select = $connection->select()
                    ->from($this->getTable('lof_ps_rate_method'))
                    ->where(
                        'entity_id = ' . (int)$shippingMethodId
                    )
                    ->limit(1);

                    $results = $connection->fetchAll($select);
                    if ($results && isset($results[0])) {
                        $item->setData('method_name', $results[0]["method_name"]);
                    }
                }
            }
        }
    }

    /**
     * filter product id
     *
     * @param int $productId
     * @return $this
     */
    public function addProductToFilter($productId)
    {
        $this->getSelect()->join(
            ['rate_product_table' => $this->getTable('lof_ps_rate_product')],
            'main_table.lofshipping_id = rate_product_table.lofshipping_id',
            []
        )
        ->where('rate_product_table.product_id = ?', (int)$productId)
        ->group(
            'main_table.lofshipping_id'
        )
        ->order('rate_product_table.position ASC');

        return $this;
    }

}
