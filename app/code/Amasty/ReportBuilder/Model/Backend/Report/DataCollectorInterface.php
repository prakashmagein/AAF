<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Model\Backend\Report;

use Amasty\ReportBuilder\Api\Data\ReportInterface;

interface DataCollectorInterface
{
    public function collect(ReportInterface $report, array $inputData): array;
}
