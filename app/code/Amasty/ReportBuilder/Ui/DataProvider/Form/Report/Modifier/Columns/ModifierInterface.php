<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Ui\DataProvider\Form\Report\Modifier\Columns;

use Amasty\ReportBuilder\Api\Data\ReportColumnInterface;

interface ModifierInterface
{
    public function convert(ReportColumnInterface $reportColumn, array &$columnData): void;
}
