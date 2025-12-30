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

namespace Aheadworks\RewardPoints\Model\Calculator\Spending\Calculator;

use Aheadworks\RewardPoints\Api\Data\ActionInterface;
use Aheadworks\RewardPoints\Api\Data\SpendRateInterface;
use Aheadworks\RewardPoints\Api\Data\SpendRateInterfaceFactory;
use Aheadworks\RewardPoints\Model\Action\AttributeProcessor;
use Magento\Framework\Api\AttributeInterface;

/**
 * Class RateResolver
 */
class RateResolver
{
    /**
     * RateResolver constructor.
     *
     * @param AttributeProcessor $attributeProcessor
     * @param SpendRateInterfaceFactory $spendRateFactory
     */
    public function __construct(
        private AttributeProcessor $attributeProcessor,
        private SpendRateInterfaceFactory $spendRateFactory
    ) {
    }

    /**
     * Sort rates by lifetime sales amount
     *
     * @param ActionInterface[] $actionItems
     * @return array
     * @throws \Exception
     */
    public function sortRatesByLifetimeSalesAmount(array $actionItems): array
    {
        $result = [];
        foreach ($actionItems as $actionItem) {
            $spendRate = $this->getSpendRateByAttributes($actionItem->getAttributes());
            $result[(float)$spendRate->getLifetimeSalesAmount()] = $actionItem;
        }
        krsort($result);
        return $result;
    }

    /**
     * Get spend rate
     *
     * @param AttributeInterface[] $attributes
     * @return SpendRateInterface
     * @throws \Exception
     */
    public function getSpendRateByAttributes(array $attributes): SpendRateInterface
    {
        $lifetimeSalesAmount = $this->attributeProcessor->getAttributeValueByCode(
            SpendRateInterface::LIFETIME_SALES_AMOUNT,
            $attributes
        );
        $points = $this->attributeProcessor->getAttributeValueByCode(
            SpendRateInterface::POINTS,
            $attributes
        );
        $baseAmount = $this->attributeProcessor->getAttributeValueByCode(
            SpendRateInterface::BASE_AMOUNT,
            $attributes
        );

        $spendRate = $this->spendRateFactory->create();
        return $spendRate
            ->setPoints($points)
            ->setBaseAmount($baseAmount)
            ->setLifetimeSalesAmount($lifetimeSalesAmount);
    }
}
