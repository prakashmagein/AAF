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

namespace Aheadworks\RewardPoints\Ui\DataProvider\SpendRule\DataProcessor;

use Aheadworks\RewardPoints\Api\Data\SpendRuleInterface;
use Aheadworks\RewardPoints\Model\Data\ProcessorInterface;

/**
 * Class DiscardSubsequentRules
 */
class DiscardSubsequentRules implements ProcessorInterface
{
    /**
     * Process data
     *
     * @param array $data
     * @return array
     */
    public function process($data): array
    {
        if (isset($data[SpendRuleInterface::DISCARD_SUBSEQUENT_RULES])) {
            $value = (int) $data[SpendRuleInterface::DISCARD_SUBSEQUENT_RULES];
            $data[SpendRuleInterface::DISCARD_SUBSEQUENT_RULES] = (string)$value;
        }
        return $data;
    }
}
