<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Model\Chart;

class IsChartTypeAvailable
{
    /**
     * @var string[]
     */
    private $availableChartTypes;

    public function __construct(array $availableChartTypes = [])
    {
        $this->availableChartTypes = $availableChartTypes;
    }

    public function execute(string $chartType): bool
    {
        return in_array($chartType, $this->availableChartTypes, true);
    }
}
