<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Model\Report;

use Amasty\ReportBuilder\Api\ColumnInterface;
use Amasty\ReportBuilder\Api\Data\ReportColumnInterface;
use Amasty\ReportBuilder\Model\ResourceModel\Report\Column\Collection;
use Amasty\ReportBuilder\Model\ResourceModel\Report\Column\CollectionFactory;

class ColumnProvider
{
    /**
     * @var array
     */
    private $collectionRegistry = [];

    /**
     * @var array
     */
    private $sortingColumnRegistry = [];

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var ColumnRegistry
     */
    private $columnRegistry;

    public function __construct(CollectionFactory $collectionFactory, ColumnRegistry $columnRegistry)
    {
        $this->collectionFactory = $collectionFactory;
        $this->columnRegistry = $columnRegistry;
    }

    /**
     * Array key - column id string
     *
     * @api
     * @param int $reportId
     *
     * @return ReportColumnInterface[]
     */
    public function getColumnsByReportId(int $reportId): array
    {
        if (!$this->columnRegistry->isSet($reportId)) {
            foreach ($this->getCollection($reportId)->getItems() as $item) {
                $this->columnRegistry->addItem($reportId, $item);
            }
        }

        return $this->columnRegistry->get($reportId);
    }

    /**
     * @param int $reportId
     *
     * @return ReportColumnInterface|null
     */
    public function getReportSortingColumn(int $reportId): ?ReportColumnInterface
    {
        if (!array_key_exists($reportId, $this->sortingColumnRegistry)) {
            $this->sortingColumnRegistry[$reportId] = null;
            foreach ($this->getColumnsByReportId($reportId) as $column) {
                if ($column->getOrder() !== ColumnInterface::ORDER_NONE) {
                    $this->sortingColumnRegistry[$reportId] = $column;
                    break;
                }
            }
        }

        return $this->sortingColumnRegistry[$reportId];
    }

    /**
     * Returns loaded collection of columns prepared for a Report.
     *
     * @param int $reportId
     *
     * @return Collection
     * @api
     */
    public function getCollection(int $reportId): Collection
    {
        if (!isset($this->collectionRegistry[$reportId])) {
            /** @var Collection $columnCollection */
            $columnCollection = $this->collectionFactory->create();
            $columnCollection->filterByReport($reportId);
            $columnCollection->setOrder(
                ReportColumnInterface::POSITION,
                Collection::SORT_ORDER_ASC
            );
            $columnCollection->load();

            $this->collectionRegistry[$reportId] = $columnCollection;
        }

        return $this->collectionRegistry[$reportId];
    }

    public function clear(?int $reportId = null): void
    {
        if ($reportId) {
            $this->columnRegistry->unset($reportId);
            unset(
                $this->collectionRegistry[$reportId],
                $this->sortingColumnRegistry[$reportId],
            );
        } else {
            $this->columnRegistry->clear();
            $this->collectionRegistry = $this->sortingColumnRegistry = [];
        }
    }
}
