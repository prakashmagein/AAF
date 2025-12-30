<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Model\SelectResolver;

use Amasty\ReportBuilder\Model\ResourceModel\Report\Data\Select;

interface ColumnBuilderInterface
{
    const ALIAS = 'alias';
    const TABLE = 'table';
    const CONDITION = 'condition';
    const TYPE = 'type';
    const COLUMNS = 'columns';
    const PARENT = 'parent';

    /**
     * Method builds select form before prepared relations
     *
     * @param Select $select
     */
    public function build(Select $select): void;
}
