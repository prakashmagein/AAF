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
namespace Aheadworks\RewardPoints\Model;

use Aheadworks\RewardPoints\Api\Data\SpendRateInterface;
use Aheadworks\RewardPoints\Model\ResourceModel\SpendRate as SpendRateResource;

/**
 * Class Aheadworks\RewardPoints\Model\SpendRate
 */
class SpendRate extends \Magento\Framework\Model\AbstractModel implements SpendRateInterface
{
    /**
     * {@inheritDoc}
     */
    protected function _construct()
    {
        $this->_init(SpendRateResource::class);
    }

    /**
     * {@inheritDoc}
     */
    public function setId($id)
    {
        return $this->setData(self::RATE_ID, $id);
    }

    /**
     * {@inheritDoc}
     */
    public function getId()
    {
        return parent::getData(self::RATE_ID);
    }

    /**
     * {@inheritDoc}
     */
    public function setWebsiteId($websiteId)
    {
        return $this->setData(self::WEBSITE_ID, $websiteId);
    }

    /**
     * {@inheritDoc}
     */
    public function getWebsiteId()
    {
        return parent::getData(self::WEBSITE_ID);
    }

    /**
     * {@inheritDoc}
     */
    public function setCustomerGroupId($customerGroupId)
    {
        return $this->setData(self::CUSTOMER_GROUP_ID, $customerGroupId);
    }

    /**
     * {@inheritDoc}
     */
    public function getCustomerGroupId()
    {
        return parent::getData(self::CUSTOMER_GROUP_ID);
    }

    /**
     * {@inheritDoc}
     */
    public function setLifetimeSalesAmount($lifetimeSalesAmount)
    {
        return $this->setData(self::LIFETIME_SALES_AMOUNT, $lifetimeSalesAmount);
    }

    /**
     * {@inheritDoc}
     */
    public function getLifetimeSalesAmount()
    {
        return parent::getData(self::LIFETIME_SALES_AMOUNT);
    }

    /**
     * {@inheritDoc}
     */
    public function setPoints($points)
    {
        return $this->setData(self::POINTS, $points);
    }

    /**
     * {@inheritDoc}
     */
    public function getPoints()
    {
        return parent::getData(self::POINTS);
    }

    /**
     * {@inheritDoc}
     */
    public function setBaseAmount($baseAmount)
    {
        return $this->setData(self::BASE_AMOUNT, $baseAmount);
    }

    /**
     * {@inheritDoc}
     */
    public function getBaseAmount()
    {
        return parent::getData(self::BASE_AMOUNT);
    }
}
