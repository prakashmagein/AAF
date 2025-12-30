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
 * Class Website
 */
class Website implements ProcessorInterface
{
    /**
     * Process data
     *
     * @param array $data
     * @return array
     */
    public function process($data): array
    {
        if (isset($data[SpendRuleInterface::WEBSITE_IDS]) && is_array($data[SpendRuleInterface::WEBSITE_IDS])) {
            foreach ($data[SpendRuleInterface::WEBSITE_IDS] as $key => $value) {
                $data[SpendRuleInterface::WEBSITE_IDS][$key] = (string)$value;
            }
        }
        return $data;
    }
}
