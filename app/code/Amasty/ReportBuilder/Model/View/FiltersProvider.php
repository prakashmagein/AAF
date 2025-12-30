<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Model\View;

use Amasty\ReportBuilder\Model\ReportRegistry;
use Amasty\ReportBuilder\Model\Source\IntervalType;
use Amasty\ReportBuilder\Model\View\Ui\Component\Listing\BookmarkProvider;
use Amasty\ReportBuilder\ViewModel\Adminhtml\View\Toolbar;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Store\Model\Store;

class FiltersProvider
{
    public const FILTER_STORE = 'store';
    public const FILTER_INTERVAL = 'interval';
    public const FILTER_FROM = 'from';
    public const FILTER_TO = 'to';
    public const DEFAULT_INTERVAL = 'day';

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var TimezoneInterface
     */
    private $timezone;

    /**
     * @var ReportRegistry
     */
    private $reportRegistry;

    /**
     * @var BookmarkProvider
     */
    private $bookmarkProvider;

    /**
     * @var array
     */
    private $bookmarkFilters = null;

    public function __construct(
        RequestInterface $request,
        TimezoneInterface $timezone,
        BookmarkProvider $bookmarkProvider,
        ReportRegistry $reportRegistry
    ) {
        $this->request = $request;
        $this->timezone = $timezone;
        $this->bookmarkProvider = $bookmarkProvider;
        $this->reportRegistry = $reportRegistry;
    }

    public function getDateFilter(): array
    {
        $fromFilter = $this->getFilter(self::FILTER_FROM) ?: strtotime(Toolbar::DEFAULT_FROM);
        $toFilter = $this->getFilter(self::FILTER_TO) ?: time();

        $fromFilter = $this->timezone->scopeDate(null, $fromFilter);
        $toFilter = $this->timezone->scopeDate(null, $toFilter);
        $toFilter->setTime(23, 59, 59);

        $filter[self::FILTER_FROM] = $fromFilter;
        $filter[self::FILTER_TO] = $toFilter;

        return $filter;
    }

    public function getInterval(): string
    {
        if ($this->isGridDataExist()) {
            return $this->getGridDataInterval();
        }

        $filter = $this->getFilter(self::FILTER_INTERVAL);
        if (!$filter) {
            $filter = self::DEFAULT_INTERVAL;
        }

        return (string) $filter;
    }

    public function getStoreId(): int
    {
        $filter = $this->getFilter(self::FILTER_STORE) ?? Store::DEFAULT_STORE_ID;
        return (int) $filter;
    }

    public function getGridDataInterval(): string
    {
        if (!$this->isGridDataExist()) {
            return $this->getInterval();
        }

        $filters = $this->getFilter('filters', 'grid_data');
        $filter = $filters[self::FILTER_INTERVAL] ?? self::DEFAULT_INTERVAL;

        return (string) $filter;
    }

    public function getBookMarkFilters(): array
    {
        if (!$this->bookmarkFilters) {
            $filters = [];
            $report = $this->reportRegistry->getReport();
            $bookmarks = $this->bookmarkProvider->execute($report->getReportId());
            foreach ($bookmarks as $bookmark) {
                $config = $bookmark->getConfig();
                $configFilters = $config['current']['filters'] ?? [];
                if (!isset($filters[self::FILTER_FROM]) && isset($configFilters[self::FILTER_FROM])) {
                    $filters[self::FILTER_FROM] = $configFilters[self::FILTER_FROM];
                }
                if (!isset($filters[self::FILTER_TO]) && isset($configFilters[self::FILTER_TO])) {
                    $filters[self::FILTER_TO] = $configFilters[self::FILTER_TO];
                }
                if (!isset($filters[self::FILTER_STORE]) && isset($configFilters[self::FILTER_STORE])) {
                    $filters[self::FILTER_STORE] = $configFilters[self::FILTER_STORE];
                }
                if (!isset($filters[self::FILTER_INTERVAL]) && isset($configFilters[self::FILTER_INTERVAL])) {
                    $filters[self::FILTER_INTERVAL] = $configFilters[self::FILTER_INTERVAL];
                }
            }
            $this->bookmarkFilters = $filters;
        }

        return $this->bookmarkFilters;
    }

    public function getFilter(string $filterName, string $paramName = 'filters')
    {
        $filters = $this->request->getParam($paramName, []);

        if (isset($filters[$filterName])) {
            return $filters[$filterName];
        }

        $bookmarkFilters = $this->getBookMarkFilters();

        return $bookmarkFilters[$filterName] ?? null;
    }

    /**
     * Mean that all filters must be in grid_data array.
     * Grid data used for chart.
     */
    private function isGridDataExist(): bool
    {
        return (bool) $this->request->getParam('grid_data');
    }
}
