<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Ui\Component\Listing\View\Columns\Adapter;

use Amasty\ReportBuilder\Api\Data\ReportColumnInterface;
use Amasty\ReportBuilder\Model\EntityScheme\Column\DataType;
use Amasty\ReportBuilder\Model\EntityScheme\Provider;
use Amasty\ReportBuilder\Model\ReportResolver;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

class DateAdapter implements AdapterInterface
{
    /**
     * @var TimezoneInterface
     */
    private $timezone;

    /**
     * @var ReportResolver
     */
    private $reportResolver;

    /**
     * @var Provider
     */
    private $schemeProvider;

    public function __construct(TimezoneInterface $timezone, ReportResolver $reportResolver, Provider $schemeProvider)
    {
        $this->timezone = $timezone;
        $this->reportResolver = $reportResolver;
        $this->schemeProvider = $schemeProvider;
    }

    public function modify(ReportColumnInterface $reportColumn, array &$config): void
    {
        if ($config['dataType'] !== 'date') {
            return;
        }

        $type = $this->getDateType($reportColumn);
        foreach ($this->getDateTypeConfig($type) as $key => $value) {
            $config[$key] = $value;
        }
    }

    /**
     * @param ReportColumnInterface $reportColumn
     *
     * @return string
     */
    public function getDateType(
        ReportColumnInterface $reportColumn
    ): string {
        if ($this->isPeriodColumn($reportColumn)) {
            return DataType::DATE;
        }

        $schemeColumn = $this->schemeProvider->getEntityScheme()->getColumnById($reportColumn->getColumnId());
        if ($schemeColumn === null) {
            return DataType::DATE;
        }

        return $schemeColumn->getType() ?: DataType::DATE;
    }

    /**
     * @param string $type
     *
     * @return array
     */
    public function getDateTypeConfig(string $type): array
    {
        $config = [];
        if ($type === DataType::DATETIME || $type === DataType::TIMESTAMP) {
            $config['dateFormat'] = $this->timezone->getDateTimeFormat(\IntlDateFormatter::MEDIUM);
            $config['timezone'] = $this->timezone->getConfigTimezone();
        } else {
            $config['dateFormat'] = $this->timezone->getDateFormat(\IntlDateFormatter::MEDIUM);
            $config['timezone'] = $this->timezone->getDefaultTimezone();
        }

        return $config;
    }

    /**
     * @param ReportColumnInterface $reportColumn
     *
     * @return bool
     */
    public function isPeriodColumn(
        ReportColumnInterface $reportColumn
    ): bool {
        $report = $this->reportResolver->resolve();

        if (!$report->getUsePeriod()) {
            return false;
        }

        $periodColumnId = $this->schemeProvider->getEntityScheme()
            ->getEntityByName($report->getMainEntity())
            ->getPeriodColumn()
            ->getColumnId();

        return $periodColumnId === $reportColumn->getColumnId();
    }
}
