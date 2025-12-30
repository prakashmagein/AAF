<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Model\Backend\Report\DataCollector\Columns;

use Amasty\ReportBuilder\Api\Data\ReportColumnInterface;
use Magento\Framework\Serialize\Serializer\Json;

class FilterCollector implements ColumnDataCollectorInterface
{
    public const COLUMN_DATA_FILTER = 'filtration';
    public const COLUMN_DATA_FILTER_IS_ACTIVE = 'isActive';
    public const COLUMN_DATA_FILTER_VALUE = 'value';

    /**
     * @var Json
     */
    private $serializer;

    public function __construct(Json $serializer)
    {
        $this->serializer = $serializer;
    }

    public function collectData(ReportColumnInterface $column, array $columnData): void
    {
        $filter = '';
        $isActiveFilter = $columnData[self::COLUMN_DATA_FILTER][self::COLUMN_DATA_FILTER_IS_ACTIVE] ?? false;
        if ($isActiveFilter) {
            $value = $columnData[self::COLUMN_DATA_FILTER][self::COLUMN_DATA_FILTER_VALUE];
            if (!is_array($value)) {
                $value = ['value' => $value];
            }
            $filter = $value ? $this->serializer->serialize($value) : $value;
        }

        $column->setFilter($filter);
    }
}
