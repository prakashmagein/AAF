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
use Aheadworks\RewardPoints\Model\Action as RuleAction;
use Aheadworks\RewardPoints\Model\Data\ProcessorInterface;
use Magento\Framework\Api\AttributeInterface;

/**
 * Class Action
 * @package Aheadworks\RewardPoints\Ui\DataProvider\EarnRule\DataProcessor
 */
class Action implements ProcessorInterface
{
    /**
     * {@inheritdoc}
     */
    public function process($data)
    {
        if (isset($data[EarnRuleInterface::ACTION]) && is_array($data[EarnRuleInterface::ACTION])) {
            $action = $data[EarnRuleInterface::ACTION];
            $actionData = [
                RuleAction::TYPE => $action[RuleAction::TYPE]
            ];

            if (isset($action[RuleAction::ATTRIBUTES])) {
                foreach ($action[RuleAction::ATTRIBUTES] as $attributeData) {
                    $actionData[$attributeData[AttributeInterface::ATTRIBUTE_CODE]] =
                        (string)$attributeData[AttributeInterface::VALUE];
                }
            }
            $data[EarnRuleInterface::ACTION] = $actionData;
        }
        return $data;
    }
}
