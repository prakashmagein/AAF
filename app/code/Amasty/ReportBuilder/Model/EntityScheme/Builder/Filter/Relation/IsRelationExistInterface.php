<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Model\EntityScheme\Builder\Filter\Relation;

interface IsRelationExistInterface
{
    /**
     * @param array $schemeData All scheme data
     * @param string $entityName
     * @param string $relationName
     * @return bool
     */
    public function execute(array $schemeData, string $entityName, string $relationName): bool;
}
