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
namespace Aheadworks\RewardPoints\Model\ResourceModel\EarnRule\Hydrator;

use Aheadworks\RewardPoints\Api\Data\EarnRuleInterface;
use Aheadworks\RewardPoints\Model\EarnRule\Condition\Converter as ConditionConverter;
use Aheadworks\RewardPoints\Api\Data\ConditionInterface;
use Magento\Framework\EntityManager\HydratorInterface;
use Magento\Framework\Serialize\SerializerInterface;

/**
 * Class Condition
 * @package Aheadworks\RewardPoints\Model\ResourceModel\EarnRule\Hydrator
 */
class Condition implements HydratorInterface
{
    /**
     * @var ConditionConverter
     */
    private $conditionConverter;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @param ConditionConverter $conditionConverter
     * @param SerializerInterface $serializer
     */
    public function __construct(
        ConditionConverter $conditionConverter,
        SerializerInterface $serializer
    ) {
        $this->serializer = $serializer;
        $this->conditionConverter = $conditionConverter;
    }

    /**
     * {@inheritdoc}
     */
    public function extract($entity)
    {
        $data = [];
        $condition = $entity->getCondition();
        if ($condition) {
            $data[EarnRuleInterface::CONDITION] = $this->getConditionSerialized($condition);
        }

        return $data;
    }

    /**
     * Get condition serialized
     *
     * @param ConditionInterface $condition
     * @return string
     */
    private function getConditionSerialized($condition)
    {
        $conditionData = $this->conditionConverter->dataModelToArray($condition);

        return $this->serializer->serialize($conditionData);
    }

    /**
     * {@inheritdoc}
     */
    public function hydrate($entity, array $data)
    {
        if (isset($data[EarnRuleInterface::CONDITION])) {
            /** @var Rule $entity */
            $entity->setCondition($this->getConditionUnserialized($data[EarnRuleInterface::CONDITION]));
        }

        return $entity;
    }

    /**
     * Get unserialized condition
     *
     * @param string $serializedCondition
     * @return ConditionInterface
     */
    private function getConditionUnserialized($serializedCondition)
    {
        $conditionData = $this->serializer->unserialize($serializedCondition);

        return $this->conditionConverter->arrayToDataModel($conditionData);
    }
}
