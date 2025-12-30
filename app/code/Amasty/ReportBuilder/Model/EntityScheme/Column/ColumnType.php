<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Model\EntityScheme\Column;

class ColumnType
{
    public const DEFAULT_TYPE = 'default';
    public const EAV_TYPE = 'eav';
    public const FOREIGN_TYPE = 'foreign';

    /**
     * Returns all scheme column types
     *
     * @return string[]
     */
    public function get(): array
    {
        return [
            self::DEFAULT_TYPE => self::DEFAULT_TYPE,
            self::EAV_TYPE => self::EAV_TYPE,
            self::FOREIGN_TYPE =>  self::FOREIGN_TYPE
        ];
    }
}
