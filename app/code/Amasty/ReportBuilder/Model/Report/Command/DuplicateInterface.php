<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Model\Report\Command;

use Amasty\ReportBuilder\Api\Data\ReportInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

interface DuplicateInterface
{
    /**
     * Duplicate report based on input report.
     *
     * @throws CouldNotSaveException
     * @throws NoSuchEntityException
     */
    public function execute(ReportInterface $report): ReportInterface;
}
