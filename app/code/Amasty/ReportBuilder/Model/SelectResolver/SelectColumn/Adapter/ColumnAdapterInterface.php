<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Model\SelectResolver\SelectColumn\Adapter;

use Amasty\ReportBuilder\Api\ColumnInterface;
use Amasty\ReportBuilder\Api\Data\ReportColumnInterface;
use Amasty\ReportBuilder\Model\SelectResolver\SelectColumn\ColumnStorageInterface;

interface ColumnAdapterInterface
{
    /**
     * Build Select Column
     *
     * @param ReportColumnInterface $reportColumn
     * @param ColumnInterface|null $schemeColumn
     *
     * @return \Amasty\ReportBuilder\Api\Data\SelectColumnInterface|null
     */
    public function process(
        ReportColumnInterface $reportColumn,
        ?ColumnInterface $schemeColumn
    ): \Amasty\ReportBuilder\Api\Data\SelectColumnInterface;

    /**
     * @param ReportColumnInterface $reportColumn
     * @param ColumnInterface|null $schemeColumn
     *
     * @return mixed
     */
    public function isApplicable(ReportColumnInterface $reportColumn, ?ColumnInterface $schemeColumn): bool;

    /**
     * @param string $columnId
     * @throw \Amasty\ReportBuilder\Exception\NotExistColumnException
     */
    public function validate(string $columnId): void;
}
