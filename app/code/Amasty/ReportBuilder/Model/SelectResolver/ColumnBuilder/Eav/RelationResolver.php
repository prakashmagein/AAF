<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Model\SelectResolver\ColumnBuilder\Eav;

use Amasty\ReportBuilder\Model\SelectResolver\ColumnBuilder\Eav\Relation\BuilderInterface;
use Amasty\ReportBuilder\Model\SelectResolver\SelectColumn\SelectEavColumn;

class RelationResolver
{
    /**
     * @var BuilderInterface[]
     */
    private $pool;

    public function __construct(array $pool = [])
    {
        $this->pool = $pool;
    }

    public function resolve(
        SelectEavColumn $selectColumn,
        string $linkedField,
        string $indexField,
        string $tableName
    ): array {
        $relations = [];
        foreach ($this->pool as $relationDataBuilder) {
            if ($relationDataBuilder instanceof BuilderInterface && $relationDataBuilder->isApplicable($tableName)) {
                $relations[] = $relationDataBuilder->execute($selectColumn, $linkedField, $indexField, $tableName);
            }
        }

        return $relations;
    }
}
