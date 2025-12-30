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

namespace Aheadworks\RewardPoints\Model\ResourceModel\SpendRule;

use Magento\Framework\EntityManager\HydratorInterface;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\EntityManager\Hydrator as EntityManagerHydrator;
use Magento\Framework\EntityManager\TypeResolver;
use Magento\Framework\EntityManager\MapperPool;

/**
 * Class Hydrator
 */
class Hydrator extends EntityManagerHydrator
{
    /**
     * @param DataObjectProcessor $dataObjectProcessor
     * @param DataObjectHelper $dataObjectHelper
     * @param TypeResolver $typeResolver
     * @param MapperPool $mapperPool
     * @param array $additionalHydrators
     */
    public function __construct(
        DataObjectProcessor $dataObjectProcessor,
        DataObjectHelper $dataObjectHelper,
        TypeResolver $typeResolver,
        MapperPool $mapperPool,
        private array $additionalHydrators = []
    ) {
        parent::__construct($dataObjectProcessor, $dataObjectHelper, $typeResolver, $mapperPool);
    }

    /**
     * Extract data from object
     *
     * @param object $entity
     * @return array
     */
    public function extract($entity): array
    {
        $data = parent::extract($entity);
        $extractedData = [];
        /** @var HydratorInterface $hydrator */
        foreach ($this->additionalHydrators as $hydrator) {
            $extractedData[] = $hydrator->extract($entity);
        }

        return array_merge($data, ...$extractedData);
    }

    /**
     * Populate entity with data
     *
     * @param object $entity
     * @param array $data
     * @return object
     */
    public function hydrate($entity, array $data): object
    {
        $entity = parent::hydrate($entity, $data);
        /** @var HydratorInterface $hydrator */
        foreach ($this->additionalHydrators as $hydrator) {
            $entity = $hydrator->hydrate($entity, $data);
        }

        return $entity;
    }
}
