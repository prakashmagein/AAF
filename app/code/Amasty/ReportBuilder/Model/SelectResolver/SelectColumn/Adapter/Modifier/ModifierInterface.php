<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Model\SelectResolver\SelectColumn\Adapter\Modifier;

use Amasty\ReportBuilder\Api\ColumnInterface;
use Amasty\ReportBuilder\Api\Data\SelectColumnInterface;

interface ModifierInterface
{
    public function modify(
        SelectColumnInterface $selectColumn,
        ?ColumnInterface $schemeColumn
    ): void;
}
