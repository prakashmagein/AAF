<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Model\View;

use Amasty\ReportBuilder\Api\Data\ChartInterface;
use Amasty\ReportBuilder\Model\Chart\AxisFactory;
use Amasty\ReportBuilder\Model\Chart\AxisInterface;
use Amasty\ReportBuilder\Model\EntityScheme\Column\DataType;
use Amasty\ReportBuilder\Model\Report\Chart\Axis\Query\GetAxisListInterface;
use Amasty\ReportBuilder\Model\ReportResolver;
use Amasty\ReportBuilder\Model\ResourceModel\Report\Data\Collection;
use IntlDateFormatter;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponentInterface;

class ChartConfig implements ArgumentInterface
{
    /**
     * @var array ['column_id' => AxisInterface, ...]
     */
    private $axises;

    /**
     * @var ReportResolver
     */
    private $resolver;

    /**
     * @var FilterApplier
     */
    private $filterApplier;

    /**
     * @var FiltersProvider
     */
    private $filtersProvider;

    /**
     * @var UiComponentFactory
     */
    private $uiFactory;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var AxisFactory
     */
    private $axisFactory;

    /**
     * @var GetAxisListInterface
     */
    private $getAxisList;

    /**
     * @var TimezoneInterface
     */
    private $timezone;

    public function __construct(
        ReportResolver $resolver,
        FilterApplier $filterApplier,
        FiltersProvider $filtersProvider,
        UiComponentFactory $uiFactory,
        RequestInterface $request,
        AxisFactory $axisFactory,
        GetAxisListInterface $getAxisList,
        TimezoneInterface $timezone
    ) {
        $this->resolver = $resolver;
        $this->filterApplier = $filterApplier;
        $this->filtersProvider = $filtersProvider;
        $this->uiFactory = $uiFactory;
        $this->request = $request;
        $this->axisFactory = $axisFactory;
        $this->getAxisList = $getAxisList;
        $this->timezone = $timezone;
    }

    public function getChartConfig(int $reportId = null): array
    {
        $report = $this->resolver->resolve($reportId);
        $chart = $report->getExtensionAttributes()->getChart();
        if ($chart === null) {
            return [];
        }

        foreach ($this->getAxisList->execute((int) $chart->getId()) as $axis) {
            $currentAxis = $this->getAxis($axis->getValue(), $reportId); // init axis by column id
            if ($currentAxis === null) {
                return []; // no chart data if some of axis undefined
            }
        }

        $config = [
            'data' => $this->getChartData($chart),
            'interval' => $this->filtersProvider->getGridDataInterval()
        ];

        foreach ($this->getAxisList->execute((int) $chart->getId()) as $axis) {
            $config[sprintf('%sAxisType', $axis->getType())] = $this->getAxis($axis->getValue())->getType();
        }

        return $config;
    }

    private function getChartData(ChartInterface $chart): array
    {
        $chartData = [];
        foreach ($this->getCollection() as $item) {
            $axisesData = [];
            foreach ($this->getAxisList->execute((int) $chart->getId()) as $axis) {
                $axisesData[sprintf('value%s', strtoupper($axis->getType()))] = $this->getItemData(
                    $this->getAxis($axis->getValue()),
                    $item
                );
            }
            $chartData[] = $axisesData;
        }

        return $chartData;
    }

    private function getCollection(): Collection
    {
        $bookmarkData = $this->request->getParam('grid_data');
        if (isset($bookmarkData['filters']['applied'])) {
            $this->request->setParam('filters', $bookmarkData['filters']['applied']);
        }

        $component = $this->uiFactory->create('amreportbuilder_view_listing');
        $this->prepareComponent($component);
        $collection = $component->getContext()->getDataProvider()->getCollection();
        $collection->setPageSize(null)->setCurPage(null);

        $this->filterApplier->execute($this->resolver->resolve(), $collection);

        return $collection;
    }

    private function prepareComponent(UiComponentInterface $component)
    {
        foreach ($component->getChildComponents() as $child) {
            $this->prepareComponent($child);
        }

        $component->prepare();
    }

    private function getItemData(AxisInterface $axis, DataObject $item): string
    {
        $options = $axis->getOptions();
        if (!empty($options)) {
            $aggregatedOptions = explode(',', (string)$item->getData($axis->getAlias()));
            $preparedOptions = [];
            foreach ($aggregatedOptions as $option) {
                foreach ($options as $columnOption) {
                    if ($columnOption['value'] == $option) {
                        $preparedOptions[] = $columnOption['label'];
                    }
                }
            }
            if (!$preparedOptions) {
                $preparedOptions = $aggregatedOptions;
            }
            return str_replace('-', ' ', implode(', ', $preparedOptions));
        }

        if (in_array($axis->getType(), DataType::DATE_TYPES, true)) {
            return $this->timezone->formatDateTime(
                $this->timezone->scopeDate(null, (string) $item->getData($axis->getAlias()), true),
                IntlDateFormatter::MEDIUM,
                IntlDateFormatter::MEDIUM
            );
        }

        return str_replace('-', ' ', (string) $item->getData($axis->getAlias()));
    }

    private function getAxis(string $columnId, ?int $reportId = null): ?AxisInterface
    {
        if (!isset($this->axises[$columnId])) {
            $report = $this->resolver->resolve($reportId);
            $this->axises[$columnId] = $this->axisFactory->create($report->getReportId(), $columnId);
        }

        return $this->axises[$columnId];
    }
}
