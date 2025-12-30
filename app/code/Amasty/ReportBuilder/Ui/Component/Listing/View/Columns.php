<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Ui\Component\Listing\View;

use Amasty\ReportBuilder\Model\Report\ColumnsResolver;
use Amasty\ReportBuilder\Model\SelectResolver\SelectColumn\ColumnBuilder;
use Amasty\ReportBuilder\Ui\Component\Listing\View\Columns\ColumnFactory;
use Amasty\ReportBuilder\Ui\Component\Listing\View\Columns\ConfigAdapter;
use Magento\Framework\View\Element\UiComponent\ContextInterface;

class Columns extends \Magento\Ui\Component\Listing\Columns
{
    /**
     * @var ColumnFactory
     */
    private $columnFactory;

    /**
     * @var ColumnsResolver
     */
    private $columnsResolver;

    /**
     * @var ConfigAdapter
     */
    private $configAdapter;

    /**
     * @var ColumnBuilder
     */
    private $columnBuilder;

    public function __construct(
        ContextInterface $context,
        ColumnFactory $columnFactory,
        ColumnsResolver $columnsResolver,
        ConfigAdapter $configAdapter,
        ColumnBuilder $columnBuilder,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $components, $data);
        $this->columnFactory = $columnFactory;
        $this->columnsResolver = $columnsResolver;
        $this->configAdapter = $configAdapter;
        $this->columnBuilder = $columnBuilder;
    }

    public function prepare(): void
    {
        foreach ($this->columnsResolver->getReportColumns() as $reportColumn) {
            $this->columnBuilder->validateColumn($reportColumn); // exception trigger redirect for edit page
            $name = $reportColumn->getColumnAlias();
            if (!isset($this->components[$name]) && $reportColumn->getVisibility()) {
                $config = $this->configAdapter->execute($reportColumn);
                $column = $this->columnFactory->create($reportColumn->getColumnAlias(), $this->getContext(), $config);
                $column->prepare();
                $this->addComponent($name, $column);
            }
        }

        parent::prepare();
    }
}
