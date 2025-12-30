<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Model\Report\Chart\Command;

use Amasty\QuoteAttributes\Api\Data\QuoteEntityInterface;
use Amasty\ReportBuilder\Api\Data\ChartInterface;
use Magento\Framework\Exception\CouldNotSaveException;

interface SaveInterface
{
    /**
     * @throws CouldNotSaveException
     */
    public function execute(ChartInterface $chart): void;
}
