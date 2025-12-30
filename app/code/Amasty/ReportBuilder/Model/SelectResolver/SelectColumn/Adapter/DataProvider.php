<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Model\SelectResolver\SelectColumn\Adapter;

use Amasty\ReportBuilder\Api\ColumnInterface;
use Amasty\ReportBuilder\Api\Data\ReportColumnInterface;
use Amasty\ReportBuilder\Api\Data\SelectColumnInterface;
use Amasty\ReportBuilder\Model\SelectResolver\SelectColumn\Adapter\Modifier\ModifierInterface;

class DataProvider
{
    /**
     * @var ModifierInterface[]
     */
    private $modifiers;

    /**
     * @param ModifierInterface[] $modifiers
     */
    public function __construct(array $modifiers = [])
    {
        $this->modifiers = $modifiers;
    }

    public function fulfill(
        SelectColumnInterface $selectColumn,
        ReportColumnInterface $reportColumn,
        ?ColumnInterface $schemeColumn
    ): void {
        if ($selectColumn->getColumnId() === null) {
            $selectColumn->setColumnId($reportColumn->getColumnId());
        }

        if ($selectColumn->getAlias() === null) {
            $selectColumn->setAlias($reportColumn->getColumnAlias());
        }

        if ($schemeColumn !== null) {
            if ($selectColumn->getEntityName() === null) {
                $selectColumn->setEntityName($schemeColumn->getEntityName());
            }

            if (!empty($reportColumn->getAggregationType())) {
                $schemeColumn->setAggregationType((string) $reportColumn->getAggregationType());
            }
        }

        foreach ($this->modifiers as $modifier) {
            $modifier->modify($selectColumn, $schemeColumn);
        }
    }
}
