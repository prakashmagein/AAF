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
declare(strict_types=1);

namespace Lof\ProductShipping\Model;

use Lof\ProductShipping\Api\Data\ProductShippingMethodInterface;
use Magento\Framework\DataObject\IdentityInterface;
use \Magento\Framework\Model\AbstractModel;

class Shippingmethod extends AbstractModel implements ProductShippingMethodInterface, IdentityInterface
{
    /**
     * CMS page cache tag
     */
    const CACHE_TAG = 'lofproductshipping';

    /**
     * @var string
     */
    protected $_cacheTag = 'lofproductshipping';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'lofproductshipping';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Lof\ProductShipping\Model\ResourceModel\Shippingmethod');
    }

    /**
     * Return unique ID(s) for each object in system
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getEntityId()];
    }

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getEntityId()
    {
        return $this->getData(self::ENTITY_ID);
    }

    /**
     * set ID
     *
     * @param int $id
     * @return $this
     */
    public function setEntityId($id)
    {
        return $this->setData(self::ENTITY_ID, $id);
    }

    /**
     * @inheritdoc
     */
    public function getPartnerId()
    {
        return $this->getData(self::PARTNER_ID);
    }

    /**
     * @inheritdoc
     */
    public function setPartnerId($partner_id)
    {
        return $this->setData(self::PARTNER_ID, $partner_id);
    }

    /**
     * @inheritdoc
     */
    public function getMethodName()
    {
        return $this->getData(self::METHOD_NAME);
    }

    /**
     * @inheritdoc
     */
    public function setMethodName($method_name)
    {
        return $this->setData(self::METHOD_NAME, $method_name);
    }
}
