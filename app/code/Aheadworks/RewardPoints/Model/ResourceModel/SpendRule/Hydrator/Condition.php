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

namespace Aheadworks\RewardPoints\Model\ResourceModel\SpendRule\Hydrator;

use Aheadworks\RewardPoints\Api\Data\SpendRuleInterface;
use Aheadworks\RewardPoints\Model\SpendRule\Condition\Converter as ConditionConverter;
use Aheadworks\RewardPoints\Api\Data\ConditionInterface;
use Magento\Framework\EntityManager\HydratorInterface;
use Magento\Framework\Serialize\SerializerInterface;

/**
 * Class Condition
 */
class Condition implements HydratorInterface
{
    /**
     * @param ConditionConverter $conditionConverter
     * @param SerializerInterface $serializer
     */
    public function __construct(
        private ConditionConverter $conditionConverter,
        private SerializerInterface $serializer
    ) {
    }

    /**
     * Extract data from object
     *
     * @param object $entity
     * @return array
     */
    public function extract($entity): array
    {
        $data = [];
        $condition = $entity->getCondition();
        if ($condition) {
            $data[SpendRuleInterface::CONDITION] = $this->getConditionSerialized($condition);
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
     * Populate entity with data
     *
     * @param object $entity
     * @param array $data
     * @return object
     */
    public function hydrate($entity, array $data)
    {
        if (isset($data[SpendRuleInterface::CONDITION])) {
            /** @var Rule $entity */
            $entity->setCondition($this->getConditionUnserialized($data[SpendRuleInterface::CONDITION]));
        }

        return $entity;
    }

    /**
     * Get unserialized condition
     *
     * @param string $serializedCondition
     * @return ConditionInterface
     */
    private function getConditionUnserialized(string $serializedCondition): ConditionInterface
    {
        $conditionData = $this->serializer->unserialize($serializedCondition);

        return $this->conditionConverter->arrayToDataModel($conditionData);
    }
}
