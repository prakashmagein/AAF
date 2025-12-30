<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Model\SelectResolver\ColumnBuilder;

use Amasty\ReportBuilder\Api\Data\SelectColumnInterface;
use Amasty\ReportBuilder\Model\EntityScheme\Provider as SchemeProvider;
use Amasty\ReportBuilder\Model\ResourceModel\Report\Data\Select;
use Amasty\ReportBuilder\Model\SelectResolver\ColumnBuilder\Simple\AddColumnToSelect;
use Amasty\ReportBuilder\Model\SelectResolver\Context;
use Amasty\ReportBuilder\Model\SelectResolver\SelectColumn\ForeignColumn;

class Foreign implements BuilderInterface
{
    /**
     * @var SchemeProvider
     */
    private $schemeProvider;

    /**
     * @var AddColumnToSelect
     */
    private $addColumnToSelect;

    public function __construct(
        Context $context,
        AddColumnToSelect $addColumnToSelect
    ) {
        $this->schemeProvider = $context->getEntitySchemeProvider();
        $this->addColumnToSelect = $addColumnToSelect;
    }

    /**
     * @param Select $select
     * @param SelectColumnInterface|ForeignColumn $selectColumn
     */
    public function build(Select $select, SelectColumnInterface $selectColumn): void
    {
        $columnId = $selectColumn->getColumnId();
        $scheme = $this->schemeProvider->getEntityScheme();
        $column = $scheme->getColumnById($columnId);

        $this->addColumnToSelect->execute($select, $column->getParentColumn(), $selectColumn);
    }
}
