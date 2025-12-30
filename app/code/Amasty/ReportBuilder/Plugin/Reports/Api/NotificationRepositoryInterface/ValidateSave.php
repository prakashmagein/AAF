<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Plugin\Reports\Api\NotificationRepositoryInterface;

use Amasty\ReportBuilder\Api\Data\ReportColumnInterface;
use Amasty\ReportBuilder\Api\ReportRepositoryInterface;
use Amasty\ReportBuilder\Model\EntityScheme\Column\DataType;
use Amasty\ReportBuilder\Model\EntityScheme\Provider;
use Amasty\ReportBuilder\Model\Report\ColumnProvider;
use Amasty\ReportBuilder\Plugin\Reports\Model\Email\ReportContent\GetContent;
use Amasty\Reports\Api\Data\NotificationInterface;
use Amasty\Reports\Api\NotificationRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;

class ValidateSave
{
    /**
     * @var ColumnProvider
     */
    private $columnProvider;

    /**
     * @var Provider
     */
    private $schemeProvider;

    /**
     * @var ReportRepositoryInterface
     */
    private $reportRepository;

    public function __construct(
        ColumnProvider $columnProvider,
        Provider $schemeProvider,
        ReportRepositoryInterface $reportRepository
    ) {
        $this->columnProvider = $columnProvider;
        $this->schemeProvider = $schemeProvider;
        $this->reportRepository = $reportRepository;
    }

    /**
     * @throws LocalizedException
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeSave(NotificationRepositoryInterface $subject, NotificationInterface $notification): void
    {
        foreach (explode(',', $notification->getReports()) as $reportCode) {
            if (strpos($reportCode, GetContent::AMREPORTBUILDER_REPORT_IDENTIFIER) === false) {
                continue;
            }

            $reportId = (int)str_replace(GetContent::AMREPORTBUILDER_REPORT_IDENTIFIER, '', $reportCode);
            foreach ($this->columnProvider->getColumnsByReportId($reportId) as $column) {
                if ($this->isDateType($column)) {
                    continue 2;
                }
            }

            throw new LocalizedException(__(
                'Please add a \'Date\' data type column to the %1 report to define the time interval
                for report generation and sending.',
                $this->reportRepository->getById($reportId)->getName()
            ));
        }
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
}
