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
use Amasty\ReportBuilder\Model\SelectResolver\SelectColumn\Adapter\Validator\IsEavAttributeValid;
use Amasty\ReportBuilder\Model\SelectResolver\SelectColumn\SelectEavColumn;
use Amasty\ReportBuilder\Model\SelectResolver\SelectColumn\SelectEavColumnFactory;

class EavEntitiesAdapter implements ColumnAdapterInterface
{
    /**
     * @var SelectEavColumnFactory
     */
    private $columnFactory;

    /**
     * @var DataProvider
     */
    private $dataProvider;

    /**
     * @var Validator\IsEavAttributeValid
     */
    private $validator;

    public function __construct(
        SelectEavColumnFactory $columnFactory,
        DataProvider $dataProvider,
        IsEavAttributeValid $validator
    ) {
        $this->columnFactory = $columnFactory;
        $this->dataProvider = $dataProvider;
        $this->validator = $validator;
    }

    public function process(
        ReportColumnInterface $reportColumn,
        ?ColumnInterface $schemeColumn
    ): SelectColumnInterface {
        $this->validate($reportColumn->getColumnId());

        /** @var SelectEavColumn $selectColumn */
        $selectColumn = $this->columnFactory->create();

        $selectColumn->setExpression($reportColumn->getColumnAlias());
        $selectColumn->setAttributeId($schemeColumn->getAttributeId());

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
        return $schemeColumn !== null && $schemeColumn->getColumnType() === ColumnType::EAV_TYPE;
    }

    public function validate(string $columnId): void
    {
        $this->validator->execute($columnId);
    }
}
