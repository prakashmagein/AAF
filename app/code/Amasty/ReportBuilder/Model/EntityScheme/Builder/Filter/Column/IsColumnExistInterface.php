<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Model\EntityScheme\Builder\Filter\Column;

use Amasty\ReportBuilder\Exception\NotExistColumnException;

interface IsColumnExistInterface
{
    /**
     * @param array $schemeData
     * @param string $entityName
     * @param string $columnName
     * @return bool
     */
    public function execute(array $schemeData, string $entityName, string $columnName): bool;
}
