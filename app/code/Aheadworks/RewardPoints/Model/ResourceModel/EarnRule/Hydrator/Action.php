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
use Aheadworks\RewardPoints\Model\Action\Converter as ActionConverter;
use Aheadworks\RewardPoints\Api\Data\ActionInterface;
use Magento\Framework\EntityManager\HydratorInterface;
use Magento\Framework\Serialize\SerializerInterface;

/**
 * Class Action
 * @package Aheadworks\RewardPoints\Model\ResourceModel\EarnRule\Hydrator
 */
class Action implements HydratorInterface
{
    /**
     * @var ActionConverter
     */
    private $actionConverter;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @param ActionConverter $actionConverter
     * @param SerializerInterface $serializer
     */
    public function __construct(
        ActionConverter $actionConverter,
        SerializerInterface $serializer
    ) {
        $this->actionConverter = $actionConverter;
        $this->serializer = $serializer;
    }

    /**
     * {@inheritdoc}
     */
    public function extract($entity)
    {
        $data = [];
        $action = $entity->getAction();
        if ($action) {
            $data[EarnRuleInterface::ACTION] = $this->getActionSerialized($action);
        }

        return $data;
    }

    /**
     * Get action serialized
     *
     * @param ActionInterface $action
     * @return string
     */
    private function getActionSerialized($action)
    {
        $actionData = $this->actionConverter->dataModelToArray($action);

        return $this->serializer->serialize($actionData);
    }

    /**
     * {@inheritdoc}
     */
    public function hydrate($entity, array $data)
    {
        if (isset($data[EarnRuleInterface::ACTION])) {
            /** @var EarnRuleInterface $entity */
            $entity->setAction($this->getActionUnserialized($data[EarnRuleInterface::ACTION]));
        }

        return $entity;
    }

    /**
     * Get unserialized action
     *
     * @param string $serializedAction
     * @return ActionInterface
     */
    private function getActionUnserialized($serializedAction)
    {
        $actionData = $this->serializer->unserialize($serializedAction);

        return $this->actionConverter->arrayToDataModel($actionData);
    }
}
