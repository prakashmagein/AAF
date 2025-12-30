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
 * Class Website
 * @package Aheadworks\RewardPoints\Ui\DataProvider\EarnRule\DataProcessor
 */
class Website implements ProcessorInterface
{
    /**
     * {@inheritdoc}
     */
    public function process($data)
    {
        if (isset($data[EarnRuleInterface::WEBSITE_IDS]) && is_array($data[EarnRuleInterface::WEBSITE_IDS])) {
            foreach ($data[EarnRuleInterface::WEBSITE_IDS] as $key => $value) {
                $data[EarnRuleInterface::WEBSITE_IDS][$key] = (string)$value;
            }
        }
        return $data;
    }
}
