<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Api\Data;

interface ChartInterface
{
    public const ID = 'id';
    public const REPORT_ID = 'report_id';
    public const CHART_TYPE = 'chart_type';

    /**
     * @return int|null
     */
    public function getId();

    /**
     * @return null|int
     */
    public function getReportId(): ?int;

    /**
     * @param int $reportId
     * @return void
     */
    public function setReportId(int $reportId): void;

    /**
     * @return null|string
     */
    public function getChartType(): ?string;

    /**
     * @param string $chartType
     * @return void
     */
    public function setChartType(string $chartType): void;
}
