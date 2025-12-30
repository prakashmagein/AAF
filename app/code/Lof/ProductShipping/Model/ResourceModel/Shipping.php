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

namespace Lof\ProductShipping\Model\ResourceModel;

/**
 * ProductShipping mysql resource
 */
class Shipping extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('lof_ps_rate', 'lofshipping_id');
    }

    /**
     * @inheritdoc
     */
    protected function _afterSave(\Magento\Framework\Model\AbstractModel $object)
    {
        // products Related
        if ($relatedProducts = $object->getData('products')) {
            $table = $this->getTable('lof_ps_rate_product');

            $where = ['lofshipping_id = ?' => (int)$object->getId()];
            $this->getConnection()->delete($table, $where);

            $data = [];
            foreach ($relatedProducts as $k => $_post) {
                $position = isset($_post["position"]) ? $_post["position"] : 0;
                $data[] = [
                    'lofshipping_id' => (int)$object->getId(),
                    'product_id' => $k,
                    'position' => isset($_post["product_position"]) ? $_post['product_position'] : $position
                ];
            }
            $this->getConnection()->insertMultiple($table, $data);
        }
        return parent::_afterSave($object);
    }

    /**
     * @inheritdoc
     */
    protected function _beforeDelete(\Magento\Framework\Model\AbstractModel $object)
    {
        // products Related
        if (null !== ($object->getData('products'))) {
            $table = $this->getTable('lof_ps_rate_product');
            $where = ['lofshipping_id = ?' => (int)$object->getId()];
            $this->getConnection()->delete($table, $where);
        }
        return parent::_beforeDelete($object);
    }

    /**
     * @inheritdoc
     */
    protected function _afterLoad(\Magento\Framework\Model\AbstractModel $object)
    {
        if ($id = $object->getId()) {
            $connection = $this->getConnection();
            $select = $connection->select()
                ->from($this->getTable('lof_ps_rate_product'))
                ->where(
                    'lofshipping_id = ' . (int)$id
                );
            $products = $connection->fetchAll($select);
            $object->setData('products', $products);

            if ($shippingMethodId = $object->getShippingMethodId()) {
                $select = $connection->select()
                ->from($this->getTable('lof_ps_rate_method'))
                ->where(
                    'entity_id = ' . (int)$shippingMethodId
                )
                ->limit(1);

                $results = $connection->fetchAll($select);
                if ($results && isset($results[0])) {
                    $object->setData('method_name', $results[0]["method_name"]);
                }
            }
        }
        return parent::_afterLoad($object);
    }

    /**
     * get product ids
     *
     * @param int|null $shippingMethodId
     * @return mixed|array
     */
    public function getProductIds($shippingMethodId = null)
    {
        $productIds = [];
        if ($shippingMethodId) {
            $connection = $this->getConnection();
            $select = $connection->select()
                ->from($this->getTable('lof_ps_rate_product'))
                ->where(
                    'lofshipping_id = '.(int)$shippingMethodId
                );

            $results = $connection->fetchAll($select);

            if ($results) {
                foreach ($results as $_record) {
                    $productIds[] = $_record["product_id"];
                }
            }
        }
        return $productIds;
    }

}


