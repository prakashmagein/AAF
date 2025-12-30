<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Model\SelectResolver\SelectColumn;

use Amasty\ReportBuilder\Api\Data\SelectColumnInterface;
use Amasty\ReportBuilder\Model\EntityScheme\Column\ColumnType;
use Magento\Framework\Api\AbstractSimpleObject;

class SelectColumn extends SelectColumnAbstract
{
    public function getType(): string
    {
        return ColumnType::DEFAULT_TYPE;
    }
}
