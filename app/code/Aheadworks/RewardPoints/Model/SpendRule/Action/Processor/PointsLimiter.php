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

namespace Aheadworks\RewardPoints\Model\SpendRule\Action\Processor;

use Aheadworks\RewardPoints\Model\Calculator\Spending\SpendItemInterface;
use Aheadworks\RewardPoints\Model\SpendRule\Action\ProcessorInterface as ActionProcessorInterface;
use Magento\Framework\Api\AttributeInterface;
use Aheadworks\RewardPoints\Model\Action\AttributeProcessor;

/**
 * Class RateMultiplier
 */
class PointsLimiter implements ActionProcessorInterface
{
    /**
     * RateMultiplier constructor.
     *
     * @param AttributeProcessor $attributeProcessor
     */
    public function __construct(
        private AttributeProcessor $attributeProcessor,
    ) {
    }

    /**
     * Process
     *
     * @param SpendItemInterface $spendItem
     * @param AttributeInterface[] $attributes
     * @param int|null $customerId
     * @param int|null $websiteId
     * @return bool
     */
    public function process(
        SpendItemInterface $spendItem,
        array $attributes,
        ?int $customerId = null,
        ?int $websiteId = null
    ): bool {
        $itemPercent = (float)$spendItem->getShareCoveredPercent();
        $rulePercent = $this->getShareCoveredPercentByAttributes($attributes);
        $spendItem->setShareCoveredPercent($itemPercent + $rulePercent);
        return true;
    }

    /**
     * Get share covered percent by attributes
     *
     * @param array $attributes
     * @return float
     */
    public function getShareCoveredPercentByAttributes(array $attributes): float
    {
        return (float)$this->attributeProcessor->getAttributeValueByCode(
            'coverage_percent',
            $attributes
        );
    }
}
