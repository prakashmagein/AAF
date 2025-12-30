<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Model\Backend\Report\DataCollector;

use Amasty\ReportBuilder\Api\Data\ReportInterface;
use Amasty\ReportBuilder\Model\Backend\Report\DataCollectorInterface;
use Amasty\ReportBuilder\Model\Report\ColumnProvider;

class Scheme implements DataCollectorInterface
{
    /**
     * @var ColumnProvider
     */
    private $columnProvider;

    /**
     * @var Entity\EntityCollectorInterface[]
     */
    private $entityCollectors;

    /**
     * @param ColumnProvider $columnProvider
     * @param Entity\EntityCollectorInterface[] $entityCollectors
     */
    public function __construct(
        ColumnProvider $columnProvider,
        array $entityCollectors = []
    ) {
        $this->columnProvider = $columnProvider;
        $this->entityCollectors = $entityCollectors;
    }

    /**
     * @param ReportInterface $report
     * @param array $inputData
     *
     * @return array[]
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function collect(ReportInterface $report, array $inputData): array
    {
        $relations = [];
        $columns = $this->columnProvider->getColumnsByReportId($report->getReportId());

        foreach ($columns as $reportColumn) {
            foreach ($this->entityCollectors as $collector) {
                $collector->collect($report, $reportColumn, $relations);
            }
        }

        return [ReportInterface::SCHEME => $relations];
    }
}
