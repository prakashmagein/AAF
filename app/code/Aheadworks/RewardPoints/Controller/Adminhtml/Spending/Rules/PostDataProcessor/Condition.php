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

use Aheadworks\RewardPoints\Api\Data\ConditionInterface;
use Aheadworks\RewardPoints\Model\Data\ProcessorInterface;
use Aheadworks\RewardPoints\Api\Data\SpendRuleInterface;
use Aheadworks\RewardPoints\Model\SpendRule\Condition\AbstractCondition as ConditionRuleFactory;
use Aheadworks\RewardPoints\Model\SpendRule\Condition\Converter as ConditionConverter;
use Magento\Framework\Serialize\SerializerInterface;

/**
 * Class Condition
 */
class Condition implements ProcessorInterface
{
    /**
     * @var ConditionConverter
     */
    private $conditionConverter;

    /**
     * @var ConditionRuleFactory
     */
    private $conditionRuleFactory;

    /**
     * @var array
     */
    private $typeConditionMap;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @param ConditionConverter $conditionConverter
     * @param ConditionRuleFactory $conditionRuleFactory
     * @param SerializerInterface $serializer
     * @param array $typeConditionMap
     */
    public function __construct(
        ConditionConverter $conditionConverter,
        ConditionRuleFactory $conditionRuleFactory,
        SerializerInterface $serializer,
        array $typeConditionMap
    ) {
        $this->conditionConverter = $conditionConverter;
        $this->conditionRuleFactory = $conditionRuleFactory;
        $this->serializer = $serializer;
        $this->typeConditionMap = $typeConditionMap;
    }

    /**
     * Process data
     *
     * @param array $data
     * @return array
     */
    public function process($data): array
    {
        $conditionType = $this->typeConditionMap[$data[SpendRuleInterface::TYPE]];
        $data[SpendRuleInterface::CONDITION] = $this->prepareRuleConditionData(
            $data,
            $conditionType
        );

        return $data;
    }

    /**
     * Prepare condition data
     *
     * @param array $data
     * @param string $conditionType
     * @return ConditionInterface|string
     */
    private function prepareRuleConditionData(array $data, string $conditionType)
    {
        $ruleConditionArray = [];
        if (isset($data['rule'][$conditionType])) {
            $conditionArray = $this->convertFlatToRecursive($data['rule'], [$conditionType]);
            if (is_array($conditionArray[$conditionType]['1'])) {
                $ruleConditionArray = $conditionArray[$conditionType]['1'];
            }
        } else {
            if (isset($data[SpendRuleInterface::CONDITION])) {
                $ruleConditionArray = $this->serializer->unserialize($data[SpendRuleInterface::CONDITION]);
            } else {
                $conditionRule = $this->conditionRuleFactory->create();
                $defaultConditions = [];
                $defaultConditions['rule'] = [];
                $defaultConditions['rule'][$conditionType] = $conditionRule
                    ->setConditions([])
                    ->getConditions()
                    ->asArray();
                $ruleConditionArray = $this->convertFlatToRecursive($defaultConditions, [$conditionType]);
            }
        }

        $dataModel = $this->conditionConverter->arrayToDataModel($ruleConditionArray);
        return $this->conditionConverter->dataModelToArray($dataModel);
    }

    /**
     * Get conditions data recursively
     *
     * @param array $data
     * @param array $allowedKeys
     * @return array
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    private function convertFlatToRecursive(array $data, array $allowedKeys = []): array
    {
        $result = [];
        foreach ($data as $key => $value) {
            if (in_array($key, $allowedKeys) && is_array($value)) {
                $result += $this->convertDataItem($key, $value);
            }
        }

        return $result;
    }

    /**
     * Convert data item
     *
     * @param string $key
     * @param array $value
     * @return array
     */
    private function convertDataItem(string $key, array $value): array
    {
        $result = [];
        foreach ($value as $id => $data) {
            $path = explode('--', (string)$id);
            $node = & $result;

            for ($i = 0, $l = count($path); $i < $l; $i++) {
                if (!isset($node[$key][$path[$i]])) {
                    $node[$key][$path[$i]] = [];
                }
                $node = & $node[$key][$path[$i]];
            }
            foreach ($data as $k => $v) {
                if (is_array($v)) {
                    foreach ($v as $dk => $dv) {
                        if (empty($dv)) {
                            unset($v[$dk]);
                        }
                    }
                    if (!count($v)) {
                        continue;
                    }
                }

                $node[$k] = $v;
            }
        }
        return $result;
    }
}
