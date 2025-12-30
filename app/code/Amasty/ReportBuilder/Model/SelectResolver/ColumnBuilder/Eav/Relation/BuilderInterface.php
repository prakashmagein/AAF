<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Model\SelectResolver\ColumnBuilder\Eav\Relation;

use Amasty\ReportBuilder\Model\SelectResolver\SelectColumn\SelectEavColumn;

interface BuilderInterface
{
    /**
     * Build eav sub-select join relation
     *
     * @param SelectEavColumn $selectColumn
     * @param string $linkedField
     * @param string $indexField
     * @param string $tableName
     *
     * @return array
     */
    public function execute(
        SelectEavColumn $selectColumn,
        string $linkedField,
        string $indexField,
        string $tableName
    ): array;

    /**
     * @param string $tableName
     *
     * @return bool
     */
    public function isApplicable(string $tableName): bool;
}
