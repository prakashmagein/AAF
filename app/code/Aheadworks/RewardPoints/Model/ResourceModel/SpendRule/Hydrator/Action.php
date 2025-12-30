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
use Aheadworks\RewardPoints\Model\Action\Converter as ActionConverter;
use Aheadworks\RewardPoints\Api\Data\ActionInterface;
use Magento\Framework\EntityManager\HydratorInterface;
use Magento\Framework\Serialize\SerializerInterface;

/**
 * Class Action
 */
class Action implements HydratorInterface
{
    /**
     * @param ActionConverter $actionConverter
     * @param SerializerInterface $serializer
     */
    public function __construct(
        private ActionConverter $actionConverter,
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
        $action = $entity->getAction();
        if ($action || is_array($action)) {
            $data[SpendRuleInterface::ACTION] = $this->getActionSerialized($action);
        }

        return $data;
    }

    /**
     * Get action serialized
     *
     * @param ActionInterface[] $action
     * @return string
     */
    private function getActionSerialized($action): string
    {
        $actionData = [];
        foreach ($action as $actionItem) {
            $actionData[] = $this->actionConverter->dataModelToArray($actionItem);
        }

        return $this->serializer->serialize($actionData);
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
        if (isset($data[SpendRuleInterface::ACTION])) {
            /** @var SpendRuleInterface $entity */
            $entity->setAction($this->getActionUnserialized($data[SpendRuleInterface::ACTION]));
        }

        return $entity;
    }

    /**
     * Get unserialized action
     *
     * @param string $serializedAction
     * @return ActionInterface[]
     */
    private function getActionUnserialized(string $serializedAction): array
    {
        $actionItems = [];
        $actionData = $this->serializer->unserialize($serializedAction);
        foreach ($actionData as $actionDataItem) {
            $actionItems[] = $this->actionConverter->arrayToDataModel($actionDataItem);
        }
        return $actionItems;
    }
}
