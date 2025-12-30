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

namespace Aheadworks\RewardPoints\Model\SpendRule\Condition;

use Aheadworks\RewardPoints\Api\Data\ConditionInterface;
use Aheadworks\RewardPoints\Api\Data\ConditionInterfaceFactory;

/**
 * Class Converter
 */
class Converter
{
    /**
     * @var ConditionInterfaceFactory
     */
    private $conditionFactory;

    /**
     * @param ConditionInterfaceFactory $conditionFactory
     */
    public function __construct(
        ConditionInterfaceFactory $conditionFactory
    ) {
        $this->conditionFactory = $conditionFactory;
    }

    /**
     * Convert recursive array into condition data model
     *
     * @param array $input
     * @return ConditionInterface
     */
    public function arrayToDataModel(array $input): ConditionInterface
    {
        /** @var ConditionInterface $conditionModel */
        $conditionModel = $this->conditionFactory->create();
        foreach ($input as $key => $value) {
            switch ($key) {
                case 'type':
                    $conditionModel->setType($value);
                    break;
                case 'attribute':
                    $conditionModel->setAttribute($value);
                    break;
                case 'attribute_scope':
                    $conditionModel->setAttributeScope($value);
                    break;
                case 'operator':
                    $conditionModel->setOperator($value);
                    break;
                case 'value':
                    $conditionModel->setValue($value);
                    break;
                case 'value_type':
                    $conditionModel->setValueType($value);
                    break;
                case 'aggregator':
                    $conditionModel->setAggregator($value);
                    break;
                case 'conditions':
                case 'cart':
                case 'catalog':
                    $conditions = [];
                    /** @var array $condition */
                    foreach ($value as $condition) {
                        $conditions[] = $this->arrayToDataModel($condition);
                    }
                    $conditionModel->setConditions($conditions);
                    break;
                default:
            }
        }
        return $conditionModel;
    }

    /**
     * Convert recursive condition data model into array
     *
     * @param ConditionInterface $dataModel
     * @return array
     */
    public function dataModelToArray(ConditionInterface $dataModel): array
    {
        $output = [
            'type' => $dataModel->getType(),
            'attribute' => $dataModel->getAttribute(),
            'attribute_scope' => $dataModel->getAttributeScope(),
            'operator' => $dataModel->getOperator(),
            'value' => $dataModel->getValue(),
            'value_type' => $dataModel->getValueType(),
            'aggregator' => $dataModel->getAggregator()
        ];

        foreach ((array)$dataModel->getConditions() as $conditions) {
            $output['conditions'][] = $this->dataModelToArray($conditions);
        }
        return $output;
    }
}
