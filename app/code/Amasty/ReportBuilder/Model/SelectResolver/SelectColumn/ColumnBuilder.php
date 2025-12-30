<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Model\SelectResolver\SelectColumn;

use Amasty\ReportBuilder\Api\Data\ReportColumnInterface;
use Amasty\ReportBuilder\Exception\NotExistColumnException;
use Amasty\ReportBuilder\Exception\NotExistTableException;
use Amasty\ReportBuilder\Model\EntityScheme\Provider;
use Amasty\ReportBuilder\Model\SelectResolver\SelectColumn\Adapter\ColumnAdapterInterface;

class ColumnBuilder
{
    /**
     * @var Provider
     */
    private $schemeProvider;

    /**
     * @var ColumnAdapterInterface[]
     */
    private $adapters;

    /**
     * @param Provider $schemeProvider
     * @param ColumnAdapterInterface[] $adapters
     */
    public function __construct(Provider $schemeProvider, array $adapters = [])
    {
        $this->schemeProvider = $schemeProvider;
        $this->adapters = $adapters;
    }

    /**
     * @param ReportColumnInterface[] $reportColumns
     * @param ColumnStorageInterface $storage
     *
     * @api
     */
    public function build(array $reportColumns, ColumnStorageInterface $storage): void
    {
        $entityScheme = $this->schemeProvider->getEntityScheme();
        foreach ($reportColumns as $reportColumn) {
            $columnId = $reportColumn->getColumnId();
            $schemeColumn = $entityScheme->getColumnById($columnId);

            foreach ($this->adapters as $columnAdapter) {
                if ($columnAdapter && $columnAdapter->isApplicable($reportColumn, $schemeColumn)) {
                    $selectColumn = $columnAdapter->process($reportColumn, $schemeColumn);
                    $storage->addColumn($columnId, $selectColumn);
                    continue 2;
                }
            }
        }
    }

    /**
     * @throws NotExistColumnException
     * @throws NotExistTableException
     */
    public function validateColumn(ReportColumnInterface $reportColumn): void
    {
        $entityScheme = $this->schemeProvider->getEntityScheme();
        $schemeColumn = $entityScheme->getColumnById($reportColumn->getColumnId());
        foreach ($this->adapters as $adapter) {
            if ($adapter->isApplicable($reportColumn, $schemeColumn)) {
                $adapter->validate($reportColumn->getColumnId());
                return;
            }
        }

        throw new NotExistColumnException(__('Column with ID %1 does not exist', $reportColumn->getColumnId()));
    }
}
