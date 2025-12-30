<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Ui\Component\Listing\View\Columns;

use Amasty\ReportBuilder\Api\Data\ReportColumnInterface;

/**
 * Collect UI grid column configuration
 */
class ConfigAdapter
{
    /**
     * @var Adapter\AdapterInterface[]
     */
    private $adapters;

    /**
     * @param Adapter\AdapterInterface[] $adapters
     */
    public function __construct(array $adapters = [])
    {
        $this->adapters = $adapters;
    }

    public function execute(ReportColumnInterface $reportColumn): array
    {
        $config = [];

        $config['sortOrder'] = $reportColumn->getPosition();
        $config['label'] = $reportColumn->getCustomTitle();
        $config['aggregation_type'] = $reportColumn->getAggregationType();

        foreach ($this->adapters as $adapter) {
            $adapter->modify($reportColumn, $config);
        }

        return $config;
    }
}
