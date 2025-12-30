<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Model\Backend\Report\DataCollector\Entity;

use Amasty\ReportBuilder\Api\Data\ReportColumnInterface;
use Amasty\ReportBuilder\Api\Data\ReportInterface;

/**
 * Collect columns entity dependencies paths
 * @api SPI
 */
interface EntityCollectorInterface
{
    /**
     * @param ReportInterface $report
     * @param ReportColumnInterface $reportColumn
     * @param array $relations = [
     *     [
     *          ReportInterface::SCHEME_SOURCE_ENTITY => (string)'parent_entity_name',
     *          ReportInterface::SCHEME_ENTITY => (string)'child_entity_name'
     *     ],
     *   ]
     */
    public function collect(ReportInterface $report, ReportColumnInterface $reportColumn, array &$relations): void;
}
