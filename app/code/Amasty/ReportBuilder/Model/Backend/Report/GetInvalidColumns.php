<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Model\Backend\Report;

use Amasty\ReportBuilder\Api\Data\ReportColumnInterface;
use Amasty\ReportBuilder\Api\EntityScheme\ProviderInterface as SchemeProvider;
use Amasty\ReportBuilder\Model\Report\ColumnsResolver;
use Amasty\ReportBuilder\Model\SelectResolver\SelectColumn\ColumnBuilder;
use Amasty\ReportBuilder\Model\View\ReportLoader;
use Magento\Framework\Exception\LocalizedException;

class GetInvalidColumns
{
    /**
     * @var SchemeProvider
     */
    private $schemeProvider;

    /**
     * @var ColumnsResolver
     */
    private $columnsResolver;

    /**
     * @var ColumnBuilder
     */
    private $columnBuilder;

    public function __construct(
        SchemeProvider $schemeProvider,
        ColumnsResolver $columnsResolver,
        ColumnBuilder $columnBuilder
    ) {
        $this->schemeProvider = $schemeProvider;
        $this->columnsResolver = $columnsResolver;
        $this->columnBuilder = $columnBuilder;
    }

    public function execute(bool $recollectScheme = false): array
    {
        if ($recollectScheme) {
            $this->schemeProvider->clear();
        }

        $invalidColumns = [];
        foreach ($this->columnsResolver->getReportColumns() as $column) {
            try {
                $this->columnBuilder->validateColumn($column);
            } catch (LocalizedException $e) {
                $invalidColumns = $this->populateInvalidColumns($invalidColumns, $column);
            }
        }

        return $invalidColumns;
    }

    /**
     * @param array $invalidColumns ['entityName' => ['columnName', ...], ...]
     * @param ReportColumnInterface $column
     */
    private function populateInvalidColumns(array $invalidColumns, ReportColumnInterface $column): array
    {
        $columnId = $column->getColumnId();
        if (strpos($columnId, '.') !== false) {
            [$entityName, $columnName] = explode('.', $columnId);
        } else {
            $entityName = '-';
            $columnName = $column->getCustomTitle() ?: $columnId;
        }
        if (!isset($invalidColumns[$entityName])) {
            $invalidColumns[$entityName] = [$columnName];
        } else {
            $invalidColumns[$entityName][] = $columnName;
        }

        return $invalidColumns;
    }
}
