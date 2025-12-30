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
use Amasty\ReportBuilder\Model\SelectResolver\SelectColumn\Adapter\Validator\IsSimpleColumnValid;
use Amasty\ReportBuilder\Model\SelectResolver\SelectColumn\SelectColumn;
use Amasty\ReportBuilder\Model\SelectResolver\SelectColumn\SelectColumnFactory;

class SimpleEntitiesAdapter implements ColumnAdapterInterface
{
    /**
     * @var SelectColumnFactory
     */
    private $columnFactory;

    /**
     * @var Validator\IsSimpleColumnValid
     */
    private $validator;

    /**
     * @var DataProvider
     */
    private $dataProvider;

    public function __construct(
        SelectColumnFactory $columnFactory,
        IsSimpleColumnValid $validator,
        DataProvider $dataProvider
    ) {
        $this->columnFactory = $columnFactory;
        $this->validator = $validator;
        $this->dataProvider = $dataProvider;
    }

    /**
     * @param ReportColumnInterface $reportColumn
     * @param ColumnInterface|null $schemeColumn
     *
     * @return SelectColumnInterface|null
     */
    public function process(
        ReportColumnInterface $reportColumn,
        ?ColumnInterface $schemeColumn
    ): SelectColumnInterface {
        $columnId = $reportColumn->getColumnId();
        $this->validate($columnId);

        /** @var SelectColumn $selectColumn */
        $selectColumn = $this->columnFactory->create();

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
        return $schemeColumn !== null && $schemeColumn->getColumnType() === ColumnType::DEFAULT_TYPE;
    }

    public function validate(string $columnId): void
    {
        $this->validator->execute($columnId);
    }
}
