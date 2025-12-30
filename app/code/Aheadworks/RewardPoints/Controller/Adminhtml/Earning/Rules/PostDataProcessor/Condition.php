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
namespace Aheadworks\RewardPoints\Controller\Adminhtml\Earning\Rules\PostDataProcessor;

use Aheadworks\RewardPoints\Api\Data\ConditionInterface;
use Aheadworks\RewardPoints\Model\Data\ProcessorInterface;
use Aheadworks\RewardPoints\Api\Data\EarnRuleInterface;
use Aheadworks\RewardPoints\Model\EarnRule\Condition\Rule\CartFactory;
use Aheadworks\RewardPoints\Model\EarnRule\Condition\Converter as ConditionConverter;
use Magento\Framework\Serialize\Serializer\Json;

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
     * @var CartFactory
     */
    private $cartRuleFactory;

    /**
     * @var array
     */
    private $typeConditionMap;

    /**
     * @var Json
     */
    private $serializer;

    /**
     * @param ConditionConverter $conditionConverter
     * @param CartFactory $cartRuleFactory
     * @param Json $serializer
     * @param array $typeConditionMap
     */
    public function __construct(
        ConditionConverter $conditionConverter,
        CartFactory $cartRuleFactory,
        Json $serializer,
        array $typeConditionMap
    ) {
        $this->conditionConverter = $conditionConverter;
        $this->cartRuleFactory = $cartRuleFactory;
        $this->serializer = $serializer;
        $this->typeConditionMap = $typeConditionMap;
    }

    /**
     * {@inheritdoc}
     */
    public function process($data): array
    {
        $conditionType = $this->typeConditionMap[$data[EarnRuleInterface::TYPE]];
        $data[EarnRuleInterface::CONDITION] = $this->prepareCartConditionData(
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
    private function prepareCartConditionData(array $data, string $conditionType)
    {
        $cartConditionArray = [];
        if (isset($data['rule'][$conditionType])) {
            $conditionArray = $this->convertFlatToRecursive($data['rule'], [$conditionType]);
            if (is_array($conditionArray[$conditionType]['1'])) {
                $cartConditionArray = $conditionArray[$conditionType]['1'];
            }
        } else {
            if (isset($data[EarnRuleInterface::CONDITION])) {
                $cartConditionArray = $this->serializer->unserialize($data[EarnRuleInterface::CONDITION]);
            } else {
                $cartRule = $this->cartRuleFactory->create();
                $defaultConditions = [];
                $defaultConditions['rule'] = [];
                $defaultConditions['rule'][$conditionType] = $cartRule
                    ->setConditions([])
                    ->getConditions()
                    ->asArray();
                $cartConditionArray = $this->convertFlatToRecursive($defaultConditions, [$conditionType]);
            }
        }

        $dataModel = $this->conditionConverter->arrayToDataModel($cartConditionArray);
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
            $path = explode('--', $id);
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
