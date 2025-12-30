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
use Amasty\ReportBuilder\Model\EntityScheme\Column\ColumnType;
use Amasty\ReportBuilder\Model\SelectResolver\ColumnResolverInterface;
use Amasty\ReportBuilder\Model\SelectResolver\SelectColumn\Adapter\Validator\IsForeignColumnValid;
use Amasty\ReportBuilder\Model\SelectResolver\SelectColumn\ForeignColumn;
use Amasty\ReportBuilder\Model\SelectResolver\SelectColumn\ForeignColumnFactory;

class ForeignAdapter implements ColumnAdapterInterface
{
    /**
     * @var ForeignColumnFactory
     */
    private $columnFactory;

    /**
     * @var Validator\IsForeignColumnValid
     */
    private $validator;

    /**
     * @var DataProvider
     */
    private $dataProvider;

    public function __construct(
        ForeignColumnFactory $columnFactory,
        IsForeignColumnValid $validator,
        DataProvider $dataProvider
    ) {
        $this->columnFactory = $columnFactory;
        $this->validator = $validator;
        $this->dataProvider = $dataProvider;
    }

    public function process(
        ReportColumnInterface $reportColumn,
        ?ColumnInterface $schemeColumn
    ): SelectColumnInterface {

        $columnId = $reportColumn->getColumnId();

        $this->validate($columnId);

        $parentColumn = $schemeColumn->getParentColumn();

        /** @var ForeignColumn $selectColumn */
        $selectColumn = $this->columnFactory->create();

        $selectColumn->setExpression($parentColumn->getColumnId());
        $selectColumn->setExpressionInternal($parentColumn->getColumnId());
        $selectColumn->setEntityName($parentColumn->getEntityName());

        $this->dataProvider->fulfill($selectColumn, $reportColumn, $schemeColumn);

        return $selectColumn;
    }

    /**
     * @param ReportColumnInterface $reportColumn
     * @param ColumnInterface|null $schemeColumn
     *
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function isApplicable(ReportColumnInterface $reportColumn, ?ColumnInterface $schemeColumn): bool
    {
        return $schemeColumn !== null
            && $schemeColumn->getParentColumn() !== null
            && $schemeColumn->getColumnType() === ColumnType::FOREIGN_TYPE;
    }

    public function validate(string $columnId): void
    {
        $this->validator->execute($columnId);
    }
}
