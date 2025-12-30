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

namespace Aheadworks\RewardPoints\Model\Calculator;

use Aheadworks\RewardPoints\Model\Config;
use Magento\Catalog\Api\Data\ProductInterface;

/**
 * Class ShareCoveredCalculator
 */
class ShareCoveredCalculator
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @param Config $config
     */
    public function __construct(
        Config $config
    ) {
        $this->config = $config;
    }

    /**
     * Returns the amount that can be covered
     *
     * @param float $price
     * @param int $websiteId
     * @param ProductInterface|null $product
     * @param float|null $rulePercent
     * @return float
     */
    public function calculateCoveredPrice(
        float $price,
        int $websiteId,
        ?ProductInterface $product = null,
        ?float $rulePercent = null
    ): float {
        if (($product instanceof ProductInterface) && $product->getAwRpShareCoveredEnabled()) {
            $shareCoveredPercent = $product->getAwRpShareCoveredPercent();
        }

        if (!isset($shareCoveredPercent)) {
            $shareCoveredPercent = $rulePercent ?? $this->config->getShareCoveredValue($websiteId);
        }

        if ($shareCoveredPercent || $rulePercent !== null) {
            $price *= $shareCoveredPercent / 100;
        }

        return round($price, 2);
    }
}
