<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Model\SelectResolver\SelectColumn;

use Amasty\ReportBuilder\Model\EntityScheme\Column\ColumnType;

class SelectEavColumn extends SelectColumnAbstract
{
    public const ATTRIBUTE_ID = 'attribute_id';

    public function getType(): string
    {
        return ColumnType::EAV_TYPE;
    }

    public function getAttributeId(): ?int
    {
        return $this->_get(self::ATTRIBUTE_ID) === null ? null
            : (int) $this->_get(self::ATTRIBUTE_ID);
    }

    public function setAttributeId(?int $attributeId): void
    {
        $this->setData(self::ATTRIBUTE_ID, $attributeId);
    }
}
