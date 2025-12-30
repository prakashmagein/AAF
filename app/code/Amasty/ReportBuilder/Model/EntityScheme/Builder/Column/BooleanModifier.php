<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Model\EntityScheme\Builder\Column;

use Amasty\ReportBuilder\Api\ColumnInterface;
use Magento\Config\Model\Config\Source\Yesno;

class BooleanModifier implements TypeModifierInterface
{
    public function execute(array $data): array
    {
        $data[ColumnInterface::SOURCE_MODEL] = Yesno::class;
        return $data;
    }
}
