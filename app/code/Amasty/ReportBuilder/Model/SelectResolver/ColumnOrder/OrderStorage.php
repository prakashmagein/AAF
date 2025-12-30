<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Model\SelectResolver\ColumnOrder;

use Amasty\ReportBuilder\Api\ColumnInterface;
use Amasty\ReportBuilder\Api\Data\ReportColumnInterface;
use Amasty\ReportBuilder\Model\EntityScheme\Provider;
use Amasty\ReportBuilder\Model\Report\ColumnsResolver;
use Magento\Framework\DB\Select;

class OrderStorage implements OrderStorageInterface
{
    /**
     * @var array
     */
    private $orders = [];

    /**
     * @var ColumnsResolver
     */
    private $columnsResolver;

    /**
     * @var Provider
     */
    private $schemeProvider;

    public function __construct(
        ColumnsResolver $columnsResolver,
        Provider $schemeProvider
    ) {
        $this->columnsResolver = $columnsResolver;
        $this->schemeProvider = $schemeProvider;
    }

    public function addOrder(string $columnName, string $direction): void
    {
        $this->orders[$columnName] = $direction;
    }

    public function removeOrder(string $columnName): void
    {
        unset($this->orders[$columnName]);
    }

    public function getAllOrders(): array
    {
        if (empty($this->orders)) {
            foreach ($this->columnsResolver->getReportColumns() as $reportColumn) {
                if (!$reportColumn->getVisibility() && $reportColumn->getOrder()) {
                    $this->addInvisibleColumnToOrder($reportColumn);
                }
            }
        }

        return $this->orders;
    }

    /**
     * Process Invisible columns with order.
     *
     * Invisible columns is static and cannot be changed on grid UI.
     * The method adds order of invisible column manually.
     */
    private function addInvisibleColumnToOrder(ReportColumnInterface $reportColumn): void
    {
        $schemeColumn = $this->schemeProvider->getEntityScheme()->getColumnById($reportColumn->getColumnId());
        if ($schemeColumn === null) {
            return;
        }

        $this->orders[$schemeColumn->getAlias()] = $reportColumn->getOrder() === ColumnInterface::ORDER_ASC
            ? Select::SQL_ASC
            : Select::SQL_DESC;
    }
}
