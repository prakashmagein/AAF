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
namespace Aheadworks\RewardPoints\Model\ResourceModel\StorefrontLabelsEntity\Relation\StorefrontLabels;

use Aheadworks\RewardPoints\Api\Data\StorefrontLabelsEntityInterface;
use Aheadworks\RewardPoints\Api\Data\StorefrontLabelsInterface;
use Aheadworks\RewardPoints\Model\ResourceModel\StorefrontLabels\Repository;
use Magento\Framework\EntityManager\Operation\ExtensionInterface;
use Aheadworks\RewardPoints\Model\StorefrontLabelsResolver;

/**
 * Class ReadHandler
 *
 * @package Aheadworks\RewardPoints\Model\ResourceModel\StorefrontLabelsEntity\Relation\StorefrontLabels
 */
class ReadHandler implements ExtensionInterface
{
    /**
     * @var Repository
     */
    private $repository;

    /**
     * @var StorefrontLabelsResolver
     */
    private $storefrontLabelsResolver;

    /**
     * @param Repository $repository
     * @param StorefrontLabelsResolver $storefrontLabelsResolver
     */
    public function __construct(
        Repository $repository,
        StorefrontLabelsResolver $storefrontLabelsResolver
    ) {
        $this->repository = $repository;
        $this->storefrontLabelsResolver = $storefrontLabelsResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function execute($entity, $arguments = [])
    {
        /** @var StorefrontLabelsEntityInterface $entity */
        if (!(int)$entity->getEntityId()) {
            return $entity;
        }

        /** @var StorefrontLabelsInterface[] $labelsObjects */
        $labelsObjects = $this->repository->get($entity);
        $currentLabelsRecord = $this->storefrontLabelsResolver->getLabelsForStore(
            $labelsObjects,
            isset($arguments['store_id']) ? $arguments['store_id'] : null
        );
        $entity
            ->setLabels($labelsObjects)
            ->setCurrentLabels($currentLabelsRecord);

        return $entity;
    }
}
