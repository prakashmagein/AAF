<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Model\SelectResolver\SelectColumn;

use Amasty\ReportBuilder\Model\Report\ColumnsResolver;

class ColumnResolver implements ColumnResolverInterface
{
    /**
     * @var ColumnStorageInterface
     */
    private $storage;

    /**
     * @var ColumnsResolver
     */
    private $reportColumnsResolver;

    /**
     * @var ColumnBuilder
     */
    private $columnBuilder;

    public function __construct(
        ColumnStorageInterface $columnStorage,
        ColumnsResolver $columnsResolver,
        ColumnBuilder $columnBuilder
    ) {
        $this->storage = $columnStorage;
        $this->reportColumnsResolver = $columnsResolver;
        $this->columnBuilder = $columnBuilder;
    }

    public function resolve(): ColumnStorageInterface
    {
        if (empty($this->storage->getAllColumns())) {
            $this->columnBuilder->build($this->reportColumnsResolver->getReportColumns(), $this->storage);
        }

        return $this->storage;
    }
}
