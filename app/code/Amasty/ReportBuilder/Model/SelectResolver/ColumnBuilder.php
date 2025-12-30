<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Model\SelectResolver;

use Amasty\ReportBuilder\Api\Data\SelectColumnInterface;
use Amasty\ReportBuilder\Model\ResourceModel\Report\Data\Select;
use Amasty\ReportBuilder\Model\SelectResolver\ColumnBuilder\BuilderInterface;
use Amasty\ReportBuilder\Model\SelectResolver\ColumnBuilder\RelationHelper;
use Amasty\ReportBuilder\Model\SelectResolver\SelectColumn\ColumnResolverInterface;

/**
 * @api
 */
class ColumnBuilder implements ColumnBuilderInterface
{
    /**
     * @var ColumnResolverInterface
     */
    private $columnResolver;

    /**
     * @var RelationHelper
     */
    private $relationHelper;

    /**
     * @var BuilderInterface[] where keys is a column types
     */
    private $pool;

    /**
     * @param Context $context
     * @param RelationHelper $relationHelper
     * @param BuilderInterface[] $pool where keys is a column types
     */
    public function __construct(
        Context $context,
        RelationHelper $relationHelper,
        array $pool = []
    ) {
        $this->columnResolver = $context->getColumnResolver();
        $this->relationHelper = $relationHelper;
        $this->pool = $pool;
    }

    public function build(Select $select): void
    {
        $columns = $this->columnResolver->resolve()->getAllColumns();

        $this->buildSelectColumn($select, $columns);
    }

    /**
     * Add columns to Select.
     *
     * @param Select $select
     * @param SelectColumnInterface[] $columns
     */
    public function buildSelectColumn(Select $select, array $columns): void
    {
        foreach ($columns as $selectColumn) {
            if (!$this->relationHelper->isColumnInSelect($select, $selectColumn)) {
                $columnBuilder = $this->pool[$selectColumn->getType()] ?? $this->pool['default'];
                $columnBuilder->build($select, $selectColumn);
            }
        }
    }
}
