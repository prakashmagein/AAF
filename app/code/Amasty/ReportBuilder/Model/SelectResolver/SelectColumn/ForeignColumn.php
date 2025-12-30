<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Model\SelectResolver\SelectColumn;

use Amasty\ReportBuilder\Model\EntityScheme\Column\ColumnType;

/**
 * Linked with parent column
 */
class ForeignColumn extends SelectColumnAbstract
{
    public function getType(): string
    {
        return ColumnType::FOREIGN_TYPE;
    }
}
