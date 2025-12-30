<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Plugin\Reports\Model\Email\ReportContent;

use Amasty\ReportBuilder\Api\Data\ReportColumnInterface;
use Amasty\ReportBuilder\Api\Data\ReportInterface;
use Amasty\ReportBuilder\Model\Email\CsvGenerator;
use Amasty\ReportBuilder\Model\EntityScheme\Column\DataType;
use Amasty\ReportBuilder\Model\EntityScheme\Provider;
use Amasty\ReportBuilder\Model\Report\ColumnProvider;
use Amasty\ReportBuilder\Model\View\ReportLoader;
use Amasty\ReportBuilder\Plugin\Ui\Model\ResourceModel\ModifyNamespace;
use Amasty\Reports\Api\Data\NotificationInterface;
use Amasty\Reports\Model\Email\ReportContent;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

class GetContent
{
    public const AMREPORTBUILDER_REPORT_IDENTIFIER = 'amreportbuilder';

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var TimezoneInterface
     */
    private $timezone;

    /**
     * @var ReportLoader
     */
    private $reportLoader;

    /**
     * @var CsvGenerator
     */
    private $csvGenerator;

    /**
     * @var ColumnProvider
     */
    private $columnProvider;

    /**
     * @var Provider
     */
    private $schemeProvider;

    public function __construct(
        RequestInterface $request,
        TimezoneInterface $timezone,
        ReportLoader $reportLoader,
        CsvGenerator $csvGenerator,
        ColumnProvider $columnProvider,
        Provider $schemeProvider
    ) {
        $this->request = $request;
        $this->timezone = $timezone;
        $this->reportLoader = $reportLoader;
        $this->csvGenerator = $csvGenerator;
        $this->columnProvider = $columnProvider;
        $this->schemeProvider = $schemeProvider;
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundGetContent(
        ReportContent $subject,
        callable $proceed,
        NotificationInterface $notification,
        string $report,
        string $storeId
    ): string {
        if (strpos($report, self::AMREPORTBUILDER_REPORT_IDENTIFIER) === false) {
            return $proceed($notification, $report, $storeId);
        }

        $reportId = (int)str_replace(self::AMREPORTBUILDER_REPORT_IDENTIFIER, '', $report);
        $this->prepareRequest($notification, $reportId, $storeId);
        $this->reportLoader->execute();

        return $this->csvGenerator->getCsvContent($reportId);
    }

    private function prepareRequest(NotificationInterface $notification, int $reportId, string $storeId): void
    {
        $params = $this->request->getParams();
        $params['namespace'] = ModifyNamespace::VIEW_LISTING_NAMESPACE;
        $params['store'] = $storeId;
        $params[ReportInterface::REPORT_ID] = $reportId;
        /** @var ReportColumnInterface $column */
        foreach ($this->columnProvider->getColumnsByReportId($reportId) as $column) {
            if ($this->isDateType($column)) {
                $params['filters'][$column->getColumnAlias()]['from'] = $this->timezone->formatDate(
                    $this->resolveDateShiftByInterval($notification)
                );
                $params['filters'][$column->getColumnAlias()]['to'] = $this->timezone->formatDate();
                break;
            }
        }
        $params['filters']['interval'] = $notification->getDisplayPeriod();

        $this->request->setParams($params);
    }

    private function isDateType(ReportColumnInterface $reportColumn): bool
    {
        $schemeColumn = $this->schemeProvider->getEntityScheme()->getColumnById($reportColumn->getColumnId());
        if ($schemeColumn === null) {
            return false;
        }

        $parentColumn = $schemeColumn->getParentColumn() ?: $schemeColumn;
        return \in_array($parentColumn->getType(), DataType::DATE_TYPES, true);
    }

    private function resolveDateShiftByInterval(NotificationInterface $notification): string
    {
        $intervalQty = $notification->getIntervalQty();
        switch ($notification->getInterval()) {
            case \Amasty\Reports\Model\Source\Date\Interval::MONTH:
                return sprintf('now -%smonth', $intervalQty);
            case \Amasty\Reports\Model\Source\Date\Interval::YEAR:
                return sprintf('now -%syear', $intervalQty);
            default:
                return sprintf('now -%sday', $intervalQty);
        }
    }
}
