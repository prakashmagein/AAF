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

namespace Aheadworks\RewardPoints\Controller\Adminhtml\Spending\Rules\PostDataProcessor;

use Aheadworks\RewardPoints\Model\Action as RuleAction;
use Aheadworks\RewardPoints\Model\Data\ProcessorInterface;
use Aheadworks\RewardPoints\Api\Data\SpendRuleInterface;
use Aheadworks\RewardPoints\Model\SpendRule\Action\TypePool as ActionTypePool;
use Magento\Framework\Api\AttributeInterface;

/**
 * Class Action
 */
class Action implements ProcessorInterface
{
    /**
     * @var ActionTypePool
     */
    private $actionTypePool;

    /**
     * Action constructor.
     *
     * @param ActionTypePool $actionTypePool
     */
    public function __construct(
        ActionTypePool $actionTypePool
    ) {
        $this->actionTypePool = $actionTypePool;
    }

    /**
     * Process data
     *
     * @param array $data
     * @return array
     */
    public function process($data): array
    {
        if (isset($data[SpendRuleInterface::ACTION][RuleAction::TYPE])) {
            $actionTypesData = $data[SpendRuleInterface::ACTION][RuleAction::TYPE];
            $data[SpendRuleInterface::ACTION] = [];
            foreach ($actionTypesData as $typeCode => $typeData) {
                $actionType = $this->actionTypePool->getTypeByCode($typeCode);
                $attributesData = [];
                foreach ($actionType->getAttributeCodes() as $attributeCode) {
                    if (isset($typeData[$attributeCode])) {
                        $attributesData[] = [
                            AttributeInterface::ATTRIBUTE_CODE => $attributeCode,
                            AttributeInterface::VALUE => $typeData[$attributeCode]
                        ];
                    }
                }
                $actionData = [
                    RuleAction::TYPE => $typeCode,
                    RuleAction::ATTRIBUTES => $attributesData,
                ];
                $data[SpendRuleInterface::ACTION][] = $actionData;
            }
        }

        return $data;
    }
}
