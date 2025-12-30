<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Model\Report;

use Amasty\ReportBuilder\Api\Data\ReportColumnInterface;

class ColumnRegistry
{
    /**
     * @var array(ReportColumnInterface[])
     */
    private $registryByReport = [];

    /**
     * @param int $reportId
     *
     * @return ReportColumnInterface[]
     */
    public function get(int $reportId): array
    {
        return $this->registryByReport[$reportId] ?? [];
    }

    /**
     * @param int $reportId
     * @param string $columnId
     *
     * @return ReportColumnInterface|null
     */
    public function getItem(int $reportId, string $columnId): ?ReportColumnInterface
    {
        return $this->registryByReport[$reportId][$columnId] ?? null;
    }

    /**
     * @param int $reportId
     * @param ReportColumnInterface $item
     */
    public function addItem(int $reportId, ReportColumnInterface $item): void
    {
        $this->registryByReport[$reportId][$item->getColumnId()] = $item;
    }

    /**
     * @param int $reportId
     *
     * @return bool
     */
    public function isSet(int $reportId): bool
    {
        return isset($this->registryByReport[$reportId]);
    }

    /**
     * @param int $reportId
     */
    public function unset(int $reportId): void
    {
        unset($this->registryByReport[$reportId]);
    }

    public function clear(): void
    {
        $this->registryByReport = [];
    }
}
