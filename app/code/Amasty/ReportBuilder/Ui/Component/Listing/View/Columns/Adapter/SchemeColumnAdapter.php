<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Ui\Component\Listing\View\Columns\Adapter;

use Amasty\ReportBuilder\Api\ColumnInterface;
use Amasty\ReportBuilder\Api\Data\ReportColumnInterface;
use Amasty\ReportBuilder\Model\EntityScheme\Column\OptionsResolver;
use Amasty\ReportBuilder\Model\EntityScheme\Provider;

class SchemeColumnAdapter implements AdapterInterface
{
    /**
     * Scheme data type to UI component data type map
     *
     * @var string[]
     */
    private const DATA_TYPE_MAP = [
        'default' => 'text',
        'text' => 'text',
        'boolean' => 'select',
        'select' => 'select',
        'multiselect' => 'multiselect',
        'date' => 'date',
        'datetime' => 'date',
        'timestamp' => 'date',
    ];

    /**
     * @var Provider
     */
    private $schemaProvider;

    /**
     * @var OptionsResolver
     */
    private $optionsResolver;

    public function __construct(Provider $schemeProvider, OptionsResolver $optionsResolver)
    {
        $this->schemaProvider = $schemeProvider;
        $this->optionsResolver = $optionsResolver;
    }

    public function modify(ReportColumnInterface $reportColumn, array &$config): void
    {
        $schemeColumn = $this->schemaProvider->getEntityScheme()->getColumnById($reportColumn->getColumnId());
        if ($schemeColumn === null) {
            return;
        }

        $parentColumn = $schemeColumn->getParentColumn() ? : $schemeColumn;

        if (!$config['label']) {
            $config['label'] = $this->resolveLabel($schemeColumn);
        }

        if (!isset($config['dataType'])) {
            $config['dataType'] = $this->getDataType($parentColumn->getType());
        }
        if ($config['aggregation_type']) {
            $config['aggregation_type'] = $parentColumn->getAggregationType();
        }

        if ($parentColumn->getUiGridClass()) {
            $config['class'] = $parentColumn->getUiGridClass();
        }

        $this->processFrontendModel($parentColumn, $config);

        if (!$reportColumn->getIsDateFilter()) {
            $config['filter'] = $parentColumn->getFrontendModel();
        }

        $config['options'] = $this->optionsResolver->resolve($schemeColumn, true);
        $config['entity_name'] = $schemeColumn->getEntityName();
    }

    /**
     * Get UI data type by scheme column data type.
     *
     * @param string $type
     *
     * @return string
     */
    public function getDataType(string $type): string
    {
        return self::DATA_TYPE_MAP[$type] ?? self::DATA_TYPE_MAP['default'];
    }

    /**
     * @param ColumnInterface|null $column
     *
     * @return string
     */
    private function resolveLabel(
        ColumnInterface $column
    ): string {
        if ($column->getTitle()) {
            return $column->getTitle();
        }

        if ($column->getParentColumn() !== null) {
            return $column->getParentColumn()->getTitle();
        }

        return '';
    }

    private function processFrontendModel(ColumnInterface $parentColumn, array &$config): void
    {
        if ($parentColumn->getFrontendModel() === 'image') {
            $config['component'] = 'Magento_Ui/js/grid/columns/thumbnail';
            $config['hasPreview'] = 0;
            $config['sortable'] = false;
        }
    }
}
