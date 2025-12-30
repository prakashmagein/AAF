<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Model\Report\Chart\Query;

use Amasty\ReportBuilder\Api\Data\ChartInterface;
use Magento\Framework\Exception\NoSuchEntityException;

interface GetByReportIdInterface
{
    /**
     * @throws NoSuchEntityException
     */
    public function execute(int $reportId): ChartInterface;
}
