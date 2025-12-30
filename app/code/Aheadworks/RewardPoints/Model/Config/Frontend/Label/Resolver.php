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

namespace Aheadworks\RewardPoints\Model\Config\Frontend\Label;

use Aheadworks\RewardPoints\Model\Config;

class Resolver
{
    /**
     * @param Config $config
     */
    public function __construct(private readonly Config $config)
    {
    }

    /**
     * Get label name RewardPoints
     *
     * @param int|null $websiteId
     * @return string
     */
    public function getLabelNameRewardPoints(?int $websiteId = null): string
    {
        $label = $this->config->getLabelNameRewardPoints($websiteId);

        return $label !== Config::DEFAULT_LABEL_NAME ? $label : Config::DEFAULT_POINTS_NAME;
    }
}
