<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Model\Relation;

use Amasty\ReportBuilder\Api\Data\ReportInterface;

class DependenciesUtility
{
    /**
     * Build scheme entity dependencies pairs by dependencies Path
     * and inject it to main array.
     *
     * @param array $relations = [
     *     [
     *          ReportInterface::SCHEME_SOURCE_ENTITY => (string)'parent_entity_name',
     *          ReportInterface::SCHEME_ENTITY => (string)'child_entity_name'
     *     ],
     *   ]
     * @param string[] $dependenciesPath = \Amasty\ReportBuilder\Model\Relation\DependencyResolver::resolve
     *
     * @return array
     */
    public function injectRelationsByPath(array $relations, array $dependenciesPath): array
    {
        foreach ($dependenciesPath as $sourceEntity) {
            $entity = next($dependenciesPath);
            if ($entity === false) {
                break;
            }
            foreach ($relations as $relation) {
                if ($relation[ReportInterface::SCHEME_ENTITY] === $entity) {
                    continue 2;
                }
            }
            $relations[] = [
                ReportInterface::SCHEME_SOURCE_ENTITY => $sourceEntity,
                ReportInterface::SCHEME_ENTITY => $entity
            ];
        }

        return $relations;
    }
}
