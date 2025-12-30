<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Model\View\Ui\Component\Listing;

use Amasty\ReportBuilder\Api\ColumnInterface;
use Amasty\ReportBuilder\Model\ReportResolver;
use Amasty\ReportBuilder\Model\SelectResolver\Context;
use Amasty\Base\Model\Serializer;
use Amasty\ReportBuilder\Model\View\FiltersProvider;
use Amasty\ReportBuilder\ViewModel\Adminhtml\View\Toolbar;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Store\Model\Store;

class DefaultConfigurationProvider
{
    /**
     * @var ReportResolver
     */
    private $reportResolver;

    /**
     * @var \Amasty\ReportBuilder\Model\EntityScheme\Provider
     */
    private $schemeProvider;

    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * @var TimezoneInterface
     */
    private $timezone;

    /**
     * @var \Amasty\ReportBuilder\Model\Report\ColumnsResolver
     */
    private $columnsResolver;

    public function __construct(
        Context $context,
        Serializer $serializer,
        TimezoneInterface $timezone,
        \Amasty\ReportBuilder\Model\Report\ColumnsResolver $columnsResolver
    ) {
        $this->reportResolver = $context->getReportResolver();
        $this->schemeProvider = $context->getEntitySchemeProvider();
        $this->serializer = $serializer;
        $this->timezone = $timezone;
        $this->columnsResolver = $columnsResolver;
    }

    public function execute(): array
    {
        $report = $this->reportResolver->resolve();
        if (!$report->getReportId()) {
            return [];
        }

        $config = $this->getDefaultConfig();
        $this->modifyFilters($config);
        $this->modifyOrders($config);
        $this->modifyToolbarFilters($config);

        return $config;
    }

    private function getDefaultConfig(): array
    {
        $config = [];
        $scheme = $this->schemeProvider->getEntityScheme();
        $counter = 0;
        foreach ($this->columnsResolver->getReportColumnIds() as $columnId) {
            $column = $scheme->getColumnById($columnId);
            if ($column !== null) {
                $alias = $column->getAlias();
                $config['current']['columns'][$alias]['sorting'] = false;
                $config['current']['columns'][$alias]['visible'] = true;
                $config['current']['positions'][$alias] = $counter++;
            }
        }

        if (!isset($config['current']['filters'])) {
            $config['current']['filters']['applied']['placeholder'] = true;
        }
        $config['current']['displayMode'] = 'grid';

        return $config;
    }

    private function modifyFilters(&$config): void
    {
        $scheme = $this->schemeProvider->getEntityScheme();
        foreach ($this->columnsResolver->getReportColumns() as $reportColumn) {
            if ($filter = $reportColumn->getFilter()) {
                if (!is_array($filter)) {
                    $filter = $this->serializer->unserialize($filter);
                }
                $column = $scheme->getColumnById($reportColumn->getColumnId());
                $config['current']['filters']['applied'][$column->getAlias()] = $filter['value'] ?? $filter;
            }
        }
    }

    private function modifyToolbarFilters(&$config): void
    {
        $config['current']['filters'][FiltersProvider::FILTER_STORE] = Store::DEFAULT_STORE_ID;
        $config['current']['filters'][FiltersProvider::FILTER_INTERVAL] = FiltersProvider::DEFAULT_INTERVAL;
        $from = $this->timezone->date(strtotime(Toolbar::DEFAULT_FROM));
        $config['current']['filters'][FiltersProvider::FILTER_FROM] = $from;
        $config['current']['filters'][FiltersProvider::FILTER_TO] = $this->timezone->date(time());
    }

    private function modifyOrders(&$config): void
    {
        $report = $this->reportResolver->resolve();
        $scheme = $this->schemeProvider->getEntityScheme();
        $columnId = $this->columnsResolver->getSortingColumnId();
        if ($columnId) {
            $alias = $scheme->getColumnById($columnId)->getAlias();
        } else {
            $mainEntity = $scheme->getEntityByName($report->getMainEntity());
            $alias = $mainEntity->getPrimaryColumn()->getAlias();
            if ($report->getUsePeriod()) {
                $alias = $mainEntity->getPeriodColumn()->getAlias();
            }
        }

        $config['current']['columns'][$alias]['sorting'] = strtolower(
            $this->columnsResolver->getSortingColumnExpression()
        );
    }
}
