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

namespace Aheadworks\RewardPoints\Model\Service\RewardPointsCartService;

use Magento\Framework\DataObject;
use Aheadworks\RewardPoints\Model\Config;

/**
 * @method $this setBaseAvailablePointsAmount(float $baseAvailablePointsAmount)
 * @method $this setAvailablePointsAmount(float $availablePointsAmount)
 * @method $this setAvailablePoints(int $availablePoints)
 * @method $this setBaseUsedPointsAmount(float $baseUsedPointsAmount)
 * @method $this setUsedPointsAmount(float $usedPointsAmount)
 * @method $this setUsedPoints(int $usedPoints)
 * @method $this setBaseItemsTotal(float $baseItemsTotal)
 * @method $this setItemsTotal(float $itemsTotal)
 * @method $this setItemsCount(int $itemsCount)
 * @method $this setBaseShippingAmount(float $baseShippingAmount)
 * @method $this setShippingAmount(float $shippingAmount)
 * @method $this setBaseTaxAmount(float $baseTaxAmount)
 * @method $this setTaxAmount(float $taxAmount)
 * @method $this setLabelName(string $labelName)
 * @method $this setTabLabelName(string $tabLabelName)
 * @method $this setSpendItems(array $spendItems)
 *
 * @method float getBaseAvailablePointsAmount()
 * @method float getAvailablePointsAmount()
 * @method int getAvailablePoints()
 * @method float getBaseUsedPointsAmount()
 * @method float getUsedPointsAmount()
 * @method int getUsedPoints()
 * @method float getBaseItemsTotal()
 * @method float getItemsTotal()
 * @method int getItemsCount()
 * @method float getBaseShippingAmount()
 * @method float getShippingAmount()
 * @method float getBaseTaxAmount()
 * @method float getTaxAmount()
 * @method string getLabelName()
 * @method string getTabLabelName()
 * @method array getSpendItems()
 */
class SpendingData extends DataObject
{
    /**
     * @param array $data
     */
    public function __construct(
        array $data = []
    ) {
        parent::__construct($data);

        $this
            ->setBaseAvailablePointsAmount(.0)
            ->setAvailablePointsAmount(.0)
            ->setAvailablePoints(0)
            ->setBaseUsedPointsAmount(.0)
            ->setUsedPointsAmount(.0)
            ->setUsedPoints(0)
            ->setBaseItemsTotal(.0)
            ->setItemsTotal(.0)
            ->setItemsCount(0)
            ->setBaseShippingAmount(.0)
            ->setShippingAmount(.0)
            ->setBaseTaxAmount(.0)
            ->setTaxAmount(.0)
            ->setTabLabelName(Config::DEFAULT_LABEL_NAME)
            ->setLabelName(Config::DEFAULT_LABEL_NAME);
    }

    /**
     * Get base available points amount left
     *
     * @return float
     */
    public function getBaseAvailablePointsAmountLeft(): float
    {
        return max(.0, $this->getBaseAvailablePointsAmount() - $this->getBaseUsedPointsAmount());
    }

    /**
     * Get available points amount left
     *
     * @return float
     */
    public function getAvailablePointsAmountLeft(): float
    {
        return max(.0, $this->getAvailablePointsAmount() - $this->getUsedPointsAmount());
    }

    /**
     * Get available points left
     *
     * @return int
     */
    public function getAvailablePointsLeft(): int
    {
        return max(0, $this->getAvailablePoints() - $this->getUsedPoints());
    }
}
