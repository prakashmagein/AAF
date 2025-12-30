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

namespace Aheadworks\RewardPoints\Model\Calculator\Earning\Calculator;

use Aheadworks\RewardPoints\Model\Config;
use Aheadworks\RewardPoints\Model\Source\Calculation\PointsRoundingRule;

class Rounding
{
    public function __construct(
        private readonly Config $config
    ) {
    }

    /**
     * Apply rounding
     *
     * @param float $points
     * @param int|null $websiteId
     * @return int
     */
    public function apply(float $points, ?int $websiteId = null): int
    {
        $roundingRule = $this->config->getPointsRoundingRule($websiteId);
        $points = match ($roundingRule) {
            PointsRoundingRule::ROUND_DOWN => floor($points),
            PointsRoundingRule::ROUND_UP => ceil($points),
            default => round($points),
        };

        return (int)$points;
    }
}
