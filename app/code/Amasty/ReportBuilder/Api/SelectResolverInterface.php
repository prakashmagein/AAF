<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Api;

use Amasty\ReportBuilder\Model\ResourceModel\Report\Data\Select;

interface SelectResolverInterface
{
    public function getSelect(): Select;

    public function setReportId(int $reportId): void;

    public function setInterval(string $interval): void;

    public function applyFilters(Select $select): void;
}
