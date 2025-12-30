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

namespace Aheadworks\RewardPoints\Model;

use Aheadworks\RewardPoints\Api\Data\ConditionExtensionInterface;
use Aheadworks\RewardPoints\Api\Data\ConditionInterface;
use Magento\Framework\Api\AbstractExtensibleObject;

class Condition extends AbstractExtensibleObject implements ConditionInterface
{
    /**#@+
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    public const TYPE          = 'type';
    public const CONDITIONS    = 'conditions';
    public const AGGREGATOR    = 'aggregator';
    public const OPERATOR      = 'operator';
    public const ATTRIBUTE     = 'attribute';
    public const ATTRIBUTE_SCOPE = 'attribute_scope';
    public const VALUE         = 'value';
    public const VALUE_TYPE    = 'value_type';
    /**#@-*/

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return $this->_get(self::TYPE);
    }

    /**
     * {@inheritdoc}
     */
    public function setType($type)
    {
        return $this->setData(self::TYPE, $type);
    }

    /**
     * {@inheritdoc}
     */
    public function getConditions()
    {
        return $this->_get(self::CONDITIONS);
    }

    /**
     * {@inheritdoc}
     */
    public function setConditions(array $conditions = null)
    {
        return $this->setData(self::CONDITIONS, $conditions);
    }

    /**
     * {@inheritdoc}
     */
    public function getAggregator()
    {
        return $this->_get(self::AGGREGATOR);
    }

    /**
     * {@inheritdoc}
     */
    public function setAggregator($aggregator)
    {
        return $this->setData(self::AGGREGATOR, $aggregator);
    }

    /**
     * {@inheritdoc}
     */
    public function getOperator()
    {
        return $this->_get(self::OPERATOR);
    }

    /**
     * {@inheritdoc}
     */
    public function setOperator($operator)
    {
        return $this->setData(self::OPERATOR, $operator);
    }

    /**
     * {@inheritdoc}
     */
    public function getAttribute()
    {
        return $this->_get(self::ATTRIBUTE);
    }

    /**
     * {@inheritdoc}
     */
    public function setAttribute($attribute)
    {
        return $this->setData(self::ATTRIBUTE, $attribute);
    }

    /**
     * Get attribute scope
     *
     * @return string|null
     */
    public function getAttributeScope(): ?string
    {
        return $this->_get(self::ATTRIBUTE_SCOPE);
    }

    /**
     * Set attribute
     *
     * @param string|null $scope
     * @return $this
     */
    public function setAttributeScope(?string $scope): ConditionInterface
    {
        return $this->setData(self::ATTRIBUTE_SCOPE, $scope);
    }

    /**
     * {@inheritdoc}
     */
    public function getValue()
    {
        return $this->_get(self::VALUE);
    }

    /**
     * {@inheritdoc}
     */
    public function setValue($value)
    {
        return $this->setData(self::VALUE, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getValueType()
    {
        return $this->_get(self::VALUE_TYPE);
    }

    /**
     * {@inheritdoc}
     */
    public function setValueType($valueType)
    {
        return $this->setData(self::VALUE_TYPE, $valueType);
    }

    /**
     * {@inheritdoc}
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * Set an extension attributes object
     *
     * @param ConditionExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        ConditionExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}
