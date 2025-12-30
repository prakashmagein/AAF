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
namespace Aheadworks\RewardPoints\Ui\DataProvider\EarnRule\DataProcessor;

use Aheadworks\RewardPoints\Api\Data\EarnRuleInterface;
use Aheadworks\RewardPoints\Model\Data\ProcessorInterface;

/**
 * Class CustomerGroup
 * @package Aheadworks\RewardPoints\Ui\DataProvider\EarnRule\DataProcessor
 */
class CustomerGroup implements ProcessorInterface
{
    /**
     * {@inheritdoc}
     */
    public function process($data)
    {
        if (isset($data[EarnRuleInterface::CUSTOMER_GROUP_IDS])
            && is_array($data[EarnRuleInterface::CUSTOMER_GROUP_IDS])
        ) {
            foreach ($data[EarnRuleInterface::CUSTOMER_GROUP_IDS] as $key => $value) {
                $data[EarnRuleInterface::CUSTOMER_GROUP_IDS][$key] = (string)$value;
            }
        }
        return $data;
    }
}
