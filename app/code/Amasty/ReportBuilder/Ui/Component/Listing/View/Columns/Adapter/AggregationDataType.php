<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Ui\Component\Listing\View\Columns\Adapter;

use Amasty\ReportBuilder\Api\Data\ReportColumnInterface;
use Amasty\ReportBuilder\Model\EntityScheme\Column\AggregationType;

/**
 * Some aggregation types changes column data type.
 */
class AggregationDataType implements AdapterInterface
{
    public function modify(ReportColumnInterface $reportColumn, array &$config): void
    {
        switch ($reportColumn->getAggregationType()) {
            case AggregationType::TYPE_COUNT:
                $config['dataType'] = 'text';
                unset($config['options']);
                break;
            case AggregationType::TYPE_GROUP_CONCAT:
                $config['dataType'] = 'text';
                break;
        }
    }
}
