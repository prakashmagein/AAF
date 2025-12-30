<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Model\Backend\Report\DataCollector\Entity;

use Amasty\ReportBuilder\Api\ColumnInterface;
use Amasty\ReportBuilder\Api\Data\ReportColumnInterface;
use Amasty\ReportBuilder\Api\Data\ReportInterface;
use Amasty\ReportBuilder\Model\EntityScheme\Provider;
use Amasty\ReportBuilder\Model\Relation\DependenciesUtility;
use Amasty\ReportBuilder\Model\Relation\DependencyResolver;

/**
 * Collect default columns entity dependencies paths
 */
class ColumnEntity implements EntityCollectorInterface
{
    /**
     * @var Provider
     */
    private $schemeProvider;

    /**
     * @var DependencyResolver
     */
    private $relationDependencyResolver;

    /**
     * @var DependenciesUtility
     */
    private $dependenciesUtility;

    public function __construct(
        Provider $schemeProvider,
        DependencyResolver $relationDependencyResolver,
        DependenciesUtility $dependenciesUtility
    ) {
        $this->schemeProvider = $schemeProvider;
        $this->relationDependencyResolver = $relationDependencyResolver;
        $this->dependenciesUtility = $dependenciesUtility;
    }

    /**
     * @param ReportInterface $report
     * @param ReportColumnInterface $reportColumn
     * @param array $relations = [
     *     [
     *          ReportInterface::SCHEME_SOURCE_ENTITY => (string)'parent_entity_name',
     *          ReportInterface::SCHEME_ENTITY => (string)'child_entity_name'
     *     ],
     *   ]
     */
    public function collect(ReportInterface $report, ReportColumnInterface $reportColumn, array &$relations): void
    {
        $column = $this->getSchemeColumn($reportColumn);

        if ($column === null) {
            return;
        }
        $entityName = $column->getEntityName();

        if ($entityName === $report->getMainEntity()) {
            return;
        }

        foreach ($relations as $relation) {
            if ($relation[ReportInterface::SCHEME_ENTITY] === $entityName) {
                return;
            }
        }

        $dependenciesPath = $this->relationDependencyResolver->resolve(
            $report->getMainEntity(),
            $entityName
        );

        $relations = $this->dependenciesUtility->injectRelationsByPath($relations, $dependenciesPath);
    }

    private function getSchemeColumn(ReportColumnInterface $reportColumn): ?ColumnInterface
    {
        $scheme = $this->schemeProvider->getEntityScheme();

        $column = $scheme->getColumnById($reportColumn->getColumnId());
        if ($column !== null && $column->getParentColumn()) {
            return $column->getParentColumn();
        }
        
        return $column;
    }
}
