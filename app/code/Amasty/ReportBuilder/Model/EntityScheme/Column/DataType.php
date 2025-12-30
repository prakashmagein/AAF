<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Model\EntityScheme\Column;

class DataType
{
    public const DATE = 'date';
    public const DATETIME = 'datetime';
    public const TIMESTAMP = 'timestamp';
    public const INTEGER = 'int';
    public const TEXT = 'text';
    public const DECIMAL = 'decimal';
    public const VARCHAR = 'varchar';
    public const BOOLEAN = 'boolean';
    
    public const DATE_TYPES = [
        self::DATE,
        self::DATETIME,
        self::TIMESTAMP
    ];

    /**
     * @var string[]
     */
    private $typeMap = [
        'smallint' => 'int',
        'bigint' => 'int'
    ];

    public function getTypesMap(): array
    {
        return $this->typeMap;
    }
}
