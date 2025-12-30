<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Model\SelectResolver;

use Amasty\ReportBuilder\Api\Data\ReportInterface;
use Amasty\ReportBuilder\Model\ReportResolver;
use Amasty\ReportBuilder\Model\SelectResolver\RelationModifiers\RelationModifierInterface;

class RelationResolver implements RelationResolverInterface
{
    /**
     * @var ReportResolver
     */
    private $reportResolver;

    /**
     * @var RelationStorageInterface
     */
    private $relationStorage;

    /**
     * @var RelationValidatorInterface
     */
    private $relationValidator;

    /**
     * @var RelationModifierInterface[]
     */
    private $pool;

    /**
     * @param ReportResolver $reportResolver
     * @param RelationStorageInterface $relationStorage
     * @param RelationValidatorInterface $relationValidator
     * @param RelationModifierInterface[] $pool
     */
    public function __construct(
        ReportResolver $reportResolver,
        RelationStorageInterface $relationStorage,
        RelationValidatorInterface $relationValidator,
        array $pool = []
    ) {
        $this->reportResolver = $reportResolver;
        $this->relationStorage = $relationStorage;
        $this->relationValidator = $relationValidator;
        $this->pool = $pool;
    }

    public function resolve(): array
    {
        $relations = $this->relationStorage->getAllRelations();
        if (empty($relations)) {

            $relations = $this->getReportRelations();

            $this->relationValidator->execute($relations);
            foreach ($this->pool as $modifier) {
                $relations = $modifier->modify($relations);
            }

            $this->relationStorage->setRelations($relations);
        }

        return $relations;
    }

    public function getRelationByName(string $name): array
    {
        $relations = $this->resolve();

        return $relations[$name] ?? [];
    }

    private function getReportRelations(): array
    {
        $report = $this->reportResolver->resolve();
        $relations = [];

        foreach ($report->getRelationScheme() as $relation) {
            $relations[$relation[ReportInterface::SCHEME_ENTITY]] = $relation;
        }

        return $relations;
    }
}
