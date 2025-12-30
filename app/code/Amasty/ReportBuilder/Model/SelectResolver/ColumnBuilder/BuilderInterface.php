<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Model\SelectResolver\ColumnBuilder;

use Amasty\ReportBuilder\Api\Data\SelectColumnInterface;
use Amasty\ReportBuilder\Model\ResourceModel\Report\Data\Select;

interface BuilderInterface
{
    /**
     * @param Select $select
     * @param SelectColumnInterface $selectColumn
     *
     * @return void
     */
    public function build(Select $select, SelectColumnInterface $selectColumn): void;
}
