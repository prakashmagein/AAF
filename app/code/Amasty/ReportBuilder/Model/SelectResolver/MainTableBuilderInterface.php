<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Model\SelectResolver;

use Amasty\ReportBuilder\Model\ResourceModel\Report\Data\Select;

interface MainTableBuilderInterface
{
    /**
     * Method builds main select
     *
     * @param string|null $interval
     * @return Select
     */
    public function build(?string $interval = null): Select;
}
